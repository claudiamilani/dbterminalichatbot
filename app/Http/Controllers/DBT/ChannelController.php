<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Channel;
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

class ChannelController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    public function getTranslationFile(): string
    {
        return 'DBT/channels';
    }

    public function index()
    {
        try {
            $this->authorize('list', Channel::class);
            $channels = Channel::search(['search'])->sortable(['id' => 'asc'])->paginate();

            return view('dbt.channels.index', compact('channels'));

        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function show($id)
    {
        try {
            $channel = Channel::findOrFail($id);
            $this->authorize('view', $channel);
            return view('dbt.channels.show', compact('channel'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function create()
    {
        $this->authorize('create', Channel::class);

        try {
            return view('dbt.channels.create');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', Channel::class);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'name' => 'required|unique:channels|max:255',
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $channel = new Channel();
            $channel->fill($request->all());
            $channel->createdBy()->associate(Auth::user());
            $channel->updatedBy()->associate(Auth::user());
            $channel->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/channels.create.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.channels.show', $channel->id)->with(['alerts' => [
            ['message' => trans('DBT/channels.create.success'), 'type' => 'success']
        ]]);
    }

    public function edit($id)
    {
        $channel = Channel::findOrFail($id);
        $this->authorize('update', $channel);

        try {
            return view('dbt.channels.edit', compact('channel'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }
    }

    public function update(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);
        try {
            $this->authorize('update', $channel);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'name' => ['required', 'string', 'max:255', Rule::unique('channels')->ignore($channel->id)],
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $channel->fill($request->all());
            $channel->updatedBy()->associate(Auth::user());
            $channel->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/channels.edit.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.channels.show', $channel->id)->with(['alerts' => [
            ['message' => trans('DBT/channels.edit.success'), 'type' => 'success']
        ]]);
    }

    public function delete($id)
    {
        try {
            $channel = Channel::findOrFail($id);
            $this->authorize('delete', $channel);
            return view('dbt.channels.delete', compact('channel'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    public function destroy($id)
    {
        try {
            $channel = Channel::findOrFail($id);
            $this->authorize('delete', $channel);
            $channel->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(), ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('DBT/channels.delete.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::dbt.channels.index')->with(['alerts' => [
            ['message' => trans('DBT/channels.delete.success'), 'type' => 'success']
        ]]);
    }

    public function select2(Request $request)
    {
        return Channel::select('id', 'name as text', 'id as existing')->where('name', 'ILIKE', '%' . $request->q . '%')->get();
    }
}
