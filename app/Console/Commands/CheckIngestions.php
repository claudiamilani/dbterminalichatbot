<?php

namespace App\Console\Commands;

use App\DBT\Imports\IngestionGsma;
use App\DBT\Imports\IngestionMobileThink;
use App\DBT\Imports\IngestionWindTre;
use App\DBT\Models\Ingestion;
use App\DBT\Models\IngestionSource;
use App\Jobs\IngestRecord;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckIngestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbt:check-ingestions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for ingestions to process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $dispatched = 0;
            $this->info('Checking for ingestions to process...');
            $ingestions_to_process = Ingestion::requested()->orderBy('created_at')->get();
            $ingestion_processing = Ingestion::active()->count();
            $this->info('Found ' .$ingestions_to_process->count(). ' ingestions waiting to be processed');
            if($ingestions_to_process->count()){
                Log::channel('ingestion')->info('Found ' .$ingestions_to_process->count(). ' ingestions waiting to be processed');
            }
            $this->info('Found ' .$ingestion_processing. ' ingestion processing');
            if($ingestion_processing > 0){
                Log::channel('ingestion')->info('Found ' .$ingestion_processing. ' ingestion processing');
            }
            if ($ingestions_to_process->count() && $ingestion_processing == 0) {
                IngestRecord::dispatch($ingestions_to_process->first())->onQueue('ingestion');
                $dispatched++;
            }
            $this->info('Dispatched '.$dispatched.' ingestion jobs');
            if($dispatched > 0){
                Log::channel('ingestion')->info('Dispatched '.$dispatched.' ingestion jobs');
            }
        } catch(Exception $e) {
            $this->error($e->getMessage());
            Log::channel('ingestion')->error($e->getMessage());
        }
    }
}
