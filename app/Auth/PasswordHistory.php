<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordHistory extends Model
{
    protected $fillable = ['user_id', 'password'];

    /**
     * The user for the password history
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
