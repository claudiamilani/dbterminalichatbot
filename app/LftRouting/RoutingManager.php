<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\LftRouting;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RoutingManager
{
    public static function adminAlias()
    {
        return Config::get('lft.admin_routes.as');
    }

    public static function publicAlias()
    {
        return Config::get('lft.public_routes.as');
    }

    public static function adminLoginRoute(): string
    {
        return Config::get('lft.admin_routes.as').Config::get('lft.admin_routes.login_route');
    }

    public static function adminHome(): string
    {
        return Config::get('lft.admin_routes.as').Config::get('lft.admin_routes.home');
    }

    public static function adminPwdResetRoute(): string
    {
        return Config::get('lft.admin_routes.as').Config::get('lft.admin_routes.password_reset_route');
    }

    public static function pwdResetRoute(): string
    {
        return Config::get('lft.public_routes.enabled', true) ? Config::get('lft.public_routes.as').Config::get('lft.public_routes.password_reset_route') : self::adminPwdResetRoute();
    }

    public static function home(): string
    {
        return Config::get('lft.public_routes.enabled', true) ? Config::get('lft.public_routes.as').Config::get('lft.public_routes.home') : self::adminHome();
    }

    public static function loginRoute(): string
    {
        return Config::get('lft.public_routes.enabled', true) ? Config::get('lft.public_routes.as').Config::get('lft.public_routes.login_route') : self::adminLoginRoute();
    }

    public static function isActiveRoute(): bool
    {
        $args = func_get_args();
        foreach($args as $arg){
            if(Str::is($arg,Route::currentRouteName())){
                return true;
            }
        }
        return false;
    }
}