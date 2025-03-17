<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * Author: Francesco Tesone
 * Email: tesone@medialogic.it
 * Date: 12/11/2018
 * Time: 13:13
 */

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Foundation\Application;

trait Activable
{
    /**
     * Any model with an enabled field set to true
     * @param $query
     * @return mixed
     */
    public function scopeEnabled($query): mixed
    {
        $field = (!empty($this->activable)) ? $this->activable : 'enabled';
        return $query->where($field, 1);
    }

    /**
     * Any model with an enabled field set to false
     * @param $query
     * @return mixed
     */
    public function scopeDisabled($query): mixed
    {
        $field = (!empty($this->activable)) ? $this->activable : 'enabled';
        return $query->where($field, 0);
    }

    /**
     * Any model that has expired based on the enabled_from and enabled_to interval
     * @param $query
     * @return mixed
     */
    public function scopeExpired($query): mixed
    {
        $validFrom = (!empty($this->activable_from)) ? $this->activable_from : 'enabled_from';
        $validTo = (!empty($this->activable_to)) ? $this->activable_to : 'enabled_to';
        return $query->where(function ($query) use ($validFrom, $validTo) {
            $query->where($validFrom, '>', Carbon::now())
                ->orWhere($validTo, '<', Carbon::now());
        });
    }

    /**
     * Any model that has not expired based on the enabled_from and enabled_to interval
     * @param $query
     * @return mixed
     */
    public function scopeNotExpired($query): mixed
    {
        $validFrom = (!empty($this->activable_from)) ? $this->activable_from : 'enabled_from';
        $validTo = (!empty($this->activable_to)) ? $this->activable_to : 'enabled_to';
        return $query->where(function ($query) use ($validFrom, $validTo) {
            $query->where(function ($query) use ($validFrom, $validTo) {
                $query->where($this->getTable().'.'.$validFrom, '<=', Carbon::now())->where($this->getTable().'.'.$validTo, '>', Carbon::now());
            })->orWhere(function ($query) use ($validFrom, $validTo) {
                $query->whereNull($this->getTable().'.'.$validFrom)->where($this->getTable().'.'.$validTo, '>', Carbon::now());
            })->orWhere(function ($query) use ($validFrom, $validTo) {
                $query->where($this->getTable().'.'.$validFrom, '<=', Carbon::now())->whereNull($this->getTable().'.'.$validTo);
            })->orWhere(function ($query) use ($validFrom, $validTo) {
                $query->whereNull($this->getTable().'.'.$validFrom)->whereNull($this->getTable().'.'.$validTo);
            });
        });
    }

    /**
     * Any model that has not expired
     * @return bool
     */
    public function getAvailableAttribute(): bool
    {
        return $this->isNotExpired();
    }

    /**
     * Return true if model is not expired or false if not
     * @return bool
     */
    public function isNotExpired(): bool
    {
        $validFrom = (!empty($this->activable_from)) ? $this->activable_from : 'enabled_from';
        $validTo = (!empty($this->activable_to)) ? $this->activable_to : 'enabled_to';
        return ((($this->{$validFrom} <= Carbon::now() && $this->{$validTo} > Carbon::now()) ||
                (empty($this->{$validFrom}) && $this->{$validTo} > Carbon::now()) ||
                ($this->{$validFrom} <= Carbon::now() && empty($this->{$validTo})) ||
                (empty($this->{$validFrom}) && empty($this->{$validTo}))) && $this->isEnabled());
    }

    /**
     * Any model that is enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        $field = (!empty($this->activable)) ? $this->activable : 'enabled';
        return $this->{$field} == 1;
    }

    /**
     * Formats the available label
     * @return Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
     */
    public function getAvailableLabelAttribute(): Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        return ($this->isNotExpired() ? ($this->isEnabled() ? trans('common.active') : trans('common.disabled')) : (!$this->isEnabled() ? ($this->enabled_from >= Carbon::now() ? trans('common.enabled_from') . ' '.$this->getItEnabledFromAttribute() : trans('common.disabled')) : ($this->enabled_from >= Carbon::now() ? trans('common.enabled_from') . ' '.$this->getItEnabledFromAttribute() : trans('common.expired'))));
    }

    /**
     * Formats the enabled_from date
     * @return mixed|string
     */
    public function getItEnabledFromAttribute(): mixed
    {
        $validFrom = (!empty($this->activable_from)) ? $this->activable_from : 'enabled_from';
        return empty($this->{$validFrom}) ? $this->{$validFrom} : Carbon::createFromFormat('Y-m-d H:i:s', $this->{$validFrom})->format('d/m/Y H:i');
    }

    /**
     * Formats the enabled_to date
     * @return mixed|string
     */
    public function getItEnabledToAttribute(): mixed
    {
        $validTo = (!empty($this->activable_to)) ? $this->activable_to : 'enabled_to';
        return empty($this->{$validTo}) ? $this->{$validTo} : Carbon::createFromFormat('Y-m-d H:i:s', $this->{$validTo})->format('d/m/Y H:i');
    }
}