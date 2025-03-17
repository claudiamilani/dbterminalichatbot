<?php


use App\AppConfiguration;
use Illuminate\Database\Migrations\Migration;

class UpdatePwdComplexityRegexp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $config = AppConfiguration::current(true);
        $config->pwd_regexp = str_replace('(?=.*[\d\X])','(?=.*[\d])',$config->pwd_regexp);
        $config->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $config = AppConfiguration::current(true);
        $config->pwd_regexp = str_replace('(?=.*[\d])','(?=.*[\d\X])',$config->pwd_regexp);
        $config->save();
    }
}
