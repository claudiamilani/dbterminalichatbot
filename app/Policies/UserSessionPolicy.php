<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\User;
use App\Auth\UserSession;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Session;

class UserSessionPolicy
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
     * Checks if user can list user sessions
     * @param User $user
     * @return bool
     */
    public function list(User $user): bool
    {
        return $user->can('list_sessions');
    }

    /**
     * Checks if a user can delete another users session
     * @param User $currentUser
     * @param UserSession $userSession
     * @return bool
     */
    public function delete(User $currentUser, UserSession $userSession): bool
    {
        return ($currentUser->can('delete_sessions') && $userSession->id != Session::getId()) || ($currentUser->isAdmin() && $userSession->id != Session::getId());
    }

    /**
     * Checks if a user can purge all user sessions
     * @param User $currentUser
     * @return bool
     */
    public function purge(User $currentUser): bool
    {
        return $currentUser->can('delete_sessions');
    }

    /**
     * Users with admin role will still be checked for the following user session permissions
     * @return string[]
     */
    public function alwaysCheck(): array
    {
        return ['delete'];
    }
}
