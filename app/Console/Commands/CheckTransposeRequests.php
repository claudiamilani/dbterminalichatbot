<?php

namespace App\Console\Commands;

use App\DBT\Transpose;
use App\DBT\TransposeRequest;
use App\Jobs\ProcessTranspose;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTransposeRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbt:check-transpose-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for pending TransposeRequest';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Checking for TransposeRequests to process...');
            $tr_to_process = TransposeRequest::requested()->orderBy('created_at')->first();
            $tr_processing = TransposeRequest::active()->first();
            Transpose::clearArchived();
            if($tr_processing && $tr_to_process){
                $this->info('Found Transpose request ID: ' .$tr_processing->id.' still processing.');
                return;
            }
            if($tr_to_process){
                $this->info('Starting process for Transpose request ID: ' .$tr_to_process->id.'.');
                ProcessTranspose::dispatch($tr_to_process)->onQueue(TransposeRequest::QUEUE);
            }

        } catch(Exception $e) {
            $this->error($e->getMessage());
            Log::channel('transpose')->error($e->getMessage());
        }
    }
}
