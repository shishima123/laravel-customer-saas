<?php
// phpcs:disable Generic.Files.LineLength.TooLong
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateCustomerUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $password;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($password = null)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->line(__('message.email.create_customer.line_1'));
        if ($this->password) {
            $mailMessage->line(__('message.email.create_customer.line_2', ['password' => $this->password]));
        }
        return $mailMessage
            ->action(__('message.email.fail_subscription.line_2'), route('login'))
            ->line(__('message.email.create_customer.line_3'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
