<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\Ota;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class OtaPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */

    public function list(User $user): bool
    {
        return $user->can('list_otas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ota $vendor): bool
    {
        return $user->can('view_otas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_otas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ota $vendor): bool
    {
        return $user->can('update_otas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ota $vendor): bool
    {
        return $user->can('delete_otas');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
