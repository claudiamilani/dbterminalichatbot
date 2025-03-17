<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\Tac;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TacPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_tacs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tac $tac): bool
    {
        return $user->can('view_tacs');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tacs');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tac $tac): bool
    {
        return $user->can('update_tacs');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tac $tac): bool
    {
        return $user->can('delete_tacs');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
