<?php

namespace App\DBT\Models;

use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransposeConfig extends Model
{
    use HasFactory, Searchable, Sortable;

    protected $guarded = ['id'];

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('label', 'ILIKE', "%$search%")->orWhereHas('dbtAttribute', function($query) use ($search) {
                $query->where('name', 'ILIKE', "%$search%");
            });
        });
    }

    public function dbtAttribute(): BelongsTo
    {
        return $this->belongsTo(DbtAttribute::class);
    }

}

