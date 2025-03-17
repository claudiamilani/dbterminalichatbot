<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\Ingestion;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class IngestionPolicy
{
    use HandlesAuthorization, HasAdminPolicy;

    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_ingestions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ingestion $ingestion): bool
    {
        return $user->can('view_ingestions');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ingestions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ingestion $ingestion): bool
    {
        return ($user->can('update_ingestions') || $user->isAdmin()) && in_array($ingestion->status, [Ingestion::STATUS_DRAFT,Ingestion::STATUS_REQUESTED]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ingestion $ingestion): bool
    {
        return ($user->can('delete_ingestions') || $user->isAdmin()) && in_array($ingestion->status, [Ingestion::STATUS_DRAFT,Ingestion::STATUS_REQUESTED]);
    }

    public function alwaysCheck(): array
    {
        return ['update', 'delete'];
    }
}
