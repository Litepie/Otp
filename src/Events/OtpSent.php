<?php

namespace Litepie\Otp\Events;

use Litepie\Otp\Otp;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpSent
{
    use Dispatchable, SerializesModels;

    /**
     * The OTP instance.
     *
     * @var Otp
     */
    public Otp $otp;

    /**
     * The channel name.
     *
     * @var string
     */
    public string $channel;

    /**
     * Create a new event instance.
     *
     * @param Otp $otp
     * @param string $channel
     */
    public function __construct(Otp $otp, string $channel)
    {
        $this->otp = $otp;
        $this->channel = $channel;
    }
}
