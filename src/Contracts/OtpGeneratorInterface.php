<?php

namespace Litepie\Otp\Contracts;

interface OtpGeneratorInterface
{
    /**
     * Generate a new OTP code.
     *
     * @param int $length
     * @param string $type
     * @return string
     */
    public function generate(int $length = 6, string $type = 'numeric'): string;

    /**
     * Check if the given code is valid format.
     *
     * @param string $code
     * @param string $type
     * @return bool
     */
    public function isValidFormat(string $code, string $type = 'numeric'): bool;
}
