<?php

namespace App\Auth\Drivers;

use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\NeedExternalAuthException;
use App\Auth\ExternalRole;
use App\Auth\Role;
use App\Auth\User;
use App\Events\LoginSuccess;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SamlvasAuthDriver extends AuthDriver
{
    const PWD_COMPLEXITY = 0;
    const PWD_EXPIRES = 0;
    const RESET_PASSWORD = 0;

    private $raw_request;
    private $raw_response;

    /**
     *
     * Execute logic to authenticate via Samlvas provider
     *
     * @param $data
     * @param $guard
     * @return User|false
     * @throws NeedExternalAuthException
     */
    public function authenticate($data, $guard = null): User|false
    {
        throw new NeedExternalAuthException(trans('Samlvas Auth need external provider authentication'));
    }

    /**
     * @param $data
     *
     * Method to automatically create a local User starting from an SamlVas user.
     *
     * @return User
     * @throws AuthTypeException
     */
    public function autoRegister($data): User
    {

        if (empty($data['name']) || empty($data['surname'])) {
            Log::channel('auth')->info('Missing required informations to autoregister user account', ['user' => $data['email'], 'driver' => class_basename($this)]);
            throw new AuthTypeException(trans('Samlvas local user not created - missing information'));
        }
        $local_user = User::create([
            'user' => $data['email'],
            'email' => $data['email'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'password' => bcrypt('password'),
            'auth_type_id' => AuthType::SAMLVAS,
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
     * Return the local User model linked to the incoming Samlvas user
     *
     * @return User
     * @throws AuthTypeException
     */
    public function getAppUserInstance($data): User
    {
        $auth_type = $this->getAuthType();
        $local_user = User::where('auth_type_id',$auth_type->id)->where('user', $data['email'])->first();
        if(!$local_user){
            if($auth_type->isEnabled() && $auth_type->isAutoRegEnabled()){
                $local_user = $this->autoRegister($data);
            }else{
                Log::channel('auth')->info('AuthType disabled or AutoRegister not enabled',['user' => $data['email'] ?? 'N/A','driver' => class_basename($this)]);
                throw new AuthTypeException(trans('auth.account_not_configured'));
            }
        }
        $this->mapExternalRoles($local_user,$data);
        return $local_user;
    }

    /**
     * Return AuthType related to SamlVas Auth Driver
     *
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return AuthType::find(AuthType::SAMLVAS);
    }

    /**
     * @param $user
     * @return void
     */
    public function onSuccessLogin($user): void
    {
        event(new LoginSuccess($user));
    }

    /**
     * @param $user
     * @param $data
     * @return void
     * @throws AuthTypeException
     */
    public function mapExternalRoles($user, $data)
    {
        //$synced_roles = $user->roles()->pluck('id');
        $unmapped_local_roles = $user->roles()->doesntHave('externalRole')->pluck('id')->toArray();
        $mapped_local_roles = [];
        $external_roles = ExternalRole::where('auto_register_users',1)->where('auth_type_id', AuthType::SAMLVAS)->whereIn('external_role_id', $data['groups'])->get();

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

    /**
     * Format Api Call request and response for logging purpose.
     *
     * @param $var
     * @return \Closure
     */
    public function formattedLogs(&$var)
    {
        return function (callable $handler) use ($var) {
            return function (RequestInterface $request, array $options) use ($handler, $var) {
                $raw_request = (new MessageFormatter("{request}"))->format($request);
                $var->raw_request = $raw_request;
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($request, $var) {
                        $raw_response = (new MessageFormatter("{response}"))->format($request, $response);
                        $var->raw_response = $raw_response;
                        return $response;
                    }
                );
            };
        };
    }

    /**
     * @param $session_id
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function apiCall($session_id)
    {
        try {
            Log::channel('auth')->info('Executing session id check api_call to Samlvas');
            $stack = HandlerStack::create();
            $stack->push(
                $this->formattedLogs($this)
            );
            //we remove last "/" character if present in configured api url
            $api_url =  rtrim(config('dbt.samlvas.api_url'), '/');
            $client = new Client(config('dbt.samlvas.ssl')===true? ['handler' => $stack] : ['handler' => $stack, 'verify'=>false]);
                $clientCall = $client->request('GET',$api_url.'/user/'.$session_id, [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . config('dbt.samlvas.token'),
                    'Accept' => 'application/json',
                ],
            ]);
            Log::channel('auth')->info('Samlvas Request: ' . $this->raw_request);
            Log::channel('auth')->info('Samlvas Response: ' . $this->raw_response);

        } catch (ClientException $e) {
            Log::channel('auth')->error('Client Exception: ' . $e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            Log::channel('auth')->error('Samlvas Request: ' . $this->raw_request);
            Log::channel('auth')->error('Samlvas Request: ' . $this->raw_request);
            throw $e;
        } catch (\Exception $e) {
            Log::channel('auth')->error('Error executing api call: ' . $e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            throw $e;
        }
        return $clientCall;

    }
}