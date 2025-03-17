<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Console\Commands;

use App\AppConfiguration;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetFailedLoginAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lft:reset-failed-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets failed login attempts counter.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try{
            $this->info('Attempting to reset failed login counter...');
            DB::table('users')->where('login_failed_on','<', Carbon::today()->subDays(AppConfiguration::current()->failed_login_reset_interval))->update(['failed_login_count' => 0]);
            $this->info('Failed login counter reset completed.');
        }catch(Exception $e){
            $this->error('Failed login counter reset error: '.$e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
