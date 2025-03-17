<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\PermissionType;
use App\Auth\Role;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Auth;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionsController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'permissions';
    }

    /**
     * The view for listing permissions
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
         $this->authorize('managePermissions', Role::class);
            $roles = Role::all();
            $roles_select = $roles->pluck('name', 'id');
            $types = PermissionType::with('permissions.roles')->get()->sortBy('translatedName');
            $types_select = $types;

            foreach($types as $item){
                $item->name = $item->translatedName;
            }
            $types_select = $types_select->pluck('name','id');
            return view('auth.permissions.index', compact('roles_select', 'types_select', 'roles', 'types'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Edits an existing permission and stores the passed values
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        if (collect($request->roles)->contains(1) && !Auth::user()->isAdmin()) {
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('permissions.edit.not_admin'), 'type' => 'error']
            ]]);
        }

        DB::beginTransaction();
        $roles = Role::all();

        foreach ($roles as $role) {
            $permissions = $request->get($role->id);
            try {
                $this->authorize('managePermissions','App\Auth\Role');
                $role->permissions()->sync($permissions);

            } catch (AuthorizationException $e) {
                Log::channel('admin_gui')->info($e->getMessage());
                abort(403, trans('common.http_err.403'));
            } catch (Exception $e) {
                Log::channel('admin_gui')->info($e->getMessage());
                Log::channel('admin_gui')->info($e->getTraceAsString());
                DB::rollBack();
                return redirect()->back()->with(['alerts' =>
                    ['message' => trans('permissions.edit.error'), 'type' => 'error']
                ])->withInput();
            }
        }
        DB::commit();
        return redirect()->route('admin::permissions.index')->with(['alerts' => [
            ['message' => trans('permissions.edit.success'), 'type' => 'success']]]);
    }
}
