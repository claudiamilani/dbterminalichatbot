<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

use App\LFT\Menu\SplitBtn;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;


if (!function_exists('sort_link')) {

    function sort_link($col, $title = null, $key = 'sort',$fragment = null): string
    {
        $sort = request()->filled($key) ? explode(',', request($key)) : [];
        $indicator = '';
        if (in_array('a_' . $col, $sort) || in_array('d_' . $col, $sort)) {
            foreach ($sort as $filter) {
                //CONTROLLO SE CI TROVIAMO SULLA COLONNA DELL'ATTRIBUTO PRESENTE IN REQUEST
                if (substr($filter, 2) == $col) {
                    // SE SI CONTROLLO IL TIPO DI ORDINAMENTO IN REQUEST PER QUELL ATTRIBUTO (a,d)
                    switch (substr($filter, 0, 1)) {
                        case 'a' :
                            $indicator = '&uarr;';
                            $order = 'd_' . $col;
                            $posizione = array_search('a_' . $col, $sort);
                            $sort[$posizione] = $order;
                            break;
                        case 'd' :
                            $indicator = '&darr;';
                            $order = 'a_' . $col;
                            $posizione = array_search('d_' . $col, $sort);
                            $sort[$posizione] = $order;
                            break;

                    }
                }
            }
        } else {
            $sort[] = 'a_' . $col;
        }

        // We have parent_route in query string. We should use that to build the sortable url
        if($parent_route = base64_decode(request('pr') ?? '')){
            $params = array_merge(json_decode(base64_decode(request('pr_params')),true),request()->all(),[$key => implode(',', $sort)]);
            return link_to_route($parent_route, "$title $indicator", array_merge($params, request()->route()->parameters,$fragment ? ['#'.$fragment] : []), ['data-sort_reset' => sort_reset($col, $key,$fragment), 'title' => trans('common.reset_button_legend')]) . sort_filter($col, $key);
        }

        $params = array_merge(request()->all(), [$key => implode(',', $sort)],['pr' => base64_encode(Route::currentRouteName()),'pr_params' => base64_encode(json_encode(request()->route()->parameters))]);
        return link_to_route(Route::currentRouteName(), "$title $indicator", array_merge($params, request()->route()->parameters,$fragment ? ['#'.$fragment] : []), ['data-sort_reset' => sort_reset($col, $key,$fragment), 'title' => trans('common.reset_button_legend')]) . sort_filter($col, $key);
    }
}


if (!function_exists('sort_filter')) {

    function sort_filter($col, $key = 'sort'): string
    {
        $sort = request()->filled($key) ? explode(',', request($key)) : [];
        $result = (($found = array_search('a_' . $col, $sort)) !== false || ($found = array_search('d_' . $col, $sort)) !== false) ? $found : '';
        return (is_numeric($result) && count($sort) > 1) ? (('<sup>') . ($result + 1) . ('</sup>')) : '';
    }

}


if (!function_exists('sort_reset')) {

    function sort_reset($col, $key = 'sort', $fragment = null): string
    {
        $sort = explode(',', request($key) ?? '');

        if (($found = array_search('a_' . $col, $sort)) !== false || ($found = array_search('d_' . $col, $sort)) !== false) {
            unset($sort[$found]);
        }
        return route(request()->route()->getName(), array_merge(request()->except($key), request()->route()->parameters, $sort ? [$key => implode(',', $sort)] : [],$fragment ? ['#'.$fragment] : [] ));
    }

}

if (!function_exists('sort_reset_button')) {

    function sort_reset_button($key = 'sort',$fragment = null): HtmlString
    {

        return link_to_route(Route::currentRouteName(), trans('common.reset_button'), array_merge(request()->except($key), request()->route()->parameters,$fragment ? ['#'.$fragment] : []));

    }


}

if (!function_exists('nav_fragment')) {

    function nav_fragment($f): string
    {
        return str_slug(trans($f));
    }
}

if (!function_exists('getTranslatedAttributes')) {

    function getTranslatedAttributes($file, array $attributes): array
    {
        foreach ($attributes as $attribute => $replacement) {
            $attributes[$attribute] = trans($file . ".attributes.$attribute");
        }
        return $attributes;

    }


}

if (!function_exists('activeGuard')) {

    function activeGuard(): string
    {
        $ret = [];
        foreach (array_keys(config('auth.guards')) as $guard) {

            if (auth()->guard($guard)->check()) {
                $ret[] = $guard;
            }

        }
        return implode($ret);
    }
}


if (!function_exists('isActiveRoute')) {

    function isActiveRoute(): bool
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (Str::is($arg, Route::currentRouteName())) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('isNotActiveRoute')) {

    function isNotActiveRoute(): bool
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (Str::is($arg, Route::currentRouteName())) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('getAppAvailableStorage')) {

    function getAppAvailableStorage($withMeasureUnit = true): string
    {
        try {
            return formatBytes(disk_free_space(app_path()), 2, $withMeasureUnit);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}

if (!function_exists('getAppTotalStorage')) {

    function getAppTotalStorage($withMeasureUnit): ?string
    {
        try {
            return formatBytes(disk_total_space(app_path()), 2, $withMeasureUnit);
        } catch (Exception) {
            return null;
        }
    }
}

if (!function_exists('getAppUsedStoragePercent')) {

    function getAppUsedStoragePercent(): int|string
    {
        try {
            return number_format((disk_total_space(app_path()) - disk_free_space(app_path())) / disk_total_space(app_path()) * 100, 0);
        } catch (Exception) {
            return 0;
        }
    }
}

if (!function_exists('getAppAvailableStoragePercent')) {

    function getAppAvailableStoragePercent(): int|string
    {
        try {
            return number_format(disk_free_space(app_path()) / disk_total_space(app_path()) * 100, 0);
        } catch (Exception) {
            return 0;
        }
    }
}

if (!function_exists('formatBytes')) {

    function formatBytes($bytes, $precision = 2, $append_measure_unit = true): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format(round($bytes, $precision), $precision, ',', '') . (($append_measure_unit) ? (' ' . $units[$pow]) : '');
    }
}

if (!function_exists('paramsWithBackTo')) {

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function paramsWithBackTo($params = [], $backToRoute = null, $backToRouteParams = []): array
    {
        $params = !is_array($params) ? (array)$params : $params;
        $backToRouteParams = !is_array($backToRouteParams) ? (array)$backToRouteParams : $backToRouteParams;
        if ($backToRoute) {
            return array_merge($params, ['backTo' => Route::has($backToRoute) ? route($backToRoute, $backToRouteParams) : $backToRoute]);
        }
        return array_merge($params, (request()->get('backTo') ? ['backTo' => request()->get('backTo')] : []));
    }
}

if (!function_exists('backToSource')) {

    function backToSource($route, $params = [])
    {
        if ($backTo = request('backTo')) {
            return Route::has($backTo) ? route($backTo) : $backTo;
        }
        return route($route, $params);
    }
}

if (!function_exists('pageSortSearchParams')) {

    function pageSortSearchParams()
    {
        return collect(request()->all())->filter(function ($value, $key) {
            return str_starts_with($key, 'sort') || str_ends_with($key,'page');
        });
    }
}

if (!function_exists('memoryUsage')) {

    function memoryUsage(): string
    {
        /* Currently used memory */
        $mem_usage = round(memory_get_usage()/1024);

        /* Peak memory usage */
        $mem_peak = round(memory_get_peak_usage()/1024);

        return "Using $mem_usage KB of $mem_peak KB allocated memory";
    }
}

if (!function_exists('splitBtnModel')) {

    function splitBtnModel($model)
    {
        $instance = new SplitBtn();
        $instance->setModel($model);
        $instance->setRoutesPrefix('admin::');
        return $instance;
    }
}
