<?php

namespace App\Jobs;

use App\DBT\Models\LegacyImport;
use App\DBT\Models\LegacyImportItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportLegacyRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public LegacyImport $import;
    public $importItem;
    public $row;
    public $model;

    /**
     * Create a new job instance.
     */
    public function __construct(LegacyImport $import, $row)
    {
        $this->import = $import;
        $this->row = $row;
        $this->model = LegacyImport::IMPORTABLE_MODELS[$this->import->type];
    }

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->retrieveItem();
        $this->importItem->save();
        if (in_array($this->import->status,[LegacyImport::STATUS_QUEUED,LegacyImport::STATUS_REQUESTED])) {
            $this->import->update(['status' => LegacyImport::STATUS_PROCESSING]);
        }

        $res = $this->model::importLegacyRecord($this->row, $this->import->update_existing);
        $this->importItem->fill(['message' => $res->message, 'result' => $res->status, 'status' => LegacyImportItem::STATUS_PROCESSED])->save();
    }

    public function failed(\Throwable $e)
    {
        $this->retrieveItem();
        $this->importItem->fill(['message' => $e->getMessage(), 'status' => LegacyImportItem::STATUS_ERROR])->save();
    }

    private function retrieveItem(){
        $this->importItem = $this->import->items()->firstOrNew(['legacy_id' => $this->row->{$this->model::legacyPrimaryKey()}],['legacy_id' => $this->row->{$this->model::legacyPrimaryKey()}]);
        if (!$this->importItem->legacy_import_id) {
            $this->importItem->legacyImport()->associate($this->import);
        }
    }
}
