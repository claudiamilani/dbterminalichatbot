<?php

namespace App\Jobs;

use App\DBT\Imports\IngestionGsma;
use App\DBT\Imports\IngestionMobileThink;
use App\DBT\Imports\IngestionWindTre;
use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\Ingestion;
use App\DBT\Models\IngestionSource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IngestRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public Ingestion $ingestion;

    public $failOnTimeout = false;

    /**
     * Create a new job instance.
     */
    public function __construct(Ingestion $ingestion)
    {
        $this->ingestion = $ingestion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(!AttributeHeaderMapping::where('ingestion_source_id',$this->ingestion->ingestion_source_id)->count()){
            $this->fail(new \Exception('Missing Attribute Header Mapping'));
        }
        switch ($this->ingestion->source->id) {
            case IngestionSource::SRC_ADMIN:
                $source = new IngestionWindTre($this->ingestion);
                $source->processFile();
                Log::channel('ingestion')->info('Processed ingestion: ', ['id' => $this->ingestion->id, 'ingestion_source_id' => $this->ingestion->source->id]);
                break;
            case IngestionSource::SRC_MOBILETHINK:
                $source = new IngestionMobileThink($this->ingestion);
                $source->processFile();
                Log::channel('ingestion')->info('Processed ingestion: ', ['id' => $this->ingestion->id, 'ingestion_source_id' => $this->ingestion->source->id]);
                break;
            case IngestionSource::SRC_GSMA:
                $source = new IngestionGsma($this->ingestion);
                $source->processFile();
                Log::channel('ingestion')->info('Processed ingestion: ', ['id' => $this->ingestion->id, 'ingestion_source_id' => $this->ingestion->source->id]);
                break;
            default:
                break;
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->ingestion->update(['message' => $exception->getMessage(), 'status' => Ingestion::STATUS_ERROR]);
    }
}
