<?php

namespace App\Http\Controllers\DBT;
use App\DBT\Models\Vendor;
use App\Http\Controllers\Controller;
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

class VendorController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/vendors';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $this->authorize('list', Vendor::class);
            $vendors = Vendor::search(['search', 'published'])->sortable(['id' => 'desc'])->paginate();

            $published = collect([0 => trans('common.no'), 1 => trans('common.yes')])->withFilterLabel(trans('DBT/vendors.attributes.published'));
            return view('dbt.vendors.index', compact('vendors', 'published'));
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
            $this->authorize('create', Vendor::class);

            return view('dbt.vendors.create');
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
            $this->authorize('create', Vendor::class);

            $validator = Validator::make($request->all(), $attributes = [
                'name' => 'required|string|unique:vendors|max:255',
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $vendor = new Vendor();
            $vendor->fill($request->all());

            $vendor->createdBy()->associate(Auth::id());
            $vendor->updatedBy()->associate(Auth::id());

            $vendor->save();

            return redirect()->route('admin::dbt.vendors.index')->withAlerts([
                ['message' => trans('DBT/vendors.create.success'), 'type' => 'success']]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/vendors.create.error'), 'type' => 'error']
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
            $vendor = Vendor::findOrFail($id);
            $this->authorize('view', $vendor);

            return view('dbt.vendors.show', compact('vendor'));
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
            $vendor = Vendor::findOrFail($id);
            $this->authorize('update', $vendor);

            return view('dbt.vendors.edit', compact('vendor'));
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
            $vendor = Vendor::findOrFail($id);
            $this->authorize('update', $vendor);

            $validator = Validator::make($request->all(), $attributes = [
                'name' => ['required', Rule::unique('vendors')->ignore($vendor->id)],
            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $vendor->fill($request->all());
            $vendor->save();

            return redirect()->route('admin::dbt.vendors.index')->withAlerts([
                ['message' => trans('DBT/vendors.edit.success'), 'type' => 'success', 'tmp' => '']
            ]);
        }  catch(AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/vendors.edit.error'), 'type' => 'error', 'tmp' => '']
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $this->authorize('delete', $vendor);

            return view('dbt.vendors.delete', compact('vendor'));
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
            $vendor = Vendor::findOrFail($id);
            $this->authorize('delete' , $vendor);

            $vendor->delete();
        }  catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.forbidden_message'));
        } catch (\Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return redirect()->back()->withAlerts([
                ['message' => trans('DBT/vendors.delete.error'), 'type' => 'error']
            ]);
        }

        return redirect()->back()->withAlerts([
            ['message' => trans('DBT/vendors.delete.success'), 'type' => 'success']
        ]);
    }

    public function select2(Request $request)
    {
        return Vendor::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE', '%' . $request->q . '%')->get();
    }
}