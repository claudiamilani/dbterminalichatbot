<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Role::create(['name' => 'Administrator', 'description' => 'Everything is allowed']);
        Role::create(['name' => 'Manager', 'description' => 'Allowed to manage all contents']);
        Role::create(['name' => 'Registered User', 'description' => 'Basic user']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}
