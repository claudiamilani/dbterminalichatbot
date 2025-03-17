<?php

namespace App\DBT\Models;

use App\DBT\Traits\LegacyImportable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id'];

    public function searchFilter($query, $search): Builder
    {
        return $query;
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(IngestionSource::class, 'ingestion_source_id');
    }

    public function dbtAttribute()
    {
        return $this->belongsTo(DbtAttribute::class);
    }

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function scopeSourceAdmin($query)
    {
        return $query->where('ingestion_source_id', IngestionSource::SRC_ADMIN);
    }

    public function scopeSourceMobileThink($query)
    {
        return $query->where('ingestion_source_id', IngestionSource::SRC_MOBILETHINK);
    }

    public function scopeSourceGSMA($query)
    {
        return $query->where('ingestion_source_id', IngestionSource::SRC_GSMA);
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        //Legacy attribute 147 is linked to the Terminal image, we don't need it anymore, we directly import
        //TerminalPicture and associate them
        return $query->whereNull('attribute_value.deleted')
            ->where('attribute_value.id_attribute', '!=', 147)
            ->join('attribute', function ($join) {
                $join->on('attribute.id_attribute', '=', 'attribute_value.id_attribute')->whereNull('attribute.deleted');
            })
            ->join('terminal', function ($join) {
                $join->on('terminal.id_terminal', '=', 'attribute_value.id_terminal')->whereNull('terminal.deleted');
            })
            ->join('category', function ($join) {
                $join->on('category.id_category', '=', 'attribute.id_category')->whereNull('category.deleted')->whereNotIn('category.id_category', [1, 100, 1000]);
            });
    }

    public function scopeForTerminal($query, $terminal_id)
    {
        return $query->where('terminal_id', $terminal_id);
    }

    protected static function legacyTable(): string
    {
        return 'attribute_value';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_attribute_value';
    }

    protected static function createFromLegacy(object $row): void
    {
        $terminal = Terminal::imported($row->{Terminal::legacyPrimaryKey()})->firstOrFail();
        $attribute = DbtAttribute::imported($row->{DbtAttribute::legacyPrimaryKey()})->firstOrFail();
        $row->id_attribute = $attribute->id;
        $row->id_terminal = $terminal->id;
        Validator::make((array)$row, [
            'id_attribute' => 'required',
            'id_terminal' => 'required',
            //'value' => ['required', new ValidateLegacyAttributeValue($attribute)],
        ])->validate();

        $attribute_value = DbtAttribute::createAttributeValue($attribute->id, $terminal->id, $row->id_ingestion, $row->value);
        $attribute_value->update(['legacy_id' => $row->{AttributeValue::legacyPrimaryKey()}]);
    }

    protected function updateFromLegacy(object $row): void
    {
        $terminal = Terminal::imported($row->{Terminal::legacyPrimaryKey()})->firstOrFail();
        $attribute = DbtAttribute::imported($row->{DbtAttribute::legacyPrimaryKey()})->firstOrFail();

        $row->id_attribute = $attribute->id;
        $row->id_terminal = $terminal->id;

        Validator::make((array)$row, [
            //'value' => 'required',
            'id_attribute' => 'required',
            'id_terminal' => 'required',
        ])->validate();
        DbtAttribute::updateAttributeValue($this, $row->value);
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

    public function getFormattedValue()
    {
        if(is_array(json_decode($this->value,true) )){
           return array_combine(json_decode($this->value, true),
                json_decode($this->value, true));
        }
        return $this->value;
    }

    public function getReadableValue()
    {
        if (!empty($this->value) && is_array(json_decode($this->value, true))) {
            return implode('|', json_decode($this->value, true));
        } else if ($this->dbtAttribute->type == DbtAttribute::TYPE_BOOLEAN && $this->value === '1') {
            return trans('common.yes');
        } else if ($this->dbtAttribute->type == DbtAttribute::TYPE_BOOLEAN && $this->value === '0') {
            return trans('common.no');
        } else if($this->dbtAttribute->type == DbtAttribute::TYPE_INT) {
            //return number_format((int)$this->value,0, ',','.');
            return $this->value;
        } else if($this->dbtAttribute->type == DbtAttribute::TYPE_DECIMAL) {
            //return number_format((float)$this->value,2, ',','.');
            return $this->value;
        }else {
            return $this->value;
        }
    }
}
