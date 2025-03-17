<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Application;

class PermissionType extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * The roles for a permission type
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * The permissions for a permission type
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class,'permission_type_id');
    }

    /**
     * Translates the permission type name
     * @return Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
     */
    public function getTranslatedNameAttribute(): Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        return trans('permission_types.'.$this->name);
    }

}
