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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;

class Ota extends Model
{
    use Searchable, Sortable;

    protected $guarded = ['id', 'created_by_id', 'updated_by_id'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function configs(): HasMany
    {
        return $this->hasMany(TerminalConfig::class);
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
                case 'type':
                    if ($value !== '-') {
                        $query->where('type', $value);
                    }
                    break;
                case 'sub_type':
                    if ($value !== '-') {
                        $query->where('sub_type', $value);
                    }
                    break;
                case 'ext_0':
                    if ($value !== '-') {
                        $query->where('ext_0', $value);
                    }
                    break;
                case 'ext_number':
                    if ($value !== '-') {
                        $query->where('ext_number', $value);
                    }
                    break;
            }
        }
        return $query;
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
    use HasFactory, LegacyImportable;

    protected static function legacyTable(): string
    {
        return 'ota';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_ota';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->whereNull('deleted');

    }

    protected static function createFromLegacy(object $row): void
    {
        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'subtype' => 'required|string|max:255',
            'ext_0' => 'required|string|max:255',
            'ext_number' => 'required|string|max:255',
        ])->validate();

        Ota::unguard();
        Ota::create(['name' => $row->label, 'type' => $row->type, 'sub_type' => $row->subtype, 'ext_0' => $row->ext_0, 'ext_number' => $row->ext_number, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        Ota::reguard();
    }

    protected function updateFromLegacy(object $row): void
    {
        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'subtype' => 'required|string|max:255',
            'ext_0' => 'required|string|max:255',
            'ext_number' => 'required|string|max:255',
        ])->validate();
        Ota::unguard();
        $this->update(['name' => $row->label, 'type' => $row->type, 'sub_type' => $row->subtype, 'ext_0' => $row->ext_0, 'ext_number' => $row->ext_number,]);
        Ota::reguard();
    }
}
