<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\DbtAttribute;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Testing\Fluent\Concerns\Has;

class AttributePolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_attributes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DbtAttribute $attribute): bool
    {
        return $user->can('view_attributes');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attributes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DbtAttribute $attribute): bool
    {
        return $user->can('update_attributes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DbtAttribute $attribute): bool
    {
        return $user->can('delete_attributes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DbtAttribute $attribute): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DbtAttribute $attribute): bool
    {
        return true;
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
