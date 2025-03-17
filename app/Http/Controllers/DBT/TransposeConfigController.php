<?php

namespace App\Http\Controllers\DBT;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\Ingestion;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Tac;
use App\DBT\Models\Terminal;
use App\DBT\Models\TransposeConfig;
use App\DBT\Transpose;
use App\Http\Controllers\Controller;
use App\Rules\ValidateColumnName;
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
use App\Traits\TranslatedValidation;
use Illuminate\Validation\Rule;

class TransposeConfigController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/transpose_configs';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', TransposeConfig::class);
            $configs = TransposeConfig::search(['search'])->sortable(['display_order' => 'asc'])->paginate();
            return view('dbt.transpose_configs.index', compact('configs'));
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
            $this->authorize('create', TransposeConfig::class);
            $types = [
                DbtAttribute::TYPE_BOOLEAN => DbtAttribute::TYPE_BOOLEAN,
                DbtAttribute::TYPE_VARCHAR => DbtAttribute::TYPE_VARCHAR,
            ];
            $max_order =(ceil( TransposeConfig::max('display_order') / 10) * 10)+10;
            return view('dbt.transpose_configs.create', compact('types','max_order'));
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
            $this->authorize('create', TransposeConfig::class);
            $validator = Validator::make($request->all(), $attributes = [
                'dbt_attribute_id' => 'required|integer|exists:dbt_attributes,id|unique:transpose_configs',
                'label' => new ValidateColumnName()
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $config = new TransposeConfig();
            $config->fill($request->except('label'));
            $config->label = strtolower(trim($request->label));
            if(!$request->display_order && $request->display_order != 0){
                $config->display_order = (ceil( TransposeConfig::max('display_order') / 10) * 10)+10;
            }
            $config->dbtAttribute()->associate($request->dbt_attribute_id);
            $config->save();
            return redirect()->route('admin::dbt.transpose_configs.index')->withAlerts([
                ['message' => trans('DBT/transpose_configs.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/transpose_configs.create.error'), 'type' => 'error']
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
            $config = TransposeConfig::findOrFail($id);
            $this->authorize('view', $config);
            return view('dbt.transpose_configs.show', compact('config'));
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
            $config = TransposeConfig::findOrFail($id);
            $this->authorize('update', $config);
            $types = [
                DbtAttribute::TYPE_BOOLEAN => DbtAttribute::TYPE_BOOLEAN,
                DbtAttribute::TYPE_VARCHAR => DbtAttribute::TYPE_VARCHAR,
            ];
            return view('dbt.transpose_configs.edit', compact('config','types'));
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
            $config = TransposeConfig::findOrFail($id);
            $this->authorize('update', $config);
            $validator = Validator::make($request->all(), $attributes = [
                'dbt_attribute_id' => ['required',Rule::unique('transpose_configs')->ignore($config->id)],
                'label' => new ValidateColumnName()

            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $config->fill($request->except('label'));
            $config->label = strtolower(trim($request->label));
            if(!$request->display_order && $request->display_order != 0){
                $config->display_order = (ceil( TransposeConfig::max('display_order') / 10) * 10)+10;
            }
            $config->dbtAttribute()->associate($request->dbt_attribute_id);
            $config->save();

            return redirect()->route('admin::dbt.transpose_configs.index')->withAlerts([
                ['message' => trans('DBT/transpose_configs.edit.success'), 'type' => 'success']
            ]);
        }  catch(AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/transpose_configs.edit.error'), 'type' => 'error']
            ]);
        }
    }

    public function importRequest()
    {
        try {
            if(TransposeConfig::count()){
                throw new AuthorizationException('Transpose config already exists');
            }
            $this->authorize('create', TransposeConfig::class);

            return view('dbt.transpose_configs.import');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.forbidden_message')]);
        }
    }

    public function import()
    {
        $this->authorize('create', TransposeConfig::class);
        try{
            Transpose::createTransposeConfigTableFromLegacy();
            return redirect()->route('admin::dbt.transpose_configs.index')->withAlerts([
                ['message' => trans('DBT/transpose_configs.import.success'), 'type' => 'success']]);
        }catch (Exception $e){
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/transpose_configs.import.error'), 'type' => 'error']
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $config = TransposeConfig::findOrFail($id);
            $this->authorize('delete', $config);

            return view('dbt.transpose_configs.delete', compact('config'));
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
            $config = TransposeConfig::findOrFail($id);
            $this->authorize('delete' , $config);
            $config->delete();
        }  catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/transpose_configs.delete.error'), 'type' => 'error']
            ]);
        }
        return redirect()->back()->withAlerts([
            ['message' => trans('DBT/transpose_configs.delete.success'), 'type' => 'success']
        ]);
    }

    public function select2DbtAttribute(Request $request)
    {
        return DbtAttribute::select('id', 'name as text', 'id as existing')
            ->where('name', 'ILIKE', '%' . $request->q . '%')
            ->whereNotIn('id', TransposeConfig::pluck('dbt_attribute_id'))
            ->get();
    }

}