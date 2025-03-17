<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Permission::create(['name' => 'create_users']);
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'update_users']);
        Permission::create(['name' => 'list_users']);
        Permission::create(['name' => 'delete_users']);
        Permission::create(['name' => 'manage_users_roles']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
    }
}
