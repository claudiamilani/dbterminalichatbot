<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Middleware;

use App\LftRouting\RoutingManager;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        if($request->ajax() && !$request->expectsJson()) {
            abort(401);
        }
        $login_route = in_array('admins', $guards) ? (RoutingManager::adminLoginRoute()) : (RoutingManager::loginRoute());
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $request->expectsJson()? null : route($login_route)
        );
    }
}
