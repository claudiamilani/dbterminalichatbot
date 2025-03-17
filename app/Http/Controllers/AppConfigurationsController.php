<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers;

use App\AppConfiguration;
use App\Auth\PasswordHistory;
use App\Traits\TranslatedValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppConfigurationsController extends Controller
{
    use TranslatedValidation;

    /**
     * Provides the translation file to use when translating attributes
     * @return string
     */
    public function getTranslationFile(): string
    {
        return 'app_configuration';
    }

    /**
     * The view to show the app configuration
     * @return View
     */
    public function show(): View
    {
        try {
            $this->authorize('view', AppConfiguration::class);
            $app_config = AppConfiguration::current();
            $app_config->manual_file_path = Storage::exists($app_config->manual_file_path ?? '') ? $app_config->manual_file_path : '';
            return view('app_configuration.show', compact('app_config'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to edit the app configuration
     * @return View
     */
    public function edit(): View
    {
        try {
            $this->authorize('update', AppConfiguration::class);
            $app_config = AppConfiguration::current(true);
            $app_config->manual_file_path = Storage::exists($app_config->manual_file_path ?? '') ? $app_config->manual_file_path : '';
            return view('app_configuration.edit', compact('app_config'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Edits the app configuration and stores the passed values
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $this->authorize('update', AppConfiguration::class);
            $app_config = AppConfiguration::current();
            $validator = Validator::make($request->all(), $attributes = [
                'max_failed_login_attempts' => [
                    'required',
                    function($attribute, $value, $fail){
                        if($value < 0){
                            $fail(trans('app_configuration.attributes.max_failed_login_attempts') . ' deve essere un numero maggiore o uguale a 0.');
                        }
                    }
                ],
                'failed_login_reset_interval' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value < 1) {
                            $fail(trans('app_configuration.attributes.failed_login_reset_interval') . ' deve essere un numero maggiore o uguale a 1.');
                        }
                    }
                ],
                'pwd_min_length' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value < 8) {
                            $fail(trans('app_configuration.attributes.pwd_min_length') . ' deve essere un numero maggiore o uguale a 8.');
                        }
                    }
                ],
                'pwd_regexp' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        try {
                            preg_match($value, null);
                        } catch (Exception) {
                            $fail(trans('app_configuration.attributes.pwd_regexp') . ' non è una espressione regolare valida.');
                        }
                    }],
                'pwd_history' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value <= 0 || $value > 99) {
                            $fail(trans('app_configuration.attributes.pwd_history') . ' deve essere un numero maggiore a 0 e minore a 99');
                        }
                    }
                ],
                'pwd_expires_in' => [
                    function ($attribute, $value, $fail) {
                        if ($value <= 0 || $value > 99) {
                            $fail(trans('app_configuration.attributes.pwd_expires_in') . ' deve essere un numero maggiore a 0 e minore a 99');
                        }
                    }
                ],
                'pwd_complexity_err_msg' => 'required',

            ], [], $this->getTranslatedAttributes($attributes));

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $app_config->fill($request->except('pwd_history'));
            if (!empty ($file = $request->manual_file_path)) {
                if (!$storagePath = $file->storeAs(config('lft.manuals_path'), (str_slug($request->manual_file_name ?: 'ManualeCliente')) . '.' . $file->getClientOriginalExtension())) {
                    Log::channel('admin_gui')->error('Error uploading file.');
                    return redirect()->back()->with(['alerts' => [
                        ['message' => trans('app_configuration.edit.error'), 'type' => 'error']
                    ]])->withInput();
                }
                if ($app_config->manual_file_path && $app_config->manual_file_path != $storagePath) {
                    try {
                        Storage::delete($app_config->manual_file_path);
                    } catch (Exception $e) {
                        Log::channel('admin_gui')->debug('Previous file not found: ' . $app_config->manual_file_path . '.' . $e->getMessage());
                    }
                }
                $app_config->manual_file_path = $storagePath;
            }
            if ($request->pwd_history < $app_config->pwd_history) {
                try {
                    $users = PasswordHistory::distinct()->select(['user_id'])->get();
                    foreach ($users as $user) {
                        $passwords = PasswordHistory::where('user_id', $user->user_id)->get();
                        $number_to_delete = $request->pwd_history - $passwords->count();
                        if (abs($number_to_delete) > 0 && $passwords->count() >= $request->pwd_history) {
                            PasswordHistory::where('user_id', $user->user_id)->orderBy('id', 'ASC')->take(abs($number_to_delete))->delete();
                            Log::channel('admin_gui')->info('deleted '.abs($number_to_delete). ' passwords for '.$user->user_id);
                        }
                    }
                } catch (Exception $e) {
                    Log::channel('admin_gui')->error($e->getMessage());
                    return redirect()->back()->with(['alerts' => [
                        ['message' => trans('app_configuration.edit.error'), 'type' => 'error']
                    ]])->withInput();
                }
            }
            $app_config->pwd_history = $request->pwd_history;
            $app_config->save();

            if (!$app_config->isPasswordResetEnabled()) {
                try {
                    Log::channel('admin_gui')->info('Deleting all Password Recoveries');
                    DB::table('password_recoveries')->delete();
                } catch (Exception $e) {
                    Log::channel('admin_gui')->error($e->getMessage());
                    return redirect()->back()->with(['alerts' => [
                        ['message' => trans('app_configuration.edit.error'), 'type' => 'error']
                    ]])->withInput();
                }
            }

            return redirect()->route('admin::app_configuration.show')->with(['alerts' => [
                ['message' => trans('app_configuration.edit.success'), 'type' => 'success']
            ]]);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('app_configuration.edit.error'), 'type' => 'error']
            ]])->withInput();
        }
    }

    /**
     * Shows the uploaded manual
     * @return RedirectResponse|BinaryFileResponse
     */
    public function viewManual()
    {
        try {
            $app_config = AppConfiguration::firstOrFail();
            return response()->file(storage_path('app' . DIRECTORY_SEPARATOR . $app_config->manual_file_path));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->route('admin::app_configuration.show')->with(['alerts' => [
                ['message' => trans('common.file_404'), 'type' => 'error']]]);
        }
    }
}

