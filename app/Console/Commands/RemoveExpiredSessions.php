<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Console\Commands;

use App\Auth\UserSession;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lft:remove-expired-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes sessions that have expired.';

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
     */
    public function handle(): void
    {
        try {
            $this->info('Attempting to remove expired sessions...');
            if(Config::get('session.driver') != 'custom-database') {
                $this->error('Session driver needs to be using database.');
                exit;
            }
            DB::beginTransaction();
            $sessions = UserSession::all();
            $sessionCount = 0;
            foreach ($sessions as $session) {
                if ($session->last_activity && (time() - $session->last_activity->getTimeStamp()) > (Config::get('session.lifetime') * 60))
                {
                    $sessionCount++;
                    $session->delete();
                }
            }
            DB::commit();

            $this->info('Removed '.$sessionCount.' expired sessions.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Failed to remove expired sessions error '.$e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
