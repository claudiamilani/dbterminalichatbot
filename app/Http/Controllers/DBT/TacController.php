<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\Http\Controllers\Controller;
use App\Traits\ControllerPathfinder;
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
use Illuminate\Validation\Rule;

class TacController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/tacs';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', Tac::class);

            $tacs = Tac::search(['search', 'terminal'])->with('terminal')->sortable(['id' => 'desc'])->paginate();

            $terminal = Terminal::where('id', request('terminal'))->get()->pluck('name', 'id');

            return view('dbt.tacs.index', compact('tacs', 'terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
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
            $this->authorize('create', Tac::class);
            if(request('terminal_id')){
                $terminal= Terminal::where('id', request('terminal_id'))->first();
            }else{
                $terminal = null;
            }
            return view('dbt.tacs.create',compact('terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
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
            $this->authorize('create', Tac::class);

            $validator = Validator::make($request->all(), $attributes = [
                'terminal_id' => [
                    'required',
                    'integer',
                    'exists:terminals,id'
                ],

                'value' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tacs')
                ]
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $tac = new Tac();
            $tac->fill($request->all());

            $tac->terminal()->associate($request->terminal_id);
            $tac->createdBy()->associate(Auth::id());
            $tac->updatedBy()->associate(Auth::id());

            $tac->save();

            return $this->returnPath('admin::dbt.tacs.index')->withAlerts([
                ['message' => trans('DBT/tacs.create.success'), 'type' => 'success']
            ])->withFragment(str_slug(trans('DBT/tacs.title')));

        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/tacs.create.error'), 'type' => 'error']
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
            $tac = Tac::findOrFail($id);
            $this->authorize('view', $tac);

            return view('dbt.tacs.show', compact('tac'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
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
            $tac = Tac::findOrFail($id);
            $this->authorize('update', $tac);
            $terminal = [$tac->terminal_id => $tac->terminal->name];

            return view('dbt.tacs.edit', compact('tac', 'terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
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
            $tac = Tac::findOrFail($id);
            $this->authorize('update', $tac);

            $validator = Validator::make($request->all(), $attributes = [
                'terminal_id' => [
                    'required',
                    'integer',
                    'exists:terminals,id'
                ],

                'value' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tacs')->ignore($tac->id)
                ],
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $tac->fill($request->all());
            $tac->terminal()->associate($request->terminal_id);
            $tac->save();

            return $this->returnPath('admin::dbt.tacs.index')->withAlerts([
                ['message' => trans('DBT/tacs.edit.success'), 'type' => 'success']
            ])->withFragment(str_slug(trans('DBT/tacs.title')));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/tacs.edit.error'), 'type' => 'error']
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $tac = Tac::findOrFail($id);
            $this->authorize('delete', $tac);

            return view('dbt.tacs.delete', compact('tac'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
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
            $tac = Tac::findOrFail($id);
            $this->authorize('delete', $tac);

            $tac->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/tacs.delete.error'), 'type' => 'error']
            ]);
        }

        return $this->returnPath('admin::dbt.tacs.index')->withAlerts([
            ['message' => trans('DBT/tacs.delete.success'), 'type' => 'success']
        ])->withFragment(str_slug(trans('DBT/tacs.title')));
    }

    public function select2Terminal(Request $request)
    {
        return Terminal::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE',
            '%'.$request->q.'%')->take(10000)->get();
    }
}