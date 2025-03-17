<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Notifications;

use App\AppConfiguration;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MyResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;

    public string $host;

    public function __construct(string $token, $host)
    {
        parent::__construct($token);
        $this->host = $host;
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        //Config::set('app.url', $this->host);
        $host = $this->host;
        $configuration = AppConfiguration::current();
        $token = $this->token;
        return (new MailMessage())->subject($configuration->pwdr_mail_obj_u ?: 'Richiesta recupero password ' .config('app.name'))->view('emails.myUserResetPassword', compact('configuration','token','host'));
    }
}
