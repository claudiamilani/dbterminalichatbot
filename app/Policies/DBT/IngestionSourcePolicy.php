<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\IngestionSource;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class IngestionSourcePolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_ingestion_sources');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IngestionSource $ingestionSource): bool
    {
        return $user->can('view_ingestion_sources');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ingestion_sources');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IngestionSource $ingestionSource): bool
    {
        return $user->can('update_ingestion_sources');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IngestionSource $ingestionSource): bool
    {
        return $user->can('delete_ingestion_sources');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
