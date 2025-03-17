<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\AuthType;
use App\Auth\ExternalRole;
use App\Auth\Role;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExternalRolesController extends Controller
{
    use TranslatedValidation;

    public function getTranslationFile(): string
    {
        return 'external_roles';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $this->authorize('list', ExternalRole::class);
            $external_roles = ExternalRole::search()->sortable()->paginate();
            return view('auth.external_roles.index', compact('external_roles'));
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        try {
            $this->authorize('create', ExternalRole::class);

            $roles = Role::pluck('name','id');
            $external_roles = ExternalRole::pluck('external_role_id','id');
            $auth_types = AuthType::where('id','!=',AuthType::LOCAL)->orderBy('id','desc')->pluck('name','id');
            return view('auth.external_roles.create', compact('roles', 'external_roles','auth_types'));
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            return redirect()->back()->withAlerts([
                ['message' => trans('external_roles.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', ExternalRole::class);
            $validator = Validator::make($request->all(), $attributes = [
                'external_role_id' => 'required',

            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $external_role = new ExternalRole();
            $external_role->fill(request()->except('roles'));
            $external_role->save();
            $external_role->roles()->sync(request('roles'));


            return redirect()->route('admin::external_roles.index')->withAlerts([
                ['message' => trans('external_roles.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            return redirect()->back()->withAlerts([
                ['message' => trans('external_roles.create.error'), 'type' => 'error']
            ])->withInput();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $this->authorize('create', ExternalRole::class);

            $external_role = ExternalRole::find($id);
            $auth_types = AuthType::where('id','!=',AuthType::LOCAL)->pluck('name','id');
            $roles = Role::pluck('name','id');
            $selected_roles = $external_role->roles()->pluck('id');

            return view('auth.external_roles.edit', compact('external_role','auth_types','roles','selected_roles'));
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $external_role = ExternalRole::findOrFail($id);
        $validator = Validator::make($request->all(), $attributes = [
            //
        ], [], $this->getTranslatedAttributes($attributes));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $this->authorize('update', $external_role);
            $external_role->fill($request->except('roles'));
            $external_role->roles()->sync(request('roles'));
            $external_role->save();
            return redirect()->route('admin::external_roles.index')->withAlerts([
                ['message' => trans('external_roles.edit.success'), 'type' => 'success']]);

        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withAlerts([
                ['message' => trans('external_roles.edit.error'), 'type' => 'error']
            ])->withInput();
        }
    }

    /**
     * Show the confirmation dialog before deleting the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $external_role = ExternalRole::findOrFail($id);
        try {
            $this->authorize('delete', $external_role);
            return view('auth.external_roles.delete', compact('external_role'));
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $external_role = ExternalRole::findOrFail($id);
        try {
            $this->authorize('delete', $external_role);
            $external_role->delete();
            return redirect()->route('admin::external_roles.index')->withAlerts([
                ['message' => trans('external_roles.delete.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString())       ;
            return redirect()->back()->withAlerts([
                ['message' => trans('external_roles.delete.error'), 'type' => 'error']
            ]);
        }
    }


    public function select2()
    {
        //TODO: check MySQL compatibility with true as disabled
        return ExternalRole::select('id', 'external_role_id as text','id as existing', DB::raw("true as disabled"))->where('auth_type_id',request('auth_type_id'))->get();

    }

}
