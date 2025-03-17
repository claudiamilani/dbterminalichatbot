<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\Vendor;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class VendorPolicy
{
    use HandlesAuthorization, HasAdminPolicy
;    /**
     * Determine whether the user can view any models.
     */

    public function list(User $user): bool
    {
        return $user->can('list_vendors');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vendor $vendor): bool
    {
        return $user->can('view_vendors');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_vendors');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        return $user->can('update_vendors');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->can('delete_vendors');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
