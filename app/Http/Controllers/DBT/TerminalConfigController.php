<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Document;
use App\DBT\Models\Ota;
use App\DBT\Models\Terminal;
use App\DBT\Models\TerminalConfig;
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

class TerminalConfigController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    public function getTranslationFile(): string
    {
        return 'DBT/terminal_configs';
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {

    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function show($terminal_id, $config_id)
    {
        try {
            $terminal_config = TerminalConfig::findOrFail($config_id);

            $this->authorize('view', $terminal_config);

            return view('dbt.terminals.terminal_configs.show', compact('terminal_id', 'config_id', 'terminal_config'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create($terminal_id)
    {
        try {
            $terminal = Terminal::findOrFail($terminal_id);
            $this->authorize('create', TerminalConfig::class);
            return view('dbt.terminals.terminal_configs.create', compact('terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(Request $request, $terminal_id)
    {
        try {
            $this->authorize('create', TerminalConfig::class);
            $validator = Validator::make($request->all(), $attributes = [
                'ota_id' => 'required|integer|exists:otas,id',
                'document_id' => 'nullable|integer'
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $terminal = Terminal::findOrFail($terminal_id);
            $config = new TerminalConfig();
            $config->fill($request->all());
            $ota = Ota::findOrFail($request->get('ota_id'));
            $config->terminal()->associate($terminal);
            $config->ota()->associate($ota);
            if ($request->has('document_id')) {
                $document = Document::findOrFail($request->get('document_id'));
                $config->document()->associate($document);
            }
            $config->createdBy()->associate(Auth::id());
            $config->updatedBy()->associate(Auth::id());
            $config->save();
            return $this->returnPath('admin::dbt.terminals.show', [$config->terminal_id])->withAlerts([
                ['message' => trans('DBT/terminal_configs.create.success'), 'type' => 'success']
            ]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);

            return back()->with([
                'alerts' => [
                    ['message' => trans('common.http_err.403'), 'type' => 'error']
                ]
            ]);
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_configs.delete.error'), 'type' => 'error']
                ]
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit($terminal_id, $config_id)
    {
        try {
            $terminal_config = TerminalConfig::findOrFail($config_id);
            $this->authorize('update', $terminal_config);
            return view('dbt.terminals.terminal_configs.edit', compact('terminal_id', 'config_id', 'terminal_config'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return  RedirectResponse
     * @throws Exception
     */
    public function update(Request $request, $terminal_id, $config_id)
    {
        try {
            $terminal_config = TerminalConfig::findOrFail($config_id);
            $this->authorize('update', $terminal_config);
            Terminal::findOrFail($terminal_id);
            $validator = Validator::make($request->all(), $attributes = [
                'ota_id' => 'required|string|exists:otas,id',
                'document_id' => 'nullable|integer'
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $terminal_config->fill($request->all());
            $terminal_config->ota()->associate(Ota::findOrFail($request->get('ota_id')));
            if ($request->filled('document_id')) {
                $terminal_config->document()->associate(Document::findOrFail($request->get('document_id')));
            } else {
                $terminal_config->document()->dissociate();
            }
            $terminal_config->updatedBy()->associate(Auth::id());
            $terminal_config->save();
            return $this->returnPath('admin::dbt.terminals.show', [$terminal_config->terminal_id])->withAlerts([
                ['message' => trans('DBT/terminal_configs.edit.success'), 'type' => 'success']
            ]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return back()->with([
                'alerts' => [
                    ['message' => trans('common.http_err.403'), 'type' => 'error']
                ]
            ]);
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_configs.delete.error'), 'type' => 'error']
                ]
            ]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function delete(int $terminal_id, int $config_id)
    {
        try {
            $config = TerminalConfig::findOrFail($config_id);
            $this->authorize('delete', $config);

            Terminal::findOrFail($terminal_id);

            return view('dbt.terminals.terminal_configs.delete', compact('config'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(int $terminal_id, int $config_id)
    {
        try {
            $config = TerminalConfig::findOrFail($config_id);
            $this->authorize('delete', $config);

            Terminal::findOrFail($terminal_id);

            $config->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_configs.delete.error'), 'type' => 'error']
                ]
            ]);
        }

        return $this->returnPath('admin::dbt.terminals.show', [$terminal_id])->withAlerts([
            ['message' => trans('DBT/terminal_configs.delete.success'), 'type' => 'success']
        ])->withFragment(str_slug(trans('DBT/terminal_configs.title')));

    }

    public function select2Ota(Request $request)
    {
        return Ota::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE', '%'.$request->q.'%')->get();
    }

    public function select2Document(Request $request)
    {
        return Document::select('id', 'title as text', 'id as existing')->where('title', 'ILIKE',
            '%'.$request->q.'%')->get();
    }
}
