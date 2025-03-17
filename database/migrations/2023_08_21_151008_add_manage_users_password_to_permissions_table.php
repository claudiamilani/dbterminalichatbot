<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function () {
            Permission::unguard();
            Permission::updateOrCreate(['name' => 'manage_users_password'], ['name' => 'manage_users_password', 'label' => 'Manage Password', 'permission_type_id' => 1]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function () {
            Permission::where('name', 'manage_users_password')->delete();
        });
    }
};
