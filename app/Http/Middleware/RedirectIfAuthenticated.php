<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Middleware;

use App\LftRouting\RoutingManager;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                switch ($guard) {
                    case 'admins':
                        return redirect()->route(RoutingManager::adminHome());
                    default:
                        if (!isActiveRoute(RoutingManager::adminAlias() . '*')) {
                            return redirect()->route(RoutingManager::home());
                        }
                }
            }
        }


        return $next($request);
    }
}
