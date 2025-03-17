<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\Drivers;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\MustChangePwdException;
use App\Auth\User;
use App\Events\LoginFailed;
use App\Events\LoginSuccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

abstract class AuthDriver
{

    /**
     * RESET_PASSWORD will flag the driver as available for ResetPasswords flow,
     * if extended driver will need password reset functionalities, it will need to implement
     * the proper logic in changePassword and resetPassword methods
     * the canResetPwd method will check this const against the AppConfiguration setting
     */

    const RESET_PASSWORD = 0;

    /**
     * PWD_EXPIRES will flag extended driver password as subject to expiration.
     * the pwdExpires method will check this const against the AppConfiguration setting
     * for password expiration purpose.
     */

    const PWD_EXPIRES = 0;

    /**
     * PWD_COMPLEXITY will flag driver if need to validate via regex provided password.
     *
     */

    const PWD_COMPLEXITY = 0;

    /**
     * @param array $data
     * @param null $guard
     * @return User|false
     *
     * Start the login flow
     */
    public abstract function authenticate(array $data, $guard = null): User|false;


    /**
     * @return AuthType
     */
    public abstract function getAuthType(): AuthType;

    /**
     * @param $data
     * @return User
     * @throws AuthTypeException
     *
     * Return the App\Auth\User instance of logged user.
     *
     */
    public function getAppUserInstance($data): User
    {
        $auth_type = $this->getAuthType();
        $local_user = User::where('auth_type_id', $auth_type->id)->where('user', $data['user'])->first();
        if (!$local_user) {
            if ($auth_type->isEnabled() && $auth_type->isAutoRegEnabled() && $local_user = $this->autoRegister($data)) {
                return $local_user;
            } else {
                throw new AuthTypeException(trans('auth.account_not_configured'));
            }
        } else {
            $local_user->password = bcrypt($data['password']);
            $local_user->save();
        }
        return $local_user;
    }

    /**
     * @param array $data
     * @return User|false
     *
     * Create the local Auth/User based on $data array
     *
     */
    public function autoRegister(array $data): User|false
    {
        return false;
    }

    /**
     * @param User $user
     * @return bool
     *
     * Execute the "checks" methods to validate User after successfully authenticated
     *
     * @throws AuthTypeException
     */

    public function postAuthChecks(User $user): bool
    {
        $this->checkAppUserEnabled($user);
        $this->checkAppUserExpired($user);
        $this->checkAppUserLocked($user);

        return true;
    }

    /**
     * @param User $user
     * @return bool
     *
     * Check the Password Complexity rule and updated pwd_change_required field if needed.
     *
     */
    public function checkAppUserPwdRules(User $user): bool
    {
        $app_config = AppConfiguration::current();

        if (!$this->pwdComplexity(request('password'))) {
            Session::flash('alerts', [['type' => 'warning', 'message' => trans('auth.insecure_pwd_should_be_changed') . '<br>' . $app_config->pwd_complexity_err_msg, 'options' => '{timeOut:0,extendedTimeOut:0}']]);
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'pwd_change_required' => 1
                ]);
        }
        return true;
    }


    /**
     * @param User $user
     * @return bool
     * @throws AuthTypeException
     *
     * Check if User is flagged as enabled.
     *
     */
    public function checkAppUserEnabled(User $user): bool
    {
        if (!$user->isEnabled()) {
            Log::channel('auth')->info('Local login denied: account is disabled',['user' => $user->user]);
            throw new AuthTypeException(trans('auth.disabled'));
        } else {
            return true;
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws AuthTypeException
     *
     * Check is User is flagged as locked.
     *
     */
    public function checkAppUserLocked(User $user): bool
    {
        if ($user->isLocked()) {
            Log::channel('auth')->info('Local login denied: account is locked',['user' => $user->user]);
            throw new AuthTypeException(trans('auth.locked'));
        } else {
            return true;
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws AuthTypeException
     *
     * Check if User is flagged as expired.
     *
     */
    public function checkAppUserExpired(User $user): bool
    {
        if (!$user->isNotExpired()) {
            Log::channel('auth')->info('Local login denied: account is expired',['user' => $user->user]);
            throw new AuthTypeException(trans('auth.expired'));

        } else {
            return true;
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws MustChangePwdException
     *
     * Check if user need to change pwd before login.
     *
     */
    public function checkAppUserMustChangePwd(User $user): bool
    {
        if ($user->mustChangePassword() && $this->canResetPwd()) {
            Log::channel('auth')->info('Password reset required before login',['user' => $user->user]);
            throw new MustChangePwdException(trans('passwords.must_reset_password'));

        } else {
            return true;
        }
    }

    /**
     * @param User $user
     * @param null $guard
     * @param bool|null $remember
     * @return bool
     *
     * Performs the login initializing the authenticated session for the local app
     */
    public final function appLogin(User $user, $guard = null, ?bool $remember = false): bool
    {
        $guard = ($guard && array_key_exists($guard, config('auth.guards'))) ? $guard : config('auth.defaults.guard');
        Auth::guard($guard)->login($user, $remember);
        Log::channel('auth')->info('Authenticated session initialized', ['user' => $user->user, 'driver' => class_basename($this), 'guard' => $guard, 'remember' => $remember]);
        return true;
    }

    /**
     * @param $user
     *
     * Additional logic for onFailedLogin case
     *
     */

    public function onFailedLogin($user): void
    {
        event(new LoginFailed($user));
    }

    /**
     * @param $user
     *
     * Additional logic for onSuccessLogin case (es. roles bindingins)
     *
     */

    public function onSuccessLogin($user): void
    {
        event(new LoginSuccess($user));
    }

    /**
     * @return bool
     *
     * Check if AuthDriver is configured for password expiration and compare it with AppConfiguration setting.
     *
     */

    public function pwdExpires(): bool
    {
        return $this::PWD_EXPIRES && (optional(AppConfiguration::current(true))->isPasswordExpiresEnabled() ?? false);
    }

    /**
     * @param string $password
     *
     *
     *
     * @return bool
     */
    public function pwdComplexity(string $password): bool
    {
        if ($this::PWD_COMPLEXITY) {
            return $this->checkPwdComplexity($password);
        }
        return true;
    }

    private function checkPwdComplexity(string $password): bool
    {
        //Regex di default prevede una lettera maiuscola, un numero, un carattere speciale tra !$#%@^&+= ed essere lunga almeno 8 caratteri.
        $custom_regex = '/^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d])(?=.*[!$#%@^&+=]).*$/';
        $app_config = AppConfiguration::current();
        return preg_match($app_config->pwd_regexp ? $app_config->pwd_regexp : $custom_regex, $password);
    }

    /**
     * @return bool
     *
     * Check if AuthDriver is configured for reset passwords and compare it with AppConfiguration setup.
     *
     */
    public function canResetPwd(): bool
    {
        return $this::RESET_PASSWORD && optional(AppConfiguration::current(true))->isPasswordResetEnabled() ?? false;
    }

    /**
     * @return bool
     *
     * AuthDriver change password logics.
     *
     */

    public function changePassword(User $user, array $data): bool
    {
        return false;
    }

    /**
     * @return bool
     *
     * AuthDriver reset password logics.
     *
     */
    public function resetPassword(User $user, array $data): bool
    {
        return false;
    }
}