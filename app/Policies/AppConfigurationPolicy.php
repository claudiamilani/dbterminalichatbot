<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\User;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppConfigurationPolicy
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
     * Checks if user can view app configuration
     * @param User $currentUser
     * @return bool
     */
    public function view(User $currentUser): bool
    {
        return $currentUser->can('view_app_configuration');
    }

    /**
     * Checks if user can update app configuration
     * @param User $currentUser
     * @return bool
     */
    public function update(User $currentUser): bool
    {
        return ($currentUser->can('update_app_configuration'));
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
