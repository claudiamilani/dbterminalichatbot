<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordRecovery extends Model
{
    use Searchable;
    protected $fillable = ['email','ipv4','token', 'user'];

    /**
     * The user for the password recovery
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Filters query results for user, name, surname and email
     * @param $query
     * @param $search
     * @return Builder
     */
    public function searchFilter($query, $search): Builder
    {
        return $query->where(function($query) use($search){
            $query->where('user','ILIKE', "%$search%")->orWhereHas('account',function($query)use($search){
                $query->where('name','ILIKE',"%$search%")->orWhere('surname','ILIKE',"%$search%")->orWhere('email','ILIKE',"%$search%");
            });
        });
    }
}
