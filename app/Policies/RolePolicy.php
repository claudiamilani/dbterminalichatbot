<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\Role;
use App\Auth\User;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization, HasAdminPolicy;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Checks if user can list roles
     * @param User $user
     * @return bool
     */
    public function list(User $user): bool
    {
        return $user->can('list_roles');
    }

    /**
     * Checks if a user can view a role
     * @param User $currentUser
     * @return bool
     */
    public function view(User $currentUser): bool
    {
        return $currentUser->can('view_roles');
    }

    /**
     * Checks if a user can create a role
     * @param User $currentUser
     * @return bool
     */
    public function create(User $currentUser): bool
    {
        return $currentUser->can('create_roles');
    }

    /**
     * Checks if a user can update an existing role
     * @param User $currentUser
     * @return bool
     */
    public function update(User $currentUser): bool
    {
        return $currentUser->can('update_roles');
    }

    /**
     * Checks if a user can delete an existing role
     * @param User $currentUser
     * @param Role $role
     * @return bool
     */
    public function delete(User $currentUser, Role $role): bool
    {
        return $currentUser->can('delete_roles') && $role->id > 3 || ($role->id > 3 && $currentUser->isAdmin());
    }

    /**
     * Checks if a user can manage permissions attached to a role
     * @param User $currentUser
     * @return bool
     */
    public function managePermissions(User $currentUser): bool
    {
        return $currentUser->can('manage_permissions');
    }

    /**
     * Users with admin role will still be checked for the following role permissions
     * @return string[]
     */
    public function alwaysCheck(): array
    {
        return ['delete'];
    }
}
