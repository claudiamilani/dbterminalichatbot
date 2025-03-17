<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\AttrCategory;
use App\DBT\Models\AttributeValue;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\DBT\Models\Terminal;
use App\DBT\Models\Vendor;
use App\Exports\ModelsExport;
use App\Http\Controllers\Controller;
use App\Services\RtmpService;
use App\Traits\ControllerPathfinder;
use App\Traits\TranslatedValidation;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class TerminalController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/terminals';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', Terminal::class);
            $terminals = Terminal::with('updatedBy', 'createdBy', 'vendor', 'ingestionSource')->search(['search', 'vendor', 'ota_vendor', 'certified', 'published', 'dbt_attribute_id', 'attribute_condition', 'attribute_value', 'type', 'ingestion_source'])->sortable(['id' => 'asc'])->paginate();

            $vendor = Vendor::where('id', request('vendor'))->get()->pluck('name', 'id');
            $ingestion_source = IngestionSource::pluck('name', 'id')->withFilterLabel(trans('DBT/terminals.attributes.ingestion_source_id'));

            $ota_vendor = collect(['null' => trans('common.unassociated'), 'not_null' => trans('common.associated')])->withFilterLabel(trans('DBT/terminal_associate_otas.title'));
            $certified = collect([0 => trans('common.no'), 1 => trans('common.yes')])->withFilterLabel(trans('DBT/terminals.attributes.certified'));
            $published = collect([0 => trans('common.no'), 1 => trans('common.yes')])->withFilterLabel(trans('DBT/terminals.attributes.published'));
            $attributes1 = DbtAttribute::pluck('name', 'id')->toArray();
            $attributes2 = DbtAttribute::whereNotNull('description')->pluck('description', 'id')->toArray();
            foreach ($attributes1 as $id => $name) {
                $all_attributes[$id] = $name;
            }
            foreach ($attributes2 as $id => $description) {
                $all_attributes[$id] = $description;
            }
            $dbt_attribute_id = request('dbt_attribute_id') ? [request('dbt_attribute_id') => $all_attributes[request('dbt_attribute_id')]] : [];
            $attribute = DbtAttribute::find(request('dbt_attribute_id'));
            $attribute_condition = ['more' => 'Maggiore di', 'less' => 'Minore di', 'equals' => 'Uguale a', 'like' => 'Simile a'];
            $attribute_value = request('attribute_value') ?? null;


            return view('dbt.terminals.index', compact('terminals', 'vendor', 'ota_vendor', 'certified', 'published', 'attribute_value', 'attribute_condition', 'dbt_attribute_id', 'attribute', 'ingestion_source'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function show(int $id)
    {
        try {
            $terminal = Terminal::with('pictures')->findOrFail($id);
            $this->authorize('view', $terminal);
            $pictures = $terminal->pictures()->sortable('sort_pics', ['display_order' => 'asc'])->paginate();
            $configs = $terminal->configs()->sortable('sort_configs', ['id' => 'asc'])->paginate();
            $attributes = DbtAttribute::with(['attributeValues' => function ($query) use ($id) {
                $query->where('terminal_id', $id);
            }])->orderBy('display_order')->get();
            $categories = AttrCategory::has('dbtAttributes')->where('name', '!=', 'legacy')->orderBy('display_order')->get();
            $ingestion_sources = IngestionSource::orderBy('id')->get();
            $categories_filter = $categories->pluck('description', 'id');
            $tacs = $terminal->tacs()->search(['search_tacs'])->sortable('sort_tacs')->paginate();
            return view('dbt.terminals.show', compact('terminal', 'configs', 'pictures', 'attributes', 'categories', 'ingestion_sources', 'tacs', 'categories_filter'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
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
            $this->authorize('create', Terminal::class);

            $attributes = DbtAttribute::orderBy('display_order')->get();
            $categories = AttrCategory::has('dbtAttributes')->orderBy('display_order')->get();
            $ingestion_sources = IngestionSource::orderBy('id')->get();

            return view('dbt.terminals.create', compact('categories', 'attributes', 'ingestion_sources', 'categories'));
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
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Terminal::class);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
        $validator = Validator::make($request->all(), $attributes = [
            'name' => 'required|string|unique:terminals|max:255',
            'vendor_id' => 'required|integer|exists:vendors,id',
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            DB::beginTransaction();
            //Attributi
            $attributes_to_validate = $request->except('published', '_token', 'certified', 'name', 'vendor_id', 'ota_model', 'ota_vendor', '_method');
            $terminal = new Terminal();
            $terminal->fill($request->all());
            $terminal->ingestionSource()->associate(IngestionSource::SRC_ADMIN);
            $terminal->vendor()->associate(Vendor::find($request->vendor_id));
            $terminal->createdBy()->associate(Auth::user());
            $terminal->updatedBy()->associate(Auth::user());
            $terminal->save();
            foreach ($attributes_to_validate as $attribute_id => $value) {
                try {
                    if (is_array($value)) {
                        if (!empty(array_filter($value))) {
                            DbtAttribute::createAttributeValue($attribute_id, $terminal->id, IngestionSource::SRC_ADMIN, array_filter($value));
                        }
                    } elseif (!is_array($value) && $value != null && $value != '') {
                        DbtAttribute::createAttributeValue($attribute_id, $terminal->id, IngestionSource::SRC_ADMIN, $value);
                    }
                } catch (ValidationException $e) {
                    Log::channel('admin_gui')->error($e->getMessage());
                    Log::channel('admin_gui')->error($e->getTraceAsString());
                    $errors[] = $e->getMessage();
                }
            }
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()->withErrors($errors)->withInput();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminals.create.error'), 'type' => 'error']
                ]
            ]);
        }

        return redirect()->route('admin::dbt.terminals.index', $terminal->id)->with([
            'alerts' => [
                ['message' => trans('DBT/terminals.create.success'), 'type' => 'success']
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $terminal = Terminal::findOrFail($id);
            $this->authorize('update', $terminal);
            $vendor_id = [$terminal->vendor->id => $terminal->vendor->name];
            $attributes = DbtAttribute::with(['attributeValues' => function ($query) use ($id) {
                $query->where('terminal_id', $id);
            }])->orderBy('display_order')->get();
            $categories = AttrCategory::has('dbtAttributes')->where('name', '!=', 'legacy')->orderBy('display_order')->get();
            $ingestion_sources = IngestionSource::orderBy('id')->get();
            $categories_filter = $categories->pluck('description', 'id');
            return view('dbt.terminals.edit', compact('terminal', 'vendor_id', 'categories', 'attributes', 'ingestion_sources', 'categories_filter'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $terminal = Terminal::findOrFail($id);
        try {
            $this->authorize('update', $terminal);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'name' => ['required', 'string', 'max:255', Rule::unique('terminals')->ignore($terminal->id)],
            'vendor_id' => 'required|integer|exists:vendors,id',
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            DB::beginTransaction();
            //Attributi
            $attributes_to_validate = $request->except('published', '_token', 'certified', 'name', 'vendor_id', 'ota_model', 'ota_vendor', '_method');
            $saved_values = AttributeValue::where('ingestion_source_id', IngestionSource::SRC_ADMIN)
                ->where('terminal_id', $terminal->id)
                ->get();
            $not_saved_values = array_diff(array_keys($attributes_to_validate), $saved_values->pluck('dbt_attribute_id')->toArray());
            foreach ($saved_values as $attribute_value) {
                try {
                    DbtAttribute::updateAttributeValue($attribute_value, $attributes_to_validate[$attribute_value->dbtAttribute->id] ?? null);
                } catch (ValidationException $e) {
                    Log::channel('admin_gui')->error($e->getMessage());
                    Log::channel('admin_gui')->error($e->getTraceAsString());
                    $errors[] = $e->getMessage();
                }
            }
            foreach ($not_saved_values as $key => $attribute_id) {
                try {

                    if (is_array($attributes_to_validate[$attribute_id])) {
                        DbtAttribute::createAttributeValue($attribute_id, $terminal->id, IngestionSource::SRC_ADMIN, array_filter($attributes_to_validate[$attribute_id]));

                    } else if ($attributes_to_validate[$attribute_id] != null && $attributes_to_validate[$attribute_id] != '') {
                        DbtAttribute::createAttributeValue($attribute_id, $terminal->id, IngestionSource::SRC_ADMIN, $attributes_to_validate[$attribute_id]);
                    }
                } catch (ValidationException $e) {
                    DB::rollback();
                    Log::channel('admin_gui')->error($e->getMessage());
                    Log::channel('admin_gui')->error($e->getTraceAsString());
                    $errors[] = $e->getMessage();
                }
            }
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()->withErrors($errors)->withInput();
            }
            $terminal->fill($request->only('name', 'certified', 'published'));
            $terminal->vendor()->associate(Vendor::find($request->vendor_id));
            $terminal->updatedBy()->associate(Auth::user());
            $terminal->save();
            DB::commit();
        } catch
        (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminals.edit.error'), 'type' => 'error']
                ]
            ]);
        }
        return redirect()->route('admin::dbt.terminals.show', $terminal->id)->with([
            'alerts' => [
                ['message' => trans('DBT/terminals.edit.success'), 'type' => 'success']
            ]
        ]);
    }

    public function delete($id)
    {
        try {
            $terminal = Terminal::findOrFail($id);
            $this->authorize('delete', $terminal);
            return view('dbt.terminals.delete', compact('terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            $terminal = Terminal::findOrFail($id);
            $this->authorize('delete', $terminal);
            $terminal->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminals.delete.error'), 'type' => 'error']
                ]
            ]);
        }
        return redirect()->route('admin::dbt.terminals.index')->with([
            'alerts' => [
                ['message' => trans('DBT/terminals.delete.success'), 'type' => 'success']
            ]
        ]);
    }

    /**
     * Show the form to associate an OTA to the specified terminal.
     *
     * @param int $terminal_id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function associateOta(int $terminal_id)
    {
        try {
            $terminal = Terminal::findOrFail($terminal_id);
            $this->authorize('update', $terminal);

            return view('dbt.terminals.associate_otas.edit', compact('terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Retrieve the list of vendors from an external service.
     *
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Response|ResponseFactory
     * @throws GuzzleException|Throwable
     */
    public function getVendors()
    {
        $tag = '<?xml version="1.0" encoding="UTF-8" ?>';

        try {
            /* Connection parameters for the call */
            $request_user = config('dbt.configuration_rtmp.request_user');
            $request_password = config('dbt.configuration_rtmp.request_password');
            $request_url = config('dbt.configuration_rtmp.request_url');

            $requestData = (object)[
                'user' => $request_user,
                'password' => $request_password,
            ];

            /* Generate XML for the call & pass values to view */
            $requestXml = view('dbt.terminals.associate_otas.xml_files.getVendors_xml',
                compact('requestData'))->render();

            $requestXml = $tag . $requestXml;

            /* Call rtmpService for sending the XML request */
            $rtmp_service = new RtmpService();
            $response = $rtmp_service->RtmpClient($requestXml, $request_url);

            /* If the XML call succeeds */
            if ($response['success']) {
                $response_body = $response['response_xml'];
                return response($response_body, 200)->header('Content-Type', 'application/xml');
            }
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_associate_otas.errors.request_error'), 'type' => 'error']
                ]
            ]);
        }
    }

    /**
     * Retrieve the list of models for the specified vendor.
     *
     * @param string $vendor
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Response|ResponseFactory
     * @throws GuzzleException|Throwable
     */
    public function getModels(string $vendor)
    {
        $tag = '<?xml version="1.0" encoding="UTF-8" ?>';

        try {
            /* Connection parameters for the call */
            $request_user = config('dbt.configuration_rtmp.request_user');
            $request_password = config('dbt.configuration_rtmp.request_password');
            $request_url = config('dbt.configuration_rtmp.request_url');

            $requestData = (object)[
                'user' => $request_user,
                'password' => $request_password,
                'vendor' => $vendor
            ];

            /* Generate XML for the call & pass values to view */
            $requestXml = $tag . view('dbt.terminals.associate_otas.xml_files.getModels_xml',
                    compact('requestData'))->render();

            /* Call rtmpService for sending the XML request */
            $rtmp_service = new RtmpService();
            $response = $rtmp_service->RtmpClient($requestXml, $request_url);

            /* If the XML call succeeds */
            if ($response['success']) {
                $response_body = $response['response_xml'];
                return response($response_body, 200)->header('Content-Type', 'application/xml');
            }
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_associate_otas.errors.request_error'), 'type' => 'error']
                ]
            ]);
        }
    }

    /**
     * Link the OTA from the specified terminal.
     *
     * @param Request $request
     * @param int $terminal_id
     * @return RedirectResponse
     */
    public function linkOta(Request $request, int $terminal_id)
    {
        try {
            $terminal = Terminal::findOrFail($terminal_id);
            $this->authorize('update', $terminal);

            $validator = Validator::make($request->all(), $attributes = [
                'vendor' => 'required|string',
                'model' => 'required|string',
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = [
                'ota_vendor' => $request->input('vendor'),
                'ota_model' => $request->input('model')
            ];

            $terminal->fill($data);
            $terminal->updatedBy()->associate(Auth::user());

            $terminal->save();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_associate_otas.edit.error'), 'type' => 'error']
                ]
            ]);
        }

        return redirect()->route('admin::dbt.terminals.index', $terminal_id)->withAlerts([
            ['message' => trans('DBT/terminal_associate_otas.edit.success'), 'type' => 'success']
        ]);
    }

    /**
     * Show the form to disassociate an OTA from the specified terminal.
     *
     * @param int $terminal_id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function disassociateOta(int $terminal_id)
    {
        try {
            $terminal = Terminal::findOrFail($terminal_id);
            $this->authorize('update', $terminal);

            return view('dbt.terminals.associate_otas.delete', compact('terminal'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Unlink the OTA from the specified terminal.
     *
     * @param int $terminal_id
     * @return RedirectResponse
     */
    public function unlinkOta(int $terminal_id)
    {
        try {
            $terminal = Terminal::findOrFail($terminal_id);
            $this->authorize('update', $terminal);

            $terminal->ota_vendor = null;
            $terminal->ota_model = null;

            $terminal->save();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_associate_otas.delete.error'), 'type' => 'error']
                ]
            ]);
        }

        return redirect()->route('admin::dbt.terminals.index', $terminal_id)->withAlerts([
            ['message' => trans('DBT/terminal_associate_otas.delete.success'), 'type' => 'success']
        ]);
    }

    public function exportQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Terminal::search(['search', 'vendor', 'ota_vendor', 'certified', 'published', 'dbt_attribute_id', 'attribute_condition', 'attribute_value', 'type'])->sortable(['name' => 'asc']);
    }

    public function export()
    {
        return Excel::download(new ModelsExport($this->exportQuery(),
            ['id', 'name', 'vendor.name', 'ingestion.id', 'ingestionSource.name', 'ota_vendor', 'ota_model', 'createdBy.fullName', 'updatedBy.fullName', 'created_at', 'updated_at'],
            ['id', trans('DBT/terminals.attributes.name'), trans('DBT/terminals.attributes.vendor_id'), trans('Ingestion ID'), trans('DBT/terminals.attributes.ingestion_source_id'), trans('DBT/terminals.attributes.ota_vendor'), trans('DBT/terminals.attributes.ota_model'), trans('DBT/terminals.attributes.created_by_id'), trans('DBT/terminals.attributes.updated_by_id'), trans('DBT/terminals.attributes.created_at'), trans('DBT/terminals.attributes.updated_at')]), 'report.xlsx');
    }

    public function exportTranspose()
    {
        try {
            return Storage::download(config('dbt.transpose.export_file_path') . DIRECTORY_SEPARATOR . 'export.csv');
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->route('admin::dbt.terminals.index')->with(['alerts' => [
                ['message' => trans('common.file_404'), 'type' => 'error']]]);
        }
    }

    public function select2(Request $request)
    {
        return Vendor::select('id', 'name as text', 'id as existing')
            ->whereIn('id', Terminal::select('vendor_id')->distinct())
            ->where('name', 'ILIKE', '%' . $request->q . '%')
            ->get();

    }

    public function select2Terminals(Request $request)
    {
        return Terminal::select('id', 'name as text', 'id as existing')
            ->where('name', 'ILIKE', '%' . $request->q . '%')->take(10000)->get();
    }

    public function select2Attributes(Request $request)
    {
        return DbtAttribute::
        select(DB::raw('coalesce(dbt_attributes.description, dbt_attributes.name) as text, dbt_attributes.id as existing, dbt_attributes.id, dbt_attributes.type, dbt_attributes.attr_category_id'))
            ->where(function ($query) use ($request) {
                $query->where('dbt_attributes.name', 'ILIKE', '%' . $request->q . '%')
                    ->orWhere('dbt_attributes.description', 'ILIKE', '%' . $request->q . '%');
            })
            ->join('attr_categories', 'attr_categories.id', '=', 'dbt_attributes.attr_category_id')
            ->orderBy('attr_categories.display_order')
            ->orderBy('dbt_attributes.display_order')
            ->get();
    }
}
