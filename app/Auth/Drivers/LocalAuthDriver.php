<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\Drivers;

use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\InvalidAppPwdException;
use App\Auth\Exceptions\MustChangePwdException;
use App\Auth\Exceptions\PasswordChangeException;
use App\Auth\User;
use App\Events\PasswordReset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LocalAuthDriver extends AuthDriver
{
    const RESET_PASSWORD = 1;
    const PWD_EXPIRES = 1;
    const PWD_COMPLEXITY = 1;


    /**
     * @param array $data
     * @param $guard
     * @param bool|null $remember
     *
     *  Execute logic to authenticate via local provider
     *
     * @return User|false
     * @throws AuthTypeException
     * @throws InvalidAppPwdException
     * @throws MustChangePwdException
     */
    public function authenticate(array $data, $guard = null, ?bool $remember = false): User|false
    {
        Log::channel('auth')->info('Login attempt', ['user' => $data['user'] ?? null, 'driver' => class_basename($this), 'guard' => $guard, 'remember' => $remember]);
        if (Auth::once(['user' => $data['user'], 'password' => $data['password']])) {
            Log::channel('auth')->info('Credentials check passed', ['user' => $data['user'], 'driver' => class_basename($this)]);
            $local_user = $this->getAppUserInstance($data);
            $this->postAuthChecks($local_user);
            $this->appLogin($local_user, $guard, $remember);
            $this->onSuccessLogin($local_user);
            return $local_user;
        } else {
            // Utente ha sbagliato credenziali sul provider, cerchiamo utenza localmente
            $local_user = User::where('user', $data['user'])->where('auth_type_id', AuthType::LOCAL)->first();
            if ($local_user) {
                // se trovo utenza locale lancio logiche aggiuntive della failedLogin
                $this->onFailedLogin($local_user);
                throw new InvalidAppPwdException($local_user);
            } else {
                throw new AuthTypeException(trans('auth.failed'));
            }
        }
    }

    /**
     *
     * Method that will execute after login checks on specific User account
     *
     * @throws AuthTypeException
     * @throws MustChangePwdException
     */
    public function postAuthChecks($user): bool
    {
        $this->checkAppUserEnabled($user);
        $this->checkAppUserExpired($user);
        $this->checkAppUserLocked($user);
        $this->checkAppUserMustChangePwd($user);
        $this->checkAppUserPwdRules($user);
        return true;
    }

    /**
     *
     * Return AuthType related to Local Auth Driver
     *
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return AuthType::find(AuthType::LOCAL);
    }

    /**
     * @param $user
     * @param $data
     * @return bool
     * @throws PasswordChangeException
     */
    public function resetPassword($user, $data): bool
    {
        Log::channel('auth')->info('Resetting account password', ['user' => $user->user, 'driver' => class_basename($this)]);
        if (Hash::check($data['password'], $user->password)) {
            Log::channel('auth')->info('Failed resetting account password: password already used', ['user' => $user->user, 'driver' => class_basename($this)]);
            throw new PasswordChangeException(trans('passwords.change.same'));
        }
        $user->password = bcrypt($data['password']);
        $user->pwd_change_required = 0;
        $user->pwd_changed_at = Carbon::now();
        $user->save();

        $user->checkPasswordHistories($user);

        event(new PasswordReset($user));
        Log::channel('auth')->info('Account password successfully reset', ['user' => $user->user, 'driver' => class_basename($this)]);
        return true;

    }

    /**
     * @param $user
     * @param $data
     *
     * Execute logic to change user password
     *
     * @return bool
     * @throws PasswordChangeException
     */
    public function changePassword($user, $data): bool
    {
        Log::channel('auth')->info('Changing account password', ['user' => $user->user, 'driver' => class_basename($this)]);
        if (Hash::check($data['password'], $user->password)) {
            Log::channel('auth')->info('Failed changing account password: password already used', ['user' => $user->user, 'driver' => class_basename($this)]);
            throw new PasswordChangeException(trans('passwords.change.same'));
        }
        if (!Hash::check($data['current_password'], $user->password)) {
            Log::channel('auth')->info('Failed changing account password: wrong password', ['user' => $user->user, 'driver' => class_basename($this)]);
            throw new PasswordChangeException(trans('passwords.change.wrong_password'));

        }
        $user->password = bcrypt($data['password']);
        $user->pwd_change_required = 0;
        $user->pwd_changed_at = Carbon::now();
        $user->save();

        $user->checkPasswordHistories($user);

        event(new PasswordReset($user));
        Log::channel('auth')->info('Account password successfully changed', ['user' => $user->user, 'driver' => class_basename($this)]);
        return true;

    }

}