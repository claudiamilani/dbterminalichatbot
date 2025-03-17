<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Traits;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Route;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait ControllerPathfinder
{

    /**
     * @param $default
     * @param array $params
     * @return Application|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function returnPath($default, array $params = []): Application|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        return redirect($this->returnPathUrl($default, $params));
    }

    /**
     * @param $default
     * @param array $params
     * @return Closure|mixed|object|string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function returnPathUrl($default, array $params = []): mixed
    {
        return request()->get('backTo') ?? (Route::has($default) ? route($default, $params) : $default);
    }
}