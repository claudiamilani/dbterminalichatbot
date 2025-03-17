<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Traits;


use App\Auth\Permission;
use App\Auth\Role;
use Illuminate\Database\Eloquent\Model;

trait HasRoles
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole($role): Model
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return (bool)$role->intersect($this->roles)->count();
    }

    public function hasPermission(Permission $permission)
    {
        return $this->hasRole($permission->roles);
    }

}