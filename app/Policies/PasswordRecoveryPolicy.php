<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\User;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class PasswordRecoveryPolicy
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
     * Checks if user can list password recoveries
     * @param User $user
     * @return bool
     */
    public function list(User $user): bool
    {
        return $user->can('list_password_recovery_requests');
    }

    /**
     * Checks if user can view a password recovery
     * @param User $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can('view_password_recovery_requests');
    }

    /**
     * Checks if user can delete a password recovery
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete_password_recovery_requests');
    }

    /**
     * Users with admin role will still be checked for the following user permissions
     * @return array
     */
    public function alwaysCheck(): array
    {
        return [];
    }
}
