<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PDOException;

class AppConfiguration extends Model
{
    protected $fillable = ['pwdr_mail_obj_u','pwdr_mail_body_u','max_failed_login_attempts'
        ,'failed_login_reset_interval','pwd_reset_unlocks_account','pwd_min_length','pwd_regexp','pwd_complexity_err_msg','pwd_history','pwd_expires_in','pwd_never_expires','manual_file_path','manual_file_name','allow_pwd_reset'];

    public static function current($refresh = false)
    {
        try{
            if(($cached = Cache::get('app_configuration')) && !$refresh){
                return $cached;
            }
        }catch (\RedisException $e){
            Log::critical('Could not connect to redis: '.$e->getMessage());
            Log::critical($e->getTraceAsString());
            return null;
        }

        try {
            if (Schema::hasTable('app_configurations')) {
                Cache::put('app_configuration',$cached = self::firstOrFail(),60);
                return $cached;
            }
        } catch(PDOException $e) {
            Log::critical('Could not connect to database: '.$e->getMessage());
            Log::critical($e->getTraceAsString());
            return null;
        }

        return new AppConfiguration();
    }

    /**
     * @return bool
     */
    public function isPasswordResetEnabled(): bool
    {
        return $this->allow_pwd_reset == 1;
    }

    /**
     * @return bool
     */
    public function isPasswordExpiresEnabled(): bool
    {
        return $this->pwd_never_expires == 0;
    }
}
