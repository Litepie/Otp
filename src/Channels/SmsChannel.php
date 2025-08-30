<?php

namespace Litepie\Otp\Channels;

use Litepie\Otp\Contracts\OtpChannelInterface;

class SmsChannel implements OtpChannelInterface
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
            $provider = config('otp.channels.sms.provider', 'log');
            
            return match ($provider) {
                'log' => $this->sendViaLog($identifier, $code, $data),
                'nexmo' => $this->sendViaNexmo($identifier, $code, $data),
                'twilio' => $this->sendViaTwilio($identifier, $code, $data),
                default => $this->sendViaLog($identifier, $code, $data),
            };
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
        // Basic phone number validation
        return preg_match('/^\+?[1-9]\d{1,14}$/', $identifier);
    }

    /**
     * Send SMS via log (for testing).
     *
     * @param string $phone
     * @param string $code
     * @param array $data
     * @return bool
     */
    protected function sendViaLog(string $phone, string $code, array $data): bool
    {
        $message = config('otp.channels.sms.message', 'Your OTP code is: {code}');
        $message = str_replace('{code}', $code, $message);
        
        logger("SMS to {$phone}: {$message}");
        
        return true;
    }

    /**
     * Send SMS via Nexmo/Vonage.
     *
     * @param string $phone
     * @param string $code
     * @param array $data
     * @return bool
     */
    protected function sendViaNexmo(string $phone, string $code, array $data): bool
    {
        // Implementation would depend on Nexmo SDK
        // This is a placeholder for the actual implementation
        throw new \Exception('Nexmo SMS implementation not configured. Please implement this method.');
    }

    /**
     * Send SMS via Twilio.
     *
     * @param string $phone
     * @param string $code
     * @param array $data
     * @return bool
     */
    protected function sendViaTwilio(string $phone, string $code, array $data): bool
    {
        // Implementation would depend on Twilio SDK
        // This is a placeholder for the actual implementation
        throw new \Exception('Twilio SMS implementation not configured. Please implement this method.');
    }
}
