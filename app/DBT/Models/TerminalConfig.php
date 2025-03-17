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

class TerminalConfig extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id', 'ota_id', 'terminal_id', 'created_by_id', 'updated_by_id'];

    protected $casts = [
        'published' => 'boolean'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    public function ota(): BelongsTo
    {
        return $this->belongsTo(Ota::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('value', 'ILIKE', "%$search%")->orWhere('id', (int) $search);
        });
    }

    public function getCreatedAtInfoAttribute(): string
    {
        $result = $this->created_at ? $this->created_at->format('d/m/Y H:i') : '';

        if ($this->created_by_id && $result) {
            $result = $result.' '.trans('common.from').' '.$this->createdBy->fullName;
        }
        return $result;
    }

    public function getUpdatedAtInfoAttribute(): string
    {
        $result = $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : '';

        if ($this->updated_by_id && $result) {
            $result = $result.' '.trans('common.from').' '.$this->updatedBy->fullName;
        }
        return $result;
    }

    protected static function legacyTable(): string
    {
        return 'detail';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_detail';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->whereNull('deleted');
    }

    protected static function createFromLegacy(object $row): void
    {
        $terminal = Terminal::imported($row->{Terminal::legacyPrimaryKey()})->firstOrFail();
        $row->id_terminal = $terminal->id;

        $ota = Ota::imported($row->{Ota::legacyPrimaryKey()})->first();

        if ($ota) {
            $row->id_ota = $ota->id;
        }

        $document = Document::imported($row->{Document::legacyPrimaryKey()})->first();

        if ($document) {
            $row->id_document = $document->id;
        } else {
            $row->id_document = null;
        }

        //TODO Should validate the incoming array $row.
        Validator::make((array) $row, [
            'id_terminal' => 'required',
            'id_ota' => 'required'
        ])->validate();

        TerminalConfig::unguard();
        TerminalConfig::create([
            'terminal_id' => $row->id_terminal, 'ota_id' => $row->id_ota, 'document_id' => $row->id_document,
            'legacy_id' => $row->{self::legacyPrimaryKey()}
        ]);
        TerminalConfig::reguard();
    }

    protected function updateFromLegacy(object $row): void
    {
        $terminal = Terminal::imported($row->{Terminal::legacyPrimaryKey()})->firstOrFail();
        $row->id_terminal = $terminal->id;

        $ota = Ota::imported($row->{Ota::legacyPrimaryKey()})->first();

        if ($ota) {
            $row->id_ota = $ota->id;
        }

        $document = Document::imported($row->{Document::legacyPrimaryKey()})->first();

        if ($document) {
            $row->id_document = $document->id;
        } else {
            $row->id_document = null;
        }

        //TODO Should validate the incoming array $row.
        Validator::make((array) $row, [
            'id_terminal' => 'required',
            'id_ota' => 'required'
        ])->validate();
        TerminalConfig::unguard();
        $this->update([
            'terminal_id' => $row->id_terminal, 'ota_id' => $row->id_ota, 'document_id' => $row->id_document
        ]);
        TerminalConfig::reguard();
    }
}
