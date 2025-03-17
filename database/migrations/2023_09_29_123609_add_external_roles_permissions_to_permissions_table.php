<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\Permission;
use App\Auth\PermissionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $permission_type = PermissionType::create(['name' => 'ExternalRoles']);
            Permission::unguard();
            Permission::updateOrCreate(['name' => 'list_external_roles'], ['name' => 'list_external_roles', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::updateOrCreate(['name' => 'create_external_roles'], ['name' => 'create_external_roles', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::updateOrCreate(['name' => 'update_external_roles'], ['name' => 'update_external_roles', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);
            Permission::updateOrCreate(['name' => 'delete_external_roles'], ['name' => 'delete_external_roles', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission_type = PermissionType::where('name','ExternalRoles')->first();

        foreach($permission_type->permissions as $permission){
            $permission->delete();
        }

        $permission_type->delete();

    }
};
