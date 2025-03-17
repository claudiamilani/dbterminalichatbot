<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auth_type_id')->index();
            $table->string('external_role_id');
            $table->boolean('auto_register_users')->default(1);
            $table->timestamps();

            $table->unique([
               'auth_type_id',
               'external_role_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_roles');
    }
};
