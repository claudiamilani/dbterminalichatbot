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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory, Searchable, Sortable, LegacyImportable;
    protected $guarded = ['id', 'created_by_id', 'updated_by_id', 'document_type_id'];
    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'ILIKE', "%$search%")->orWhere('id', (int)$search);
        });
    }
    public function advancedSearchFilter($query, $search): Builder
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'documentType':
                    if ($value !== '-') {
                        $query->whereHas('documentType', function($query) use ($value) {
                            $query->where('id', (int)$value);
                        });
                    }
                    break;
                case 'fileMimeType':
                    if ($value !== '-') {
                        $query->where('file_mime_type', $value);
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

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    protected static function legacyTable(): string
    {
        return 'document';
    }

    protected static function legacyPrimaryKey(): string
    {
        return 'id_document';
    }

    protected static function legacyImportFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        return $query->join('document_type',function($join){
            $join->on('document_type.id_document_type','=','document.id_document_type')->whereNull('document_type.deleted')->whereNull('document.deleted');
        });

    }

    public static function generateUuidFilename($filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $uuid = Str::uuid()->toString();

        return $uuid . '.' . $extension;
    }

    protected static function createFromLegacy(object $row): void
    {
        $filename = self::generateUuidFilename(urldecode($row->url));
        if (Storage::disk('remote')->exists(urldecode($row->url))) {
            $file = Storage::disk('remote')->get(urldecode($row->url));
            Storage::disk('documents')->put($filename, $file);
            if (Storage::disk('documents')->exists($filename)) {
                $mimeType = Storage::disk('documents')->mimeType($filename);
            } else {
                throw new \Exception('File not copied locally ' . $filename);
            }
        } else {
            throw new \Exception('File missing on remote ' . urldecode($row->url));
        }

            $documentType = DocumentType::imported($row->{DocumentType::legacyPrimaryKey()})->firstOrFail();
            $row->id_document_type = $documentType->id;

            //TODO Should validate the incoming array $row.
            Validator::make((array)$row, [
                'url' => 'required|string|max:255',
                'id_document_type' => 'required|integer'
            ])->validate();

            Document::unguard();
            Document::create(['file_path' => $filename, 'title' => pathinfo(basename(urldecode($row->url)), PATHINFO_FILENAME), 'file_mime_type' => $mimeType, 'document_type_id' => $row->id_document_type, 'legacy_id' => $row->{self::legacyPrimaryKey()}]);
            Document::reguard();

    }

    protected function updateFromLegacy(object $row): void
    {
        $filename = $this->file_path ?? self::generateUuidFilename(urldecode($row->url));

        if (Storage::disk('remote')->exists(urldecode($row->url))) {
            if (!Storage::disk('documents')->exists($filename)) {
                $file = Storage::disk('remote')->get(urldecode($row->url));
                Storage::disk('documents')->put($filename, $file);
            }

            if (Storage::disk('documents')->exists($filename)) {
                $mimeType = Storage::disk('documents')->mimeType($filename);
            } else {
                throw new \Exception('File not copied locally ' . $filename);
            }
        } else {
            throw new \Exception('File missing on remote ' . urldecode($row->url));
        }

        $documentType = DocumentType::imported($row->id_document_type)->firstOrFail();
        $row->id_document_type = $documentType->id;

        //TODO Should validate the incoming array $row.
        Validator::make((array)$row, [
            'url' => 'required|string|max:255',
            'id_document_type' => 'required|integer'
        ])->validate();

        Document::unguard();
        $this->update(['file_path' => $filename, 'title' => pathinfo(basename(urldecode($row->url)), PATHINFO_FILENAME), 'file_mime_type' => $mimeType, 'document_type_id' => $row->id_document_type]);
        Document::reguard();
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
