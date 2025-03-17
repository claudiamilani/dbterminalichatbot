<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('auth_type_id')->default(1)->index()->comment('Authentication repository. Local, ldap...');

            $table->foreign('auth_type_id')->references('id')->on('auth_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_auth_type_id_foreign');
            $table->dropColumn('auth_type_id');

        });
    }
};
