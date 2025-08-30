<?php

namespace Litepie\Otp\Events;

use Litepie\Otp\Otp;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * The OTP instance.
     *
     * @var Otp
     */
    public Otp $otp;

    /**
     * Create a new event instance.
     *
     * @param Otp $otp
     */
    public function __construct(Otp $otp)
    {
        $this->otp = $otp;
    }
}
