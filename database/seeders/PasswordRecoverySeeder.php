<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasswordRecoverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('password_recoveries')->insert([
           'user_id' => 1,
           'user' => 'admin',
           'email' => 'devphp@medialogic.it',
           'ipv4' => '172.20.0.1',
           'token' => 'KI53RUBcPdzDJkSS7i4KQIqPIq63RTEbF8XIvQx7n1Nbu6LxIApsFScBhPnK'
        ]);
    }
}
