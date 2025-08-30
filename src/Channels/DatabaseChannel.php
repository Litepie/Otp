<?php

namespace Litepie\Otp\Channels;

use Litepie\Otp\Contracts\OtpChannelInterface;

class DatabaseChannel implements OtpChannelInterface
{
    /**
     * Send the OTP through this channel.
     * For database channel, this just returns true as the OTP is already stored.
     *
     * @param string $identifier
     * @param string $code
     * @param array $data
     * @return bool
     */
    public function send(string $identifier, string $code, array $data = []): bool
    {
        // For database channel, the OTP is already stored in the database
        // This method just confirms the "delivery" was successful
        return true;
    }

    /**
     * Check if this channel can handle the given identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function canHandle(string $identifier): bool
    {
        // Database channel can handle any identifier
        return true;
    }
}
