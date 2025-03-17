<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\User;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
     * Checks if user can list users
     * @param User $user
     * @return bool
     */
    public function list(User $user): bool
    {
        return $user->can('list_users') || $user->isAdmin();
    }

    /**
     * Checks if a user can view a user
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function view(User $currentUser, User $account): bool
    {
        return ($currentUser->can('view_users') && (!$account->isAdmin() || $currentUser->isAdmin())) || $currentUser->id == $account->id;
    }

    /**
     * Checks if a user can create a new user
     * @param User $currentUser
     * @return bool
     */
    public function create(User $currentUser): bool
    {
        return $currentUser->can('create_users');
    }

    /**
     * Checks if a user can update an existing user
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function update(User $currentUser, User $account): bool
    {
        return ($currentUser->can('update_users') && (!$account->isAdmin() || $currentUser->isAdmin())) || $currentUser->id == $account->id;
    }

    /**
     * Checks if a user can delete another user. Default Admin user cannot be deleted
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function delete(User $currentUser, User $account): bool
    {
        return ($currentUser->can('delete_users') && $account->id != 1 && (!$account->isAdmin() || $currentUser->isAdmin())) || $currentUser->isAdmin() && $account->id != 1;
    }

    /**
     * Checks if a user can manage roles
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function manageRoles(User $currentUser, User $account): bool
    {
        return ($currentUser->can('manage_users_roles') && (!$account->isAdmin() || $currentUser->isAdmin())) || $currentUser->isAdmin();
    }

    /**
     * Checks if a user can manage a users status
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function manageStatus(User $currentUser, User $account): bool
    {
        return ($currentUser->can('manage_users_status') && (!$account->isAdmin() || $currentUser->isAdmin())) || $currentUser->isAdmin();
    }

    /**
     * Checks if a user is allowed to change their password
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function changePassword(User $currentUser, User $account): bool
    {
        return $currentUser->id == $account->id && $account->authType->driverInstance::RESET_PASSWORD;
    }

    /**
     * Checks if a user can reset their password for their authentication type
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function resetPassword(User $currentUser, User $account): bool
    {
        return $currentUser->id == $account->id && $account->authType->driverInstance->canResetPwd() || $currentUser->isAdmin() && $account->authType->driverInstance->canResetPwd();
    }

    /**
     * Checks if a user who does not have admin role, but has manage users permission can manage a users password for their authentication type
     * @param User $currentUser
     * @param User $account
     * @return bool
     */
    public function managePassword(User $currentUser, User $account): bool
    {
        return $currentUser->can('manage_users_password') && !$account->isAdmin() && $account->authType->driverInstance->canResetPwd();
    }

    /**
     * Users with admin role will still be checked for the following user permissions
     * @return string[]
     */
    public function alwaysCheck(): array
    {
        return ['delete','manageRoles','manageStatus','changePassword','resetPassword','managePassword'];
    }
}
