<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\AuthType;
use App\Auth\Exceptions\PasswordChangeException;
use App\Auth\PasswordHistory;
use App\Auth\Role;
use App\Auth\User;
use App\Http\Controllers\Controller;
use App\Rules\PasswordComplexity;
use App\Rules\PasswordHistories;
use App\Traits\TranslatedValidation;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'users';
    }

    /**
     * The view for listing users
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', User::class);
            $accounts = User::with('roles')->search()->sortable(['surname' => 'asc'])->paginate();
            return view('auth.users.index', compact('accounts'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to create a new user
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create', User::class);
            if (Auth::user()->isAdmin()) {
                $roles = Role::pluck('name', 'id');
            } else {
                $roles = Role::pluck('name', 'id')->except(1);
            }
            $auth_types = AuthType::enabled()->pluck('name', 'id');

            return view('auth.users.create', compact('roles', 'auth_types'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Creates a new user and stores the passed values
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', User::class);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
        $messages = [
            'password.same' => trans('validation.custom.password.same'),
        ];

        $driver = AuthType::findOrFail(request('auth_type_id'))->driverInstance;

        $validator = Validator::make($request->all(), $attributes = [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'user' => [
                'required',
                Rule::unique('users', 'user'),
                'regex:/^\S*$/'
            ],
            'password' => [
                'required_if:pwd_change_required,0',
                'same:password_check',
                new PasswordComplexity($driver)
            ],
        ], $messages, $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            DB::beginTransaction();
            $account = new User;
            $account->fill($request->all());
            $account->pwd_changed_at = Carbon::now();
            $account->save();

            $passwordHistory = PasswordHistory::create([
                'user_id' => $account->id,
                'password' => $account->password,
            ]);
            $passwordHistory->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('users.create.error'), 'type' => 'error']
            ]])->withInput();
        }
        try {
            $account->roles()->sync($request->get('roles'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('users.create.error'), 'type' => 'error']
            ]])->withInput();
        }
        DB::commit();
        return redirect()->route('admin::users.index')->with(['alerts' => [
            ['message' => trans('users.create.success'), 'type' => 'success']
        ]]);
    }

    /**
     * The view to edit an existing user
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $account = User::with('roles')->findOrFail($id);
            $this->authorize('update', $account);
            if (Auth::user()->isAdmin()) {
                $roles = Role::pluck('name', 'id');
            } else {
                $roles = Role::pluck('name', 'id')->except(1);
            }
            $driver = $account->authType->driverInstance;
            return view('auth.users.edit', compact('account', 'roles', 'driver'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Edits an existing user and stores the passed values
     * @param Request $request
     * @param $id
     * @return RedirectResponse|never
     */
    public function update(Request $request, $id)
    {
        $account = User::findOrFail($id);
        $driver = $account->authType->driverInstance;

        try {
            $this->authorize('update', $account);
        } catch (AuthorizationException $e) {
            return back()->with(['alerts' =>
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]);
        }

        $validator = Validator::make($attributes = $request->all(), [
            'password' =>
                [
                    'same:password_check',
                    'required_with:current_password',
                    'required_with:password_check',
                    //new PasswordComplexity($driver),
                    //new PasswordHistories($id),
                ],
            'current_password' => [
                'required_with:password',
                'required_with:password_check',
            ],
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'user' => [
                'required',
                Rule::unique('users', 'user')->ignore($id),
                'regex:/^\S*$/'
            ]
        ], [], $this->getTranslatedAttributes($attributes));

        $validator->sometimes('password', [new PasswordComplexity($driver),new PasswordHistories($id)], function ($input)use($request) {
            return $request->current_password && $request->password_check;
        });


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!empty(request('password'))) {
            try {
                $driver->changePassword($account, $request->all());
            } catch (PasswordChangeException $e) {
                Log::channel('auth')->error($e->getMessage());
                Log::channel('auth')->error($e->getTraceAsString());
                return redirect()->route('admin::users.edit', $account->id)->with(['alerts' => [
                    ['message' => $e->getMessage(), 'type' => 'error']
                ]]);

            } catch (Exception $e) {
                Log::channel('auth')->error($e->getMessage());
                Log::channel('auth')->error($e->getTraceAsString());
                return redirect()->route('admin::users.edit', $e)->with(['alerts' => [
                    ['message' => trans('users.edit.error'), 'type' => 'error']
                ]]);
            }

            Log::info('Password changed for user ' . $account->user);
        }
        DB::beginTransaction();
        try {
            $account->fill($request->except('password'));

            if($request->locked == 0 && $account->isDirty('locked'))
            {
                $account->failed_login_count = 0;
            }

            $account->pwd_change_required = (Auth::user()->can('resetPassword',$account ) ? $request->pwd_change_required : $account->pwd_change_required);

            // If an admin unlock a user account we should reset his failed login counts.
            $account->failed_login_count = ($account->isDirty('locked') && $request->locked == 0) ? 0 : $account->failed_login_count;
            $account->save();

        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('users.edit.error'), 'type' => 'error']
            ]]);
        }

        try {
            $this->authorize('manageRoles', $account);
            $account->roles()->sync(request('roles'));
        } catch (AuthorizationException $e) {
            DB::rollBack();
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('users.edit.error'), 'type' => 'error']
            ]]);
        }
        DB::commit();


        if (Auth::user()->id == $account->id && !$account->enabled) {
            return redirect()->route('admin::users.edit', $account->id)->with(['alerts' => [
                ['message' => trans('users.edit.success'), 'type' => 'success'],
                ['message' => trans('users.edit.warning'), 'type' => 'warning', 'options' => "{timeOut:0, extendedTimeOut:0}"]
            ]]);
        } elseif (Auth::user()->can('list', User::class)) {
            return redirect()->route('admin::users.index')->with(['alerts' => [
                ['message' => trans('users.edit.success'), 'type' => 'success']
            ]]);
        }
        return redirect()->route('admin::users.edit', $account->id)->with(['alerts' => [
            ['message' => trans('users.edit.success'), 'type' => 'success']
        ]]);
    }

    /**
     * The view to delete an existing user
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    public function delete($id)
    {
        try {
            $account = User::findOrFail($id);
            $this->authorize('delete', $account);
            return view('auth.users.delete', compact('account'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Deletes an existing user
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $account = User::findOrFail($id);
            $this->authorize('delete', $account);
            $account->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]]);
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with(['alerts' => [
                ['message' => trans('users.delete.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::users.index')->with(['alerts' => [
            ['message' => trans('users.delete.success'), 'type' => 'success']
        ]]);
    }

    public function select2(Request $request)
    {
        return Role::select('id', 'name as text', 'id as existing')->where('name','ILIKE','%'.$request->q.'%')->get();
    }

    public function list(Request $request)
    {
        return User::select('id', 'name', 'surname')
            ->where('name', 'ILIKE', '%' . $request->q . '%')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->fullname,
                    'existing' => $user->id,
                ];
            });
    }

}
