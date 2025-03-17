<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Mail;

use App\AppConfiguration;
use App\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public $host;
    public bool $is_admin_route;
    public mixed $configuration;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param null $host
     */
    public function __construct(User $user, $host = null)
    {
        $this->user = $user;
        $this->host = $host;
        $this->is_admin_route = isActiveRoute('admin::*');
        $this->configuration = AppConfiguration::current();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->subject($this->configuration->pwdr_mail_obj_u ? : 'Richiesta cambio password ' .config('app.name'))->view('emails.myUserResetPassword');
    }
}
