<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth;

use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuthType extends Model
{
    use Searchable, Sortable;

    const LOCAL = 1;
    const LDAP = 2;
    const AZURE = 3;
    const SAMLVAS = 4;

    protected $fillable = ['id', 'name', 'default', 'enabled', 'auto_register', 'driver'];

    public function scopeDefault($query)
    {
        return $query->where('default', 1)->first();
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled == 1;
    }

    /**
     * Return if the AuthType is configured to automatically create local user on successful login
     *
     * @return bool
     */
    public function isAutoRegEnabled(): bool
    {
        return $this->auto_register == 1;
    }

    /**
     * @param $query
     * @param $search
     * @return Builder
     */
    public function searchFilter($query, $search): Builder
    {
        return $query->where(function($query)use($search){
          $query->where('name', 'ILIKE', "%$search%")->orWhere('id', (int)$search);
        });
    }

    /**
     * @return mixed
     */
    public function getDriverInstanceAttribute(): mixed
    {
        return new $this->driver;
    }

    /**
     * Return true or false if Azure auth driver is enabled
     *
     * @return bool|null
     */
    public static function IsAzureEnabled(): bool|null
    {
        return AuthType::where('id', self::AZURE)->first()->enabled;
    }

    /**
     * Return true or false if SAMLVAS auth driver is enabled
     *
     * @return bool|null
     */
    public static function IsSAMLVASEnabled(): bool|null
    {
        return AuthType::where('id', self::SAMLVAS)->first()->enabled;
    }

    /**
     * Save the AuthType as Default login mode
     *
     * @return void
     */
    public function saveAsDefault(): void
    {
        AuthType::where('id', '<>', $this->id)->update(['default' => 0]);
        $this->default = 1;
        $this->enabled = 1;
        $this->save();
    }

}
