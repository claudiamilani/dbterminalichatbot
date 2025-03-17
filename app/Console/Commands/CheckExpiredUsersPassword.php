<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Console\Commands;

use App\AppConfiguration;
use App\Auth\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredUsersPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lft:check-expired-users-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if any users passwords need to be expired.';

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
        $this->info('Attempting to request change of password for any expiring passwords...');
        Log::debug('Attempting to request change of password for any expiring passwords...');
        $users = User::mustNotChangePassword()->get();
        $app_config = AppConfiguration::current();

        foreach ($users as $user) {
            $driver = $user->authType->driverInstance;

            if (!$driver->pwdExpires()) {

                continue;
            }
            try {

                if ($user->pwd_changed_at != null) {
                    $user_password_date = $user->pwd_changed_at;
                    $user_password_date->hour = 0;
                    $user_password_date->minute = 0;
                    $user_password_date->second = 0;
                } else {
                    $user_password_date = null;
                }

                if ($user_password_date != null && $user_password_date->diffInDays(Carbon::today()) > $app_config->pwd_expires_in) {
                    $this->forcePasswordChange($user);
                } elseif ($user_password_date == null) {
                    $this->forcePasswordChange($user);
                }

            } catch (Exception $e) {
                $this->error('Failed to request change for the expiring password of user: ' . $user->user);
                Log::debug('Failed to request change for the expiring password of user: ' . $user->user);
                Log::error($e->getMessage());
                $this->error('error ' . $e->getMessage());
            }
        }
    }

    public function forcePasswordChange($user): void
    {
        $user->pwd_change_required = 1;
        $user->save();
        $this->info('account password of ' . $user->user . ' was flagged as expired');
    }
}
