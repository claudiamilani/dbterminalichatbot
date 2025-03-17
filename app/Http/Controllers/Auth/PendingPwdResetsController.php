<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

use App\Auth\PasswordRecovery;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PendingPwdResetsController extends Controller
{
    public function index()
    {
        try{
            $this->authorize('list',PasswordRecovery::class);
            $pending_pwd_resets = PasswordRecovery::with('account')->search()->paginate();
            return view('pending_pwd_resets.index',compact('pending_pwd_resets'));
        }catch(AuthorizationException $e){
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        }

    }

    public function show($id)
    {
        try{
            $pending_pwd_reset = PasswordRecovery::with('account')->findOrFail($id);
            $host = request()->getSchemeAndHttpHost();
            $this->authorize('view',$pending_pwd_reset);
            return view('pending_pwd_resets.show',compact('pending_pwd_reset', 'host'));
        }catch (AuthorizationException $e){
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }catch(ModelNotFoundException $e){
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.generic_modal', ['message' => trans('common.record_404')]);
        }

    }

    public function delete($id)
    {
        try{
            $pending_pwd_reset = PasswordRecovery::with('account')->findOrFail($id);
            $this->authorize('delete',$pending_pwd_reset);
            return view('pending_pwd_resets.delete',compact('pending_pwd_reset'));
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            return view('errors.403_modal', ['message' => trans('common.http_err.403')]);
        }

    }

    public function destroy($id)
    {
        try {
            $pending_pwd_reset = PasswordRecovery::with('account')->findOrFail($id);
            $this->authorize('delete', $pending_pwd_reset);
            $pending_pwd_reset->delete();
            Log::channel('admin_gui')->info('Password recovery request for account '.$pending_pwd_reset->account->user.' deleted by '.Auth::user()->user);
            Log::channel('auth')->info('Password recovery request for account '.$pending_pwd_reset->account->user.' deleted by '.Auth::user()->user);
        } catch (AuthorizationException $e) {
            Log::channel('admin_gui')->info($e->getMessage());
            abort(403, trans('common.http_err.403'));
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return back()->with(['alerts' => [
                ['message' => trans('pending_pwd_resets.delete.error'), 'type' => 'error']
            ]]);
        }
        return redirect()->route('admin::pending_pwd_resets.index')->with(['alerts' => [
            ['message' => trans('pending_pwd_resets.delete.success'), 'type' => 'success']
        ]]);
    }
}
