<?php

namespace Litepie\Otp\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpFailed
{
    use Dispatchable, SerializesModels;

    /**
     * The identifier.
     *
     * @var string
     */
    public string $identifier;

    /**
     * The OTP type.
     *
     * @var string
     */
    public string $type;

    /**
     * The attempted code.
     *
     * @var string
     */
    public string $code;

    /**
     * The failure reason.
     *
     * @var string
     */
    public string $reason;

    /**
     * Create a new event instance.
     *
     * @param string $identifier
     * @param string $type
     * @param string $code
     * @param string $reason
     */
    public function __construct(string $identifier, string $type, string $code, string $reason)
    {
        $this->identifier = $identifier;
        $this->type = $type;
        $this->code = $code;
        $this->reason = $reason;
    }
}
