<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\Permission;
use App\Auth\PermissionType;
use App\Auth\Role;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'roles';
    }

    /**
     * The view for listing roles
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', Role::class);
            $roles = Role::search()->sortable()->withCount('users')->paginate();
            return view('auth.roles.index', compact('roles'));
        } catch (AuthorizationException $e) {
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to create a new role
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create', Role::class);
            $types = PermissionType::with('permissions.roles')->get()->sortBy('translatedName');
            $types_select = $types;

            foreach ($types as $item) {
                $item->name = $item->translatedName;
            }
            $types_select = $types_select->pluck('name', 'id');
            return view('auth.roles.create', compact('types', 'types_select'));
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Creates a new role and stores the passed values
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Role::class);
            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required',
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            $role = new Role();
            $role->fill($request->all())->save();
            $role->permissions()->sync($request->get('permissions'));
            DB::commit();
            return redirect()->route('admin::roles.index')->with(['alerts' =>[
                ['message' => trans('roles.create.success'), 'type' => 'success']]]);
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' =>
                ['message' => trans('roles.create.error'), 'type' => 'error']
            ])->withInput();
        }

    }

    /**
     * The view to edit an existing role
     *
     * @param  $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit($id)
    {
        try {
            $role = Role::with('permissions')->withCount('users')->findOrFail($id);
            $this->authorize('update', $role);
            $types = PermissionType::with('permissions.roles')->get()->sortBy('translatedName');
            $types_select = $types;

            foreach($types as $item){
                $item->name = $item->translatedName;
            }
            $types_select = $types_select->pluck('name','id');
            return view('auth.roles.edit', compact('role', 'types', 'types_select'));
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Edits an existing role and stores the passed values
     *
     * @param Request $request
     * @param  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $role = Role::with('users')->findOrFail($id);
        $permissions = $request->get('permissions');
        // If the role is already in use we cannot change its name or description.
        $roleInUse = $role->users->count() > 0;

        try {
            $this->authorize('update', $role);
            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required',
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            if (!$roleInUse) {
                $role->fill($request->all())->save();
            }
            $role->permissions()->sync($permissions);
            DB::commit();
            if (!$roleInUse) {
                return redirect()->route('admin::roles.index')->with(['alerts' =>
                    ['message' => trans('roles.edit.success'), 'type' => 'success']]);
            }
            return redirect()->route('admin::roles.index')->with(['alerts' =>[
                ['message' => trans('roles.edit.success_but_in_use'), 'type' => 'warning']]]);
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with(['alerts' =>[
                ['message' => trans('roles.edit.error'), 'type' => 'error']]
            ])->withInput();
        }
    }

    /**
     * The view to delete an existing role
     *
     * @param  $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function delete($id)
    {
        $role = Role::withCount('users')->findOrFail($id);
        try {
            $this->authorize('delete', $role);
            return view('auth.roles.delete', compact('role'));
        } catch (AuthorizationException $e) {
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Deletes the existing role
     *
     * @param  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        try {
            $this->authorize('delete', $role);
            $role->delete();
            return redirect()->route('admin::roles.index')->with(['alerts' =>[
                ['message' => trans('roles.delete.success'), 'type' => 'success']]]);
        } catch (AuthorizationException $e) {
            return abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with(['alerts' =>[
                ['message' => trans('roles.delete.error'), 'type' => 'error']]
            ]);
        }
    }


    public function select2()
    {
        return Role::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE', '%' . request('q') . '%')->get();

    }

    public function defaultPermissionsModal()
    {
        try {
            $this->authorize('managePermissions', Role::class);
            return view('auth.roles.default_permissions');
        } catch (AuthorizationException $e) {
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function defaultPermissions()
    {
        try {
            $this->authorize('managePermissions', Role::class);

            foreach(config('lft.defaults.permissions') as $k => $v){
                if(Str::contains($k,'*')){
                    $roles = Role::where('name',$k)->get();
                }else{
                    $roles = Role::where('name','ILIKE',str_replace('*','%',$k))->get();
                }

                if(!$roles->count()){
                    continue;
                }
                foreach($roles as $role){
                    if($v == ['*']){
                        $role->permissions()->sync(Permission::pluck('id'));
                        continue;
                    }
                    $permissions = Permission::whereIn('name',$v)->pluck('id');
                    $role->permissions()->sync($permissions);
                }
            }
            return redirect()->route('admin::roles.index')->withAlerts([
                ['message' => trans('roles.default_permissions.success'), 'type' => 'success']]);

        } catch (AuthorizationException $e) {
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        } catch (Exception $e){
            return redirect()->back()->withAlerts([
                ['message' => trans('roles.default_permissions.error'), 'type' => 'error']
            ]);
        }
    }
}
