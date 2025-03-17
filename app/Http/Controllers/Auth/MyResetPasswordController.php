<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\AppConfiguration;
use App\Auth\User;
use App\Http\Controllers\Controller;
use App\LftRouting\RoutingManager;
use App\Rules\PasswordComplexity;
use App\Rules\PasswordHistories;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MyResetPasswordController extends controller
{

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        $passwordReset = AppConfiguration::current(true)->isPasswordResetEnabled();

        if (!$passwordReset) {
            abort(403, trans('common.http_err.403'));
        }

        return view('layouts.adminlte.password-recovery');
    }

    /**
     * @param Request $request
     * @param $token
     *
     * Show the form to reset password providing the proper token
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('layouts.adminlte.password-reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * @param Request $request
     *
     * Provide logic to send reset password token via mail to user
     *
     * @return RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $account = User::where('user', $request->user)->firstOrFail();

            if (!$account->authType->driverInstance->canResetPwd()) {
                return redirect()->back()->with(['alerts' => [
                    ['message' => trans('auth.pwd_reset_unavailable'), 'type' => 'danger']]]);
            }

            if (($error = $account->sendPasswordRecovery()) !== true) {
                return redirect()->back()->with(['alerts' => [
                    ['message' => $error, 'type' => 'danger']]]);
            }
        } catch (ModelNotFoundException) {
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('auth.invalid_user'), 'type' => 'danger']]]);
        }

        return redirect()->back()->with(['alerts' => [
            ['message' => trans('passwords.change.mail_sent'), 'type' => 'success']]]);
    }

    /**
     * @param Request $request
     *
     * Provide logic to actual change password with the new one provided by the user
     *
     * @return RedirectResponse
     */
    public function reset(Request $request)
    {
        $token = request('token');
        $user = request('user');

        $check_user = User::whereHas('passwordRecovery', function ($query) use ($user) {
            return $query->where('user', $user);
        })->first();

        $check_token = User::whereHas('passwordRecovery', function ($query) use ($token) {
            return $query->where('token', $token);
        })->first();

        if (!$check_user) {
            Log::channel('auth')->info('Unable to validate password reset user+token: invalid user', ['user' => $user]);
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('passwords.resets.invalid_user'), 'type' => 'danger']]]);
        }

        if (!$check_token) {
            Log::channel('auth')->info('Unable to validate password reset user+token: invalid token', ['user' => $user, 'token' => $token]);
            return redirect()->back()->with(['alerts' => [
                ['message' => trans('passwords.resets.invalid_token'), 'type' => 'danger']]]);
        }

        $user = User::where('user', $user)->first();
        $driver = $user->authType->driverInstance;

        if (!$driver->canResetPwd()) {

            return redirect()->back()->with(['alerts' => [
                ['message' => trans('Reset pwd disabled'), 'type' => 'danger']]]);
        }
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'same:password_confirmation',
                new PasswordComplexity($driver),
                new PasswordHistories($user->id)
            ],
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->onlyInput('user');
        }
        try {
            if (($driver->resetPassword($user, $request->all())) !== true) {
                return redirect()->back()->with(['alerts' => [
                    ['message' => trans('Errore al cambio pwd'), 'type' => 'danger']]])->onlyInput('user');
            }
        } catch (Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
            return redirect()->back()->with(['alerts' => [
                ['message' => $e->getMessage(), 'type' => 'danger']]])->onlyInput('user');
        }

        try {
            $user->passwordRecovery()->delete();
        } catch (Exception ) {
            Log::channel('auth')->error('Unable to clear password reset request. Password changed anyway.');
        }

        return redirect()->route(RoutingManager::adminLoginRoute())->with(['alerts' => [
            ['message' => trans('passwords.change.success'), 'type' => 'success']]]);
    }
}
