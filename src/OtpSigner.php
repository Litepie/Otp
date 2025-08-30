<?php

namespace Litepie\Otp;

use Litepie\Otp\Contracts\OtpSignerInterface;

class OtpSigner implements OtpSignerInterface
{
    /**
     * The secret key for signing.
     *
     * @var string
     */
    protected string $secretKey;

    /**
     * Create a new OTP signer instance.
     */
    public function __construct()
    {
        $this->secretKey = config('otp.signing.secret', config('app.key'));
    }

    /**
     * Sign the OTP code with additional data.
     *
     * @param string $code
     * @param string $identifier
     * @param string $type
     * @param int $expiresAt
     * @return string
     */
    public function sign(string $code, string $identifier, string $type, int $expiresAt): string
    {
        $payload = implode('|', [$code, $identifier, $type, $expiresAt]);
        
        return hash_hmac('sha256', $payload, $this->secretKey);
    }

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
    public function verify(string $code, string $signature, string $identifier, string $type, int $expiresAt): bool
    {
        $expectedSignature = $this->sign($code, $identifier, $type, $expiresAt);
        
        return hash_equals($expectedSignature, $signature);
    }
}
