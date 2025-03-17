<?php

namespace App\Http\Controllers\DBT;

use App\Auth\User;
use App\DBT\TransposeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransposeRequestController extends Controller
{
    public function index()
    {
        try {

            $this->authorize('list', TransposeRequest::class);

            $tr_requests = TransposeRequest::search(['search', 'requested_by', 'status'])->sortable(['status' => 'desc','updated_at' => 'asc'])->paginate();

            $requested_by = User::where('id', request('requested_by'))->get()->pluck('fullname','id');

            $status = collect([TransposeRequest::STATUS_REQUESTED => trans('DBT/transpose_requests.status.0'), TransposeRequest::STATUS_QUEUED => trans('DBT/transpose_requests.status.1'), TransposeRequest::STATUS_PROCESSING => trans('DBT/transpose_requests.status.2'), TransposeRequest::STATUS_ERROR => trans('DBT/transpose_requests.status.3'), TransposeRequest::STATUS_PROCESSED => trans('DBT/transpose_requests.status.4')])->withFilterLabel('DBT/transpose_requests.attributes.status');

            return view('dbt.transpose_requests.index', compact('tr_requests', 'requested_by', 'status'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function create()
    {
        try {
            $this->authorize('create', TransposeRequest::class);

            return view('dbt.transpose_requests.create');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', TransposeRequest::class);
            $tr_request = new TransposeRequest($request->all());
            $tr_request->requestedBy()->associate(Auth::user());
            $tr_request->save();
            return redirect()->route('admin::dbt.transpose_requests.index')->with(['alerts' => [
                ['message' => trans('DBT/transpose_requests.create.success'), 'type' => 'success']
            ]]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (\Exception $e){
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/transpose_requests.create.error'), 'type' => 'error']
            ]])->withInput();
        }
    }

    public function show($id)
    {
        $tr_request = TransposeRequest::findOrFail($id);
        try {
            $this->authorize('view', $tr_request);
            return view('dbt.transpose_requests.show', compact('tr_request'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function delete($id)
    {
        try {
            $tr_request = TransposeRequest::findOrFail($id);
            $this->authorize('delete', $tr_request);
            return view('dbt.transpose_requests.delete',compact('tr_request'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function destroy($id)
    {
        try {
            $tr_request = TransposeRequest::findOrFail($id);
            $this->authorize('delete', $tr_request);
            if($tr_request->file_path){
                Storage::delete($tr_request->file_path);
            }
            $tr_request->delete();
            return redirect()->route('admin::dbt.transpose_requests.index')->with(['alerts' => [
                ['message' => trans('DBT/transpose_requests.delete.success'), 'type' => 'success']
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
                ['message' => trans('DBT/transpose_requests.delete.error'), 'type' => 'error']
            ]]);
        }
    }

    public function download($id)
    {
        try {
            $tr_request = TransposeRequest::findOrFail($id);
            $this->authorize('download',$tr_request);
            if(!$tr_request->file_path){
                throw new \Exception('File path empty');
            }
            return response()->download(Storage::path($tr_request->file_path));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]]);
        } catch (\Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->route('admin::dbt.transpose_requests.index')->with(['alerts' => [
                ['message' => trans('common.file_404'), 'type' => 'error']]]);
        }
    }
}
