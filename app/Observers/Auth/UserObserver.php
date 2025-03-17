<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Observers\Auth;

use App\Auth\User;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        !$user->pwd_change_required ?  $user->password = bcrypt($user->password) : $user->password =  Str::random(8);
    }
}
