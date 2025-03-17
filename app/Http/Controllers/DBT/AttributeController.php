<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\AttrCategory;
use App\DBT\Models\DbtAttribute;
use App\Http\Controllers\Controller;
use App\Rules\ValidateAttributeOptions;
use App\Traits\ControllerPathfinder;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AttributeController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/attributes';
    }

    /**
     * The view for listing attributes
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', DbtAttribute::class);
            $attributes = DbtAttribute::search(['search', 'category_id', 'attribute_type', 'published'])->sortable(['display_order' => 'asc'])->paginate();
            if (request('category_id') && $category = AttrCategory::find(request('category_id'))) {
                $category = [$category->id => $category->name];
            } else {
                $category = collect();
            }
            $attribute_types = collect([
                DbtAttribute::TYPE_BOOLEAN => trans('DBT/attributes.types.' . DbtAttribute::TYPE_BOOLEAN),
                DbtAttribute::TYPE_VARCHAR => trans('DBT/attributes.types.' . DbtAttribute::TYPE_VARCHAR),
                DbtAttribute::TYPE_INT => trans('DBT/attributes.types.' . DbtAttribute::TYPE_INT),
                DbtAttribute::TYPE_DECIMAL => trans('DBT/attributes.types.' . DbtAttribute::TYPE_DECIMAL),
            ])->withFilterLabel(trans('DBT/attributes.attributes.type'));

            $published = collect([0 => trans('common.no'), 1 => trans('common.yes')])->withFilterLabel(trans('DBT/attributes.attributes.published'));

            return view('dbt.attributes.index', compact('attributes', 'category', 'attribute_types', 'published'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to create a new attribute
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create', DbtAttribute::class);
            $types = [
                DbtAttribute::TYPE_VARCHAR => trans('DBT/attributes.types.' . DbtAttribute::TYPE_VARCHAR),
                DbtAttribute::TYPE_TEXT => trans('DBT/attributes.types.' . DbtAttribute::TYPE_TEXT),
                DbtAttribute::TYPE_BOOLEAN => trans('DBT/attributes.types.' . DbtAttribute::TYPE_BOOLEAN),
                DbtAttribute::TYPE_INT => trans('DBT/attributes.types.' . DbtAttribute::TYPE_INT),
                DbtAttribute::TYPE_DECIMAL => trans('DBT/attributes.types.' . DbtAttribute::TYPE_DECIMAL),
            ];

            $input_types = [
                'TEXT' => trans('DBT/attributes.type_options.input_types.TEXT'),
                'SELECT' => trans('DBT/attributes.type_options.input_types.SELECT'),
                'CHECKBOX' => trans('DBT/attributes.type_options.input_types.CHECKBOX'),
            ];
            return view('dbt.attributes.create', compact('types', 'input_types'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     * Creates a new attribute and stores the passed values
     *
     * @param Request $request
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', DbtAttribute::class);

            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required|string|max:255',
                'attr_category_id' => 'required',
                'description' => 'string|max:65535|nullable',
                'display_order' => 'nullable|integer|min:0|max:100',
                'type' => ['required', Rule::in([DbtAttribute::TYPE_INT, DbtAttribute::TYPE_TEXT, DbtAttribute::TYPE_BOOLEAN, DbtAttribute::TYPE_VARCHAR, DbtAttribute::TYPE_DECIMAL]),],
                'published' => 'integer|min:0|max:1',
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            $attribute = new DbtAttribute;
            $attribute->fill($request->all());
            $attribute->category()->associate(AttrCategory::find($request->attr_category_id));
            $attribute->createdBy()->associate(Auth::id());
            $attribute->updatedBy()->associate(Auth::id());

            try {
                $options = $attribute->createOptions([
                    'input_type' => $request->get('input_type'),
                    'required' => $request->get('required'),
                    'searchable' => $request->get('searchable'),
                    'multiple' => $request->get('multiple'),
                    'options' => $request->get('options'),
                    'default_value' => $request->get('default_bool_value') ? array($request->get('default_bool_value')) : $request->get('default_value'),
                    'type' => $request->get('type')
                ]);
                $attribute->type_options = $options['config'];
                $attribute->default_value = $options['default_value'];
                $attribute->save();
            } catch (ValidationException $e) {
                return redirect()->back()->withErrors($e->validator->messages())->withInput();
            }

        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/attributes.create.error'), 'type' => 'error']
            ]])->withInput();
        }
        DB::commit();
        return $this->returnPath('admin::dbt.attributes.index')->with(['alerts' => [
            ['message' => trans('DBT/attributes.create.success'), 'type' => 'success']
        ]]);
    }

    /**
     * The view to show an attribute
     *
     * @param $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse|\Illuminate\View\View|void
     */
    public function show($id)
    {
        try {
            $attribute = DbtAttribute::find($id);
            $this->authorize('view', $attribute);
            return view('dbt.attributes.show', compact('attribute'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/attributes.show.error'), 'type' => 'error']
            ]]);
        }
    }


    /**
     * The view to edit an existing attribute
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $attribute = DbtAttribute::findOrFail($id);
            $block_update_type = false;
            $this->authorize('update', $attribute);
            $types = [
                DbtAttribute::TYPE_VARCHAR => trans('DBT/attributes.types.' . DbtAttribute::TYPE_VARCHAR),
                DbtAttribute::TYPE_TEXT => trans('DBT/attributes.types.' . DbtAttribute::TYPE_TEXT),
                DbtAttribute::TYPE_BOOLEAN => trans('DBT/attributes.types.' . DbtAttribute::TYPE_BOOLEAN),
                DbtAttribute::TYPE_INT => trans('DBT/attributes.types.' . DbtAttribute::TYPE_INT),
                DbtAttribute::TYPE_DECIMAL => trans('DBT/attributes.types.' . DbtAttribute::TYPE_DECIMAL),
            ];
            $category = $attribute->category()->pluck('name', 'id');
            $input_types = [
                'TEXT' => trans('DBT/attributes.type_options.input_types.TEXT'),
                'TEXTAREA' => trans('DBT/attributes.type_options.input_types.TEXTAREA'),
                'NUMBER' => trans('DBT/attributes.type_options.input_types.NUMBER'),
                'SELECT' => trans('DBT/attributes.type_options.input_types.SELECT'),
                'CHECKBOX' => trans('DBT/attributes.type_options.input_types.CHECKBOX'),

            ];
            //If values are already saved for this attribute we will only let user to edit the options,
            //not type or input_type
            if ($attribute->attributeValues->count()) {
                $block_update_type = true;
            }

            return view('dbt.attributes.edit', compact('attribute', 'category', 'types', 'input_types', 'block_update_type'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     * Edits an existing attribute and stores the passed values
     *
     * @param Request $request
     * @param $id
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update(Request $request, $id)
    {
        try {
            $attribute = DbtAttribute::findOrFail($id);
            $this->authorize('update', $attribute);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }


        $validator = Validator::make($request->all(), $attributes = [
            'name' => 'required|string|max:255',
            'attr_category_id' => 'required',
            'description' => 'string|max:65535|nullable',
            'display_order' => 'nullable|integer|min:0|max:100',
            'published' => 'integer|min:0|max:1',
        ], [], $this->getTranslatedAttributes($attributes));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            //If we have already saved AttributeValues for this DbtAttribute we only update name, description, display_order, published
            //and type options
            try {
                if ($attribute->attributeValues->count()) {
                    DB::beginTransaction();
                    $attribute->fill($request->only('name', 'description', 'display_order', 'published'));
                    $attribute->category()->associate(AttrCategory::find($request->attr_category_id));
                    $options = $attribute->createOptions([
                        'input_type' => $attribute->getInputTypeOption(),
                        'required' => $request->get('required'),
                        'searchable' => $request->get('searchable'),
                        'multiple' => $request->get('multiple'),
                        'options' => $request->get('options'),
                        'default_value' => $request->get('default_bool_value') ? (array)$request->get('default_bool_value') : $request->get('default_value'),
                        'type' => $attribute->type
                    ]);
                    $attribute->type_options = $options['config'];
                    $attribute->default_value = $options['default_value'];
                    $attribute->save();
                    Db::commit();
                    return $this->returnPath('admin::dbt.attributes.show', [$attribute->id])->with(['alerts' => [
                        ['message' => trans('DBT/attributes.edit.partial.success'), 'type' => 'warning']
                    ]]);
                }

                DB::beginTransaction();
                $attribute->fill($request->only('name', 'description', 'display_order', 'published'));
                $attribute->category()->associate(AttrCategory::find($request->attr_category_id));
                $attribute->updatedBy()->associate(Auth::id());

                $options = $attribute->createOptions([
                    'input_type' => $request->get('input_type'),
                    'required' => $request->get('required'),
                    'searchable' => $request->get('searchable'),
                    'multiple' => $request->get('multiple'),
                    'options' => $request->get('options'),
                    'default_value' => $request->get('default_bool_value') ? (array)$request->get('default_bool_value') : $request->get('default_value'),
                    'type' => $request->get('type')
                ]);
                $attribute->type_options = $options['config'];
                $attribute->default_value = $options['default_value'];
                $attribute->save();
            } catch (ValidationException $e) {
                return redirect()->back()->withErrors($e->validator->messages())->withInput();
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage())->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/attributes.edit.error'), 'type' => 'error']
            ]])->withInput();
        }
        DB::commit();
        return $this->returnPath('admin::dbt.attributes.show', [$attribute->id])->with(['alerts' => [
            ['message' => trans('DBT/attributes.edit.success'), 'type' => 'success']
        ]]);
    }

    /**
     * The view to delete an existing attribute
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    public function delete($id)
    {
        try {
            $attribute = DbtAttribute::findOrFail($id);
            $this->authorize('delete', $attribute);
            return view('dbt.attributes.delete', compact('attribute'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }


    /**
     * Deletes an existing attribute
     *
     * @param $id
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroy($id)
    {
        try {
            $attribute = DbtAttribute::findOrFail($id);
            $this->authorize('delete', $attribute);
            $attribute->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return back()->with(['alerts' => [
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]]);
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/attributes.delete.error'), 'type' => 'error']
            ]]);
        }
        return $this->returnPath('admin::dbt.attributes.index')->with(['alerts' => [
            ['message' => trans('DBT/attributes.delete.success'), 'type' => 'success']
        ]]);
    }

    public function select2Category(Request $request)
    {
        return AttrCategory::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE', '%' . $request->q . '%')->get();
    }

    public function select2Ingestion(Request $request)
    {
        return [];
    }

    public function select2IngestionSource(Request $request)
    {
        return [];

    }

    public function select2TypeOptions(Request $request)
    {
        if ($request->attribute_id) {
            $attribute_id = $request->attribute_id;
            $results = DB::table('dbt_attributes')
                ->select('dbt_attributes.*', DB::raw("jsonb_array_elements_text(type_options::jsonb->'options') AS id"), DB::raw("jsonb_array_elements_text(type_options::jsonb->'options') AS text"))
                ->where('dbt_attributes.id', $attribute_id)
                ->whereExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from(DB::raw("jsonb_array_elements_text(dbt_attributes.type_options::jsonb->'options') AS option_element"))
                        ->where(DB::raw("option_element"), 'ILIKE', '%' . $request->q . '%');
                })
                ->get();

            return $results;
        } else {
            return [];
        }
    }

    public function select2SearchableAttributes(Request $request)
    {
        $attribute_id = $request->attribute_id;
        $searchTerm = trim($request->q);
        $query = DB::table('dbt_attributes')
            ->select('dbt_attributes.*',
                DB::raw("option_element AS id"),
                DB::raw("option_element AS text"))
            ->where('dbt_attributes.id', $attribute_id)
            ->join(DB::raw("jsonb_array_elements_text(dbt_attributes.type_options::jsonb->'options') AS option_element"), function ($join) use ($searchTerm) {
                if ($searchTerm !== '') {
                    $join->whereRaw("option_element ILIKE ?", ['%' . $searchTerm . '%']);
                } else {
                    $join->on(DB::raw("true"), '=', DB::raw("true")); // Condizione sempre vera
                }
            });
        $results = $query->get();
        return response()->json($results);
    }


    public function loadOptions(Request $request)
    {
        $type_config = DbtAttribute::TYPES_CONFIGURATION;
        $types = $type_config[$request->type];
        $translated = [];
        foreach ($types['input_types'] as $type) {
            $translated[$type] = trans('DBT/attributes.type_options.input_types.' . $type);
        }

        return response()->json(['type_config' => $translated]);
    }

    public function loadInputTypeOptions(Request $request)
    {
        $input_types_config = DbtAttribute::INPUT_TYPES_CONFIGURATION;
        $options = $input_types_config[$request->input_type];


        return response()->json(['input_types_config' => $options]);
    }

    public function select2(Request $request)
    {
        return DbtAttribute::select('id', 'description as text', 'id as existing')->where('description','ILIKE','%'.$request->q.'%')->get();
    }
}
