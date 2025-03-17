<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\AuthType;
use App\Http\Controllers\Controller;
use App\Traits\TranslatedValidation;
use Closure;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthTypesController extends Controller
{

    use TranslatedValidation;

    /**
     * Return the translation file name for Auth Type model
     *
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'auth_types';
    }

    /**
     * @return View|void
     */
    public function index()
    {
        try {
            $this->authorize('list', AuthType::class);
            $auth_types = AuthType::search()->sortable()->paginate();
            return view('auth.auth_types.index', compact('auth_types'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * @param $id
     * @return View|void
     */
    public function edit($id)
    {
        try {
            $auth_type = AuthType::findOrFail($id);
            $this->authorize('update', $auth_type);
            return view('auth.auth_types.edit', compact('auth_type'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|never
     */
    public function update(Request $request, $id)
    {
        try {

            $auth_type = AuthType::findOrFail($id);
            $this->authorize('update', AuthType::class);

            $validator = Validator::make($request->all(), $attributes = [
                'default' => [
                    function ( $attribute,  $value, Closure $fail) use ($id) {
                        if (AuthType::find($id)->default && $value == 0) {
                            $fail(trans('auth_types.edit.default_needed'));
                        }
                    },
                ],
                'enabled' => [
                    function ( $attribute,  $value, Closure $fail) use ($id) {
                        if (!AuthType::where('enabled', 1)->where('id', '!=', $id)->count() && $value == 0) {
                            $fail(trans('auth_types.edit.need_one_enabled'));
                        }
                    },

                    function ( $attribute,  $value, Closure $fail) use ($request) {
                        if ($request->default == 1 && $value == 0) {
                            $fail(trans('auth_types.edit.enabled_needed'));
                        }
                    },
                ],
                'auto_register' => [
                    function ( $attribute,  $value, Closure $fail) use ($id) {
                        if ($id == AuthType::LOCAL) {
                            $fail(trans('auth_types.edit.no_auto_register'));
                        }
                    }

                ]


            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            DB::beginTransaction();
            $auth_type->fill($request->except('default','name'));
            if ($request->default == 1) {
                $auth_type->saveAsDefault();
            }else{
                $auth_type->save();
            }
            DB::commit();
        } catch (AuthorizationException) {
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->withAlerts([
                ['message' => trans('auth_types.edit.error'), 'type' => 'error']
            ])->withInput();
        }
        return redirect()->route('admin::auth_types.edit', $auth_type->id)->with(['alerts' => [
            ['message' => trans('auth_types.edit.success'), 'type' => 'success']
        ]]);
    }
}
