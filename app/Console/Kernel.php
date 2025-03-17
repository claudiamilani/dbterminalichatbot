<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('lft:clear-password-recoveries')->hourly();
        $schedule->command('lft:reset-failed-login')->hourly();
        $schedule->command('lft:remove-expired-sessions')->hourly();
        $schedule->command('lft:check-expired-users-password')->daily();
        $schedule->command('cache:prune-stale-tags')->hourly(); // From Laravel framework
        $schedule->command('dbt:import-legacy')->everyFifteenSeconds()->withoutOverlapping()->runInBackground();
        //$schedule->command('dbt:execute-transpose')->dailyAt('01:00');
        $schedule->command('dbt:check-ingestions')->everyMinute();
        //$schedule->command('dbt:check-transpose-requests')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
