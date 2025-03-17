<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\Tac;
use App\DBT\Models\TransposeConfig;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TransposeConfigPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_transpose_configs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransposeConfig $transpose_config): bool
    {
        return $user->can('view_transpose_configs');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_transpose_configs');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TransposeConfig $transpose_config): bool
    {
        return $user->can('update_transpose_configs');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransposeConfig $transpose_config): bool
    {
        return $user->can('delete_transpose_configs');
    }

    public function list_dwh_operations(User $user)
    {
        return $user->can('list_transpose_configs');
    }

    public function create_views(User $user)
    {
        return $user->can('create_transpose_configs');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
