<?php

namespace App\Policies\DBT;

use App\Auth\User;
use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\Channel;
use App\Traits\HasAdminPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AttributeHeaderMappingPolicy
{
    use HandlesAuthorization, HasAdminPolicy;
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return $user->can('list_attribute_header_mappings');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttributeHeaderMapping $mapping): bool
    {
        return $user->can('view_attribute_header_mappings');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attribute_header_mappings');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttributeHeaderMapping $mapping): bool
    {
        return $user->can('update_attribute_header_mappings');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttributeHeaderMapping $mapping): bool
    {
        return $user->can('delete_attribute_header_mappings');
    }

    public function alwaysCheck(): array
    {
        return [];
    }
}
