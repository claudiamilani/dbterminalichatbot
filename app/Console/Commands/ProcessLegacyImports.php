<?php

namespace App\Console\Commands;

use App\DBT\Models\LegacyImport;
use App\DBT\Models\LegacyImportItem;
use App\Jobs\ImportLegacyRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessLegacyImports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbt:import-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import legacy records requested via GUI dispatching required Jobs';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
        $dispatched = 0;
        $jobs = app()->get('queue')->connection(config('queue.default'))->size(LegacyImport::QUEUE);
        if ($import = LegacyImport::active()->first()) {
            if (!$jobs && !$import->items()->pending()->count()) {
                $import->update(['status' => LegacyImport::STATUS_PROCESSED, 'ended_at' => now()]);
                Log::channel('legacy_import')->info('Completed jobs for legacy import', ['id' => $import->id, 'type' => $import->type]);
                return 0;
            }
            $this->info('Legacy import ' . $import->id . ' type ' . $import->type . ' is currently running with ' . $jobs . ' pending jobs.');
            return 0;
        }elseif($jobs){
            $this->info('Previous or deleted Legacy import jobs are still running. Waiting for '.$jobs.' jobs to run.');
            Log::channel('legacy_import')->info('Waiting for queued Legacy Import jobs to be cleared', ['jobs' => $jobs]);
            return 0;
        }
        if ($import = LegacyImport::orderBy('created_at')->requested()->first()) {
            $import->update(['status' => LegacyImport::STATUS_QUEUED, 'started_at' => now()]);
            $this->info('Preparing legacy import ' . $import->id . ' type ' . $import->type);
            Log::channel('legacy_import')->info('Preparing legacy import', ['id' => $import->id, 'type' => $import->type]);
            throw_unless($model = LegacyImport::IMPORTABLE_MODELS[$import->type], new \Exception('Invalid legacy import type: ' . $import->type));
            try {
                foreach ($model::legacyLazy() as $row) {
                    ImportLegacyRecord::dispatch($import, $row)->onQueue(LegacyImport::QUEUE);
                    $dispatched++;
                }
                $this->info('Dispatched ' . $dispatched . ' jobs for legacy import ' . $import->id . ' type ' . $import->type);
                Log::channel('legacy_import')->info('Legacy import jobs dispatched', ['id' => $import->id, 'type' => $import->type, 'jobs' => $dispatched]);
            } catch (\Throwable $e) {
                $this->error('Error preparing legacy import ' . $import->id . ' type ' . $import->type . ': ' . $e->getMessage());
                Log::channel('legacy_import')->error('Error preparing legacy import', ['import_id' => $import->id, 'type' => $import->type, 'message' => $e->getMessage()]);
                $import->update(['status' => LegacyImport::STATUS_ERROR, 'message' => $e->getMessage(), 'ended_at' => now()]);
                return 0;
            }
        } else {
            $this->info('No import tasks to process');
            return 0;
        }
        return 0;
    }
}
