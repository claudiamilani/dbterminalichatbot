<?php

namespace App\DBT;

use App\Auth\User;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TransposeRequest extends Model
{
    use HasFactory, Searchable, Sortable;

    protected $casts = ['started_at' => 'datetime','ended_at' => 'datetime'];
    protected $guarded = ['id','created_by_id'];

    const string QUEUE = 'transpose';

    const STATUS_REQUESTED = 0;
    const STATUS_QUEUED = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_ERROR = 3;
    const STATUS_PROCESSED = 4;

    public function searchFilter($query, $search): Builder
    {
        return $query->where('id', (int)$search);
    }

    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'requested_by':
                    if ($value !== '-') {
                        $query->where('requested_by_id', $value);
                    }
                    break;
                case 'status':
                    if ($value !== '-') {
                        $query->where('status', $value);
                    }
                    break;
            }
        }
        return $query;
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        $query->whereIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    public function scopeNotActive($query){
        $query->whereNotIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    public function scopeRequested($query)
    {
        $query->where('status',self::STATUS_REQUESTED);
    }

    public function scopeQueued($query)
    {
        $query->where('status',self::STATUS_QUEUED);
    }

    public function scopeProcessing($query)
    {
        $query->where('status',self::STATUS_PROCESSING);
    }

    public function scopeError($query)
    {
        $query->where('status',self::STATUS_ERROR);
    }

    public function scopeProcessed($query)
    {
        $query->where('status', self::STATUS_PROCESSED);
    }
}
