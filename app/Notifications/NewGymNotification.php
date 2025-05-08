<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGymNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $gym;

    /**
     * Create a new notification instance.
     */
    public function __construct($gym)
    {
        $this->gym = $gym;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Gym Has Been Added!')
                    ->view('emails.new_gym', ['gym' => $this->gym]); // Using a Blade view
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'gym_name' => $this->gym->name,
            'email' => $this->gym->email,
        ];
    }
}
