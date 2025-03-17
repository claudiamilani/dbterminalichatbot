<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\Exceptions;


use App\Auth\User;
use Throwable;

class InvalidAppPwdException extends AuthTypeException
{
    public User $user;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if($message instanceof User){

            $this->user = $message;
            $message = 'Test';
        }

        parent::__construct($message, $code, $previous);
    }
}