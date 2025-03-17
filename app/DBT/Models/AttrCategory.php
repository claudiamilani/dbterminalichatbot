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
use Illuminate\Support\Facades\Validator;

class AttrCategory extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;


    protected $guarded = ['id', 'updated_by_id', 'created_by_id'];

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'ILIKE', "%$search%")
                ->orWhere('id', 'ILIKE', (int)$search);
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function setDisplayOrderAttribute($value): void
    {
        $this->attributes['display_order'] = $value ?? 0;
    }

    public function dbtAttributes()
    {
        return $this->hasMany(DbtAttribute::class);
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

    protected static function legacyTable(): string
    {
        return 'category';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_category';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->whereNull('deleted')->whereNotIn('id_category',[1,100,1000]);

    }

    protected static function createFromLegacy(object $row): void
    {
        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'name' => ['required', 'string'],
        ])->validate();

        AttrCategory::create(['name' => $row->name, 'description' => $row->description, 'published' => true, 'display_order' => $row->display_order, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
    }

    protected function updateFromLegacy(object $row): void
    {
        // TODO: Implement updateFromLegacy() method.
        Validator::make((array)$row, [
            'name' => ['required', 'string'],
        ])->validate();
        //TODO Should validate the incoming array $row.
        $this->update(['name' => $row->name, 'description' => $row->description, 'published' => true]);
    }
}
