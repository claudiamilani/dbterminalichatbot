<?php

namespace App\Jobs;

use App\DBT\DwhOperations;
use App\DBT\Transpose;
use App\DBT\TransposeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessTranspose implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tr_request;
    public $timeout = 43190;
    /**
     * Create a new job instance.
     */
    public function __construct(TransposeRequest $tr_request)
    {
        $this->tr_request = $tr_request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('transpose')->info('Transpose execution starting');
        $this->tr_request->started_at = Carbon::now();
        $this->tr_request->status = TransposeRequest::STATUS_PROCESSING;
        $this->tr_request->save();
        $transpose = new Transpose();
        $transpose->executeTranspose();
        $file_path = $transpose->export();
        if($file_path){
            $this->tr_request->file_path = $file_path;
        }
        $dwh = new DwhOperations();
        $dwh->executeDwhAttributes();
        $dwh->createDwhTraspostaView();
        $this->tr_request->status = TransposeRequest::STATUS_PROCESSED;
        $this->tr_request->ended_at = Carbon::now();
        $this->tr_request->save();
        Log::channel('transpose')->info('Transpose and DWH operations finished');
    }

    public function failed(\Throwable $e)
    {
        $this->tr_request->status = TransposeRequest::STATUS_ERROR;
        $this->tr_request->message = $e->getMessage();
        $this->tr_request->save();
    }
}
