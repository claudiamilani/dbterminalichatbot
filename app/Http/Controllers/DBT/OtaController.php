<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Ota;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OtaController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/otas';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', Ota::class);

            $otas = Ota::search(['search', 'type', 'sub_type', 'ext_0', 'ext_number'])->sortable(['id' => 'desc'])->paginate();

            $type = collect(Ota::distinct('type')->pluck('type', 'type'))->withFilterLabel(trans('DBT/otas.attributes.type'));
            $sub_type = collect(Ota::distinct('sub_type')->get()->pluck('sub_type', 'sub_type'))->withFilterLabel(trans('DBT/otas.attributes.sub_type'));
            $ext_0 = collect(Ota::distinct('ext_0')->get()->pluck('ext_0', 'ext_0'))->withFilterLabel(trans('DBT/otas.attributes.ext_0'));
            $ext_number = collect(Ota::distinct('ext_number')->get()->pluck('ext_number', 'ext_number'))->withFilterLabel('DBT/otas.attributes.ext_number');
            return view('dbt.otas.index', compact('otas', 'type', 'sub_type', 'ext_0', 'ext_number'));
        } catch(AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create', Ota::class);

            return view('dbt.otas.create');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Ota::class);

            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'sub_type' => 'required|string|max:255',
                'ext_0' => 'required|string|max:255',
                'ext_number' => 'required|string|max:255'
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $ota = new Ota();
            $ota->fill($request->all());

            $ota->createdBy()->associate(Auth::id());
            $ota->updatedBy()->associate(Auth::id());

            $ota->save();

            return redirect()->route('admin::dbt.otas.index')->withAlerts([
                ['message' => trans('DBT/otas.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/otas.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function show(int $id)
    {
        try {
            $ota = Ota::findOrFail($id);
            $this->authorize('view', $ota);

            return view('dbt.otas.show', compact('ota'));
        }  catch(AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $ota = Ota::findOrFail($id);
            $this->authorize('update', $ota);

            return view('dbt.otas.edit', compact('ota'));
        }  catch(AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            $ota = Ota::findOrFail($id);
            $this->authorize('update', $ota);

            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'sub_type' => 'required|string|max:255',
                'ext_0' => 'required|string|max:255',
                'ext_number' => 'required|string|max:255'
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $ota->update($request->all());

            return redirect()->route('admin::dbt.otas.index')->withAlerts([
                ['message' => trans('DBT/otas.edit.success'), 'type' => 'success', 'tmp' => '']
            ]);
        }  catch(AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/otas.edit.error'), 'type' => 'error', 'tmp' => '']
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $ota = Ota::findOrFail($id);
            $this->authorize('delete', $ota);

            return view('dbt.otas.delete', compact('ota'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.forbidden_message')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy(int $id)
    {
        try {
            $ota = Ota::findOrFail($id);
            $this->authorize('delete' , $ota);

            $ota->delete();
        }  catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/otas.delete.error'), 'type' => 'error']
            ]);
        }

        return redirect()->back()->withAlerts([
            ['message' => trans('DBT/otas.delete.success'), 'type' => 'success']
        ]);
    }
}
