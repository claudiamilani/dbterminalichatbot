<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace App\Policies;

use App\Auth\ExternalRole;
use App\Auth\User;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExternalRolePolicy
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

    public function list(User $user)
    {
        return $user->can('list_external_roles');
    }

    public function view(User $currentUser, ExternalRole $external_role)
    {
        return $currentUser->can('view_external_roles');
    }

    public function create(User $currentUser)
    {
        return $currentUser->can('create_external_roles');
    }

    public function update(User $currentUser, ExternalRole $external_role)
    {
        return $currentUser->can('update_external_roles');
    }

    public function delete(User $currentUser, ExternalRole $external_role)
    {
        return $currentUser->can('delete_external_roles');
    }

    public function alwaysCheck(): array
    {
        return [''];
    }
}
