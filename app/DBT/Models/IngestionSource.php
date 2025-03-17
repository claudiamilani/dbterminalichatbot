<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngestionSource extends Model
{
    use HasFactory, Searchable, Sortable;

    const int SRC_ADMIN = 1;
    const int SRC_MOBILETHINK = 2;
    const int SRC_GSMA = 3;
    const array DEFAULT_OPTIONS = ['CREATES_VENDOR' => false, 'CREATES_ATTRIBUTE' => false, 'CREATES_TERMINAL' => false];

    protected $guarded = ['id'];

    protected $casts = ['default_options'=>'array'];

    /**
     * Filters query results for name, surname and email
     * @param $query
     * @param $search
     * @return Builder
     */
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
                case 'enabled':
                    if ($value !== '-') {
                        $query->where('enabled', $value);
                    }
                    break;
            }
        }
        return $query;
    }

    public function ingestions()
    {
        return $this->hasMany(Ingestion::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
