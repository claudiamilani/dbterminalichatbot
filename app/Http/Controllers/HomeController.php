<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers;

use App\AppConfiguration;
use App\LftRouting\RoutingManager;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HomeController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return Factory|Application|View
     */
    public function index()
    {
        return view('admin.index');
    }

    /**
     * Collapse and un-collapse the sidebar
     * @return string
     */
    public function toggleSidebar()
    {
        (session('collapseSidebar')) ? (session(['collapseSidebar' => 0])) : (session(['collapseSidebar' => 1]));
        return 'collapseSidebar = ' . session('collapseSidebar');
    }

    /**
     * Show the manual
     * @return RedirectResponse|BinaryFileResponse
     */
    public function viewManual()
    {
        try {
            $app_config = AppConfiguration::firstOrFail();
            if (!empty($app_config->manual_file_path)) {
                return response()->file(storage_path('app' . DIRECTORY_SEPARATOR . $app_config->manual_file_path));
            } else {
                Log::channel('admin_gui')->info('Manual file path in app configuration is missing.');
                return redirect()->route(RoutingManager::adminHome())->with(['alerts' => [
                    ['message' => trans('common.file_404'), 'type' => 'error']]]);
            }
        } catch (Exception $e) {
            Log::channel('admin_gui')->error($e->getMessage());
            return redirect()->route(RoutingManager::adminHome())->with(['alerts' => [
                ['message' => trans('common.file_404'), 'type' => 'error']]]);
        }
    }
}
