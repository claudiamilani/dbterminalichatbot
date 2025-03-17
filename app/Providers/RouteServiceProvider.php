<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapLftAppRoutes();
        $this->mapSiteRoutes();
    }



    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapLftAppRoutes()
    {
        $routing = Route::middleware(Config::get('lft.admin_routes.middleware'))
            ->as(Config::get('lft.admin_routes.as'))
            ->namespace($this->namespace);
        if (Config::get('lft.admin_routes.fixed_domain', false)) {
            $routing->domain(Config::get('lft.admin_routes.domain'));
        } else {
            if (Config::get('lft.public_routes.enabled', true)) {
                $routing->prefix(Config::get('lft.admin_routes.prefix'));
            }
        }

        $routing->group(base_path('routes/lft.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        if (Config::get('lft.api_routes.enabled', true)) {
            Route::prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        }
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapSiteRoutes()
    {
        if (Config::get('lft.public_routes.enabled', true)) {
            $routing = Route::middleware(Config::get('lft.public_routes.middleware'))
                ->prefix(Config::get('lft.public_routes.prefix'))
                ->as(Config::get('lft.public_routes.as'));
            // If is enabled admin domain and we have the public domain properly configured we config the specific domain
            if (Config::get('lft.admin_routes.fixed_domain', false) && Config::get('lft.public_routes.domain', false)) {
                $routing->domain(Config::get('lft.public_routes.domain'));
            }

            $routing->group(base_path('routes/site.php'));
        }
    }
}
