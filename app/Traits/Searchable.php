<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * User: francesco
 * Date: 16/08/18
 * Time: 01:05
 */

namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait Searchable
{
    public function scopeSearch($query, $search = null, $search_as_input = false)
    {
        if(is_array($search) && !empty($search) && method_exists($this,'advancedSearchFilter')){

            if(!$search_as_input){
                foreach($search as $k => $item){
                    if(strlen(trim($value = request($item) ?? '')) > 0){
                        $search[$item] = $value;
                    }
                    unset($search[$k]);
                }
            }

            return $this->advancedSearchFilter($query->SearchVisibilityFilter(),$search);
        }

        if (empty($search) || is_array($search)) {
            $search = $search_as_input ? Arr::first($search) : request('search');
        }
        return $search ? $this->searchFilter($query, $search)->searchVisibilityFilter() : $query;
    }

    /**
     * Override this to implement visibility filters to the search query
     * @param $query
     * @return mixed
     */
    public function scopeSearchVisibilityFilter($query): mixed
    {
        return $query;
    }


    /**
     * Search conditions for this model when utilizing search scope in queries.
     * @param $query
     * @param $search
     * @return Builder
     */
    abstract public function searchFilter($query, $search): Builder;
}