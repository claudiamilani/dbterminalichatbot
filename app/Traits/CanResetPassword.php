<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * Author: Francesco Tesone
 * Email: tesone@medialogic.it
 * Date: 21/03/2019
 * Time: 13:27
 */

namespace App\Traits;

use App\Auth\PasswordRecovery;
use App\Mail\ResetPassword;
use Exception;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait CanResetPassword
{
    public abstract function passwordRecovery(): HasOne;

    /**
     * Send a password recovery email only if one has not been sent in the last 30 minutes
     * @param Mailable|null $mailable
     * @return Application|bool|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
     */
    public function sendPasswordRecovery(?Mailable $mailable = null): Application|bool|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        $this->loadMissing('passwordRecovery');
        $mailable = $mailable ? : new ResetPassword($this,request()->getHost());
        if (empty($this->passwordRecovery)) {
            $i = 0;
            while (!empty(PasswordRecovery::where('token', $token = Str::random(60))->first())) {
                $i++;
                if ($i == 5) {
                    // Eseguo 5 tentativi di generazione di un token univoco, dopodiche restituisco un messaggio di errore temporaneo.
                    Log::channel('auth')->debug('Unable to generate unique token in 5 tries. Giving up.');
                    return trans('passwords.change.generic_error');
                }
            }

            try {
                $password_recovery = new PasswordRecovery([
                    'user' => $this->user,
                    'email' => $this->email,
                    'token' => $token,
                    'ipv4' => request()->getClientIp(),
                ]);

                $this->passwordRecovery()->save($password_recovery);
                return $this->sendRecoveryMail($mailable);
            } catch (Exception $e) {
                Log::channel('auth')->error($e->getMessage());
                Log::channel('auth')->error($e->getTraceAsString());
                return trans('passwords.change.generic_error');
            }
        }
        // Trovata precedente richiesta di reset
        if ($this->passwordRecovery->created_at->diffInMinutes(Carbon::now()) > 30) {
            return $this->sendRecoveryMail($mailable);
        }
        Log::channel('auth')->info('Password recovery mail already sent within 30 minutes ago for '.$this->user);
        return true;
    }

    /**
     * Dispatch the password recovery email
     * @param Mailable $mailable
     * @return Application|bool|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
     */
    private function sendRecoveryMail(Mailable $mailable): Application|bool|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        if(empty($this->email)){
            Log::channel('auth')->info('Unable to send password recovery mail: missing email address',['user' => $this->user]);
        }
        try {
            Log::channel('auth')->info('Sending password recovery mail',['user' => $this->user,'email' => $this->email]);
            Mail::to($this->email)->queue(($mailable)->onQueue('mail'));
            Log::channel('auth')->info('Password recovery mail successfully sent',['user' => $this->user,'email' => $this->email]);
            return true;
        } catch (Exception $e) {
            Log::channel('auth')->error('Unable to send password recovery mail',['user' => $this->user,'email' => $this->email,'error' => $e->getMessage()]);
            return trans('passwords.resets.generic_error');
        }
    }
}