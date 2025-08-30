<?php

namespace Litepie\Otp;

use Litepie\Otp\Contracts\OtpGeneratorInterface;

class OtpGenerator implements OtpGeneratorInterface
{
    /**
     * Generate a new OTP code.
     *
     * @param int $length
     * @param string $type
     * @return string
     */
    public function generate(int $length = 6, string $type = 'numeric'): string
    {
        return match ($type) {
            'numeric' => $this->generateNumeric($length),
            'alphanumeric' => $this->generateAlphanumeric($length),
            'alphabetic' => $this->generateAlphabetic($length),
            default => $this->generateNumeric($length),
        };
    }

    /**
     * Check if the given code is valid format.
     *
     * @param string $code
     * @param string $type
     * @return bool
     */
    public function isValidFormat(string $code, string $type = 'numeric'): bool
    {
        return match ($type) {
            'numeric' => ctype_digit($code),
            'alphanumeric' => ctype_alnum($code),
            'alphabetic' => ctype_alpha($code),
            default => ctype_digit($code),
        };
    }

    /**
     * Generate numeric OTP.
     *
     * @param int $length
     * @return string
     */
    protected function generateNumeric(int $length): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }

    /**
     * Generate alphanumeric OTP.
     *
     * @param int $length
     * @return string
     */
    protected function generateAlphanumeric(int $length): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    /**
     * Generate alphabetic OTP.
     *
     * @param int $length
     * @return string
     */
    protected function generateAlphabetic(int $length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
}
