<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Site;

use App\AppConfiguration;
use App\Auth\AuthType;
use App\Auth\Exceptions\AuthTypeException;
use App\Auth\Exceptions\InvalidAppPwdException;
use App\Auth\Exceptions\MustChangePwdException;
use App\Auth\User;
use App\Http\Controllers\Auth\LoginController;
use App\LftRouting\RoutingManager;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\Config;

class SiteLoginController extends LoginController
{
    protected string $redirectTo = '/';

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Return the field name where is stored the actual username for a local account
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
     * Show the frontend login page
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showLogin(Request $request)
    {
        $requested_auth_type = !empty($found = AuthType::enabled()->find($request->auth_type)) ? $found : AuthType::default();
        $enabled_auth_types = AuthType::enabled()->get();
        $driver = $requested_auth_type->driverInstance;

        return view('layouts.site.login', compact('requested_auth_type', 'enabled_auth_types', 'driver'));
    }

    /**
     * Return the view to ask a password recovery frontend side
     *
     * @return View
     */
    public function showPasswordRecovery()
    {
        return view('layouts.site.password-recovery');
    }

    /**
     * @param Request $request
     *
     * Provide flow to login frontend side with every incoming AuthType
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function login(Request $request)
    {
        try {
            $auth_type = AuthType::enabled()->where('id', $request->auth_type)->first() ? AuthType::where('id', $request->auth_type)->first() : AuthType::default()->first();
            $driver = $auth_type->driverInstance;
            $driver->authenticate(['user' => $request->user, 'password' => $request->password]);
            return redirect()->intended(route(RoutingManager::home()));
        } catch (InvalidAppPwdException $e) {
            return $this->sendFailedLoginResponseWithAttempts($e->user);
        } catch (MustChangePwdException) {
            return redirect()->route(RoutingManager::publicAlias() . 'password.request')->with(['alerts' => [['message' => trans('passwords.must_reset_password'), 'type' => 'danger']]]);
        } catch (AuthTypeException $e) {
            return redirect()->route(RoutingManager::loginRoute(), ['auth_type' => $auth_type->id])->with(['alerts' => [['message' => $e->getMessage(), 'type' => 'danger']]]);
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect()->route(RoutingManager::loginRoute(), ['auth_type' => request('auth_type') ?? AuthType::default()->id])->with(['alerts' => [
                ['message' => 'Errore temporaneo', 'type' => 'danger']]
            ]);
        }
    }


    /**
     * @param User $user
     * @param $mainMessage
     *
     * Return the failed login error messages with remaining attempts, if configured
     *
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
     * @param Request $request
     *
     * Provide logic to logout from the app frontend side
     *
     * @return Application|\Illuminate\Foundation\Application|JsonResponse|RedirectResponse|Redirector|mixed
     */
    public function logout(Request $request)
    {
        // We are just logging out user from our app
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->loggedOut($request) ?: redirect()->route(RoutingManager::loginRoute());

    }

    /**
     * Provide logic to login via Azure provider frontend side
     *
     * @return RedirectResponse
     */
    public function loginAzure()
    {
        try {
            if (AuthType::isAzureEnabled()) {
                return Socialite::driver('mdl-azure')->setConfig($this->getFrontendConfig())->redirect();
            } else {
                return redirect()->route(RoutingManager::loginRoute());
            }
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect(route(RoutingManager::adminLoginRoute()))->withAlerts([
                ['message' => trans('common.http_err.503'), 'type' => 'error']]);
        }
    }

    /**
     * Redirect user to configured url and execute after login local user checks for AzureProvider on frontend side
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
                $driver->appLogin($local_user);
                $driver->onSuccessLogin($local_user);
                return redirect()->intended(route(RoutingManager::home()));
            } else {
                throw new AuthTypeException(trans('auth.account_not_configured'));
            }

        } catch (AuthTypeException $e) {
            Log::channel('auth')->error($e->getMessage());
            return redirect()->route(RoutingManager::loginRoute())->with(['alerts' => [['message' => $e->getMessage(), 'type' => 'danger']]]);

        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect()->route(RoutingManager::loginRoute())->with(['alerts' => [
                ['message' => trans('common.http_err.503'), 'type' => 'error']]
            ]);
        }
    }

    /**
     * Return the Azure config
     *
     * @return Config
     */
    function getFrontendConfig(): Config
    {
        return new Config(
            config('lft.azure_login.client_id'),
            config('lft.azure_login.client_secret'),
            url(config('lft.azure_login.frontend_redirect')),
            ['tenant' => config('lft.azure_login.tenant_id')]
        );
    }
}
