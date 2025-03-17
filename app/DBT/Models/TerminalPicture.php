<?php

namespace App\DBT\Models;

use App\Auth\User;
use App\DBT\Traits\LegacyImportable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TerminalPicture extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;

    protected $guarded = ['id', 'created_by_id', 'updated_by_id'];
    protected $fillable = ['terminal_id', 'file_path', 'display_order'];

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
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

    public function getFileNameAttribute(): string
    {
        $file_path = $this->file_path;
        return basename($file_path);
    }

    protected static function legacyTable(): string
    {
        return 'attribute_value';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_attribute_value';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->where('id_attribute', 147)->whereNotNull('value');
    }

    public static function generateUuidFilename($filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $uuid = Str::uuid()->toString();

        return $uuid . '.' . $extension;
    }

    protected static function createFromLegacy(object $row): void
    {
        $filename = self::generateUuidFilename($row->value);

        if (Storage::disk('remote')->exists($row->value)) {
            $file = Storage::disk('remote')->get($row->value);
            Storage::disk('terminal-pictures')->put($filename, $file);
        } else {
            throw new Exception('File missing on remote ' . $row->value);
        }

        $terminal = Terminal::imported($row->{Terminal::legacyPrimaryKey()})->firstOrFail();
        $row->id_terminal = $terminal->id;

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'value' => 'required|string|max:255',
            'id_terminal' => 'required|integer'
        ])->validate();

        TerminalPicture::unguard();
        TerminalPicture::create(['file_path' => $filename, 'title' => pathinfo(basename($row->value), PATHINFO_FILENAME), 'terminal_id' => $row->id_terminal, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
        TerminalPicture::reguard();

    }

    protected function updateFromLegacy(object $row): void
    {
        $filename = $this->file_path ?? self::generateUuidFilename($row->value);

        if (Storage::disk('remote')->exists($row->value)) {
            if (!Storage::disk('terminal-pictures')->exists($filename)) {
                $file = Storage::disk('remote')->get($row->value);
                Storage::disk('terminal-pictures')->put($filename, $file);
            }
        } else {
            throw new Exception('File missing on remote ' . $row->value);
        }

        $terminal = Terminal::imported($row->id_terminal)->firstOrFail();
        $row->id_terminal = $terminal->id;

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'value' => 'required|string|max:255',
            'id_terminal' => 'required|integer'
        ])->validate();

        TerminalPicture::unguard();
        $this->update(['file_path' => $filename, 'title' => pathinfo(basename($row->value), PATHINFO_FILENAME), 'terminal_id' => $row->id_terminal]);
        TerminalPicture::reguard();
    }

}
