<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\User;
use App\Traits\HasAdminPolicy;

class AuthTypePolicy
{
    use HasAdminPolicy;
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }

    /**
     * Checks if user can list auth types
     * @param User $user
     * @return bool
     */
    public function list(User $user): bool
    {
        return $user->can('list_auth_types');
    }

    /**
     * Checks if user can view auth type
     * @param User $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can('view_auth_types');
    }

    /**
     * Checks if user can update auth type
     * @param User $currentUser
     * @return bool
     */
    public function update(User $currentUser): bool
    {
        return $currentUser->can('update_auth_types');
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
