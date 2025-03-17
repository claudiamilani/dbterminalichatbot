<?php

namespace App\DBT\Imports;

use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\AttributeValue;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\DBT\Models\Vendor;
use Illuminate\Support\Facades\Validator;

class IngestionWindTre extends IngestionBase
{
    public function __construct($ingestion)
    {
        parent::__construct($ingestion);
        $this->headerMapping = array_merge(['TAC' => 'tac'],AttributeHeaderMapping::where('ingestion_source_id',IngestionSource::SRC_ADMIN)->with('dbtAttribute')->get()->pluck('dbtAttribute.name','header_name')->toArray());
        $this->excludeFromAttributes = [
            'tac',
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
//        $attribute = DbtAttribute::create([]);
//
//        return $attribute;
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

    protected function createTac(string $value, int $terminalId): Tac
    {
        Tac::unguard();
        $tac = Tac::create(['value' => $value, 'terminal_id' => $terminalId, 'ingestion_id' => $this->ingestion->id, 'ingestion_source_id' =>  $this->ingestion->source->id ]);
        Tac::reguard();

        return $tac;
    }

    protected function convertCsv($file)
    {
        $attrs = array();
        $attr_row = array();
        $tacs = array();
        $rows = explode("\n", file_get_contents($file));
        foreach ($rows as $row) {
            list($key, $val) = array_pad(explode(";", $row), 2, '');

            if (trim(strtolower($key)) == 'tac') {
                $tacs = explode("|", $val);
            } else {
                $attrs[$key] = $val;
            }
        }
        $attr_row[] = 'tac;' . implode(';', array_keys($attrs));
        $newfile = storage_path('app' . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_FILENAME) . '_tmp.csv');
        foreach ($tacs as $tac) {
            $attr_row[] = $tac . ";" . implode(';', array_values($attrs));
        }
        file_put_contents($newfile, implode("\n", $attr_row));

        return $newfile;
    }

    protected function additionalIngestionLogic(Terminal $terminal, Vendor $vendor, DbtAttribute $dbtAttribute, AttributeValue $attributeValue): bool
    {
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
}