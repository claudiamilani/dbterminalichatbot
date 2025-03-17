<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Rules;

use App\AppConfiguration;
use App\Auth\Drivers\AuthDriver;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordComplexity implements ValidationRule
{
    private mixed $config;
    private AuthDriver $driver;

    /**
     * Create a new rule instance.
     *
     * @param AuthDriver $driver
     */
    public function __construct(AuthDriver $driver)
    {
        $this->driver = $driver;
        $this->config = AppConfiguration::current();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->config->pwd_complexity_err_msg ?: trans('auth.pwd_complexity_error');
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     *
     *   Determine if the validation rule passes.
     *  /^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/
     *
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!request('pwd_change_required') && !$this->driver->pwdComplexity($value)){
            $fail($this->message());
        }
    }
}
