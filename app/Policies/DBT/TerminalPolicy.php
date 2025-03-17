<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\Terminal;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TerminalPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_terminals');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Terminal $terminal): bool
    {
        return $user->can('view_terminals');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_terminals');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Terminal $terminal): bool
    {
        return $user->can('update_terminals');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Terminal $terminal): bool
    {
        return $user->can('delete_terminals');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
