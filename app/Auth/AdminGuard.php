<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * Author: Francesco Tesone
 * Email: tesone@medialogic.it
 * Date: 10/12/2018
 * Time: 10:53
 */

namespace App\Auth;


use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Request;

class AdminGuard implements Guard
{
    use GuardHelpers;
    protected Request $request;
    protected $callback;

    /**
     * Create a new authentication guard.
     *
     * @param UserProvider $provider
     * @param Request $request
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->user = NULL;
    }

    /**
     * @return Authenticatable|mixed|null
     */
    public function user(): mixed
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func(
            $this->callback, $this->request, $this->getProvider()
        );
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if (! is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);
            return true;
        } else {
            return false;
        }
    }
}