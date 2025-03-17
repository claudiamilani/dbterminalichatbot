<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('login_success_on')->nullable()->comment('The datetime of last successful login attempt.');
            $table->string('login_success_ipv4')->nullable()->comment('Ipv4 of last successful login attempt.');
            $table->dateTime('login_failed_on')->nullable()->comment('The datetime of last failed login attempt.');
            $table->string('login_failed_ipv4')->nullable()->comment('Ipv4 of last failed login attempt.');
            $table->unsignedInteger('failed_login_count')->default(0)->comment('Counter for failed login attempts since last successful login.');
            $table->unsignedInteger('locked')->default(0)->comment('States whether account is locked to prevent too many consecutive failed login attempts.');
            $table->unsignedInteger('pwd_change_required')->default(0)->comment('States whether password needs to be changed upon login.');
            $table->text('user_agent_success')->nullable();
            $table->text('user_agent_failed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_success_on');
            $table->dropColumn('login_success_ipv4');
            $table->dropColumn('login_failed_on');
            $table->dropColumn('login_failed_ipv4');
            $table->dropColumn('locked');
            $table->dropColumn('pwd_change_required');
            $table->dropColumn('user_agent_success');
            $table->dropColumn('user_agent_failed');
        });
    }
}
