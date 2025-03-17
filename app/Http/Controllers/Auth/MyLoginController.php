<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\InvalidAppPwdException;
use App\Auth\Exceptions\MustChangePwdException;
use App\Auth\User;
use App\LftRouting\RoutingManager;
use Exception;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class MyLoginController extends LoginController
{

    /**
     * Redirect user admin home
     *
     * @return string
     */
    public function redirectTo()
    {
        return route(RoutingManager::adminHome());
    }

    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest:admins')->except('logout');
    }

    /**
     * Guard admins
     *
     * @return SessionGuard|Guard|StatefulGuard
     */
    public function guard()
    {
        return Auth::guard('admins');
    }

    /**
     * Return the field name wheere is stored the actual username of a local account
     *
     * @return string
     */
    public function username()
    {
        return 'user';
    }

    /**
     * @param Request $request
     *
     * Provide logic to logout from the app
     *
     * @return Application|\Illuminate\Foundation\Application|JsonResponse|RedirectResponse|Redirector|mixed
     */
    public function logout(Request $request)
    {
        // We are just logging out user from our app
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->loggedOut($request) ?: redirect()->route(RoutingManager::adminLoginRoute());
    }

    /**
     * @param Request $request
     *
     * Show the login page
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showLogin(Request $request)
    {
        $requested_auth_type = (is_numeric($request->auth_type) && !empty($found = AuthType::enabled()->find($request->auth_type))) ? $found : AuthType::default();
        $enabled_auth_types = AuthType::enabled()->get();
        $driver = $requested_auth_type->driverInstance;
        return view('layouts.adminlte.login', compact('driver', 'requested_auth_type', 'enabled_auth_types'));
    }

    /**
     * Return the view to ask a password recovery
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showPasswordRecovery()
    {
        return view('layouts.adminlte.password-recovery');
    }

    /**
     * @param Request $request
     *
     * Provide flow to login with every incoming AuthType
     *
     * @return JsonResponse|RedirectResponse|Response|mixed
     */
    public function login(Request $request)
    {
        try {
            $auth_type = AuthType::enabled()->where('id', $request->auth_type)->first() ?: AuthType::default();
            $driver = $auth_type->driverInstance;
            $driver->authenticate($request->only(['user', 'password']), 'admins', $request->remember);
            return redirect()->intended(route(RoutingManager::adminHome()));
        } catch (InvalidAppPwdException $e) {
            return $this->sendFailedLoginResponseWithAttempts($e->user);
        } catch (MustChangePwdException) {
            return redirect()->route(RoutingManager::adminAlias() . 'password.request')->with(['alerts' => [['message' => trans('passwords.must_reset_password'), 'type' => 'danger']]]);
        } catch (AuthTypeException $e) {
            return redirect()->route(RoutingManager::adminLoginRoute(), ['auth_type' => $auth_type->id])->with(['alerts' => [['message' => $e->getMessage(), 'type' => 'danger']]]);
        } catch (ValidationException $e) {
            return redirect()->route(RoutingManager::adminLoginRoute(), ['auth_type' => request('auth_type') ?? AuthType::default()->id])->with(['alerts' => [
                ['message' => 'Errore temporaneo', 'type' => 'danger']]
            ]);
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect()->route(RoutingManager::adminLoginRoute(), ['auth_type' => request('auth_type') ?? AuthType::default()->id])->with(['alerts' => [
                ['message' => 'Errore temporaneo', 'type' => 'danger']]
            ]);
        }
    }

    /**
     * @param User $user
     *
     * Return the failed login error messages with remaining attempts, if configured
     *
     * @param $mainMessage
     * @return mixed
     */
    private function sendFailedLoginResponseWithAttempts(User $user, $mainMessage = null)
    {
        $user->refresh();
        $config = AppConfiguration::current();
        if ($user->locked) {
            throw ValidationException::withMessages([
                $this->username() => [$mainMessage ?: trans('auth.failed'), trans('auth.locked')]
            ]);
        }
        throw ValidationException::withMessages([
            $this->username() => [$mainMessage ?: trans('auth.failed'), trans('auth.failed_with_attempts', ['attempts_left' => ($config->max_failed_login_attempts - $user->failed_login_count)])]
        ]);

    }

    /**
     * Provide logic to login via Azure provider
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginAzure()
    {
        try {
            if (AuthType::isAzureEnabled()) {
                return Socialite::driver('azure')->redirect();
            } else {
                return redirect()->route(RoutingManager::adminLoginRoute());
            }
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect(route(RoutingManager::adminLoginRoute()))->withAlerts([
                ['message' => trans('common.http_err.503'), 'type' => 'error']]);
        }
    }

    /**
     * Redirect user to configured url and execute after login local user checks for AzureProvider
     *
     * @return RedirectResponse
     */
    public function azureRedirect()
    {
        try {
            $oauth_user = Socialite::driver('mdl-azure')->stateless();
            $auth_type = AuthType::find(AuthType::AZURE);
            $driver = $auth_type->driverInstance;
            $local_user = $driver->getAppUserInstance($oauth_user->userWithGroups());

            if ($local_user !== false) {
                $driver->postAuthChecks($local_user);
                $driver->appLogin($local_user, 'admins');
                $driver->onSuccessLogin($local_user);
                return redirect()->intended(route(RoutingManager::adminHome()));
            } else {
                throw new AuthTypeException(trans('auth.account_not_configured'));
            }

        } catch (AuthTypeException $e) {
            return redirect()->route(RoutingManager::adminLoginRoute())->with(['alerts' => [['message' => $e->getMessage(), 'type' => 'danger']]]);
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect()->route(RoutingManager::adminLoginRoute())->with(['alerts' => [
                ['message' => trans('common.http_err.503'), 'type' => 'error']]
            ]);
        }
    }

    /**
     * Provide logic to login via SamlVas provider
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginSamlVas()
    {
        try {
            if (AuthType::isSamlVasEnabled()) {
                $redirect = config('dbt.samlvas.url') . '/auth?returnTo=' . config('dbt.samlvas.return_back');
                return redirect($redirect);
            } else {
                return redirect()->route(RoutingManager::adminLoginRoute());
            }
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect(route(RoutingManager::adminLoginRoute()))->withAlerts([
                ['message' => trans('common.http_err.503'), 'type' => 'error']]);
        }
    }

    public function samlVasRedirect()
    {
        try {
            //If a session_id is coming we call Samlvas to verify that the session_id is authenticated on Samlvas system
            if ($sessionId = request('session_id')) {
                $driver = AuthType::find(AuthType::SAMLVAS)->driverInstance;
                $response = $driver->apiCall($sessionId);
                $body = $response->getBody();
                $data = json_decode($body, true);
                if ($response->getStatusCode() === 200 && !isset($data['error_code'])) {
                    $local_user = $driver->getAppUserInstance($data['data']);
                    if(Auth::check() && Auth::user()->user != $local_user->user) {
                        Auth::logout();
                    }
                    if ($local_user !== false) {
                        $driver->postAuthChecks($local_user);
                        $driver->appLogin($local_user, 'admins');
                        $driver->onSuccessLogin($local_user);
                        return redirect()->intended(route(RoutingManager::adminHome()));
                    } else {
                        throw new AuthTypeException(trans('auth.account_not_configured'));
                    }
                } else {
                    //Api call in error
                    return redirect(route(RoutingManager::adminLoginRoute()))->withAlerts([
                        ['message' => trans('common.service_unavailable'), 'type' => 'error']]);
                }
            } else {
                return redirect(route(RoutingManager::adminLoginRoute()));
            }
        } catch (AuthTypeException $e) {
            return redirect()->route(RoutingManager::adminLoginRoute())->with(['alerts' => [['message' => $e->getMessage(), 'type' => 'danger']]]);
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect(route(RoutingManager::adminLoginRoute()))->withAlerts([
                ['message' => trans('common.http_err.503'), 'type' => 'error']]);
        }
    }

}
