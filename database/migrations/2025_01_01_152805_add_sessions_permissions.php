<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\Permission;
use App\Auth\PermissionType;
use Illuminate\Database\Migrations\Migration;

class AddSessionsPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $permission_type = PermissionType::create(['name' => 'Sessions']);
        Permission::unguard();
        Permission::create(['name' => 'list_sessions', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
        Permission::create(['name' => 'delete_sessions', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down(): void
    {
        Permission::whereIn('name',
            [
                'list_sessions',
                'delete_sessions',
            ])->delete();
        PermissionType::where('name','Sessions')->delete();
    }
}