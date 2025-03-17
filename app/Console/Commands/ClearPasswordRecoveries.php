<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Console\Commands;

use App\Auth\PasswordRecovery;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearPasswordRecoveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lft:clear-password-recoveries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes pending password recoveries from relative table.';

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
        try {
            $count_deleted_password = PasswordRecovery::where('created_at', '<', $yesterday=Carbon::now()->subDay())->delete();
            Log::debug('Deleted ' . $count_deleted_password . ' expired password recoveries older than '. $yesterday );
            $this->info('Deleted ' . $count_deleted_password . ' expired password recoveries older than '. $yesterday );
        } catch (Exception $e) {
            Log::error('Error clearing password recoveries table:'. $e->getMessage());
            $this->error('Error clearing password recoveries table:'. $e->getMessage());
        }
    }
}
