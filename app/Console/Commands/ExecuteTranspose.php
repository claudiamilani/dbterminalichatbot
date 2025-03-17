<?php

namespace App\Console\Commands;

use App\DBT\TransposeRequest;
use App\Jobs\ProcessTranspose;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecuteTranspose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbt:execute-transpose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Avvia il processo schedulato di popolamento della tabella "trasposta" e delle viste DWH_';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            if(!$tr_processing = TransposeRequest::active()->first()){
                Log::channel('transpose')->info('Scheduled Transpose execution starting');
                $tr_request = new TransposeRequest();
                $tr_request->save();
                ProcessTranspose::dispatch($tr_request)->onQueue(TransposeRequest::QUEUE);
            }else{
                Log::channel('transpose')->warning('Scheduled Transpose execution aborted, TransposeRequest with ID: ' . $tr_processing->id . ' still processing.');
                return;
            }
        } catch (\Exception $e) {
            Log::channel('transpose')->error($e->getMessage());
            Log::channel('transpose')->error($e->getTraceAsString());
        }
    }
}
