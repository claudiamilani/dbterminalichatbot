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
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->unsignedInteger('allow_pwd_reset')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->dropColumn('allow_pwd_reset');
        });
    }
};
