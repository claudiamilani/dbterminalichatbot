<?php

namespace App\Http\Controllers\DBT;

use App\Auth\User;
use App\DBT\Models\LegacyImport;
use App\DBT\Models\LegacyImportItem;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LegacyImportController extends Controller
{
    public function index()
    {
        try {

            $this->authorize('list', LegacyImport::class);

            $imports = LegacyImport::search(['search', 'update_existing', 'requested_by', 'status','type'])->sortable(['status' => 'desc','updated_at' => 'asc'])->paginate();

            $update_existing = collect([0 => trans('common.no'), 1 => trans('common.yes')])->withFilterLabel('DBT/legacy_imports.attributes.update_existing');

            $requested_by = User::where('id', request('requested_by'))->get()->pluck('fullname','id');

            $types = collect(LegacyImport::getTranslatedList())->withFilterLabel('DBT/legacy_imports.attributes.type');

            $status = collect([LegacyImport::STATUS_REQUESTED => trans('DBT/legacy_imports.status.0'), LegacyImport::STATUS_QUEUED => trans('DBT/legacy_imports.status.1'), LegacyImport::STATUS_PROCESSING => trans('DBT/legacy_imports.status.2'), LegacyImport::STATUS_ERROR => trans('DBT/legacy_imports.status.3'), LegacyImport::STATUS_PROCESSED => trans('DBT/legacy_imports.status.4')])->withFilterLabel('DBT/legacy_imports.attributes.status');

            return view('dbt.legacy_imports.index', compact('imports', 'update_existing', 'requested_by', 'status','types'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function create()
    {
        try {
            $this->authorize('create', LegacyImport::class);

            $types = LegacyImport::getTranslatedList();
            return view('dbt.legacy_imports.create',compact('types'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', LegacyImport::class);
            DB::beginTransaction();
            foreach(array_keys(LegacyImport::IMPORTABLE_MODELS) as $model){
                if(in_array($model,$request->get('type'))){
                    $import = new LegacyImport($request->except('type'));
                    $import->requestedBy()->associate(Auth::user());
                    $import->type = $model;
                    $import->save();
                }
            }
            DB::commit();
            return redirect()->route('admin::dbt.legacy_imports.index')->with(['alerts' => [
                ['message' => trans('DBT/legacy_imports.create.success'), 'type' => 'success']
            ]]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (\Exception $e){
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/legacy_imports.create.error'), 'type' => 'error']
            ]])->withInput();
        }
    }

    public function show($id)
    {
        $import = LegacyImport::withCount(['createdItems','updatedItems','skippedItems','errorItems'])->findOrFail($id);
        $item_status = collect(trans('DBT/legacy_import_items.status'))->withFilterLabel(trans('DBT/legacy_import_items.attributes.status'));
        $item_results = collect(trans('DBT/legacy_import_items.result'))->withFilterLabel(trans('DBT/legacy_import_items.attributes.result'));
        try {
            $this->authorize('view', $import);
            $items = LegacyImportItem::where('legacy_import_id', $import->id)->search(['search','status','result'])->sortable()->paginate();
            return view('dbt.legacy_imports.show', compact('import','items','item_status','item_results'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function delete($id)
    {
        try {
            $import = LegacyImport::findOrFail($id);
            $this->authorize('delete', $import);
            return view('dbt.legacy_imports.delete',compact('import'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function destroy($id)
    {
        try {
            $import = LegacyImport::findOrFail($id);
            $this->authorize('delete', $import);
            $import->delete();
            return redirect()->route('admin::dbt.legacy_imports.index')->with(['alerts' => [
                ['message' => trans('DBT/legacy_imports.delete.success'), 'type' => 'success']
            ]]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]]);
        }catch (\Exception $e){
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/legacy_imports.delete.error'), 'type' => 'error']
            ]]);
        }
    }

    public function showItem($id)
    {
        $item = LegacyImportItem::findOrFail($id);
        $model = LegacyImport::IMPORTABLE_MODELS[$item->legacyImport->type];
        try {
            $legacy_item = (new $model)->getLegacy($item->legacy_id);
        } catch (\Exception $e) {
            $legacy_item = 'Unable to connect to Legacy database: '.$e->getMessage();
        }
        try {
            $local_item = $model::imported($item->legacy_id)->first();
        } catch (\Exception $e) {
            $local_item = 'Unable to retrieve local record: '.$e->getMessage();
        }

        return view('dbt.legacy_imports.items.show', compact('item','local_item','legacy_item'));
    }

    public function showLegacyItem($id)
    {
        $item = LegacyImportItem::findOrFail($id);
        $model = LegacyImport::IMPORTABLE_MODELS[$item->legacyImport->type];
        $legacy_item = (new $model)->getLegacy($item->legacy_id);
        return view('dbt.legacy_imports.items.show_legacy', compact('legacy_item'));
    }

    public function deleteItem($id)
    {
        $item = LegacyImportItem::findOrFail($id);
        return view('dbt.legacy_imports.items.delete', compact('item'));
    }

    public function destroyItem($id)
    {
        $item = LegacyImportItem::findOrFail($id);
        return redirect()->route('admin::dbt.legacy_imports.show',$item->legacy_import_id);
    }
}
