<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttributeHeaderMapping extends Model
{
    use HasFactory, Searchable, Sortable;

    protected $guarded = ['id'];

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('header_name', 'ILIKE', "%$search%")->orWhereHas('dbtAttribute', function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%$search%");
            })->orWhere('id', (int)$search);
        });
    }

    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
            }
        }
        return $query;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dbtAttribute(): BelongsTo
    {
        return $this->belongsTo(DbtAttribute::class);
    }

    public function ingestionSource(): BelongsTo
    {
        return $this->belongsTo(IngestionSource::class);
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

    public static function importHeaderMappings($id): void
    {
        try {
            Log::channel('admin_gui')->info('Creating Attribute Header Mappings');
            $header_mappings = config('dbt.attribute_header_mappings.'.$id);
            $local_attributes = DbtAttribute::whereIn('name', array_values($header_mappings))->get();
            foreach ($header_mappings as $key => $name) {
                $local_attribute = $local_attributes->where('name', $name)->first();
                if (!$local_attribute){
                    continue;
                }
                AttributeHeaderMapping::create([
                    'header_name' => $key,
                    'dbt_attribute_id' => $local_attribute->id,
                    'ingestion_source_id' => $id,
                    'created_by_id' => Auth::user()->id,
                    'updated_by_id' => Auth::user()->id,
                ]);
            }
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
        }
    }
}
