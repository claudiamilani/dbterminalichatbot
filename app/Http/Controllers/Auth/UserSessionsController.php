<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\UserSession;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserSessionsController extends Controller
{
    /**
     * The view for listing user sessions
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function index()
    {
        try {
            $this->authorize('list', UserSession::class);
            if(config('session.driver') != 'custom-database'){
                abort(404, trans('user_sessions.session_driver_error'));
            }
            $sessions = UserSession::authenticated()->search(['search', 'user_agent', 'robot'])->paginate();
            $u_sessions = UserSession::unauthenticated()->search(['search_guest', 'u_user_agent', 'u_robot'])->paginate(null, ['*'], 'guests_page');
            $user_agents_json = UserSession::authenticated()->distinct()->get(['client']);
            $user_agents = [];

            foreach ($user_agents_json as $user_agent) {
                $user_agents[$user_agent->client] = $user_agent->client;
            }

            $u_user_agents_json = UserSession::unauthenticated()->distinct()->get(['client']);
            $u_user_agents = [];

            foreach ($u_user_agents_json as $user_agent) {
                $u_user_agents[$user_agent->client] = $user_agent->client;
            }

            return view('user_sessions.index',compact('sessions','u_sessions', 'user_agents', 'u_user_agents'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }

    }

    /**
     * The view to delete an existing user session
     * @param $id
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function delete($id)
    {
        try {
            $session = UserSession::findOrFail($id);
            $this->authorize('delete', $session);
            return view('user_sessions.delete', compact('session'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to purge authenticated user sessions
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function purgeAuthenticated()
    {
        try {
            $this->authorize('purge', UserSession::class);
            return view('user_sessions.purge');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * The view to purge unauthenticated user sessions
     * @return Application|Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|View
     */
    public function purgeUnauthenticated()
    {
        try {
            $this->authorize('purge', UserSession::class);
            return view('user_sessions.purgeUnauthenticated');
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }
    }

    /**
     * Deletes an existing user session
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $session = UserSession::findOrFail($id);
            $this->authorize('delete', $session);
            $session->delete();
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('user_sessions.delete.error'), 'type' => 'error']
            ]]);
        }

        return redirect()->route('admin::user_sessions.index')->with(['alerts' => [
            ['message' => trans('user_sessions.delete.success'), 'type' => 'success']
        ]]);
    }

    /**
     * Purges authenticated user sessions
     * @param string $authenticated
     * @return RedirectResponse
     */
    public function purge(string $authenticated = 'authenticated')
    {
        try {
            $this->authorize('purge', UserSession::class);
            if($authenticated == 'authenticated'){
                UserSession::where('id','<>',Session::getId())->whereNotNull('user_id')->delete();
            }else{
                UserSession::whereNull('user_id')->delete();
            }

        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('user_sessions.purge.error'), 'type' => 'error']
            ]]);
        }

        return redirect()->route('admin::user_sessions.index')->with(['alerts' => [
            ['message' => trans('user_sessions.purge.success'), 'type' => 'success']
        ]]);
    }
}
