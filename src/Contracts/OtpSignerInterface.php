<?php

namespace Litepie\Otp\Contracts;

interface OtpSignerInterface
{
    /**
     * Sign the OTP code with additional data.
     *
     * @param string $code
     * @param string $identifier
     * @param string $type
     * @param int $expiresAt
     * @return string
     */
    public function sign(string $code, string $identifier, string $type, int $expiresAt): string;

    /**
     * Verify the signature of an OTP.
     *
     * @param string $code
     * @param string $signature
     * @param string $identifier
     * @param string $type
     * @param int $expiresAt
     * @return bool
     */
    public function verify(string $code, string $signature, string $identifier, string $type, int $expiresAt): bool;
}
