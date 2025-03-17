<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth;

use App\Traits\Searchable;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;

class UserSession extends Model
{
    use Searchable;

    public $incrementing = false;
    protected $table = 'sessions';
    protected $casts = ['last_activity' => 'datetime'];
    protected $fillable = ['client', 'robot'];

    /**
     * The user
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Any user that is authenticated
     * @param $query
     * @return mixed
     */
    public function scopeAuthenticated($query): mixed
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Any user that is not authenticated
     * @param $query
     * @return mixed
     */
    public function scopeUnauthenticated($query): mixed
    {
        return $query->whereNull('user_id');
    }

    /**
     * Filters query results for name, surname, email and IP address for a user
     * @param $query
     * @param $search
     * @return Builder
     */
    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->whereHas('account',function($query) use($search){
                $query->where('name','ILIKE',"%$search%")->orWhere('surname','ILIKE',"%$search%")->Orwhere('email','ILIKE',"%$search%");
            })->orWhere('ip_address','ILIKE',"%$search%");
        });
    }

    /**
     * Filter query results by searchFilter method or by provided $search filters
     * @param $query
     * @param $search
     * @return Builder
     */
    public function advancedSearchFilter($query, $search): Builder
    {
        foreach($search as $filter => $value){
            switch($filter){
                case 'search':
                case 'search_guest':
                    $query = $this->searchFilter($query,$value);
                    break;
                case 'user_agent':
                    $query = $query->whereNotNull('user_id')->where('client', $value);
                    break;
                case 'u_user_agent':
                    $query = $query->whereNull('user_id')->where('client', $value);
                    break;
                case 'robot':
                    $query = $query->whereNotNull('user_id')->where('robot', $value);
                    break;
                case 'u_robot':
                    $query = $query->whereNull('user_id')->where('robot', $value);
                    break;
            }
        }
        return $query;
    }

    /**
     * Return the type of browser and version for a user session
     * @return string|null
     */
    public function getBrowserAttribute(): ?string
    {
        try {
            $browser = Agent::browser($this->user_agent);
            $browser_version = Agent::version($browser);

            return "$browser $browser_version";
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Return the OS and version for a user session
     * @return string|null
     */
    public function getOSAttribute(): ?string
    {
        try{
            $platform = Agent::platform($this->user_agent);
            $platform_version = Agent::version($platform);
            return "$platform $platform_version";
        } catch(Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Return if the user session is a robot or not
     * @return Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
     */
    public function getIsRobotAttribute(): Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        try {
            $isRobot = Agent::isRobot($this->user_agent);

            return $isRobot ? trans('common.yes') : trans('common.no');
        } catch(Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * Return the expiry time for a user session
     * @return string
     */
    public function getExpiringInAttribute(): string
    {
        try {
            return $this->last_activity->addMinutes(config('session.lifetime'))->diff(Carbon::now())->format('%hh %imin');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return 'N/A';
        }
    }

    /**
     * Return the last page a user was on for the user session
     * @return mixed
     */
    public function getLastPageAttribute(): mixed
    {
        try {
            $session = base64_decode($this->payload);
            if(config('session.encrypt')){
                $session = decrypt($session);
            }
            $session = unserialize($session);
            return $session['_previous']['url'] ?? '';
        } catch(Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return 'N/A';
        }
    }
}
