<?php

namespace App\DBT\Imports;

use App\DBT\Models\AttributeValue;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\Ingestion;
use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\DBT\Models\Vendor;
use App\Notifications\IngestionCompleted;
use App\Notifications\IngestionFailed;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

abstract class IngestionBase
{
    protected Ingestion $ingestion;
    protected $vendor;
    protected $terminal;
    protected $attribute;
    protected $attributeValue;
    protected $tac;

    protected string $separator = ';';
    protected string $multiple_separator = '|';
    protected array $header = [];
    protected array $headerMapping = [];
    protected array $excludeFromAttributes = [];
    protected array $results = [];
    protected const int UPDATE_RANGE = 6; //months

    public function __construct(Ingestion $ingestion)
    {
        $this->ingestion = $ingestion;
    }

    public function processFile(): void
    {
        $this->ingestion->status = Ingestion::STATUS_PROCESSING;
        $this->ingestion->started_at = Carbon::now();
        $this->ingestion->save();

        $file = storage_path('app' . DIRECTORY_SEPARATOR . $this->ingestion->file_path);

        try {
            $file = $this->convertCsv($file);
            if (($handle = fopen($file, 'r')) !== false) {
                $this->header = fgetcsv($handle, 0, $this->separator);

                foreach ($this->header as &$headerColumn) {
                    if (array_key_exists($headerColumn, $this->headerMapping)) {
                        $headerColumn = $this->headerMapping[$headerColumn];
                    }
                }
                if (!in_array(['device_make', 'device_model', 'tac'], $this->header)) {
                    throw new Exception('Missing device_make, device_model or tac header. Check the file or mapping configuration');
                }

                while (($row = fgetcsv($handle, 0, $this->separator)) !== false) {
                    try {
                        DB::beginTransaction();
                        $record = [];
                        foreach ($this->header as $key => $mappedColumn) {
                            if (isset($row[$key])) {
                                $record[$mappedColumn] = $row[$key];
                            }
                        }

                        $this->tac = Tac::where('value', $record['tac'])->first();
                        $this->vendor = Vendor::where('name', $record['device_make'])->first();
                        $terminalByTac = $this->tac ? $this->tac->terminal : null;
                        $terminalByMakeAndModel = $this->terminalByMakeAndModel($record['device_make'], $record['device_model']);

                        Log::channel('ingestion')->info('Starting Ingestion for TAC: ' . $record['tac']);
                        Log::channel('ingestion')->info('TerminalByTac Found: ' . $terminalByTac);
                        Log::channel('ingestion')->info('TerminalByMakeAndModel Found: ' . $terminalByMakeAndModel);

                        if (!$this->vendor) {
                            if ($this->ingestion->options['CREATES_VENDOR']) {
                                $this->vendor = $this->createVendor($record);
                                Log::channel('ingestion')->info('Vendor created: ', ['ID' => $this->vendor->id, 'Name' => $this->vendor->name, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                            } else {
                                Log::channel('ingestion')->info('Vendor not found: ', ['Name' => $record['device_make'], 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                continue;
                            }
                        }

                        if ($terminalByTac) {
                            //SE ho trovato un terminale per TAC
                            if ($terminalByMakeAndModel) {
                                //Verifico Mismatch
                                if ($terminalByTac->id !== $terminalByMakeAndModel->id) {
                                    Log::channel('Mismatch between terminalByMakeAndModel and terminalByTac');
                                    //Se il terminale recuperato ha id diverso da quello associato al tac
                                    if ($this->ingestion->options['CREATES_TERMINAL']) {
                                        $this->terminal = $this->createTerminal($record, $this->vendor->id);
                                        foreach ($terminalByTac->tacs as $tac) {
                                            $tac->update([
                                                'terminal_id' => $this->terminal->id,
                                                'ingestion_id' => $this->ingestion->id
                                            ]);
                                        }
                                        Log::channel('ingestion')->info('Terminal created: ', ['ID' => $this->terminal->id, 'Name' => $this->terminal->name, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                        Log::channel('ingestion')->info('TAC created: ', ['ID' => $this->tac->id, 'Value' => $this->tac->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    } else {
                                        Log::channel('ingestion')->info('Terminal not found: ', ['Name' => $record['device_model'], 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    }

                                } else {
                                    //Terminale recuperato e' uguale a quello dell ingestion
                                    $saved_values = $terminalByTac->attributeValues()->where('ingestion_source_id', $this->ingestion->ingestion_source_id)->where('ingestion_id', $this->ingestion->id)->count();
                                    //Stesso terminale
                                    if ($saved_values > 0) {
                                        //Attributi gia salvati per questa ingestion e questo terminale, aggiorno solo tac
                                        Log::channel('ingestion')->info('Already processed Terminal in this ingestion - skipping attribute values', ['terminal_id' => $terminalByTac->id, 'TAC' => $this->tac->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                        DB::commit();
                                        continue;
                                    } else {
                                        //Attributi non salvati, procedo
                                        Log::channel('ingestion')->info('Terminal need to update attribute values', ['terminal_id' => $terminalByTac->id, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                        $this->terminal = $terminalByTac;
                                    }
                                }
                            } else {
                                //Terminale recuperato e' uguale a quello dell ingestion
                                $saved_values = $terminalByTac->attributeValues()->where('ingestion_source_id', $this->ingestion->ingestion_source_id)->where('ingestion_id', $this->ingestion->id)->count();
                                //Stesso terminale
                                if ($saved_values > 0) {
                                    //Attributi gia salvati per questa ingestion e questo terminale, aggiorno solo tac
                                    Log::channel('ingestion')->info('Already processed Terminal in this ingestion - skipping attribute values', ['terminal_id' => $terminalByTac->id, 'TAC' => $this->tac->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    DB::commit();
                                    continue;
                                } else {
                                    //Attributi non salvati, procedo a crearli per il terminaleByTac
                                    $this->terminal = $terminalByTac;
                                }
                            }
                        } else {
                            //Terminale non recuperato dal tac
                            $terminalByMakeAndModel = $this->terminalByMakeAndModel($record['device_make'], $record['device_model']);
                            if ($terminalByMakeAndModel) {
                                $saved_values = $terminalByMakeAndModel->attributeValues()->where('ingestion_source_id', $this->ingestion->ingestion_source_id)->where('ingestion_id', $this->ingestion->id)->count();
                                if ($saved_values > 0) {
                                    //Attributi gia salvati per questa ingestion e questo terminale, aggiorno solo tac
                                    $this->tac = $this->createTac($record['tac'], $terminalByMakeAndModel->id);
                                    Log::channel('ingestion')->info('Already processed Terminal in this ingestion but new tac created: ' . $record['tac'] . ' skipping attribute values', ['terminal_id' => $terminalByMakeAndModel->id, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    DB::commit();
                                    continue;
                                } else {
                                    //Attributi non salvati
                                    $this->tac = $this->createTac($record['tac'], $terminalByMakeAndModel->id);
                                    Log::channel('ingestion')->info('Tac created: ' . $record['tac'] . ' creating/updating attribute values', ['terminal_id' => $terminalByMakeAndModel->id, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    $this->terminal = $terminalByMakeAndModel;
                                }
                            } else {
                                //Terminale non recuperato da make e model
                                if ($this->ingestion->options['CREATES_TERMINAL']) {
                                    $this->terminal = $this->createTerminal($record, $this->vendor->id);
                                    $this->tac = $this->createTac($record['tac'], $this->terminal->id);
                                    Log::channel('ingestion')->info('Terminal created: ', ['ID' => $this->terminal->id, 'Name' => $this->terminal->name, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                } else {
                                    Log::channel('ingestion')->info('Ingestion cannot create terminals: ', ['Name' => $record['device_model'], 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    continue;
                                }
                            }
                        }

                        $updateCnt = 0;
                        $createCnt = 0;
                        $errorCnt = 0;

                        $attributes = collect($record)->except($this->excludeFromAttributes);
                        if (!empty($attributes)) {
                            foreach ($attributes as $key => $value) {
                                try {
                                    $this->attribute = DbtAttribute::where('name', $key)->first();

                                    if ($this->attribute && $this->terminal) {
                                        if (!empty($value) && !in_array(strtolower($value), ['unknown', 'not specified', 'not applicable'])) {
                                            $this->attributeValue = AttributeValue::where('dbt_attribute_id', $this->attribute->id)->where('terminal_id', $this->terminal->id)->where('ingestion_source_id', $this->ingestion->source->id)->first();
                                            if ($this->attributeValue) {
                                                $this->updateAttributeValue($this->attributeValue, $value, $this->terminal->id, $this->attribute->id);
                                                Log::channel('ingestion')->info('Attribute value updated: ', ['ID' => $this->attributeValue->id, 'Value' => $this->attributeValue->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                                $updateCnt++;
                                            } else {
                                                if (!$this->attributeValue) {
                                                    if ($this->ingestion->options['CREATES_ATTRIBUTE']) {
                                                        $this->attributeValue = $this->createAttributeValue($value, $this->terminal->id, $this->attribute->id);
                                                        Log::channel('ingestion')->info('Attribute value created: ', ['ID' => $this->attributeValue->id, 'Value' => $this->attributeValue->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                                        $createCnt++;
                                                    }
                                                }
                                            }
                                            if ($this->attributeValue) {
                                                $this->additionalIngestionLogic($this->terminal, $this->vendor, $this->attribute, $this->attributeValue);
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    $errorCnt++;
                                    Log::channel('ingestion')->error($e->getMessage(), ['ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    Log::channel('ingestion')->error($e->getTraceAsString(), ['ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                                    continue;
                                }

                            }
                        }
                        Log::channel('ingestion')->info('Terminal: ', ['ID' => $this->terminal->id, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id, 'created' => $createCnt, 'updated' => $updateCnt, 'errors' => $errorCnt]);

                        $this->results[$this->terminal->id]['created'] = $createCnt;
                        $this->results[$this->terminal->id]['updated'] = $updateCnt;
                        $this->results[$this->terminal->id]['errored'] = $errorCnt;
                        DB::commit();
                    } catch (\Exception $e) {
                        Log::channel('ingestion')->error($e->getMessage(), ['ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
                        Log::channel('ingestion')->error($e->getTraceAsString());
                        DB::rollback();
                        continue;
                    }
                }
                fclose($handle);
                $this->ingestion->status = Ingestion::STATUS_COMPLETED;
                $this->ingestion->ended_at = Carbon::now();
                Log::debug('Ingestion save');
                $save = $this->ingestion->save();
                Log::debug(json_encode($save));
                $this->sendMail();
            }
        } catch (Exception $e) {
            Log::channel('ingestion')->error($e->getMessage(), ['ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            Log::channel('ingestion')->error($e->getTraceAsString(), ['ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            $this->ingestion->status = Ingestion::STATUS_ERROR;
            $this->ingestion->ended_at = Carbon::now();
            $this->ingestion->save();
        }
    }

    protected function sendMail()
    {
        if ($this->ingestion->notify_mails) {
            if ($this->ingestion->status == Ingestion::STATUS_COMPLETED) {
                Notification::route('mail', $this->ingestion->notify_mails)->notify(new IngestionCompleted($this->ingestion, $this->results));
                Log::channel('ingestion')->info('Mail INGESTION_COMPLETED sent: ', ['Emails' => $this->ingestion->notify_mails, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            } else if ($this->ingestion->status == Ingestion::STATUS_ERROR) {
                Notification::route('mail', $this->ingestion->notify_mails)->notify(new IngestionFailed($this->ingestion));
                Log::channel('ingestion')->info('Mail INGESTION_FAILED sent: ', ['Emails' => $this->ingestion->notify_mails, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            }
        } else {
            Log::channel('ingestion')->info('Mail not sent: ', ['Emails' => $this->ingestion->notify_mails, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
        }

        Log::channel('ingestion')->info('Ingestion results: ', ['attribute_values' => $this->results, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
    }

    protected abstract function convertCsv($file);

    protected abstract function createVendor(array $record): Vendor;

    protected abstract function createAttributeValue(string $value, int $terminalId, int $attributeId): AttributeValue;

    protected abstract function updateAttributeValue(AttributeValue $attributeValue, string $value, int $terminalId, int $attributeId): AttributeValue;

    protected abstract function createTerminal(array $record, int $vendorId): Terminal;

    protected abstract function updateTerminal(Terminal $terminal, array $record, int $vendorId): Terminal;

    protected abstract function createTac(string $value, int $terminalId): Tac;

    protected function convertValue(string $value): string|array
    {
        return str_contains($value, $this->multiple_separator) ? implode('|', explode($this->multiple_separator, $value)) : $value;
    }

    protected abstract function additionalIngestionLogic(Terminal $terminal, Vendor $vendor, DbtAttribute $dbtAttribute, AttributeValue $attributeValue): bool;

    protected function terminalByMakeAndModel(string $make, string $model): Terminal|null
    {
        return Terminal::whereHas('attributeValues', function ($query) use ($make, $model) {
            $query->whereIn('value', [$make, $model]);
        })
            ->whereHas('attributeValues', function ($query) use ($make) {
                $query->where('value', $make);
            })
            ->whereHas('attributeValues', function ($query) use ($model) {
                $query->where('value', $model);
            })
            ->first();
    }

    /**
     * @param array $record
     * @return void
     */
    private function createTerminalAndTac(array $record): void
    {
        if ($this->ingestion->options['CREATES_TERMINAL']) {
            $this->terminal = $this->createTerminal($record, $this->vendor->id);
            Log::channel('ingestion')->info('Terminal created: ', ['ID' => $this->terminal->id, 'Name' => $this->terminal->name, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            $this->tac = $this->createTac($record['tac'], $this->terminal->id);
            Log::channel('ingestion')->info('Tac created: ', ['ID' => $this->tac->id, 'Value' => $this->tac->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
        } else {
            Log::channel('ingestion')->info('Terminal not found: ', ['Name' => $record['device_model'], 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
        }
    }

    protected function updateTac(Tac $tac, string $value, int $terminalId): Tac
    {
        Tac::unguard();
        $this->tac = $tac->update(['value' => $value, 'terminal_id' => $terminalId, 'ingestion_id' => $this->ingestion->id]);
        Tac::reguard();

        return $tac;
    }
}