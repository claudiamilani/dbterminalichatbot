<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Listeners;

use App\AppConfiguration;
use Carbon\Carbon;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

class UserEventSubscriber
{
    /**
     * Handle user failed login events.
     * @param $event
     */
    public function onFailedLogin($event): void
    {
        DB::table('users')
            ->where('id', $event->user->id)
            ->update([
                'login_failed_on' => Carbon::now()->format('Y-m-d H:i:s'),
                'login_failed_ipv4' => request()->getClientIp(),
                'failed_login_count' => $failed_count = $event->user->failed_login_count+1,
                'locked' => ($failed_count >= AppConfiguration::current()->max_failed_login_attempts) ? 1 : 0,

            ]);
    }

    /**
     * Handle user login success events.
     * @param $event
     */
    public function onLoginSuccess($event): void
    {
        DB::table('users')
            ->where('id', $event->user->id)
            ->update([
                'login_success_on' => Carbon::now()->format('Y-m-d H:i:s'),
                'login_success_ipv4' => request()->getClientIp(),
                'failed_login_count' => 0,
            ]);
    }

    /**
     * Handle user login success events.
     * @param $event
     */
    public function onPasswordReset($event): void
    {
        DB::table('users')
            ->where('id', $event->user->id)
            ->update([
                'failed_login_count' => 0,
                'locked' => AppConfiguration::current()->pwd_reset_unlocks_account ? 0 : $event->user->locked
            ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'Illuminate\Auth\Events\Failed',
            'App\Listeners\UserEventSubscriber@onFailedLogin'
        );
        $events->listen(
            'App\Events\LoginFailed',
            'App\Listeners\UserEventSubscriber@onFailedLogin'
        );

        $events->listen(
            'App\Events\LoginSuccess',
            'App\Listeners\UserEventSubscriber@onLoginSuccess'
        );

        $events->listen(
            'App\Events\PasswordReset',
            'App\Listeners\UserEventSubscriber@onPasswordReset'
        );

        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@onLoginSuccess'
        );
    }
}
