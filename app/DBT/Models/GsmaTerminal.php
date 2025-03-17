<?php

namespace App\DBT\Models;

use App\DBT\Traits\LegacyImportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GsmaTerminal extends Model
{
    use HasFactory, LegacyImportable;

    protected $guarded = ['id'];

    protected static function legacyTable(): string
    {
        return 'terminal_gsma';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_terminal_gsma';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->whereNull('deleted')->whereNotNull('marketing_name');
    }

    /**
     * @throws \Exception
     */
    protected static function createFromLegacy(object $row): void
    {
        try {
            DB::beginTransaction();
            if (isset($row->id_terminal)) {
                // GSMA TERMINAL referencing a legacy terminal_id that wasn't imported should be skipped, throwing exception
                $terminal = Terminal::where('legacy_id', $row->id_terminal)->firstOrFail();
            }

            if (!isset($terminal)) {
                Validator::make((array)$row, [
                    'manufacturer' => 'required|string|max:255',
                    'marketing_name' => 'required|string|max:255',
                ])->validate();

                if (!$vendor = Vendor::where('name', $row->manufacturer)->first()) {
                    Vendor::unguard();
                    $vendor = Vendor::create(['name' => $row->manufacturer, 'description' => $row->manufacturer, 'published' => 0, 'ingestion_source_id' => IngestionSource::SRC_GSMA]);
                    Vendor::reguard();
                }

                Terminal::unguard();
                $terminal = Terminal::create(['name' => $row->marketing_name, 'published' => 0, 'certified' => 0, 'vendor_id' => $vendor->id, 'ingestion_source_id' => IngestionSource::SRC_GSMA]);
                Terminal::reguard();
            }

            if (!Tac::where('value', $row->tac)->first()) {
                Tac::unguard();
                Tac::create(['value' => $row->tac, 'terminal_id' => $terminal->id, 'ingestion_source_id' => IngestionSource::SRC_GSMA]);
                Tac::reguard();
            }

            GsmaTerminal::unguard();
            GsmaTerminal::create(['legacy_terminal_id' => optional($row)->id_terminal, 'legacy_id' => $row->{self::legacyPrimaryKey()}, 'terminal_id' => $terminal->id]);
            GsmaTerminal::reguard();

            // Committing main tasks before updating or creating attribute values to avoid deadlocks
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        $local_attributes = DbtAttribute::whereIn('name', array_values(self::getMapping()))->get();
        foreach (self::getMapping() as $key => $name) {
            $local_attribute = $local_attributes->where('name', $name)->first();
            if (!$local_attribute || !isset($row->{$key})) {
                continue;
            }
            $value = self::convertValue($row->{$key});
            if ($attributeValue = AttributeValue::forTerminal($terminal->id)->sourceGSMA()->where('dbt_attribute_id',$local_attribute->id)->first()) {
                DbtAttribute::updateAttributeValue($attributeValue, $value);
            } else {
                DbtAttribute::createAttributeValue($local_attribute->id, $terminal->id, IngestionSource::SRC_GSMA, $value);
            }
        }
    }

    public function updateFromLegacy(object $row): void
    {
        try {
            DB::beginTransaction();
            if (isset($row->id_terminal)) {
                // GSMA TERMINAL referencing a legacy terminal_id that wasn't imported should be skipped, throwing exception
                $terminal = Terminal::where('legacy_id', $row->id_terminal)->firstOrFail();
            }


            if (!isset($terminal)) {
                Validator::make((array)$row, [
                    'manufacturer' => 'required|string|max:255',
                    'marketing_name' => 'required|string|max:255',
                ])->validate();

                if (!$vendor = Vendor::where('name', $row->manufacturer)->first()) {
                    Vendor::unguard();
                    $vendor = Vendor::create(['name' => $row->manufacturer, 'description' => $row->manufacturer, 'published' => 0, 'ingestion_source_id' => IngestionSource::SRC_GSMA]);
                    Vendor::reguard();
                }

                Terminal::unguard();
                $terminal = Terminal::create(['name' => $row->marketing_name, 'published' => 0, 'certified' => 0, 'vendor_id' => $vendor->id, 'ingestion_source_id' => IngestionSource::SRC_GSMA]);
                Terminal::reguard();
            }

            if (!Tac::where('value', $row->tac)->first()) {
                Tac::unguard();
                Tac::create(['value' => $row->tac, 'terminal_id' => $terminal->id, 'ingestion_source_id' => IngestionSource::SRC_GSMA]);
                Tac::reguard();
            }

            GsmaTerminal::unguard();
            $this->update(['legacy_terminal_id' => optional($row)->id_terminal, 'terminal_id' => $terminal->id]);
            GsmaTerminal::reguard();

            // Committing main tasks before updating or creating attribute values to avoid deadlocks
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        $local_attributes = DbtAttribute::whereIn('name', array_values(self::getMapping()))->get();
        foreach (self::getMapping() as $key => $name) {
            $local_attribute = $local_attributes->where('name', $name)->first();
            if (!$local_attribute || !isset($row->{$key})) {
                continue;
            }
            $value = self::convertValue($row->{$key});
            if ($attributeValue = AttributeValue::forTerminal($terminal->id)->sourceGSMA()->where('dbt_attribute_id',$local_attribute->id)->first()) {
                DbtAttribute::updateAttributeValue($attributeValue, $value);
            } else {
                DbtAttribute::createAttributeValue($local_attribute->id, $terminal->id, IngestionSource::SRC_GSMA, $value);
            }
        }


    }

    protected static function getMapping(): array
    {
        return [
            'manufacturer' => 'device_make',
            'marketing_name' => 'device_model',
            'bands' => 'network_frequency_bands',
            'bands_five_g' => '5g_frequency_bands',
            'allocation_date' => 'allocation_date',
            'country_code' => 'country_code',
            'fixed_code' => 'fixed_code',
            'manufacturer_code' => 'manufacturer_code',
            'radio_interface' => 'network_protocols',
            'model_name' => 'model_name',
            'operating_system' => 'os_version',
            'device_type' => 'device_type',
            'removable_uicc' => 'removable_uicc',
            'removable_euicc' => 'removable_euicc',
            'nonremovable_uicc' => 'nonremovable_uicc',
            'nonremovable_euicc' => 'nonremovable_euicc',
        ];
    }

    protected static function convertValue(string $value): string|array
    {
        return str_contains($value, ',') ? implode('|', explode(',', $value)) : $value;
    }
}
