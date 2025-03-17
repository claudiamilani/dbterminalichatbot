<?php

use App\Auth\Permission;
use App\Auth\PermissionType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permission_type = PermissionType::create(['name' => 'AuthTypes']);

        Permission::unguard();
        Permission::create(['name' => 'view_auth_types', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
        Permission::create(['name' => 'list_auth_types', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
        Permission::create(['name' => 'update_auth_types', 'label' => 'Update', 'permission_type_id' => $permission_type->id]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission_type = PermissionType::where('name','AuthTypes')->first();

        foreach($permission_type->permissions as $permission){
            $permission->delete();
        }

        $permission_type->delete();

    }
};
