<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyImport extends Model
{
    use HasFactory, Sortable, Searchable;

    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime'];

    const string QUEUE = 'legacy_import';

    const STATUS_REQUESTED = 0;
    const STATUS_QUEUED = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_ERROR = 3;
    const STATUS_PROCESSED = 4;

    const IMPORTABLE_MODELS = [
        'VENDORS' => \App\DBT\Models\Vendor::class,
        'ATTR_CATEGORIES' => \App\DBT\Models\AttrCategory::class,
        'ATTRIBUTES' => \App\DBT\Models\DbtAttribute::class,
        'DOCUMENT_TYPES' => \App\DBT\Models\DocumentType::class,
        'DOCUMENTS' => \App\DBT\Models\Document::class,
        'OTAS' => \App\DBT\Models\Ota::class,
        'TERMINALS' => \App\DBT\Models\Terminal::class,
        'TACS' => \App\DBT\Models\Tac::class,
        'PICTURES' => \App\DBT\Models\TerminalPicture::class,
        'TERMINAL_CONFIGS' => \App\DBT\Models\TerminalConfig::class,
        'ATTRIBUTE_VALUES' => \App\DBT\Models\AttributeValue::class,
        'TERMINALS GSMA' => \App\DBT\Models\GsmaTerminal::class,
    ];

    protected $guarded = ['id'];

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
                case 'update_existing':
                    if ($value !== '-') {
                        $query->where('update_existing', $value);
                    }
                    break;
                case 'type':
                    if ($value !== '-') {
                        $query->where('type', $value);
                    }
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

    public function items()
    {
        return $this->hasMany(LegacyImportItem::class);
    }

    public function createdItems()
    {
        return $this->items()->where('result', 'CREATED');
    }

    public function updatedItems()
    {
        return $this->items()->where('result', 'UPDATED');
    }

    public function errorItems()
    {
        return $this->items()->where('result', 'FAILED');
    }

    public function skippedItems()
    {
        return $this->items()->where('result', 'SKIPPED');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        $query->whereIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    public function scopeNotActive($query)
    {
        $query->whereNotIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    public function scopeRequested($query)
    {
        $query->where('status', self::STATUS_REQUESTED);
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

    public static function getTranslatedList()
    {
        return collect(self::IMPORTABLE_MODELS)->mapWithKeys(function ($item, $key) {
            return [$key => trans('DBT/legacy_imports.types.' . $key)];
        })->toArray();
    }
}
