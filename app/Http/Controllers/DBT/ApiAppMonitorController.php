<?php

namespace App\Http\Controllers\DBT;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class ApiAppMonitorController extends Controller
{
    public function app()
    {
        try{
            Artisan::call('about --json');
            $status = json_decode(Artisan::output(),true);
            if(isset($status['environment']['maintenance_mode'])){
                if(!$status['environment']['maintenance_mode']){
                    return response()->json(['status' => 'OK', 'output' => 'App is running']);
                }
                return response()->json(['status' => 'ERROR', 'output' => 'Application is down for maintenance']);
            }else{
                return response()->json(['status' => 'ERROR', 'output' => 'Generic exception']);
            }
        }catch (Exception $e){
            return response()->json(['status' => 'ERROR', 'output' => $e->getMessage()]);
        }
    }

    public function asyncTasks()
    {
        try {
            if (! $masters = app(MasterSupervisorRepository::class)->all()) {
                $status = 'inactive';
            }else{
                $status = collect($masters)->every(function ($master) {
                    return $master->status === 'paused';
                }) ? 'paused' : 'running';
            }

            return match ($status) {
                'running' => response()->json(['status' => 'OK', 'output' => '']),
                'paused' => response()->json(['status' => 'ERROR', 'output' => 'Task scheduling is paused']),
                'inactive' => response()->json(['status' => 'ERROR', 'output' => 'Task scheduling is down']),
                default => response()->json(['status' => 'ERROR', 'output' => 'Generic exception']),
            };
        } catch (Exception $e) {
            return response()->json(['status' => 'ERROR', 'output' => $e->getMessage()]);
        }
    }
}
