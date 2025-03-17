<?php

use App\Http\Controllers\DBT\ApiAppMonitorController;
use App\Http\Controllers\DBT\ApiTerminalController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // TERMINAL WIND
    Route::get('/terminals/imei/{imei}', [ApiTerminalController::class, 'getTerminalWind'])
        ->name('getTerminalWind');
    Route::get('/monitor/mdm-dbterminali', [ApiAppMonitorController::class, 'app'])
        ->name('getTerminalWind');
    Route::get('/monitor/mdm-mdmconsole', [ApiAppMonitorController::class, 'app'])
        ->name('getTerminalWind');
    Route::get('/monitor/mdm-ingestionfile', [ApiAppMonitorController::class, 'asyncTasks'])
        ->name('getTerminalWind');
});
