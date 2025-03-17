<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\AttrCategory;
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
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AttrCategoryController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    /**
     * Provides the translation file to use when translating attribute categories
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/attr_categories';
    }

    /**
     * The view for listing attribute categories
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', arguments: AttrCategory::class);
            $attr_categories = AttrCategory::search(['search', 'published'])->sortable(['display_order' => 'asc'])->paginate();

            $published = collect([0 => trans('common.no'), 1 => trans('common.yes')])->withFilterLabel(trans('DBT/attr_categories.attributes.published'));
            return view('dbt.attr_categories.index', compact('attr_categories', 'published'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to create a new attribute category
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function create()
    {
        try {
            $this->authorize('create', AttrCategory::class);
            return view('dbt.attr_categories.create');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     *  Creates a new attribute category and stores the passed values
     *
     * @param Request $request
     * @return RedirectResponse|never
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', AttrCategory::class);
            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required|string|max:255',
                'description' => 'string|max:65535|nullable',
                'display_order' => 'nullable|integer|min:0|max:100',
                'published' => 'integer|min:0|max:1',
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $category = new AttrCategory();
            $category->fill(request()->all());
            $category->createdBy()->associate(Auth::id());
            $category->updatedBy()->associate(Auth::id());
            $category->save();

            return $this->returnPath('admin::dbt.attr_categories.index')->withAlerts([
                ['message' => trans('DBT/attr_categories.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/attr_categories.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }

    /**
     * The view to edit an existing attribute category
     *
     * @param int $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $id)
    {
        try {
            $attr_category = AttrCategory::findOrFail($id);
            $this->authorize('update', $attr_category);
            return view('dbt.attr_categories.edit', compact('attr_category'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }


    /**
     * Edits an existing attribute category and stores the passed values
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|never
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update(Request $request, $id)
    {
        $category = AttrCategory::findOrFail($id);
        try {
            $this->authorize('update', $category);
            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required|string|max:255',
                'description' => 'string|max:65535|nullable',
                'display_order' => 'nullable|integer|min:0|max:100',
                'published' => 'integer|min:0|max:1',
            ], [], $this->getTranslatedAttributes($attributes));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $category->update(request()->all());
            $category->updatedBy()->associate(Auth::id());
            $category->save();
            return $this->returnPath('admin::dbt.attr_categories.index')->withAlerts([
                ['message' => trans('DBT/attr_categories.edit.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/attr_categories.create.error'), 'type' => 'error']
            ])->withInput();
        }
    }


    /**
     * The view to show an attribute category
     *
     * @param $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse|\Illuminate\View\View|void
     */
    public function show($id)
    {
        try {
            $attr_category = AttrCategory::find($id);
            $this->authorize('view', $attr_category);
            return view('dbt.attr_categories.show', compact('attr_category'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('DBT/attr_categories.show.error'), 'type' => 'error']
            ]]);
        }
    }

    /**
     * The view to delete an existing attribute category
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    public function delete($id)
    {
        try {
            $attr_category = AttrCategory::findOrFail($id);
            $this->authorize('delete', $attr_category);
            return view('dbt.attr_categories.delete', compact('attr_category'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }


    /**
     * Deletes an existing attribute category
     *
     * @param $id
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroy($id)
    {
        try {
            $attr_category = AttrCategory::findOrFail($id);
            $this->authorize('delete', $attr_category);
            $attr_category->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return back()->with(['alerts' => [
                ['message' => trans('common.http_err.403'), 'type' => 'error']
            ]]);
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            Log::channel('admin_gui')->error($e->getTraceAsString());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/attr_categories.delete.error'), 'type' => 'error']
            ]]);
        }
        return $this->returnPath('admin::dbt.attr_categories.index')->with(['alerts' => [
            ['message' => trans('DBT/attr_categories.delete.success'), 'type' => 'success']
        ]]);
    }

}
