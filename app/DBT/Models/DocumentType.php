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

class DocumentType extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id', 'created_by_id', 'updated_by_id', 'channel_id'];

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
                case 'channel':
                    if ($value !== '-') {
                        $query->whereHas('channel', function($query) use ($value) {
                            $query->where('id', (int)$value);
                        });
                    }
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

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    protected static function legacyTable(): string
    {
        return 'document_type';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_document_type';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->whereNull('deleted');

    }

    protected static function createFromLegacy(object $row): void
    {
        $channels_map = [Channel::CHANNEL_CONSUMER, Channel::CHANNEL_CORPORATE];

        throw_unless(in_array($row->id_channel, $channels_map), \Exception::class, 'Invalid channel: ' . $row->id_channel);
        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'type' => 'required|string|max:255',
            'id_channel' => 'required|integer'
        ])->validate();

        DocumentType::unguard();
        DocumentType::create(['name' => $row->type, 'channel_id' => $row->id_channel, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        DocumentType::reguard();
    }

    protected function updateFromLegacy(object $row): void
    {
        $channels_map = [Channel::CHANNEL_CONSUMER, Channel::CHANNEL_CORPORATE];

        throw_unless(in_array($row->id_channel, $channels_map), \Exception::class, 'Invalid channel: ' . $row->id_channel);

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'type' => 'required|string|max:255',
            'id_channel' => 'required',
        ])->validate();
        DocumentType::unguard();
        $this->update(['name' => $row->type, 'channel_id' => $row->id_channel]);
        DocumentType::reguard();
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
