<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\AttributeHeaderMapping;
use App\DBT\Models\IngestionSource;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


class IngestionSourceController extends Controller
{

    use TranslatedValidation, ControllerPathfinder;

    public function getTranslationFile(): string
    {
        return 'DBT/ingestion_sources';
    }

    /**
     * The view for listing ingestion sources
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', arguments: IngestionSource::class);
            $ingestion_sources = IngestionSource::search(['search', 'enabled'])->sortable(['priority'=>'asc'])->paginate();

            $enabled = collect([0 => trans('common.disabled'), 1 => trans('common.active')])->withFilterLabel(trans('DBT/ingestion_sources.attributes.enabled'));
            return view('dbt.ingestion_sources.index', compact('ingestion_sources', 'enabled'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to edit an existing ingestion source
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $ingestion_source = IngestionSource::findOrFail($id);
            $this->authorize('update', $ingestion_source);
            return view('dbt.ingestion_sources.edit', compact('ingestion_source'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     * Edits an existing ingestion source and stores the passed values
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|never
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update(Request $request, $id)
    {
        $ingestion_source = IngestionSource::findOrFail($id);
        try {
            $this->authorize('update', $ingestion_source);
            $validator = Validator::make($request->all(), $attributes = [
                'name' => ['required','string',Rule::unique('ingestion_sources')->ignore($id)],
                'priority' => 'required|integer'
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $options['default_options']['CREATES_VENDOR'] = (bool)$request->CREATES_VENDOR;
            $options['default_options']['CREATES_ATTRIBUTE'] = (bool)$request->CREATES_ATTRIBUTE;
            $options['default_options']['CREATES_TERMINAL'] = (bool)$request->CREATES_TERMINAL;
            $ingestion_source->fill(array_merge(request()->all(), $options));
            $ingestion_source->updatedBy()->associate(Auth::id());
            $ingestion_source->save();

            return $this->returnPath('admin::dbt.ingestion_sources.index')->withAlerts([
                ['message' => trans('DBT/ingestion_sources.edit.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/ingestion_sources.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }


    /**
     * @param $id
     * @return View|void
     */
    public function show($id)
    {
        try {
            $ingestion_source = IngestionSource::find($id);
            $this->authorize('view', $ingestion_source);
            $mappings = AttributeHeaderMapping::where('attribute_header_mappings.ingestion_source_id', $id)->search(['search'])->sortable('sort_attribute_header_mappings')->paginate();
            return view('dbt.ingestion_sources.show', compact('ingestion_source', 'mappings'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }
}
