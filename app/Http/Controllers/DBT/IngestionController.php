<?php

namespace App\Http\Controllers\DBT;

use App\Auth\User;
use App\DBT\Models\Ingestion;
use App\DBT\Models\IngestionSource;
use App\Http\Controllers\Controller;
use App\Traits\ControllerPathfinder;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class IngestionController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    public function getTranslationFile(): string
    {
        return 'DBT/ingestions';
    }

    /**
     * The view for listing attribute categories
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', arguments: Ingestion::class);
            $ingestions = Ingestion::search(['search', 'source', 'status'])->sortable(['created_at'=>'desc'])->paginate();

            $source = IngestionSource::pluck('name','id')->withFilterLabel('DBT/ingestions.attributes.ingestion_source_id');

            $status = collect([Ingestion::STATUS_DRAFT => trans('DBT/ingestions.statuses.0'), Ingestion::STATUS_REQUESTED => trans('DBT/ingestions.statuses.1'), Ingestion::STATUS_QUEUED => trans('DBT/ingestions.statuses.2'), Ingestion::STATUS_PROCESSING => trans('DBT/ingestions.statuses.3'), Ingestion::STATUS_ERROR => trans('DBT/ingestions.statuses.4'), Ingestion::STATUS_COMPLETED => trans('DBT/ingestions.statuses.5')])->withFilterLabel('DBT/ingestions.attributes.status');

            return view('dbt.ingestions.index', compact('ingestions', 'source', 'status'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to create a new ingestion
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create', Ingestion::class);
            $sources = IngestionSource::enabled()->orderBy('id')->pluck('name', 'id')->prepend('-','');
            $status = collect([Ingestion::STATUS_DRAFT => trans('DBT/ingestions.statuses.0'), Ingestion::STATUS_REQUESTED => trans('DBT/ingestions.statuses.1')]);
            $default_options = IngestionSource::DEFAULT_OPTIONS;
            return view('dbt.ingestions.create', compact('sources', 'default_options', 'status'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     *  Creates a new ingestion and stores the passed values
     *
     * @param Request $request
     * @return RedirectResponse|never
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Ingestion::class);
            $validator = Validator::make($request->all(), $attributes = [
                'ingestion_source_id' => ['required', Rule::in(IngestionSource::enabled()->pluck('id'))],
                'file_path' => 'required|file',
                'notify_mails.*' => 'sometimes|email'

            ], ['notify_mails.*'=> trans('DBT/ingestions.validation.notify_mails')], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $ingestion = new Ingestion();
            if (!empty ($file = $request->file_path)) {
                if (!$storagePath = $file->storeAs('ingestions' . DIRECTORY_SEPARATOR . request('ingestion_source_id') . DIRECTORY_SEPARATOR . Carbon::now()->format('Ymd'), Carbon::now()->format('YmdHi') . '_' . str_random(5) . '.' . $file->getClientOriginalExtension())) {
                    Log::channel('admin_gui')->error('Error uploading file for Ingestion: ' . $ingestion->id);
                    return redirect()->back()->with(['alerts' => [
                        ['message' => trans('DBT/ingestions.create.error'), 'type' => 'error']
                    ]])->withInput();
                }
                $ingestion->file_path = $storagePath;
            }
            $ingestion->createdBy()->associate(Auth::id());
            $ingestion->updatedBy()->associate(Auth::id());
            $ingestion->status = (($request->status == Ingestion::STATUS_REQUESTED) ? Ingestion::STATUS_REQUESTED : Ingestion::STATUS_DRAFT);
            $options['options']['CREATES_VENDOR'] = (bool)$request->CREATES_VENDOR;
            $options['options']['CREATES_ATTRIBUTE'] = (bool)$request->CREATES_ATTRIBUTE;
            $options['options']['CREATES_TERMINAL'] = (bool)$request->CREATES_TERMINAL;
            $ingestion->fill(array_merge($request->except('file_path','status'),$options));
            $ingestion->save();
            return $this->returnPath('admin::dbt.ingestions.index')->withAlerts([
                ['message' => trans('DBT/ingestions.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/ingestions.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }

    /**
     * The view to edit an existing ingestion
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $ingestion = Ingestion::findOrFail($id);
            $this->authorize('update', $ingestion);
            $notify_mails = array_combine((array)$ingestion->notify_mails,(array)$ingestion->notify_mails);
            if(in_array($ingestion->status, [Ingestion::STATUS_DRAFT,Ingestion::STATUS_REQUESTED])){
                $status = [Ingestion::STATUS_DRAFT => trans('DBT/ingestions.statuses.0'), Ingestion::STATUS_REQUESTED => trans('DBT/ingestions.statuses.1')];
            }else{
                $status = [$ingestion->status => trans('DBT/ingestions.statuses.'.$ingestion->status)];
            }
            return view('dbt.ingestions.edit', compact('ingestion','notify_mails','status'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     * Edits an existing ingestion and update the passed values
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|never
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update(Request $request, $id)
    {
        $ingestion = Ingestion::findOrFail($id);
        try {
            $this->authorize('update', $ingestion);
            $validator = Validator::make($request->all(), $attributes = [
                'file_path' => 'file|mimes:csv',
                'notify_mails.*' => 'sometimes|email'

            ], ['notify_mails.*'=> trans('DBT/ingestions.validation.notify_mails')], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (!empty ($file = $request->file_path)) {
                //First we try to save the new file incoming, if saving fails we redirect back with error
                if (!$storagePath = $file->storeAs('ingestions' . DIRECTORY_SEPARATOR . $ingestion->ingestion_source_id . DIRECTORY_SEPARATOR . Carbon::now()->format('Ymd'), Carbon::now()->format('YmdHi') . '_' . str_random(5) . '.' . $file->getClientOriginalExtension())) {
                    Log::channel('admin_gui')->error('Error uploading file for Ingestion: ' . $ingestion->id);
                    return redirect()->back()->with(['alerts' => [
                        ['message' => trans('DBT/ingestions.create.error'), 'type' => 'error']
                    ]])->withInput();
                }
                //We succesfully saved new file, we delete the old one accessing it via $ingestion->file_path,
                //(old path has not been updated so far)
                Storage::delete($ingestion->file_path);

                //If folder is empty after file delete we delete also directory (timestamp)
                if(empty(Storage::allFiles($lastSlashPosition = strrpos($ingestion->file_path, '/')))){
                    Storage::deleteDirectory(substr($ingestion->file_path, 0, $lastSlashPosition));
                }

                //We finally updated the ingestion file_path with the new one saved in $storagePath
                $ingestion->file_path = $storagePath;
            }
            $options['options']['CREATES_VENDOR'] = (bool)$request->CREATES_VENDOR;
            $options['options']['CREATES_ATTRIBUTE'] = (bool)$request->CREATES_ATTRIBUTE;
            $options['options']['CREATES_TERMINAL'] = (bool)$request->CREATES_TERMINAL;
            $ingestion->fill(array_merge($request->except('file_path','notify_mails'),$options));
            $ingestion->updatedBy()->associate(Auth::id());
            if($ingestion->status == Ingestion::STATUS_DRAFT){
                (($request->status == Ingestion::STATUS_REQUESTED) ? Ingestion::STATUS_REQUESTED : Ingestion::STATUS_DRAFT);
            }
            $ingestion->notify_mails = is_array($request->notify_mails) ? $request->notify_mails : [];
            $ingestion->save();
            return $this->returnPath('admin::dbt.ingestions.index')->withAlerts([
                ['message' => trans('DBT/ingestions.edit.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/ingestions.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }


    /**
     * The view to show an ingestion
     *
     * @param $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse|\Illuminate\View\View|void
     */
    public function show($id)
    {
        try {
            $ingestion = Ingestion::find($id);
            $this->authorize('view', $ingestion);
            return view('dbt.ingestions.show', compact('ingestion'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/ingestions.show.error'), 'type' => 'error']
            ]]);
        }
    }

    /**
     * The view to delete an existing ingestion
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    public function delete($id)
    {
        try {
            $ingestion = Ingestion::findOrFail($id);
            $this->authorize('delete', $ingestion);
            return view('dbt.ingestions.delete', compact('ingestion'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Deletes an existing ingestion
     *
     * @param $id
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroy($id)
    {
        try {
            $ingestion = Ingestion::findOrFail($id);
            $this->authorize('delete', $ingestion);
            $lastSlashPosition = strrpos($ingestion->file_path, '/');
            $folder_path = substr($ingestion->file_path, 0, $lastSlashPosition);
            Storage::delete($ingestion->file_path);
            if(empty(Storage::allFiles($folder_path))){
                Storage::deleteDirectory($folder_path);
            }
            $ingestion->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return back()->with(['alerts' => [
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]]);
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/ingestions.delete.error'), 'type' => 'error']
            ]]);
        }
        return $this->returnPath('admin::dbt.ingestions.index')->with(['alerts' => [
            ['message' => trans('DBT/ingestions.delete.success'), 'type' => 'success']
        ]]);
    }

    /**
     * @return JsonResponse
     *
     * Load default options configured for incoming IngestionSource
     */
    public function loadOptions()
    {
        try {
            $options = IngestionSource::find(request('id'))->default_options;
            return response()->json($options);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * @return array|Collection
     *
     * Search and list for users email checking "list" permission for auth user
     */
    public function listUserMails()
    {
        try{
            $this->authorize('list', User::class);
            return DB::table('users')
                ->select('email as id', 'email as text', 'id as existing',DB::raw("CONCAT(users.name, ' ', users.surname, ' - ', users.email) as text"))
                ->where('email', 'ILIKE', "%".request('q')."%")
                ->get();
        }catch(Exception) {
            return [];
        }
    }
}
