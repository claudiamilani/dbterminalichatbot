<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\Drivers;


use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\NeedExternalAuthException;
use App\Auth\ExternalRole;
use App\Auth\Role;
use App\Auth\User;
use App\Events\LoginSuccess;
use App\Jobs\UpdateAzureUserProfileImage;
use Illuminate\Support\Facades\Log;

class AzureAuthDriver extends AuthDriver
{
    const PWD_COMPLEXITY = 0;
    const PWD_EXPIRES = 0;
    const RESET_PASSWORD = 0;

    /**
     *
     * Execute logic to authenticate via Azure provider
     *
     * @param $data
     * @param $guard
     * @return User|false
     * @throws NeedExternalAuthException
     */
    public function authenticate($data, $guard = null): User|false
    {
        throw new NeedExternalAuthException(trans('Azure Auth need external provider authentication'));
    }

    /**
     * @param $data
     *
     * Method to automatically create a local User starting from an LDAP user.
     *
     * @return User
     * @throws AuthTypeException
     */
    public function autoRegister($data): User
    {

        if (empty($data->user['givenName']) || empty($data->user['surname'])) {
            Log::channel('auth')->info('Missing required informations to autoregister user account', ['user' => $data->user['mail'], 'driver' => class_basename($this)]);
            throw new AuthTypeException(trans('Azure local user not created - missing information'));
        }
        $local_user = User::create([
            'user' => $data->user['mail'],
            'email' => $data->user['mail'],
            'name' => strtoupper($data->user['givenName']),
            'surname' => strtoupper($data->user['surname']),
            'password' => bcrypt('password'),
            'auth_type_id' => AuthType::AZURE,
            'enabled' => 1
        ]);
        $local_user->roles()->save(Role::findOrFail(3));
        $local_user->save();
        Log::channel('auth')->info('Account automatically registered',['user' => $local_user->user,'driver' => class_basename($this)]);
        return $local_user;
    }

    /**
     * @param $data
     *
     * Return the local User model linked to the incoming azure user
     *
     * @return User
     * @throws AuthTypeException
     */
    public function getAppUserInstance($data): User
    {
        $auth_type = $this->getAuthType();

        $local_user = User::where('auth_type_id',$auth_type->id)->where('user', $data->user['mail'])->first();

        if(!$local_user){
            if($auth_type->isEnabled() && $auth_type->isAutoRegEnabled()){
                $local_user = $this->autoRegister($data);
            }else{
                Log::channel('auth')->info('AuthType disabled or AutoRegister not enabled',['user' => $data->user['mail'] ?? 'N/A','driver' => class_basename($this)]);
                throw new AuthTypeException(trans('auth.account_not_configured'));
            }
        }

        $this->mapExternalRoles($local_user,$data->user['groups']);
        UpdateAzureUserProfileImage::dispatch($local_user,encrypt($data->token));

        return $local_user;
    }

    /**
     * Return AuthType related to LDAP Auth Driver
     *
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return AuthType::find(AuthType::AZURE);
    }

    public function onSuccessLogin($user): void
    {
        event(new LoginSuccess($user));
    }

    public function mapExternalRoles($user,$data)
    {
        //$synced_roles = $user->roles()->pluck('id');
        $unmapped_local_roles = $user->roles()->doesntHave('externalRole')->pluck('id')->toArray();
        $mapped_local_roles = [];
        $external_roles = ExternalRole::where('auto_register_users',1)->where('auth_type_id', AuthType::AZURE)->whereIn('external_role_id', $data)->get();

        foreach($external_roles as $external_role) {
            foreach($external_role->roles as $role){
                $mapped_local_roles[] = $role->id;
            }
        }

        if (count($mapped_local_roles) < 1){
            Log::channel('auth')->info('User external roles do not have an existing mapping',['user' => $user->user,'driver' => class_basename($this),'ad_groups' => $data]);
            throw new AuthTypeException(trans('auth.account_not_configured'));
        }

        $user->roles()->sync(array_merge($unmapped_local_roles,$mapped_local_roles));
    }

    public function saveProfileImage(User $user, array $profileImage):void{
        if(isset($profileImage['type']) && isset($profileImage['content'])){
            $user->profile_image = 'data:'.$profileImage['type'].';base64,'.$profileImage['content'];
            $user->save();
        }
    }
}