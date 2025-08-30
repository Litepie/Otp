<?php

namespace Litepie\Otp\Channels;

use Litepie\Otp\Contracts\OtpChannelInterface;
use Litepie\Otp\Notifications\OtpNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class EmailChannel implements OtpChannelInterface
{
    /**
     * Send the OTP through this channel.
     *
     * @param string $identifier
     * @param string $code
     * @param array $data
     * @return bool
     */
    public function send(string $identifier, string $code, array $data = []): bool
    {
        try {
            if (config('otp.channels.email.use_notifications', true)) {
                // Use Laravel's notification system
                Notification::route('mail', $identifier)
                    ->notify(new OtpNotification($code, $data));
            } else {
                // Use traditional mail
                $this->sendMail($identifier, $code, $data);
            }

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Check if this channel can handle the given identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function canHandle(string $identifier): bool
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Send OTP via traditional mail.
     *
     * @param string $email
     * @param string $code
     * @param array $data
     * @return void
     */
    protected function sendMail(string $email, string $code, array $data): void
    {
        $template = config('otp.channels.email.template', 'otp::emails.otp');
        $subject = config('otp.channels.email.subject', 'Your OTP Code');

        Mail::send($template, compact('code', 'data'), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });
    }
}
