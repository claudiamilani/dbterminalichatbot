<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use App\Auth\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('email')->nullable()->comment('Account owner email. Used for password recovery via mail.');
            $table->text('profile_image')->nullable()->comment('Base64 html ready string like data:image/jpeg;base64,base64data');
            $table->string('user')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedInteger('enabled')->default(0)->comment('States whether this account is allowed to login.');
            $table->dateTime('enabled_from')->nullable();
            $table->dateTime('enabled_to')->nullable();
            $table->timestamps();
        });

        User::unguard();
        User::create([
            'name' => 'Medialogic',
            'surname' => 'Support',
            'email' => 'devphp@medialogic.it',
            'user' => 'admin',
            'password' => 'M3dialogic$',
            'enabled' => 1,
        ]);
        User::reguard();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
