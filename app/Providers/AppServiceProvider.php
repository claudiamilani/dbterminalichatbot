<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Providers;

use App\AppConfiguration;
use App\Auth\User;
use App\Extensions\CustomDatabaseSessionHandler;
use App\Observers\AppConfigurationObserver;
use App\Observers\Auth\UserObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
        User::observe(UserObserver::class);
        AppConfiguration::observe(AppConfigurationObserver::class);
        Paginator::useBootstrapThree();

        Session::extend('custom-database', function () {
            $lifetime = $this->app->config->get('session.lifetime');
            $table = $this->app->config->get('session.table');
            $connection = $this->app->db->connection($this->app->config->get('session.connection'));

            return new CustomDatabaseSessionHandler($connection, $table, $lifetime, $this->app);
        });

        Collection::macro('withFilterLabel', function ($name, $noFilterLabel = null,$noFilterKey = '-') {
            if (empty($noFilterLabel)) {
                $noFilterLabel = trans('common.all');
            }
            $this->prepend($noFilterLabel, $noFilterKey);
            if (($prefix = trans($name . '.title')) == $name . '.title') {
                $prefix = trans($name);
            }
            $this->transform(function ($item) use ($prefix) {
                return "$prefix: $item";
            });

            return $this;
        });
    }
}
