<?php

namespace App\Notifications;

use App\DBT\Models\Ingestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IngestionFailed extends Notification
{
    use Queueable;

    private Ingestion $ingestion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ingestion $ingestion)
    {
        $this->ingestion = $ingestion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Elaborazione Ingestion ' .trans('DBT/ingestions.sources.'.$this->ingestion->ingestion_source_id) .' errore')
            ->view('emails.ingestion_failed', ['ingestion' => $this->ingestion]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
