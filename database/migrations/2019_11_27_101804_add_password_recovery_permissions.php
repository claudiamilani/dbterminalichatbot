<?php

use App\Auth\Permission;
use App\Auth\PermissionType;
use Illuminate\Database\Migrations\Migration;

class AddPasswordRecoveryPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $permission_type = PermissionType::create(['name' => 'PasswordRecovery']);
        Permission::unguard();
        Permission::create(['name' => 'view_password_recovery_requests', 'label' => 'View', 'permission_type_id' => $permission_type->id]);
        Permission::create(['name' => 'list_password_recovery_requests', 'label' => 'List', 'permission_type_id' => $permission_type->id]);
        Permission::create(['name' => 'delete_password_recovery_requests', 'label' => 'Delete', 'permission_type_id' => $permission_type->id]);
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down(): void
    {
        Permission::whereIn('name',
            [
                'view_password_recovery_requests',
                'list_password_recovery_requests',
                'delete_password_recovery_requests',
            ])->delete();
        PermissionType::where('name','PasswordRecovery')->delete();
    }
}
