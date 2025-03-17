<?php

namespace App\DBT\Imports;

use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\AttributeValue;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\DBT\Models\Vendor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IngestionMobileThink extends IngestionBase
{
    protected $wind_device_make_attrbute = 'Make';
    protected $wind_device_model_attrbute = 'Model';

    public function __construct($ingestion)
    {
        parent::__construct($ingestion);

        $this->headerMapping = array_merge(['TAC' => 'tac'],AttributeHeaderMapping::where('ingestion_source_id',IngestionSource::SRC_MOBILETHINK)->with('dbtAttribute')->get()->pluck('dbtAttribute.name','header_name')->toArray());
        $this->excludeFromAttributes = [
            'is_public',
            'certified',
            'tac',
        ];
    }

    protected function createVendor(array $record): Vendor
    {
        Validator::make($record, [
            'device_make' => 'required|string',
        ])->validate();

        Vendor::unguard();
        $vendor = Vendor::create(['name' => $record['device_make'], 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' => $this->ingestion->source->id]);
        Vendor::reguard();

        return $vendor;
    }

//    protected function createAttribute(string $value): DbtAttribute
//    {
//        // TODO: Implement createAttribute() method.
//    }

    protected function createAttributeValue(string $value, int $terminalId, int $attributeId): AttributeValue
    {
        $value = $this->convertValue($value);

        $count = AttributeValue::where('terminal_id', $terminalId)->where('ingestion_source_id', IngestionSource::SRC_MOBILETHINK)->count();
        $time = Carbon::now()->diffInMonths(Terminal::find($terminalId)->created_at);
        // per mobile think dobbiamo inibire la creazione dei valori se ne abbiamo  già provenienti da mobilethink o se il terminale è stato aggiunto piu di 6 mesi fa
        if ($count == 0 || $time < self::UPDATE_RANGE) {
            $attributeValue = DbtAttribute::createAttributeValue($attributeId, $terminalId, $this->ingestion->source->id, $value, $this->ingestion->id);
        } else {
            Log::channel('ingestion')->info('Attribute value not created: ', ['Value' => $value, 'DbtAttribute ID:' => $attributeId, 'Less than ' . self::UPDATE_RANGE . ' months old' => $time < self::UPDATE_RANGE, 'MobileThink' => $count, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
        }
        return $attributeValue;
    }

    protected function updateAttributeValue(AttributeValue $attributeValue, string $value, int $terminalId, int $attributeId): AttributeValue
    {
        $value = $this->convertValue($value);
        $count = AttributeValue::where('terminal_id', $terminalId)->where('ingestion_source_id', IngestionSource::SRC_MOBILETHINK)->count();
        $time = Carbon::now()->diffInMonths(Terminal::find($terminalId)->created_at);
        if ($count == 0 || $time < self::UPDATE_RANGE) {
            // per mobile think dobbiamo inibire la creazione dei valori se ne abbiamo  già provenienti da mobilethink o se il terminale è stato aggiunto piu di 6 mesi fa
            $attributeValue = DbtAttribute::updateAttributeValue($attributeValue, $value, $this->ingestion->id);
        } else {
            Log::channel('ingestion')->info('Attribute value not updated: ', ['Value' => $value, 'DbtAttribute ID:' => $attributeId, 'Less than ' . self::UPDATE_RANGE . ' months old' => $time < self::UPDATE_RANGE, 'MobileThink' => $count, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
        }
        return $attributeValue;
    }

    protected function createTerminal(array $record, int $vendorId): Terminal
    {
        Validator::make($record, [
            'device_model' => 'required|string|max:255',
        ])->validate();

        Terminal::unguard();
        $terminal = Terminal::create(['name' => $record['device_model'], 'certified' => isset($record['certified']) ? 1 : 0, 'published' => isset($record['is_public']) ? 1 : 0, 'vendor_id' => $vendorId, 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' => $this->ingestion->source->id]);
        Terminal::reguard();

        return $terminal;
    }

    protected function getOrCreateTac(string $value, int $terminalId): Tac
    {
        $tac = Tac::where('value', $value)->first();
        if (!$tac) {
            Tac::unguard();
            $tac = Tac::create(['value' => $value, 'terminal_id' => $terminalId, 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' => $this->ingestion->source->id]);
            Log::channel('ingestion')->info('Tac created: ', ['ID' => $tac->id, 'Value' => $tac->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            Tac::reguard();
        }
        return $tac;
    }

    protected function convertCsv($file)
    {
        return $file;
    }

    protected function additionalIngestionLogic(Terminal $terminal, Vendor $vendor, DbtAttribute $attribute, AttributeValue $attributeValue): bool
    {
        $time = Carbon::now()->diffInMonths($terminal->created_at);
        $mtTerminaleName = null;
        //se stiamo analizzando un attribute value per l'attributo device_make
        if ($attribute->name == $this->wind_device_model_attrbute) {
            $mtTerminaleName = $attributeValue->value;
        }
        $count = AttributeValue::where('terminal_id', $terminal->id)->where('ingestion_source_id', IngestionSource::SRC_MOBILETHINK)->count();
        if ($count == 0 || $time < self::UPDATE_RANGE) {
            //Log::channel('ingestion')->info('Already saved mobile think attributes: ' . $count);
            //Log::channel('ingestion')->info('Terminal created less than 6 months: ' . $time < self::UPDATE_RANGE);
            //Per mobilethink dobbiamo verificare che Wind non abbia già degli attribute value salvati per i campi "device_make" ( vendor ) e device_model
            $windDeviceMakeAttributeId = DbtAttribute::where('name', $this->headerMapping[$this->wind_device_make_attrbute])->first();
            $windDeviceMakeAttributeValue = optional(AttributeValue::where('dbt_attribute_id', $windDeviceMakeAttributeId->id)->where('terminal_id', $terminal->id)->where('ingestion_source_id', IngestionSource::SRC_ADMIN)->first())->value;
            $windDeviceModelAttributeValue = optional($terminal->attributeValues()->where('name', $this->wind_device_model_attrbute)->where('ingestion_source_id', IngestionSource::SRC_ADMIN))->value;

            //se non ha gia attributi mobilethink salvati e wind non ha un attribute_value per Device_make o Device_model aggiorniamo il terminale coi dati di mobilethink
            if ($terminal->name != $mtTerminaleName && !empty($mtTerminaleName)  && empty($windDeviceMakeAttributeValue) && empty($windDeviceModelAttributeValue)) {
                Log::channel('ingestion')->info('MobileThink attribute is updating terminal name and vendor',['ingestion_id'=>$this->ingestion->id, 'old_terminal_name'=>$terminal->name, 'new_terminal_name'=> $mtTerminaleName,'vendor_name'=>$vendor->name]);
                $terminal->update([
                    'name' => $mtTerminaleName,
                    'vendor_id' => $vendor->id
                ]);

                return true;
            }
        }
        return false;
    }

    protected function updateTerminal(Terminal $terminal, array $record, int $vendorId): Terminal
    {
        Validator::make($record, [
            'device_model' => 'required|string|max:255',
        ])->validate();

        Terminal::unguard();
        $terminal->update(['name' => $record['device_model'], 'vendor_id' => $vendorId, 'ingestion_id' => $this->ingestion->id,]);
        Terminal::reguard();

        return $terminal;
    }

    protected function createTac(string $value, int $terminalId): Tac
    {
        Tac::unguard();
        $tac = Tac::create(['value' => $value, 'terminal_id' => $terminalId, 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' =>  $this->ingestion->source->id ]);
        Tac::reguard();

        return $tac;
    }
}