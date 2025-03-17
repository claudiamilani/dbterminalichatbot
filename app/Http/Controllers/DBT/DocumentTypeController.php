<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Channel;
use App\DBT\Models\DocumentType;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    use TranslatedValidation;

    public function getTranslationFile(): string
    {
        return 'DBT/document_types';
    }

    public function index()
    {
        try {
            $this->authorize('list', DocumentType::class);
            $document_types = DocumentType::search(['search', 'channel'])->sortable(['id' => 'asc'])->paginate();
            if (request('channel') && $channel = Channel::find(request('channel'))) {
                $channel = [$channel->id => $channel->name];
            } else {
                $channel = collect();
            }

            return view('dbt.document_types.index', compact('document_types', 'channel'));

        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function show($id)
    {
        try {
            $document_type = DocumentType::findOrFail($id);
            $this->authorize('view', $document_type);
            return view('dbt.document_types.show', compact('document_type'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function create()
    {
        $this->authorize('create', DocumentType::class);

        try {
            return view('dbt.document_types.create');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', DocumentType::class);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'name' => 'required|unique:document_types|max:255',
            'channel_id' => 'required|integer|exists:channels,id'
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $documentType = new DocumentType();
            $documentType->fill($request->all());
            $documentType->channel()->associate($request->channel_id);
            $documentType->createdBy()->associate(Auth::user());
            $documentType->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/document_types.create.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.document_types.show', $documentType->id)->with(['alerts' => [
            ['message' => trans('DBT/document_types.create.success'), 'type' => 'success']
        ]]);
    }

    public function edit($id)
    {
        $documentType = DocumentType::findOrFail($id);
        $this->authorize('update', $documentType);

        try {
            $channel_id = [$documentType->channel->id => $documentType->channel->name];

            return view('dbt.document_types.edit', compact('documentType', 'channel_id'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function update(Request $request, $id)
    {
        $documentType = DocumentType::findOrFail($id);
        try {
            $this->authorize('update', $documentType);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'name' => ['required', 'string', 'max:255', Rule::unique('document_types')->ignore($documentType->id)],
            'channel_id' => 'required|integer|exists:channels,id'
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $documentType->fill($request->all());
            $documentType->updatedBy()->associate(Auth::user());
            $documentType->channel()->associate($request->channel_id);
            $documentType->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/document_types.edit.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.document_types.show', $documentType->id)->with(['alerts' => [
            ['message' => trans('DBT/document_types.edit.success'), 'type' => 'success']
        ]]);
    }

    public function delete($id)
    {
        try {
            $documentType = DocumentType::findOrFail($id);
            $this->authorize('delete', $documentType);
            return view('dbt.document_types.delete', compact('documentType'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function destroy($id)
    {
        try {
            $documentType = DocumentType::findOrFail($id);
            $this->authorize('delete', $documentType);
            $documentType->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/document_types.delete.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.document_types.index')->with(['alerts' => [
            ['message' => trans('DBT/document_types.delete.success'), 'type' => 'success']
        ]]);
    }

    public function select2(Request $request)
    {
        return DB::table('document_types')->select('document_types.id', 'document_types.name as text', 'document_types.id as existing', DB::raw('channels.name as channel'))->join('channels', 'channels.id', '=', 'document_types.channel_id')->where(function ($query) use ($request) {
            $query->where('document_types.name', 'ILIKE', '%' . $request->q . '%');
        })->get();
    }
}
