<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\DBT\Traits\LegacyImportable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Terminal extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id', 'vendor_id'];

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function($query)use($search){
            $query->where('name', 'ILIKE', "%$search%")->orWhere('id', (int)$search);
        });
    }

    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'vendor':
                    if ($value != '-') {
                        $query = $query->whereHas('vendor', function ($query) use ($value) {
                            $query->where('id', (int)$value);
                        });
                    }
                    break;
                case 'ingestion_source':
                    if ($value != '-') {
                        if ($value == IngestionSource::SRC_ADMIN) {
                            $query = $query->where('ingestion_source_id', (int)$value)->orWhereNull('ingestion_source_id');
                        } else {
                            $query = $query->where(function ($query) use ($value) {
                                $query->where('ingestion_source_id', (int)$value);
                            });
                        }

                    }
                    break;
                case 'ota_vendor':
                    if ($value !== '-') {
                        if ($value == 'null') {
                            $query->whereNull('ota_vendor')->whereNull('ota_model');
                        } else {
                            $query->whereNotNull('ota_vendor')->whereNotNull('ota_model');
                        }
                    }
                    break;
                case 'certified':
                    if ($value !== '-') {
                        $query->where('certified', $value);
                    }
                    break;
                case 'published':
                    if ($value !== '-') {
                        $query->where('published', $value);
                    }
                    break;

                case 'dbt_attribute_id':
                    if (isset($search['attribute_value']) && isset($search['attribute_condition'])) {
                        $dbt_attribute_values = AttributeValue::select(['id', 'value'])->where('dbt_attribute_id', $value)->cursor();

                        $search_value = $search['attribute_value'];
                        $filtered_ids = collect();

                        foreach ($dbt_attribute_values as $attribute_value) {
                            switch ($search['attribute_condition']) {
                                case 'more':
                                    if (is_numeric($search_value)) {
                                        $found = $attribute_value->value > $search_value;
                                    } else {
                                        $found = $attribute_value->value == $search_value;
                                    }
                                    break;
                                case 'less':
                                    if (is_numeric($search_value)) {
                                        $found = $attribute_value->value < $search_value;
                                    } else {
                                        $found = $attribute_value->value == $search_value;
                                    }
                                    break;
                                case 'equals':
                                    if (is_array(json_decode($attribute_value->value))) {
                                        $found = in_array($search_value, json_decode($attribute_value->value));
                                    } else {
                                        $found = $attribute_value->value == $search_value;
                                    }
                                    break;
                                case 'like':
                                    $found = str_contains(strtolower($attribute_value->value), strtolower((string)$search_value));
                                    break;
                                default:
                                    $found = $attribute_value->value == $search_value;
                                    break;
                            }

                            if ($found) {
                                $filtered_ids->push($attribute_value->id);
                            }
                        }

                        $query->whereHas('attributeValues', function ($query) use ($filtered_ids) {
                            $query->whereIn('attribute_values.id', $filtered_ids);
                        });
                    }

                    break;
            }
        }
        return $query;
    }


    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function ingestion(): BelongsTo
    {
        return $this->belongsTo(Ingestion::class);
    }

    public function ingestionSource(): BelongsTo
    {
        return $this->belongsTo(IngestionSource::class);
    }

    public function tacs()
    {
        return $this->hasMany(Tac::class);
    }

    protected static function legacyTable(): string
    {
        return 'terminal';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_terminal';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->join('vendor', function ($join) {
            $join->on('terminal.id_vendor', '=', 'vendor.id_vendor')->whereNull('vendor.deleted')->whereNull('terminal.deleted')->whereNotNull('terminal.name');
        });

    }

    protected static function createFromLegacy(object $row): void
    {
        $vendor = Vendor::imported($row->{Vendor::legacyPrimaryKey()})->firstOrFail();
        $row->id_vendor = $vendor->id;
        $rtmp_record = DB::connection(self::getLegacyConnectionName())->table('terminal_rtmp')->where('id_terminal', $row->{Terminal::legacyPrimaryKey()})->first();

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'name' => 'required|string|max:255',
            'id_vendor' => 'required',
        ])->validate();

        Terminal::unguard();
        Terminal::create(['name' => $row->name, 'published' => $row->is_public, 'certified' => $row->certified, 'vendor_id' => $row->id_vendor, 'ota_vendor' => optional($rtmp_record)->ota_vendor, 'ota_model' => optional($rtmp_record)->ota_model, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        Terminal::reguard();
    }

    public function updateFromLegacy(object $row): void
    {
        $vendor = Vendor::imported($row->id_vendor)->firstOrFail();
        $row->id_vendor = $vendor->id;
        $rtmp_record = DB::connection(self::getLegacyConnectionName())->table('terminal_rtmp')->where('id_terminal', $row->{Terminal::legacyPrimaryKey()})->first();

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'name' => 'required|string|max:255',
            'id_vendor' => 'required',
        ])->validate();
        Terminal::unguard();
        $this->update(['name' => $row->name, 'published' => $row->is_public, 'certified' => $row->certified, 'vendor_id' => $row->id_vendor, 'ota_vendor' => optional($rtmp_record)->ota_vendor, 'ota_model' => optional($rtmp_record)->ota_model]);
        Terminal::reguard();
    }

    public function getCreatedAtInfoAttribute(): string
    {
        $result = $this->created_at ? $this->created_at->format('d/m/Y H:i') : '';
        if ($this->created_by_id && $result) {
            $result = $result . ' ' . trans('common.from') . ' ' . $this->createdBy->fullName;
        }
        return $result;
    }

    public function getUpdatedAtInfoAttribute(): string
    {
        $result = $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : '';
        if ($this->updated_by_id && $result) {
            $result = $result . ' ' . trans('common.from') . ' ' . $this->updatedBy->fullName;
        }
        return $result;
    }

    public function pictures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TerminalPicture::class);
    }

    public function configs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TerminalConfig::class);
    }

    public function ota(): BelongsTo
    {
        return $this->belongsTo(Ota::class);
    }
}
