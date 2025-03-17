<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\Permission;
use Illuminate\Database\Migrations\Migration;

class AddLabelDataToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Permission::unguard();
        //USERS
        Permission::where('name', 'create_users')->update(['label' => 'Create', 'permission_type_id' => 1]);
        Permission::where('name', 'view_users')->update(['label' => 'View', 'permission_type_id' => 1]);
        Permission::where('name', 'update_users')->update(['label' => 'Update', 'permission_type_id' => 1]);
        Permission::where('name', 'list_users')->update(['label' => 'List', 'permission_type_id' => 1]);
        Permission::where('name', 'delete_users')->update(['label' => 'Delete', 'permission_type_id' => 1]);
        Permission::where('name', 'manage_users_roles')->update(['label' => 'Manage Roles', 'permission_type_id' => 1]);
        Permission::create(['name' => 'manage_users_status', 'label' => 'Manage Status', 'permission_type_id' => 1]);

        //ROLES
        Permission::create(['name' => 'create_roles', 'label' => 'Create', 'permission_type_id' => 2]);
        Permission::create(['name' => 'view_roles', 'label' => 'View', 'permission_type_id' => 2]);
        Permission::create(['name' => 'update_roles', 'label' => 'Update', 'permission_type_id' => 2]);
        Permission::create(['name' => 'list_roles', 'label' => 'List', 'permission_type_id' => 2]);
        Permission::create(['name' => 'delete_roles', 'label' => 'Delete', 'permission_type_id' => 2]);
        Permission::create(['name' => 'manage_permissions', 'label' => 'Manage Permissions', 'permission_type_id' => 2]);

        Permission::reguard();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Permission::whereIn('name',
            [
                'create_users',
                'view_users',
                'update_users',
                'list_users',
                'delete_users',
                'manage_users_roles',
                'manage_users_status',
                'create_roles',
                'view_roles',
                'update_roles',
                'list_roles',
                'delete_roles',
                'manage_permissions',
            ])->delete();
    }
}
