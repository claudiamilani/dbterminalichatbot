<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingestion extends Model
{
    use HasFactory, Searchable, Sortable;

    const int STATUS_DRAFT = 0;
    const int STATUS_REQUESTED = 1;
    const int STATUS_QUEUED = 2;
    const int STATUS_PROCESSING = 3;
    const int STATUS_ERROR = 4;
    const int STATUS_COMPLETED = 5;


    protected $guarded = ['id'];

    protected $casts = ['started_at'=>'datetime', 'ended_at'=>'datetime','options'=>'array','notify_mails'=>'array'];

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('id', 'ILIKE', (int)$search);
        });
    }

    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'source':
                    if ($value !== '-') {
                        $query->where('ingestion_source_id', (int)$value);
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

    public function source(): BelongsTo
    {
        return $this->belongsTo(IngestionSource::class, 'ingestion_source_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getStatusLabelAttribute(): string
    {
        return trans('DBT/ingestions.statuses.'.$this->attributes['status']);
    }

    public function scopeDraft($query)
    {
        $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeQueued($query)
    {
        $query->where('status', self::STATUS_REQUESTED);
    }

    public function scopeError($query)
    {
        $query->where('status', self::STATUS_ERROR);
    }

    public function scopeRequested($query)
    {
        $query->where('status', self::STATUS_REQUESTED);
    }

    public function scopeActive($query)
    {
        $query->whereIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
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
