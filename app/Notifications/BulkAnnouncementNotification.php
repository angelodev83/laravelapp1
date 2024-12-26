<?php

namespace App\Notifications;

ini_set('max_execution_time', 600);

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BulkAnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement, $emails;

    /**
     * Create a new notification instance.
     */
    public function __construct($announcement, $emails = [])
    {
        $this->announcement = $announcement;
        $this->emails = $emails;
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
                // ->view('vendor.mail.notifications.invoice')
                ->cc($this->emails)
                ->subject("📢 " . $this->announcement->subject)
                ->view('emails.announcement', ['announcement' => $this->announcement, 'notifiable' => $notifiable]);
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
