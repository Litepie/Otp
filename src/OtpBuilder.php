<?php

namespace Litepie\Otp;

use Litepie\Otp\Contracts\OtpManagerInterface;
use Litepie\Otp\Contracts\OtpChannelInterface;

class OtpBuilder
{
    /**
     * The OTP manager instance.
     *
     * @var OtpManagerInterface
     */
    protected OtpManagerInterface $manager;

    /**
     * The identifier for the OTP.
     *
     * @var string|null
     */
    protected ?string $identifier = null;

    /**
     * The type of OTP.
     *
     * @var string
     */
    protected string $type = 'default';

    /**
     * The length of the OTP.
     *
     * @var int
     */
    protected int $length = 6;

    /**
     * The format of the OTP.
     *
     * @var string
     */
    protected string $format = 'numeric';

    /**
     * The expiration time in seconds.
     *
     * @var int
     */
    protected int $expiresIn = 300; // 5 minutes

    /**
     * The delivery channels.
     *
     * @var array
     */
    protected array $channels = ['email'];

    /**
     * Additional data for the OTP.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Create a new OTP builder instance.
     *
     * @param OtpManagerInterface $manager
     */
    public function __construct(OtpManagerInterface $manager)
    {
        $this->manager = $manager;
        
        // Load default configuration
        $this->length = config('otp.default.length', 6);
        $this->format = config('otp.default.format', 'numeric');
        $this->expiresIn = config('otp.default.expires_in', 300);
        $this->channels = config('otp.default.channels', ['email']);
    }

    /**
     * Set the identifier for the OTP.
     *
     * @param string $identifier
     * @return $this
     */
    public function for(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Set the type of OTP.
     *
     * @param string $type
     * @return $this
     */
    public function type(string $type): self
    {
        $this->type = $type;
        
        // Load type-specific configuration
        $typeConfig = config("otp.types.{$type}", []);
        
        if (isset($typeConfig['length'])) {
            $this->length = $typeConfig['length'];
        }
        
        if (isset($typeConfig['format'])) {
            $this->format = $typeConfig['format'];
        }
        
        if (isset($typeConfig['expires_in'])) {
            $this->expiresIn = $typeConfig['expires_in'];
        }
        
        if (isset($typeConfig['channels'])) {
            $this->channels = $typeConfig['channels'];
        }
        
        return $this;
    }

    /**
     * Set the length of the OTP.
     *
     * @param int $length
     * @return $this
     */
    public function length(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Set the format of the OTP.
     *
     * @param string $format
     * @return $this
     */
    public function format(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Set the expiration time in seconds.
     *
     * @param int $seconds
     * @return $this
     */
    public function expiresIn(int $seconds): self
    {
        $this->expiresIn = $seconds;
        return $this;
    }

    /**
     * Set the delivery channels.
     *
     * @param string|array $channels
     * @return $this
     */
    public function via(string|array $channels): self
    {
        $this->channels = is_array($channels) ? $channels : [$channels];
        return $this;
    }

    /**
     * Set additional data for the OTP.
     *
     * @param array $data
     * @return $this
     */
    public function with(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Generate and send the OTP.
     *
     * @return Otp
     */
    public function send(): Otp
    {
        if (!$this->identifier) {
            throw new \InvalidArgumentException('Identifier is required to send OTP.');
        }

        return $this->manager->createAndSend(
            $this->identifier,
            $this->type,
            $this->length,
            $this->format,
            $this->expiresIn,
            $this->channels,
            $this->data
        );
    }

    /**
     * Generate the OTP without sending.
     *
     * @return Otp
     */
    public function create(): Otp
    {
        if (!$this->identifier) {
            throw new \InvalidArgumentException('Identifier is required to create OTP.');
        }

        return $this->manager->create(
            $this->identifier,
            $this->type,
            $this->length,
            $this->format,
            $this->expiresIn,
            $this->data
        );
    }
}
