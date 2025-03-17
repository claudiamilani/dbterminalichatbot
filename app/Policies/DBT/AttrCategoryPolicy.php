<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\AttrCategory;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AttrCategoryPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_attr_categories');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttrCategory $attrCategory): bool
    {
        return $user->can('view_attr_categories');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attr_categories');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttrCategory $attrCategory): bool
    {
        return $user->can('update_attr_categories');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttrCategory $attrCategory): bool
    {
        return $user->can('delete_attr_categories');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AttrCategory $attrCategory): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AttrCategory $attrCategory): bool
    {
        return true;
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
