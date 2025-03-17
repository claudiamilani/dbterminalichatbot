<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendConfigurationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailContent;
    public string $attachment;

    public function __construct($emailContent, $attachment)
    {
        $this->emailContent = $emailContent;
        $this->attachment = $attachment;
    }

    public function build(): false|SendConfigurationMail
    {
        try {
            $full_file_path = Storage::disk('documents')->path($this->attachment);

            return $this->subject(trans('DBT/configuration.send.mail.mail_sent'))
                ->html($this->emailContent)
                ->attach($full_file_path);
        } catch (Exception $e) {
            Log::error(trans('DBT/configuration.send.mail.mail_error').$e->getMessage());
        }

        exit;
    }
}
