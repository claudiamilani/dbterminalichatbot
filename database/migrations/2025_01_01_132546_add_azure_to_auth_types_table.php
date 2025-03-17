<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

use App\Auth\AuthType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('auth_types', function (Blueprint $table) {
            $table->boolean('default')->after('name')->default(0);
            $table->boolean('enabled')->after('default')->default(0);
            $table->boolean('auto_register')->after('enabled')->nullable();
            $table->string('driver')->after('auto_register')->nullable();
        });

        Schema::table('app_configurations', function (Blueprint $table) {
            $table->dropColumn(['allow_ldap_auth', 'autoreg_ldap_users']);
        });

        AuthType::updateOrCreate(['id' => AuthType::LOCAL], [
            'id' => AuthType::LOCAL,
            'name' => 'Local',
            'enabled' => 1,
            'default' => 1,
            'driver' => \App\Auth\Drivers\LocalAuthDriver::class
        ]);
        AuthType::updateOrCreate(['id' => AuthType::LDAP], [
            'id' => AuthType::LDAP,
            'name' => 'LDAP',
            'auto_register' => 1,
            'driver' => \App\Auth\Drivers\LdapAuthDriver::class
        ]);
        AuthType::updateOrCreate(['id' => AuthType::AZURE], [
            'id' => AuthType::AZURE,
            'name' => 'Microsoft Azure',
            'auto_register' => 1,
            'driver' => \App\Auth\Drivers\AzureAuthDriver::class
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auth_types', function (Blueprint $table) {
            $table->dropColumn(['default', 'enabled', 'auto_register', 'driver']);
        });

        Schema::table('app_configurations', function (Blueprint $table) {
            $table->unsignedInteger('allow_ldap_auth')->default(0);
            $table->unsignedInteger('autoreg_ldap_users')->default(1);
        });

        !empty($auth_type = AuthType::find(AuthType::AZURE)) ? $auth_type->delete() : null;
    }
};
