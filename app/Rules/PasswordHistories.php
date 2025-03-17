<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Rules;

use App\AppConfiguration;
use App\Auth\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class PasswordHistories implements ValidationRule
{
    private mixed $config;
    private mixed $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->config = AppConfiguration::current();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->config->pwd_history_err_msg ?: trans('auth.pwd_history_error');
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::findOrFail($this->id);
        $passwordHistories = $user->passwordHistories()->get()->all();
        foreach ($passwordHistories as $passwordHistory) {
            if (Hash::check($value, $passwordHistory->password)) {
                $fail($this->message());
            }
        }
    }
}
