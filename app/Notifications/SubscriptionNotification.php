<?php
// phpcs:disable Generic.Files.LineLength.TooLong
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class SubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $txt = new HtmlString(
            __(
                'message.notify.content_email',
                ['email' => '<strong>' . config('mail.from.address') . '</strong>']
            )
        );
        $mailMessage = (new MailMessage());

        if (!empty($notifiable->billing_contact_email)) {
            $mailMessage->cc([$notifiable->email]);
        }

        $mailMessage
            ->greeting(__('message.notify.greeting'))
            ->line(__('message.notify.email_line'))
            ->line($txt);

        return $this->attachmentFile($notifiable, $mailMessage);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function attachmentFile($notifiable, $mailMessage)
    {
        $invoice = $notifiable->subscription('premium')->invoices()->first();
        if ($invoice) {
            $filename = Str::snake($notifiable->name) .
                '_Premium_' .
                $invoice->date()->month .
                '_' .
                $invoice->date()->year . '.pdf';
            $contents = $invoice->pdf([
                'vendor' => config('services.company.name'),
                'product' => 'Premium',
                'street' => config('services.company.add1'),
                'location' => config('services.company.add2'),
                'phone' => config('services.company.phone'),
            ]);
            Storage::disk('invoice_temp')->put($filename, $contents);
            $mailMessage->attach(public_path("temp/invoices/$filename"));
        }
        return $mailMessage;
    }
}
