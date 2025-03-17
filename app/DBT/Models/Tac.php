<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\DBT\Traits\LegacyImportable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;

class Tac extends Model
{
    use Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id', 'created_by_id', 'terminal_id', 'ingestion_id', 'ingestion_source_id', 'updated_by_id'];

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

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('value', 'ILIKE', "%$search%");
        });
    }

    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                case 'search_tac':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'terminal':
                    if ($value !== '-') {
                        $query->where('terminal_id', (int)$value);
                    }
                    break;
            }
        }
        return $query;
    }

    protected static function legacyTable(): string
    {
        return 'tac';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_tac';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->join('terminal',function ($join){
            $join->on('tac.id_terminal','=','terminal.id_terminal')->whereNull('terminal.deleted')->whereNull('tac.deleted')->whereNotNull('terminal.name')->whereNull('terminal.deleted');
        })->join('vendor',function ($join){
            $join->on('terminal.id_vendor','=','vendor.id_vendor')->whereNull('vendor.deleted');
        });
    }

    protected static function createFromLegacy(object $row): void
    {
        $terminal = Terminal::imported($row->{Terminal::legacyPrimaryKey()})->firstOrFail();
        $row->id_terminal = $terminal->id;

        $ingestion_source_map = [IngestionSource::SRC_ADMIN, IngestionSource::SRC_MOBILETHINK, IngestionSource::SRC_GSMA];

        if (!in_array($row->created_by, $ingestion_source_map)) {
            $row->created_by = null;
        }

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'value' => 'required|string|max:255',
            'id_terminal' => 'required|integer'
        ])->validate();

        Tac::unguard();
        Tac::create(['value' => $row->value, 'terminal_id' => $row->id_terminal, 'ingestion_source_id' => $row->created_by, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        Tac::reguard();
    }

    protected function updateFromLegacy(object $row): void
    {
        $terminal = Terminal::imported($row->id_terminal)->firstOrFail();
        $row->id_terminal = $terminal->id;

        $ingestion_source_map = [IngestionSource::SRC_ADMIN, IngestionSource::SRC_MOBILETHINK, IngestionSource::SRC_GSMA];

        if (!in_array($row->created_by, $ingestion_source_map)) {
            $row->created_by = null;
        }

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'value' => 'required|string|max:255',
            'id_terminal' => 'required|integer'
        ])->validate();

        Tac::unguard();
        $this->update(['value' => $row->value, 'terminal_id' => $row->id_terminal, 'ingestion_source_id' => $row->created_by]);
        Tac::reguard();
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
