<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\DBT\Traits\LegacyImportable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Vendor extends Model
{
    use Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id', 'created_by_id', 'ingestion_id', 'ingestion_source_id', 'updated_by_id'];
    protected $casts = [
        'published' => 'boolean'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function ingestion(): BelongsTo
    {
        return $this->belongsTo(Ingestion::class);
    }

    public function ingestionSource(): BelongsTo
    {
        return $this->belongsTo(IngestionSource::class);
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
    }

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
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
                case 'published':
                    if ($value !== '-') {
                        $query->where('published', $value);
                    }
                    break;
            }
        }
        return $query;
    }

    public static function legacyTable(): string
    {
        return 'vendor';
    }

    public static function legacyPrimaryKey(): string
    {
        return 'id_vendor';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->whereNull('vendor.deleted')->whereNotNull('vendor.name')->whereExists(function($query){ $query->select(DB::raw(1))->from('terminal')->whereColumn('terminal.id_vendor','vendor.id_vendor');
        });
    }

    protected static function createFromLegacy(object $row): void
    {
        $ingestion_source_map = [IngestionSource::SRC_ADMIN, IngestionSource::SRC_MOBILETHINK, IngestionSource::SRC_GSMA];
        if (!in_array($row->created_by, $ingestion_source_map)) {
            $row->created_by = null;
        }
        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'name' => ['required', 'string'],
        ])->validate();
        Vendor::unguard();
        Vendor::create(['name' => $row->name, 'description' => $row->description, 'published' => $row->is_public, 'ingestion_source_id' => $row->created_by, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        Vendor::reguard();
    }

    protected function updateFromLegacy(object $row): void
    {
        $ingestion_source_map = [IngestionSource::SRC_ADMIN, IngestionSource::SRC_MOBILETHINK, IngestionSource::SRC_GSMA];
        if (!in_array($row->created_by, $ingestion_source_map)) {
            $row->created_by = null;
        }
        Validator::make((array)$row, [
            'name' => ['required', 'string'],
        ])->validate();
        //TODO Should validate the incoming array $row.
        Vendor::unguard();
        $this->update(['name' => $row->name, 'description' => $row->description, 'published' => $row->is_public, 'ingestion_source_id' => $row->created_by]);
        Vendor::reguard();
    }

    public function getCreatedAtInfoAttribute(): string
    {
        $result = $this->created_at ? $this->created_at->format('d/m/Y H:i') : '';

        if($this->created_by_id && $result){
            $result = $result . ' '. trans('common.from') .' ' . $this->createdBy->fullName;
        }
        return $result;
    }

    public function getUpdatedAtInfoAttribute(): string
    {
        $result = $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : '';

        if($this->updated_by_id && $result){
            $result = $result . ' '. trans('common.from') .' ' . $this->updatedBy->fullName;
        }
        return $result;
    }
}
