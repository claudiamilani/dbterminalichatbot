<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\Drivers;


use Adldap\Auth\BindException;
use Adldap\Auth\PasswordRequiredException;
use Adldap\Auth\UsernameRequiredException;
use Adldap\Laravel\Facades\Adldap;
use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\InvalidAppPwdException;
use App\Auth\Exceptions\MustChangePwdException;
use App\Auth\Exceptions\PasswordChangeException;
use App\Auth\User;
use App\Events\PasswordReset;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LdapAuthDriver extends AuthDriver
{

    const RESET_PASSWORD = 1;
    const PWD_EXPIRES = 1;
    const PWD_COMPLEXITY = 1;


    /**
     * @param array $data
     * @param $guard
     * @param bool|null $remember
     *
     *  Execute logic to authenticate via LDAP provider
     *
     * @return User|false
     * @throws AuthTypeException
     * @throws BindException
     * @throws InvalidAppPwdException
     * @throws MustChangePwdException
     * @throws PasswordRequiredException
     * @throws UsernameRequiredException
     */
    public function authenticate(array $data, $guard = null, ?bool $remember = false): User|false
    {
        Log::channel('auth')->info('Login attempt', ['user' => $data['user'] ?? null, 'driver' => class_basename($this), 'guard' => $guard, 'remember' => $remember]);
        if (Adldap::auth()->attempt($data['user'], $data['password'], true)) {
            Log::channel('auth')->info('Credentials check passed', ['user' => $data['user'], 'driver' => class_basename($this)]);
            $local_user = $this->getAppUserInstance($data);
            $this->postAuthChecks($local_user);
            $this->appLogin($local_user,$guard,$remember);
            $this->onSuccessLogin($local_user);
            return $local_user;
        } else {
            // Utente ha sbagliato credenziali sul provider, cerchiamo utenza localmente
            $local_user = User::where('user', $data['user'])->where('auth_type_id', AuthType::LDAP)->first();
            if ($local_user) {
                // se trovo utenza locale lancio logiche aggiuntive della failedLogin
                $this->onFailedLogin($local_user);
                throw new InvalidAppPwdException($local_user);
            } else {
                throw new AuthTypeException(trans('auth.ldap_unknown_user'));
            }
        }
    }

    /**
     * @param $data
     *
     * Method to automatically create a local User starting from an LDAP user.
     *
     * @return User|false
     * @throws AuthTypeException
     */
    public function autoRegister($data): User|false
    {
        $ldap_user = Adldap::search()->where('sAMAccountName', '=', $data['user'])->first();

        if (empty($ldap_user->givenname[0]) || empty($ldap_user->sn[0]) || empty($ldap_user->mail[0])) {
            Log::channel('auth')->info('Missing required informations to autoregister user account', ['user' => $data['user'], 'driver' => class_basename($this)]);
            throw new AuthTypeException(trans('auth.account_not_configured'));
        }

        $user = new User();
        $user->user = $data['user'];
        $user->name = $ldap_user->givenname[0];
        $user->surname = $ldap_user->sn[0];
        $user->authType()->associate(AuthType::find(AuthType::LDAP));
        $user->email = $ldap_user->mail[0];
        $user->enabled = 1;
        $user->pwd_change_required = 0;
        $user->password = $data['password'];
        $user->save();
        Log::channel('auth')->info('Account automatically registered',['user' => $user->user,'driver' => class_basename($this)]);
        return $user;
    }

    /**
     * @param User $user
     *
     * Method that will execute after login checks on specific User account
     *
     * @return bool
     * @throws AuthTypeException
     * @throws MustChangePwdException
     */
    public function postAuthChecks(User $user): bool
    {
        $this->checkAppUserEnabled($user);
        $this->checkAppUserExpired($user);
        $this->checkAppUserLocked($user);
        $this->checkAppUserMustChangePwd($user);
        $this->checkAppUserPwdRules($user);
        return true;
    }

    /**
     * Return AuthType related to LDAP Auth Driver
     *
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return AuthType::find(AuthType::LDAP);
    }

    /**
     * @param $user
     * @param $data
     *
     * Execute logic to change user password
     *
     * @return bool
     * @throws Exception
     */

    public function changePassword($user, $data): bool
    {
        Log::channel('auth')->info('Changing account password', ['user' => $user->user, 'driver' => class_basename($this)]);
        try {
            $ldap_user = Adldap::search()->users()->findOrFail($user->user);
        } catch (Exception) {
            Log::channel('auth')->info('Account not found on LDAP server. Unable to change password', ['user' => $user->user, 'driver' => class_basename($this)]);
            throw new Exception(trans('passwords.change.generic_error'));
        }

        try {
            DB::beginTransaction();
            Log::channel('auth')->info('Changing account password', ['user' => $user->user, 'driver' => class_basename($this)]);
            if(!Adldap::auth()->attempt($user->user,$data['current_password'])){
                DB::rollBack();
                Log::channel('auth')->info('Failed changing account password: wrong password', ['user' => $user->user, 'driver' => class_basename($this)]);
                throw new PasswordChangeException(trans('passwords.change.wrong_password'));

            }


            if(Adldap::auth()->attempt($user->user, $data['password'])){
                DB::rollBack();
                Log::channel('auth')->info('Failed changing account password: password already used', ['user' => $user->user, 'driver' => class_basename($this)]);
                throw new PasswordChangeException(trans('passwords.change.same'));

            }
            Adldap::connect(); // Rebinding to LDAP
            $user->password = bcrypt($data['password']);
            $user->pwd_change_required = 0;
            $user->pwd_changed_at = Carbon::now();
            $user->save();
            $ldap_user->changePassword($data['current_password'], $data['password'], true);
            $user->checkPasswordHistories($user);
            DB::commit();
            Log::channel('auth')->info('Account password successfully changed', ['user' => $user->user, 'driver' => class_basename($this)]);
            event(new PasswordReset($user));
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('auth')->error('Failed changing account password', ['user' => $user->user, 'driver' => class_basename($this)]);
            Log::channel('auth')->error($e->getMessage());
            Log::channel('ldap')->error($e->getMessage());
            throw new Exception(trans('passwords.change.generic_error'));
        }

        return true;
    }

    /**
     * @param User $user
     * @param $data
     *
     * Execute logic to reset User password
     *
     * @return bool
     * @throws Exception
     */
    public function resetPassword(User $user, $data): bool
    {
        Log::channel('auth')->info('Resetting account password', ['user' => $user->user, 'driver' => class_basename($this)]);
        try {
            $ldap_user = Adldap::search()->users()->findOrFail($user->user);
        } catch (Exception) {
            Log::channel('auth')->info('Account not found on LDAP server. Unable to reset password', ['user' => $user->user, 'driver' => class_basename($this)]);
            throw new Exception(trans('auth.ldap_account_not_found'));
        }

        try {
            DB::beginTransaction();
            if(Adldap::auth()->attempt($user->user, $data['password'])){
                DB::rollBack();
                Log::channel('auth')->info('Failed resetting account password: password already used', ['user' => $user->user, 'driver' => class_basename($this)]);
                throw new PasswordChangeException(trans('passwords.change.same'));
            }
            Adldap::connect(); // Rebinding to LDAP
            Log::channel('auth')->info('Updating account password', ['user' => $user->user, 'driver' => class_basename($this)]);
            $user->password = bcrypt($data['password']);
            $user->pwd_change_required = 0;
            $user->pwd_changed_at = Carbon::now();
            $user->save();
            $ldap_user->setPassword($data['password']);
            $ldap_user->save();

            $user->checkPasswordHistories($user);

            DB::commit();
            event(new PasswordReset($user));
            Log::channel('auth')->info('Account password successfully reset', ['user' => $user->user, 'driver' => class_basename($this)]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('auth')->error($e->getMessage());
            throw new Exception(trans('passwords.change.generic_error'));
        }
        return true;
    }
}