<?php

namespace Litepie\Otp;

use Litepie\Otp\Contracts\OtpManagerInterface;
use Litepie\Otp\Contracts\OtpGeneratorInterface;
use Litepie\Otp\Contracts\OtpSignerInterface;
use Litepie\Otp\Contracts\OtpChannelInterface;
use Litepie\Otp\Events\OtpGenerated;
use Litepie\Otp\Events\OtpSent;
use Litepie\Otp\Events\OtpVerified;
use Litepie\Otp\Events\OtpFailed;
use Litepie\Otp\Exceptions\TooManyAttemptsException;
use Litepie\Otp\Exceptions\RateLimitExceededException;
use Illuminate\Contracts\Foundation\Application;

class OtpManager implements OtpManagerInterface
{
    /**
     * The application instance.
     */
    protected Application $app;

    /**
     * The OTP generator instance.
     */
    protected OtpGeneratorInterface $generator;

    /**
     * The OTP signer instance.
     */
    protected OtpSignerInterface $signer;

    /**
     * The channel drivers.
     */
    protected array $channels = [];

    /**
     * Custom channel creators.
     */
    protected array $customCreators = [];

    /**
     * Create a new OTP manager instance.
     */
    public function __construct(Application $app, OtpGeneratorInterface $generator, OtpSignerInterface $signer)
    {
        $this->app = $app;
        $this->generator = $generator;
        $this->signer = $signer;
    }

    /**
     * Start building a new OTP.
     */
    public function generate(): OtpBuilder
    {
        return new OtpBuilder($this);
    }

    /**
     * Create and send an OTP.
     */
    public function createAndSend(
        string $identifier,
        string $type,
        int $length,
        string $format,
        int $expiresIn,
        array $channels,
        array $data = []
    ): Otp {
        $otp = $this->create($identifier, $type, $length, $format, $expiresIn, $data);
        
        $this->sendOtp($otp, $channels, $data);
        
        return $otp;
    }

    /**
     * Create an OTP without sending.
     */
    public function create(
        string $identifier,
        string $type,
        int $length,
        string $format,
        int $expiresIn,
        array $data = []
    ): Otp {
        // Check rate limiting
        $this->checkRateLimit($identifier, $type);

        // Invalidate existing OTPs for this identifier and type
        $this->invalidate($identifier, $type);

        // Generate the OTP code
        $code = $this->generator->generate($length, $format);
        $expiresAt = now()->addSeconds($expiresIn);
        
        // Sign the OTP
        $signature = $this->signer->sign($code, $identifier, $type, $expiresAt->timestamp);

        // Create the OTP record
        $otp = Otp::create([
            'identifier' => $identifier,
            'code' => $code,
            'type' => $type,
            'signature' => $signature,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'max_attempts' => config("otp.types.{$type}.max_attempts", config('otp.default.max_attempts', 3)),
            'data' => $data,
        ]);

        // Fire the generated event
        event(new OtpGenerated($otp));

        return $otp;
    }

    /**
     * Send an OTP through the specified channels.
     */
    protected function sendOtp(Otp $otp, array $channels, array $data = []): void
    {
        foreach ($channels as $channelName) {
            try {
                $channel = $this->channel($channelName);
                
                if ($channel->canHandle($otp->identifier)) {
                    $sent = $channel->send($otp->identifier, $otp->code, array_merge($otp->data ?? [], $data, [
                        'type' => $otp->type,
                        'expires_at' => $otp->expires_at,
                    ]));
                    
                    if ($sent) {
                        event(new OtpSent($otp, $channelName));
                    }
                }
            } catch (\Exception $e) {
                // Log the error but don't stop other channels
                report($e);
            }
        }
    }

    /**
     * Verify an OTP code.
     */
    public function verify(string $code, string $identifier, string $type = 'default'): bool
    {
        $otp = Otp::forIdentifier($identifier, $type)->valid()->first();

        if (!$otp) {
            event(new OtpFailed($identifier, $type, $code, 'not_found'));
            return false;
        }

        // Increment attempts
        $otp->incrementAttempts();

        // Check if max attempts exceeded
        if ($otp->attempts >= $otp->max_attempts) {
            event(new OtpFailed($identifier, $type, $code, 'max_attempts'));
            throw new TooManyAttemptsException('Maximum OTP attempts exceeded.');
        }

        // Verify the code and signature
        $isValidCode = hash_equals($otp->code, $code);
        $isValidSignature = $this->signer->verify(
            $code,
            $otp->signature,
            $identifier,
            $type,
            $otp->expires_at->timestamp
        );

        if (!$isValidCode || !$isValidSignature) {
            event(new OtpFailed($identifier, $type, $code, 'invalid'));
            return false;
        }

        // Mark as used
        $otp->markAsUsed();

        // Fire the verified event
        event(new OtpVerified($otp));

        return true;
    }

    /**
     * Check if an OTP exists for the given identifier and type.
     */
    public function exists(string $identifier, string $type = 'default'): bool
    {
        return Otp::forIdentifier($identifier, $type)->valid()->exists();
    }

    /**
     * Invalidate an OTP.
     */
    public function invalidate(string $identifier, string $type = 'default'): bool
    {
        return Otp::forIdentifier($identifier, $type)->valid()->delete();
    }

    /**
     * Get a channel instance.
     */
    public function channel(string $name): OtpChannelInterface
    {
        if (!isset($this->channels[$name])) {
            $this->channels[$name] = $this->createChannel($name);
        }

        return $this->channels[$name];
    }

    /**
     * Create a channel driver.
     */
    protected function createChannel(string $name): OtpChannelInterface
    {
        if (isset($this->customCreators[$name])) {
            return $this->customCreators[$name]($this->app);
        }

        return match ($name) {
            'email' => new Channels\EmailChannel(),
            'sms' => new Channels\SmsChannel(),
            'database' => new Channels\DatabaseChannel(),
            default => throw new \InvalidArgumentException("Channel [{$name}] not supported."),
        };
    }

    /**
     * Extend the manager with a custom channel.
     */
    public function extend(string $name, callable $callback): self
    {
        $this->customCreators[$name] = $callback;
        return $this;
    }

    /**
     * Check rate limiting for OTP generation.
     */
    protected function checkRateLimit(string $identifier, string $type): void
    {
        $rateLimit = config("otp.types.{$type}.rate_limit", config('otp.default.rate_limit'));
        
        if (!$rateLimit) {
            return;
        }

        $key = "otp_rate_limit:{$identifier}:{$type}";
        $attempts = cache()->get($key, 0);

        if ($attempts >= $rateLimit['max_attempts']) {
            throw new RateLimitExceededException(
                "Rate limit exceeded. Try again in {$rateLimit['decay_minutes']} minutes."
            );
        }

        cache()->put($key, $attempts + 1, now()->addMinutes($rateLimit['decay_minutes']));
    }
}
