<?php

use App\Auth\AuthType;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        AuthType::create([
            'id' => AuthType::SAMLVAS,
            'name' => 'Samlvas',
            'enabled' => 1,
            'default' => 0,
            'auto_register' => 1,
            'driver' => \App\Auth\Drivers\SamlvasAuthDriver::class
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //AuthType::where('id', AuthType::SAMLVAS)->delete();
    }
};
