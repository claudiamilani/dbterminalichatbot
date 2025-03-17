<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Traits\GlobalSearch;

trait GloballySearchable
{
    abstract public static function globalSearchName():string;

    abstract public static function globalSearchDisplayValue();

    public function globalSearchItemRouteParams()
    {
        return $this->id;
    }

    abstract public static function globalSearchItemRoute(): string;

    public function globalSearchItemUrl($backToSearchResults = true): string
    {
        return route(static::globalSearchItemRoute(),paramsWithBackTo($this->globalSearchItemRouteParams(),$backToSearchResults ? 'admin::global_search' : '',$backToSearchResults ? ['search' => request('search')] : []) );
    }

}