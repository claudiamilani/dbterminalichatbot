<?php

namespace App\DBT;

use App\DBT\Models\AttributeValue;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Terminal;
use App\DBT\Models\TransposeConfig;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Transpose
{
    public const TABLE_NAME = 'trasposta';
    protected $other_attributes = ['tac', 'marca_wind', 'modello_wind', 'certified', 'is_public'];
    protected $sources;
    protected $transposeConfig;

    public function __construct()
    {
        $this->sources = IngestionSource::get()->toArray();
        $this->transposeConfig = TransposeConfig::with(['dbtAttribute'])->orderBy('display_order')->get();
    }

    /**
     * Create Trasposta table
     *
     * @return bool
     */
    public function createTransposeTable(): bool
    {
        Log::channel('transpose')->debug('Creating Transpose table');

        if (!empty($this->transposeConfig->count())) {
            $query = "CREATE TABLE IF NOT EXISTS " . self::TABLE_NAME . " ( terminal_id bigint, ";
            foreach ($this->transposeConfig as $config) {
                $query .= $config->label . " $config->type, ";
            }
            $query = rtrim($query, ', ');
            $query .= ");";
            DB::statement($query);
            return true;
        } else {
            Log::channel('transpose')->warning('Transpose config table empty');
            return false;
        }

    }

    /**
     * Drop Trasposta table
     *
     * @return void
     */
    public function destroyTransposeTable(): void
    {
        Log::channel('transpose')->debug('Destroying DWH_TRASPOSTA view');
        DB::statement('DROP VIEW IF EXISTS "DWH_TRASPOSTA";');
        Log::channel('transpose')->debug('Destroying Transpose table');
        DB::statement("DROP TABLE IF EXISTS " . self::TABLE_NAME . " ;");
    }

    /**
     * To be launched only once, creates default configuration for Trasposta table, based on TransposeConfig model
     *
     * @return void
     */
    public static function createTransposeConfigTableFromLegacy(): void
    {
        try {
            Log::channel('transpose')->debug('Creating TransposeConfig table');
            $env = in_array(config('dbt.transpose.default_ini_env'), ['prod', 'production']) ? 'production' : 'staging';
            $attribute_mapping = parse_ini_file(config_path('/legacy/' . $env . '/attribute_mapping.ini'));
            $type_mapping = parse_ini_file(config_path('/legacy/' . $env . '/attribute_type_mapping.ini'));
            $local_attributes = DbtAttribute::whereIn('legacy_id', array_keys($attribute_mapping))->get();
            $count = 0;
            foreach ($attribute_mapping as $id => $name) {
                $local_attribute = $local_attributes->where('legacy_id', $id)->first();
                if (!$local_attribute) {
                    continue;
                }
                TransposeConfig::create([
                    'dbt_attribute_id' => $local_attribute->id,
                    'label' => array_key_exists($local_attribute->legacy_id, $attribute_mapping) ? $attribute_mapping[$local_attribute->legacy_id] : $local_attribute->name,
                    'type' => $type_mapping[$local_attribute->legacy_id],
                    'display_order' => $count
                ]);

                $count = $count + 10;
            }
        } catch (\Exception $e) {
            Log::channel('transpose')->error($e->getMessage());
            Log::channel('transpose')->error($e->getTraceAsString());
        }
    }

    /**
     * Execute all methods to destroy and re-create Trasposta table
     *
     * @return void
     */
    public function executeTranspose(): void
    {
        try {
            $this->destroyTransposeTable();
            if ($this->createTransposeTable()) {
                foreach (Terminal::lazy() as $terminal) {
                    Log::channel('transpose')->debug('Preparing data for terminal_id: ' . $terminal->id);
                    $data = $this->prepareData($terminal->id);
                    if ($data) {
                        Log::channel('transpose')->debug('Prepared: ' . count($data) - 1 . ' attributes');
                        $this->fillTranspose($data);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::channel('transpose')->error($e->getMessage());
            Log::channel('transpose')->error($e->getTraceAsString());
        }
    }

    /**
     * Retrieve and format the public value for each configured DBTAttribute to be inserted in Trasposta table.
     *
     * @param $terminal_id
     * @return array
     */
    public function prepareData($terminal_id): array
    {
        $attribute_values = $this->getAllPublicValuesQuery()->where('attribute_values.terminal_id', $terminal_id)->get();
        $data = [];
        foreach ($this->transposeConfig as $config) {
            $value = $attribute_values->where('dbt_attribute_id', $config->dbt_attribute_id)->first();
            //$value = $config->dbtAttribute->getPublicValue($terminal_id, $this->sources);
            $data['terminal_id'] = $terminal_id;
            if ($value) {
                $data[trim(strtolower($config->label))] = $this->formatTransposedValue($value, $config);
            }
        }
        return $data;
    }

    /**
     * Format passed value based on TransposeConfig configuration ( Boolean, Varchar)
     *
     * @param $value
     * @param $config
     * @return mixed|string
     */
    public function formatTransposedValue($value, $config): mixed
    {
        switch ($config->type) {
            case 'VARCHAR':
                $value = (string)$value->getReadableValue();
                break;
            case 'BOOLEAN':
                $value = (bool)$value->value === true ? 'true' : 'false';
                break;
            default:
                $value = $value->getReadableValue();
        }

        return $value;
    }

    /**
     * Insert row in Trasposta table
     *
     * @param $data
     * @return void
     */
    public function fillTranspose($data)
    {
        DB::table(self::TABLE_NAME)->insert($data);
    }

    /**
     * Generate CSV file joining Trasposta table data with Vendors, Terminals and Tacs
     *
     * @return string|null
     */
    public function export(): bool|string

    {
        try {
            $export_file_name = 'export.csv';
            $file_path = config('dbt.transpose.export_file_path');
            $attribute_names = $this->transposeConfig->pluck('dbtAttribute.name', 'label');
            $csv_headers = array_merge($this->other_attributes, $attribute_names->toArray());
            Log::channel('transpose')->debug('Starting export generation');
            if ($attribute_names->count()) {
                $handle = Storage::put($file_path . DIRECTORY_SEPARATOR . $export_file_name, '');
                if ($handle) {
                    $handle = fopen(Storage::path($file_path . DIRECTORY_SEPARATOR . $export_file_name), 'w');
                    fputcsv($handle, array_values($csv_headers), ';');
                }
                $query = DB::table('terminals as t')
                    ->select(
                        'tc.value AS tac',
                        'v.name AS marca_wind',
                        't.name AS modello_wind',
                        't.certified',
                        't.published'
                    );
                foreach ($attribute_names as $label => $attribute_name) {
                    $query = $query->addSelect("tr.$label");
                }
                $query = $query->join('vendors as v', 't.vendor_id', '=', 'v.id')
                    ->join('tacs as tc', 't.id', '=', 'tc.terminal_id')
                    ->join(self::TABLE_NAME . " as tr", 'tr.terminal_id', '=', 't.id')
                    ->orderBy('v.name')
                    ->orderBy('t.name');
                $partial = $query->cursor();
                foreach ($partial as $result) {
                    $formatted_result = [];
                    foreach ($result as $key => $value) {
                        if (is_string($value)) {
                            $formatted_result[$key] = str_replace(';', '|', $value);
                        } else {
                            $formatted_result[$key] = $value;
                        }
                    }
                    fputcsv($handle, $formatted_result, ';');
                }
                fclose($handle);
                if (!Storage::directoryExists(config('dbt.transpose.export_file_path') . DIRECTORY_SEPARATOR . 'archived')) {
                    Storage::makeDirectory(config('dbt.transpose.export_file_path') . DIRECTORY_SEPARATOR . 'archived');
                }

                $archived_path = config('dbt.transpose.export_file_path') . DIRECTORY_SEPARATOR . 'archived' . DIRECTORY_SEPARATOR . str_random(5).'_export_' . date("d_m_y") . '.csv';
                Storage::copy($file_path . DIRECTORY_SEPARATOR . $export_file_name, $archived_path );
                Log::channel('transpose')->debug('Transpose export end. File ' . $export_file_name . ' saved at path: ' . Storage::path($archived_path));
                return $archived_path;

            } else {
                Log::channel('transpose')->debug('No Transpose Config saved. Aborting export');
                return false;
            }
        } catch (\Exception $e) {
            Log::channel('transpose')->error('Errore nella generazione del file di export trasposta');
            Log::channel('transpose')->error($e->getMessage());
            Log::channel('transpose')->error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Clear archived exports older than "export_retention_days" configured days
     *
     * @return void
     */
    public static function clearArchived(): void
    {
        try {
            $requests = TransposeRequest::where('created_at', '<', Carbon::now()->subDays(config('dbt.transpose.export_retention_days')))->get();
            foreach($requests as $request) {
                if($request->file_path){
                    Log::channel('transpose')->debug('Deleting: ' . $request->file_path);
                    Storage::delete($request->file_path);
                }
                $request->delete();
            }
        } catch (\Exception $e) {
            Log::channel('transpose')->error('Error while deleting archived transpose export file');
            Log::channel('transpose')->error($e->getMessage());
            Log::channel('transpose')->error($e->getTraceAsString());
        }
    }

    public function getAllPublicValuesQuery()
    {
        $prioritySource = AttributeValue::selectRaw('min(ingestion_source_id) source, terminal_id, dbt_attribute_id')->groupBy(['terminal_id', 'dbt_attribute_id']);

        return AttributeValue::select('attribute_values.*')
            ->joinSub($prioritySource, 'prioritySource', function ($join) {
                $join->on('attribute_values.terminal_id', '=', 'prioritySource.terminal_id')->on('attribute_values.dbt_attribute_id', '=', 'prioritySource.dbt_attribute_id')->on('attribute_values.ingestion_source_id', '=', 'prioritySource.source');
            })->join('transpose_configs', 'transpose_configs.dbt_attribute_id', 'attribute_values.dbt_attribute_id');
    }
}