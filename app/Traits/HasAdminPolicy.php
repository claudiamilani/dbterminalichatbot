<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * User: francesco
 * Date: 23/06/18
 * Time: 19:05
 */

namespace App\Traits;

trait HasAdminPolicy
{
    /**
     * @param $currentUser
     * @param $ability
     * @return true|void
     */
    public function before($currentUser, $ability)
    {
        $always_check = is_array($this->alwaysCheck()) ? $this->alwaysCheck() : [];
        if ($currentUser->isAdmin() && !in_array($ability,$always_check)) {
            return true;
        }
    }

    abstract public function alwaysCheck(): array;
}