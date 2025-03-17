<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\TransposeRequest;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransposeRequestPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_transpose_requests');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransposeRequest $tr_request): bool
    {
        return $user->can('view_transpose_requests');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_transpose_requests');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransposeRequest $tr_request): bool
    {
        return $user->can('delete_transpose_requests');
    }

    /**
     * Determine whether the user can download the exported transpose.
     */
    public function download(User $user, TransposeRequest $tr_request): bool
    {
        return $user->can('create_transpose_requests') && $tr_request->status == TransposeRequest::STATUS_PROCESSED;
    }

    public function alwaysCheck(): array
    {
        return [''];
    }
}
