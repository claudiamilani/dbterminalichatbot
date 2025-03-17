<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Document;
use App\DBT\Models\DocumentType;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    use TranslatedValidation;

    public function getTranslationFile(): string
    {
        return 'DBT/documents';
    }

    public function index()
    {
        try {
            $this->authorize('list', Document::class);
            $documents = Document::search(['search', 'documentType', 'fileMimeType'])->sortable(['id' => 'asc'])->paginate();
            if (request('documentType') && $documentType = DocumentType::find(request('documentType'))) {
                $documentType = [$documentType->id => $documentType->name];
            } else {
                $documentType = collect();
            }
            $fileMimeType = Document::pluck('file_mime_type', 'file_mime_type')->withFilterLabel(trans('DBT/documents.attributes.file_mime_type'));

            return view('dbt.documents.index', compact('documents', 'documentType', 'fileMimeType'));

        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function show($id)
    {
        try {
            $document = Document::findOrFail($id);
            $this->authorize('view', $document);
            return view('dbt.documents.show', compact('document'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function create()
    {
        $this->authorize('create', Document::class);

        try {
            return view('dbt.documents.create');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', Document::class);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'file_path' => 'required|file|mimes:pdf,txt,doc,docx,html',
            'document_type_id' => 'required|integer|exists:document_types,id'
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $document = new Document();

            $file = $request->file('file_path');
            $filename = Document::generateUuidFilename($file->getClientOriginalName());
            if (!$storagePath = $file->storeAs('/', $filename,'documents')) {
                Log::channel('admin_gui')->error('Error uploading file.');
                return redirect()->back()->with(['alerts' => [
                    ['message' => trans('DBT/documents.edit.error'), 'type' => 'error']
                ]])->withInput();
            }
            $mimeType = $file->getMimeType();
            $document->file_path = $storagePath;
            $document->file_mime_type = $mimeType;
            $document->title = $request->title ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $document->documentType()->associate($request->document_type_id);
            $document->createdBy()->associate(Auth::user());
            $document->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/documents.create.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.documents.show', $document->id)->with(['alerts' => [
            ['message' => trans('DBT/documents.create.success'), 'type' => 'success']
        ]]);
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $this->authorize('update', $document);

        try {
            $document_type_id = [$document->documentType->id => $document->documentType->name];

            return view('dbt.documents.edit', compact('document', 'document_type_id'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        try {
            $this->authorize('update', $document);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'file_path' => 'nullable|file|mimes:pdf,txt,doc,docx,html',
            'document_type_id' => 'required|integer|exists:document_types,id'
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');
                $filename = Document::generateUuidFilename($file->getClientOriginalName());
                if (!$storagePath = $file->storeAs('/', $filename,'documents')) {
                    Log::channel('admin_gui')->error('Error uploading file.');
                    return redirect()->back()->with(['alerts' => [
                        ['message' => trans('DBT/documents.edit.error'), 'type' => 'error']
                    ]])->withInput();
                }
                if ($document->file_path && $document->file_path != $storagePath) {
                    try {
                        Storage::disk('documents')->delete($document->file_path);
                    } catch (Exception $e) {
                        Log::channel('admin_gui')->debug('Previous file not found: ' . $document->file_path . '.' . $e->getMessage());
                    }
                }
                $mimeType = $file->getMimeType();
                $document->file_path = $storagePath;
                $document->file_mime_type = $mimeType;
            }
            $document->title = $request->title ?? ($request->hasFile('file_path') ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) : $document->title);
            $document->updatedBy()->associate(Auth::user());
            $document->documentType()->associate($request->document_type_id);
            $document->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/documents.edit.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.documents.show', $document->id)->with(['alerts' => [
            ['message' => trans('DBT/documents.edit.success'), 'type' => 'success']
        ]]);
    }

    public function delete($id)
    {
        try {
            $document = Document::findOrFail($id);
            $this->authorize('delete', $document);
            return view('dbt.documents.delete', compact('document'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);
            $this->authorize('delete', $document);
            if (Storage::disk('documents')->exists($document->file_path)) {
                Storage::disk('documents')->delete($document->file_path);
            }
            $document->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/documents.delete.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.documents.index')->with(['alerts' => [
            ['message' => trans('DBT/documents.delete.success'), 'type' => 'success']
        ]]);
    }
}
