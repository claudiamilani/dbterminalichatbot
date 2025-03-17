<?php

namespace App\Http\Controllers\DBT;

use App\DBT\Models\Terminal;
use App\DBT\Models\TerminalPicture;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TerminalPictureController extends Controller
{
    use TranslatedValidation, ControllerPathfinder;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'DBT/terminal_pictures';
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
     * @return void
     */
    public function show()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $terminal_id
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View|void
     */
    public function create($terminal_id)
    {
        try {
            $terminal = Terminal::findOrFail($terminal_id);
            $this->authorize('create', TerminalPicture::class);

            return view('dbt.terminals.terminal_pictures.create', compact('terminal'));
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
    public function store(Request $request, $terminal_id)
    {
        try {
            // policy check
            try {
                $this->authorize('create', TerminalPicture::class);
            } catch (AuthorizationException $e) {
                Log::channel('admin_gui')->info($e->getMessage(),
                    ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);

                return back()->with([
                    'alerts' => [
                        ['message' => trans('common.http_err.403'), 'type' => 'error']
                    ]
                ]);
            }

            // file presence check
            try {
                if (!$request->hasFile('file_path')) {
                    throw new Exception('DBT/terminal_pictures.create.creation_errors.no_file_request');
                }

                $file = $request->file('file_path');
            } catch (Exception $e) {
                Log::channel('admin_gui')->info($e->getMessage(),
                    ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);

                return back()->with([
                    'alerts' => [
                        ['message' => trans('DBT/terminal_pictures.create.error'), 'type' => 'error']
                    ]
                ]);
            }

            // saving data and record process
            try {
                $validator = Validator::make($request->all(), $attributes = [
                    'title' => 'nullable|string|max:255',
                    'file_path' => 'required|image|mimes:jpg,gif,bmp,png',
                    'display_order' => 'nullable|integer|min:0|max:100',
                ], [], $this->getTranslatedAttributes($attributes));

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $terminal = Terminal::find($terminal_id);

                if (!$terminal) {
                    throw new Exception('DBT/terminal_pictures.create.creation_errors.terminal_not_found');
                }

                // retrieve extension from file name and assign uuid to file
                $extension = $file->clientExtension();
                $fileName = uuid_create() . '.' . $extension;

                // save the file
                $filePath = $file->storeAs('/' . $terminal->id, $fileName, 'terminal-pictures');

                if (!$filePath) {
                    throw new Exception('DBT/terminal_pictures.create.creation_errors.file_not_saved');
                }

                // set title, if not provided uses file name without extension as fallback
                $file_noExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $title = $request->input('title') ?? $file_noExt;

                // creates new record for the file
                $picture = new TerminalPicture();

                $picture->terminal()->associate($terminal);
                $picture->fill($request->all());
                $picture->title = $title;
                $picture->file_path = $filePath;
                $picture->createdBy()->associate(Auth::id());
                $picture->updatedBy()->associate(Auth::id());
                $picture->save();

                return $this->returnPath('admin::dbt.terminals.show', [$picture->terminal_id])->withAlerts([
                    ['message' => trans('DBT/terminal_pictures.create.success'), 'type' => 'success']
                ])->withFragment(str_slug(trans('DBT/terminal_pictures.title')));

            } catch (Exception $e) {
                Log::channel('admin_gui')->info($e->getMessage(),
                    ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);

                return back()->with([
                    'alerts' => [
                        ['message' => trans('DBT/terminal_pictures.create.error'), 'type' => 'error']
                    ]
                ]);
            }
        } catch (Exception $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);

            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_pictures.create.error'), 'type' => 'error']
                ]
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $terminal_id
     * @param int $picture_id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function edit(int $terminal_id, int $picture_id)
    {
        try {
            $picture = TerminalPicture::findOrFail($picture_id);
            $terminal = Terminal::findOrFail($terminal_id);

            $this->authorize('update', $picture);

            return view('dbt.terminals.terminal_pictures.edit', compact('picture', 'terminal'));
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
     * @param int $terminal_id
     * @param int $picture_id
     * @return RedirectResponse
     */
    public function update(Request $request, int $terminal_id, int $picture_id)
    {
        // policy check
        try {
            $picture = TerminalPicture::findOrFail($picture_id);
            Terminal::findOrFail($terminal_id);

            $this->authorize('update', $picture);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        }

        $validator = Validator::make($request->all(), $attributes = [
            'title' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0|max:100',
        ], [], $this->getTranslatedAttributes($attributes));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // saving record
        try {
            $picture = TerminalPicture::find($picture_id);

            $picture->update($request->except('title'));

            // if title is provided updates it otherwise excludes it from updating
            if ($request->filled('title')) {
                $picture->title = $request->input('title');
            }

            $picture->updatedBy()->associate(Auth::id());

            $picture->save();
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with([
                'alerts' => [
                    ['message' => trans('DBT/attributes.create.error'), 'type' => 'error']
                ]
            ])->withInput();
        }

        return $this->returnPath('admin::dbt.terminals.show', [$picture->terminal_id])->withAlerts([
            ['message' => trans('DBT/terminal_pictures.edit.success'), 'type' => 'success']
        ])->withFragment(str_slug(trans('DBT/terminal_pictures.title')));
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param int $terminal_id
     * @param int $picture_id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function delete(int $terminal_id, int $picture_id)
    {
        try {
            $picture = TerminalPicture::findOrFail($picture_id);
            Terminal::findOrFail($terminal_id);

            $this->authorize('delete', $picture);

            return view('dbt.terminals.terminal_pictures.delete', compact('picture'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $picture_id
     * @param int $terminal_id
     * @return RedirectResponse
     */
    public function destroy(int $terminal_id, int $picture_id)
    {
        try {
            $picture = TerminalPicture::findOrFail($picture_id);
            Terminal::findOrFail($terminal_id);
            $this->authorize('delete', $picture);
            $filePath = $picture->file_path;

            if (Storage::disk('terminal-pictures')->exists($filePath)) {
                Storage::disk('terminal-pictures')->delete($filePath);
            }

            $picture->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage(),
                ['user_id' => Auth::id(), 'user' => optional(Auth::user())->user, 'method' => __METHOD__]);
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with([
                'alerts' => [
                    ['message' => trans('DBT/terminal_pictures.delete.error'), 'type' => 'error']
                ]
            ]);
        }

        return $this->returnPath('admin::dbt.terminals.show', [$picture->terminal_id])->withAlerts([
            ['message' => trans('DBT/terminal_pictures.create.success'), 'type' => 'success']
        ])->withFragment(str_slug(trans('DBT/terminal_pictures.title')));
    }
}
