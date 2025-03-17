<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\DocumentType;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DocumentTypePolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_document_types');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentType $documentType): bool
    {
        return $user->can('view_document_types');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_document_types');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentType $documentType): bool
    {
        return $user->can('update_document_types');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentType $documentType): bool
    {
        return $user->can('delete_document_types');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
