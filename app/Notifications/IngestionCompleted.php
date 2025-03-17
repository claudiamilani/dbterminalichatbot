<?php

namespace App\Notifications;

use App\DBT\Models\Ingestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IngestionCompleted extends Notification
{
    use Queueable;

    private Ingestion $ingestion;
    public array $results;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ingestion $ingestion, Array $results)
    {
        $this->ingestion = $ingestion;
        $this->results = $results;
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
                    ->subject('Elaborazione Ingestion ' .trans('DBT/ingestions.sources.'.$this->ingestion->ingestion_source_id) .' Completata')
                    ->view('emails.ingestion_completed', ['ingestion' => $this->ingestion, 'results' => $this->results]);
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
