<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * User: gold
 * Date: 20/06/2019
 * Time: 12:19
 */

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Sortable
{
    /**
     * This scope sorts the query by using the provided array. Sorting by related models is also supported.
     * Example 1 - Simple: $sortBy = ['name' => 'desc']
     * Example 2 - Advanced - sorting by a related model field using dash symbol to join relation and field name: $sortBy = ['customer-name' => 'desc']
     * Example 3 - Advanced - sorting by a related but nested model field using laravel dot notation for relations and dash
     * symbol to join relations and field name: $sortBy = ['customerLocation.customer-name' => 'desc']
     * @param Builder $query
     * @param array $sortBy
     * @return Builder
     */
    public function scopeSortedBy(Builder $query, array $sortBy = []): Builder
    {
        $columns = Schema::getColumnListing($this->getTable());
        $join_list = [];
        foreach ($sortBy as $sortByRule => $sortByDirection) {
            // if the sort request is about a local
            if (in_array($sortByRule, $columns)) {
                $query = $query->orderBy($sortByRule, $sortByDirection);
            } else {
                if (Str::contains($sortByRule, '-')) { // it's a foreign key, find the relation used.
                    if (!empty($sortByRule)) {
                        list($relationsFragment, $column) = explode('-', $sortByRule);
                        $lastLoadedRelation = null;

                        foreach ($relations = explode('.', $relationsFragment) as $k => $relationName) {
                            try {
                                Log::debug('Loading relation ' . $relationName);

                                if ($k < 1) {
                                    $relation = $query->getRelation($relationName);
                                } else {
                                    $relation = $lastLoadedRelation->getModel()->with($relationName)->getRelation($relationName);
                                }
                                Log::debug('Relation type ' . class_basename($relation));
                                Log::debug('Before updating $join_list: ' . json_encode($join_list));
                                if (array_key_exists($related_table = $relation->getRelated()->getTable(), $join_list)) {
                                    Log::debug('Already joined ' . $related_table);
                                    $join_list[$related_table]++;
                                } else {
                                    Log::debug('First time join ' . $related_table);
                                    $join_list[$related_table] = 0;
                                }
                                Log::debug('After updating $join_list: ' . json_encode($join_list));
                                $related_table_alias = $related_table . ' as ' . $related_table . $join_list[$related_table];

                                Log::debug('$related_table: ' . $related_table);

                                if ($relation instanceof HasOne) {
                                    $qualifiedOwnerKeyName = explode('.', $relation->getQualifiedParentKeyName());
                                } elseif($relation instanceof HasMany) {
                                    $qualifiedOwnerKeyName = explode('.', $relation->getQualifiedParentKeyName());
                                } elseif($relation instanceof BelongsTo) {
                                    $qualifiedOwnerKeyName = explode('.', $relation->getQualifiedOwnerKeyName());
                                } elseif ($relation instanceof HasOneThrough) {
                                    $qualifiedOwnerKeyName = explode('.', $relation->getQualifiedParentKeyName());
                                }else{
                                    throw new Exception('Relation type ' . class_basename($relation).' is not supported yet');
                                }
                                Log::debug('before suffix $qualifiedOwnerKeyName: ' . json_encode($qualifiedOwnerKeyName));

                                $qualifiedOwnerKeyName[0] = $qualifiedOwnerKeyName[0] . ($join_list[$qualifiedOwnerKeyName[0]] ?? '');
                                $qualifiedOwnerKeyName = implode('.', $qualifiedOwnerKeyName);
                                Log::debug('$qualifiedOwnerKeyName: ' . $qualifiedOwnerKeyName);

                                $qualifiedForeignKeyName = explode('.', $relation->getQualifiedForeignKeyName());
                                $qualifiedForeignKeyName[0] = $qualifiedForeignKeyName[0] . ($join_list[$qualifiedForeignKeyName[0]] ?? '');
                                $qualifiedForeignKeyName = implode('.', $qualifiedForeignKeyName);
                                Log::debug('$qualifiedForeignKeyName: ' . $qualifiedForeignKeyName);

                                Log::debug('LeftJoin ' . join(',', [$related_table_alias, $qualifiedForeignKeyName, $qualifiedOwnerKeyName]));
                                $query = $query->leftJoin($related_table_alias, $qualifiedForeignKeyName, $qualifiedOwnerKeyName);
                                $lastLoadedRelation = $relation;
                                if (count($relations) == $k + 1) {
                                    $model = $relation->getRelated();
                                    $col = $model->getTable() . $join_list[$model->getTable()] . '.' . $column;
                                    $query = $query->orderBy($col, $sortByDirection);
                                    if ($distinct = $query->getQuery()->distinct) {

                                        $query = $query->addSelect($col)->distinct(array_merge($distinct,[$col]));
                                    }
                                }

                            } catch (Exception $e) {
                                Log::debug($e->getMessage());
                                Log::debug($e->getTraceAsString());
                            }
                        }
                    }
                }
            }
        }
        return $query->addSelect($this->getTable() . '.*');
    }

    public function scopeSortable(Builder $query, $key = 'sort', $defaultOrder = null)
    {
        $sortBy = [];
        $request_sort = request(is_array($key) ? 'sort' : $key);
        $input = empty($request_sort) ? [] : explode(',', request(is_array($key) ? 'sort' : $key));
        $defaultOrder = is_array($key) ? $key : $defaultOrder;

        foreach ($input as $sort_parameter) {
            $order = str_starts_with($sort_parameter, 'a') ? 'asc' : 'desc';
            $sortBy[substr($sort_parameter, 2)] = $order;
        }

        return $query->sortedBy($sortBy ?: $defaultOrder ?? []);
    }
}