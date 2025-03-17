<?php

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
            $permission_type = PermissionType::create(['name' => 'TransposeRequests']);
            Permission::unguard();
            Permission::create(['name' => 'create_transpose_requests', 'label' => 'Create', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'view_transpose_requests', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'list_transpose_requests', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
            Permission::create(['name' => 'delete_transpose_requests', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
            Permission::reguard();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permission', function (Blueprint $table) {
            Permission::whereIn('name', [
                'create_transpose_requests', 'view_transpose_requests', 'update_transpose_requests', 'list_transpose_requests', 'delete_transpose_requests'
            ])->delete();
            PermissionType::where('name', 'TransposeRequests')->delete();
        });
    }
};
