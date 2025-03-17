<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\DbtAttribute;
use App\DBT\Models\IngestionSource;
use App\Http\Controllers\Controller;
use App\Traits\ControllerPathfinder;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttributeHeaderMappingController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    public function getTranslationFile(): string
    {
        return 'DBT/attribute_header_mappings';
    }

    public function show($id)
    {
        try {
            $mapping = AttributeHeaderMapping::findOrFail($id);
            $this->authorize('view', $mapping);
            return view('dbt.ingestion_sources.attribute_header_mappings.show', compact('mapping'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function create($id)
    {
        $this->authorize('create', AttributeHeaderMapping::class);

        try {
            $ingestion_source = IngestionSource::findOrFail($id);
            return view('dbt.ingestion_sources.attribute_header_mappings.create', compact('ingestion_source'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $this->authorize('create', AttributeHeaderMapping::class);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'header_name' => ['required', 'string', 'max:255', Rule::unique('attribute_header_mappings', 'header_name')->where(function ($query) use ($request, $id) {
                return $query->where('ingestion_source_id', $id);
            })],
            'dbt_attribute_id' => 'required|exists:dbt_attributes,id',
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $mapping = new AttributeHeaderMapping();
            $mapping->fill($request->all());
            $mapping->ingestionSource()->associate(IngestionSource::find($id));
            $mapping->dbtAttribute()->associate(DbtAttribute::find($request->dbt_attribute_id));
            $mapping->createdBy()->associate(Auth::user());
            $mapping->updatedBy()->associate(Auth::user());
            $mapping->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/attribute_header_mappings.create.error'), 'type' => 'error']
            ]]);
        }
        return $this->returnPath('admin::dbt.ingestion_sources.attribute_header_mappings.index')->with(['alerts' => [
            ['message' => trans('DBT/attribute_header_mappings.create.success'), 'type' => 'success']
        ]])->withFragment(str_slug(trans('DBT/attribute_header_mappings.title')));
    }

    public function edit($id)
    {
        $mapping = AttributeHeaderMapping::findOrFail($id);
        $this->authorize('update', $mapping);

        try {
            $dbt_attribute = [$mapping->dbtAttribute->id => $mapping->dbtAttribute->name];
            return view('dbt.ingestion_sources.attribute_header_mappings.edit', compact('mapping', 'dbt_attribute'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function update(Request $request, $id)
    {
        $mapping = AttributeHeaderMapping::findOrFail($id);
        try {
            $this->authorize('update', $mapping);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'header_name' => ['required', 'string', 'max:255', Rule::unique('attribute_header_mappings', 'header_name')->where(function ($query) use ($request, $mapping) {
                return $query->where('ingestion_source_id', $mapping->ingestion_source_id);
            })->ignore($id)],
            'dbt_attribute_id' => 'required|exists:dbt_attributes,id',
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $mapping->fill($request->all());
            $mapping->ingestionSource()->associate(IngestionSource::find($mapping->ingestion_source_id));
            $mapping->dbtAttribute()->associate(DbtAttribute::find($request->dbt_attribute_id));
            $mapping->updatedBy()->associate(Auth::user());
            $mapping->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/attribute_header_mappings.edit.error'), 'type' => 'error']
            ]]);
        }
        return $this->returnPath('admin::dbt.ingestion_sources.attribute_header_mappings.index')->with(['alerts' => [
            ['message' => trans('DBT/attribute_header_mappings.edit.success'), 'type' => 'success']
        ]])->withFragment(str_slug(trans('DBT/attribute_header_mappings.title')));
    }

    public function delete($id)
    {
        try {
            $mapping = AttributeHeaderMapping::findOrFail($id);
            $this->authorize('delete', $mapping);
            return view('dbt.ingestion_sources.attribute_header_mappings.delete', compact('mapping'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function destroy($id)
    {
        try {
            $mapping = AttributeHeaderMapping::findOrFail($id);
            $this->authorize('delete', $mapping);
            $mapping->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/attribute_header_mappings.delete.error'), 'type' => 'error']
            ]]);
        }
        return $this->returnPath('admin::dbt.ingestion_sources.attribute_header_mappings.index')->with(['alerts' => [
            ['message' => trans('DBT/attribute_header_mappings.delete.success'), 'type' => 'success']
        ]])->withFragment(str_slug(trans('DBT/attribute_header_mappings.title')));
    }

    public function importRequest($id)
    {
        try {
            if(AttributeHeaderMapping::where('ingestion_source_id', $id)->count()){
                throw new AuthorizationException('Attribute header mappings already exist');
            }
            $this->authorize('create', AttributeHeaderMapping::class);

            return view('dbt.ingestion_sources.attribute_header_mappings.import', compact('id'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.forbidden_message')]);
        }
    }

    public function import($id)
    {
        $this->authorize('create', AttributeHeaderMapping::class);
        try{
            AttributeHeaderMapping::importHeaderMappings($id);
            return redirect()->route('admin::dbt.ingestion_sources.show', $id)->withAlerts([
                ['message' => trans('DBT/attribute_header_mappings.import.success'), 'type' => 'success']]);
        }catch (Exception $e){
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/attribute_header_mappings.import.error'), 'type' => 'error']
            ]);
        }
    }

    public function select2(Request $request)
    {
        return DbtAttribute::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE', '%' . $request->q . '%')->get();
    }
}
