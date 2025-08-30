<?php

namespace Litepie\Otp\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The OTP code.
     *
     * @var string
     */
    public string $code;

    /**
     * Additional data for the notification.
     *
     * @var array
     */
    public array $data;

    /**
     * Create a new notification instance.
     *
     * @param string $code
     * @param array $data
     */
    public function __construct(string $code, array $data = [])
    {
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = config('otp.channels.email.subject', 'Your OTP Code');
        $message = config('otp.channels.email.message', 'Your verification code is: {code}');
        $message = str_replace('{code}', $this->code, $message);

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting(config('otp.channels.email.greeting', 'Hello!'))
            ->line($message);

        if (isset($this->data['expires_at'])) {
            $expiresAt = $this->data['expires_at'];
            $mail->line("This code will expire at {$expiresAt->format('Y-m-d H:i:s')}.");
        }

        return $mail->line(config('otp.channels.email.footer', 'If you did not request this code, please ignore this email.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'code' => $this->code,
            'data' => $this->data,
        ];
    }
}
