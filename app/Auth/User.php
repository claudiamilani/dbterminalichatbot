<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth;

use App\AppConfiguration;
use App\Notifications\MyResetPassword;
use App\Traits\Activable;
use App\Traits\CanResetPassword;
use App\Traits\HasRoles;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasRoles, Notifiable, Searchable, Activable, CanResetPassword, Sortable, HasApiTokens, HasFactory;
    protected $casts = ['login_success_on' => 'datetime', 'login_failed_on' => 'datetime', 'pwd_changed_at' => 'datetime'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable =
        [
            'name', 'surname', 'user', 'email', 'password', 'api_token', 'enabled', 'enabled_from', 'enabled_to',
            'login_success_on', 'login_success_ipv4', 'login_failed_on', 'login_failed_ipv4',
            'failed_login_count', 'locked', 'pwd_change_required','auth_type_id'
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden =
        [
            'password', 'remember_token', 'api_token'
        ];

    /**
     * Creates a new user with fake information
     * @return UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * Checks if the user account is locked
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked == 1;
    }

    /**
     * Sends a password reset notification via email
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify((new MyResetPassword($token, request()->getHost())));
    }

    /**
     * Return roles for a user
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Return authentication type for user
     * @return BelongsTo
     */
    public function authType(): BelongsTo
    {
        return $this->belongsTo(AuthType::class);
    }

    /**
     * Return the fullname of the user
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "$this->name $this->surname";
    }

    /**
     * Return the fullname of the user in reverse, surname first
     * @return string
     */
    public function getFullNameReversedAttribute(): string
    {
        return $this->surname . ' ' . $this->name;
    }

    /**
     * Return the fullname of the user along with their email
     * @return string
     */
    public function getNameWithMailAttribute(): string
    {
        return $this->getFullNameAttribute() . ' (' . $this->email . ')';
    }

    /**
     * Checks if the user is an admin
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrator');
    }

    /**
     * Format the enabled_from date or return null if none present
     * @param $value
     * @return void
     */
    public function setEnabledFromAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['enabled_from'] = Carbon::createFromFormat('d/m/Y H:i', $value);
        } else {
            $this->attributes['enabled_from'] = null;
        }
    }

    /**
     * Format the enabled_to date or return null if none present
     * @param $value
     * @return void
     */
    public function setEnabledToAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['enabled_to'] = Carbon::createFromFormat('d/m/Y H:i', $value);
        } else {
            $this->attributes['enabled_to'] = null;
        }
    }

    /**
     * Filters query results for name, surname and email
     * @param $query
     * @param $search
     * @return Builder
     */
    public function searchFilter($query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'ILIKE', "%$search%")->orWhere('surname', 'ILIKE', "%$search%")->orWhere('email', 'ILIKE', "%$search%");
        });
    }

    /**
     * Filter query results by searchFilter method or by provided $search filters
     * @param $query
     * @param $search
     * @return Builder|mixed
     */
    public function advancedSearchFilter($query, $search): mixed
    {
        foreach ($search as $filter => $value) {
            switch ($filter) {
                case 'search':
                    $query = $this->searchFilter($query, $value);
                    break;
                case 'crm_role':
                    if ($value !== '-') {
                        $query = $this->searchFilter($query, $value);
                    }
            }
        }
        return $query;
    }

    /**
     * Return the type of browser and version that was used for a successfull login
     * @return string|null
     */
    public function getSuccessLoginBrowserAttribute(): ?string
    {
        try {
            $browser = $this->user_agent_success ? Agent::browser($this->user_agent_success) : null;
            $browser_version = $this->user_agent_success ? Agent::version($browser) : null;
            return "$browser $browser_version";
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * Return the type of browser and version that was used for an unsuccessfull login
     * @return string|null
     */
    public function getFailedLoginBrowserAttribute(): ?string
    {
        try {
            $browser = $this->user_agent_failed ? Agent::browser($this->user_agent_failed) : null;
            $browser_version = $this->user_agent_failed ? Agent::version($browser) : null;
            return "$browser $browser_version";
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * Deletes older user passwords based on app configuration password history retention setting and creates a new one
     * @param $user
     * @return void
     */
    public function checkPasswordHistories($user): void
    {
        $app_config = AppConfiguration::current();

        if ($user->passwordHistories()->count() >= $app_config->pwd_history && $user->passwordHistories()->count() > 0 || $app_config->pwd_history == 1) {
            $user->PasswordHistories()->orderBy('id', 'ASC')->first()->delete();
            Log::channel('auth')->info('Oldest password for ' . $user->user . ' deleted');
        }

        $passwordHistory = PasswordHistory::create([
            'user_id' => $user->id,
            'password' => $user->password,
        ]);
        $passwordHistory->save();
    }

    /**
     * Checks if a user must change their password
     * @return bool
     */
    public function mustChangePassword(): bool
    {
        return $this->pwd_change_required == 1;
    }

    /**
     * Return the password recovery for a user
     * @return HasOne
     */
    public function passwordRecovery(): HasOne
    {
        return $this->hasOne(PasswordRecovery::class);
    }

    /**
     * Return the password histories for a user
     * @return HasMany
     */
    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    /**
     * Any user that must change password
     * @param $query
     * @return mixed
     */
    public function scopeMustChangePassword($query): mixed
    {
        return $query->where('pwd_change_required', 1);
    }

    /**
     * Any user that must not change password
     * @param $query
     * @return mixed
     */
    public function scopeMustNotChangePassword($query): mixed
    {
        return $query->where('pwd_change_required', 0);
    }

    /**
     * Checks if a user has access to login via LDAP
     * @return bool
     */
    public function isLdap(): bool
    {
        return $this->auth_type_id == AuthType::LDAP;
    }

    /**
     * Checks if a user has access to login via Azure
     * @return bool
     */
    public function isAzure(): bool
    {
        return $this->auth_type_id == AuthType::AZURE;
    }
}
