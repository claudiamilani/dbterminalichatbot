<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Observers;

use App\AppConfiguration;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AppConfigurationObserver
{
    public function saved(AppConfiguration $appConfiguration): void
    {
        try{
            Cache::forever('app_configuration',$appConfiguration->refresh());
        }catch(Exception $e){
            Log::error('Unable to cache App configuration: '.$e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
