<?php

namespace App\DBT\Models;

use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyImportItem extends Model
{
    use HasFactory, Searchable, Sortable;

    const STATUS_QUEUED = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_ERROR = 3;
    const STATUS_PROCESSED = 4;

    protected $guarded = ['id', 'legacy_import_id'];

    public function legacyImport()
    {
        return $this->belongsTo(LegacyImport::class);
    }

    public function scopeActive($query)
    {
        $query->whereIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    public function scopeNotActive($query)
    {
        $query->whereNotIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    public function scopeQueued($query)
    {
        $query->where('status', self::STATUS_QUEUED);
    }

    public function scopeProcessing($query)
    {
        $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeError($query)
    {
        $query->where('status', self::STATUS_ERROR);
    }

    public function scopeProcessed($query)
    {
        $query->where('status', self::STATUS_PROCESSED);
    }

    public function scopeCompleted($query)
    {
        $query->whereIn('status', [self::STATUS_PROCESSED, self::STATUS_ERROR]);
    }

    public function scopePending($query)
    {
        $query->whereNotIn('status', [self::STATUS_PROCESSED, self::STATUS_ERROR]);
    }

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('message', 'ILIKE', '%' . $search . '%')->orWhere('legacy_id', (int)$search);
        });
    }

    /**
     * Filter query results by searchFilter method or by provided $search filters
     * @param $query
     * @param $search
     * @return Builder|mixed
     */
    public function advancedSearchFilter($query, $search): mixed
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'status':
                    if ($value !== '-') {
                        $query->where('status', $value);
                    }
                    break;
                case 'result':
                    if ($value !== '-') {
                        $query->where('result', $value);
                    }
                    break;
            }
        }
        return $query;
    }
}
