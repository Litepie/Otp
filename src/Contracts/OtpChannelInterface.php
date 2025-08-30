<?php

namespace Litepie\Otp\Contracts;

interface OtpChannelInterface
{
    /**
     * Send the OTP through this channel.
     *
     * @param string $identifier
     * @param string $code
     * @param array $data
     * @return bool
     */
    public function send(string $identifier, string $code, array $data = []): bool;

    /**
     * Check if this channel can handle the given identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function canHandle(string $identifier): bool;
}
