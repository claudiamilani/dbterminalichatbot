<?php

namespace App\DBT\Imports;

use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\AttributeValue;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\DBT\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IngestionGsma extends IngestionBase
{
    public function __construct($ingestion)
    {
        parent::__construct($ingestion);

        $this->separator = '|';

        $this->headerMapping = array_merge(['TAC' => 'tac'],AttributeHeaderMapping::where('ingestion_source_id',IngestionSource::SRC_GSMA)->with('dbtAttribute')->get()->pluck('dbtAttribute.name','header_name')->toArray());

        $this->excludeFromAttributes = [
            'tac',
            'count'
        ];
    }

    protected function createVendor(array $record): Vendor
    {
        Validator::make($record, [
            'device_make' => 'required|string',
        ])->validate();

        Vendor::unguard();
        $vendor = Vendor::create(['name' => $record['device_make'], 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' =>  $this->ingestion->source->id]);
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

        AttributeValue::unguard();
        $attributeValue = DbtAttribute::createAttributeValue($attributeId, $terminalId, $this->ingestion->source->id, $value, $this->ingestion->id);
        AttributeValue::reguard();

        return $attributeValue;
    }

    protected function updateAttributeValue(AttributeValue $attributeValue, string $value, int $terminalId, int $attributeId): AttributeValue
    {
        $value = $this->convertValue($value);

        AttributeValue::unguard();
        $attributeValue = DbtAttribute::updateAttributeValue($attributeValue, $value, $this->ingestion->id);
        AttributeValue::reguard();

        return $attributeValue;
    }

    protected function createTerminal(array $record, int $vendorId): Terminal
    {
        Validator::make($record, [
            'device_model' => 'required|string|max:255',
        ])->validate();

        Terminal::unguard();
        $terminal = Terminal::create(['name' => $record['device_model'], 'vendor_id' => $vendorId, 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' =>  $this->ingestion->source->id]);
        Terminal::reguard();

        return $terminal;
    }

    protected function getOrCreateTac(string $value, int $terminalId): Tac
    {
        $tac = Tac::where('value', $value)->first();

        if (!$tac) {
            Tac::unguard();
            $tac = Tac::create(['value' => $value, 'terminal_id' => $terminalId, 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' =>  $this->ingestion->source->id ]);
            Log::channel('ingestion')->info('Tac created: ', ['ID' => $tac->id, 'Value' => $tac->value, 'ingestion_source_id' => $this->ingestion->source->id, 'ingestion_id' => $this->ingestion->id]);
            Tac::reguard();
        }

        return $tac;
    }

    protected function convertCsv($file)
    {
        return $file;
    }

    protected function additionalIngestionLogic(Terminal $terminal, Vendor $vendor, DbtAttribute $dbtAttribute, AttributeValue $attributeValue): bool
    {
        // TODO: Implement additionalIngestionLogic() method.
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