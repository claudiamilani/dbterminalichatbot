<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\TerminalConfig;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TerminalConfigPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */

    public function list(User $user): bool
    {
        return $user->can('list_terminal_configs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TerminalConfig $config_id): bool
    {
        return $user->can('view_terminal_configs');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_terminal_configs');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TerminalConfig $config_id): bool
    {
        return $user->can('update_terminal_configs');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TerminalConfig $config_id): bool
    {
        return $user->can('delete_terminal_configs');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
