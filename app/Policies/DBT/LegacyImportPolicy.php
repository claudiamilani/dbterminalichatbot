<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\LegacyImport;
use App\DBT\Models\Terminal;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LegacyImportPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_legacy_imports');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegacyImport $import): bool
    {
        return $user->can('view_legacy_imports');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_legacy_imports');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LegacyImport $import): bool
    {
        return $user->can('update_legacy_imports');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegacyImport $import): bool
    {
        return $user->can('delete_legacy_imports');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
