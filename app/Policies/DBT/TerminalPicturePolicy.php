<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\TerminalPicture;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class TerminalPicturePolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */

    public function list(User $user): bool
    {
        return $user->can('list_terminal_pictures');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TerminalPicture $picture): bool
    {
        return $user->can('view_terminal_pictures');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_terminal_pictures');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TerminalPicture $picture): bool
    {
        return $user->can('update_terminal_pictures');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TerminalPicture $picture): bool
    {
        return $user->can('delete_terminal_pictures');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
