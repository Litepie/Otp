<?php

namespace Litepie\Otp\Contracts;

use Litepie\Otp\OtpBuilder;

interface OtpManagerInterface
{
    /**
     * Start building a new OTP.
     *
     * @return OtpBuilder
     */
    public function generate(): OtpBuilder;

    /**
     * Verify an OTP code.
     *
     * @param string $code
     * @param string $identifier
     * @param string $type
     * @return bool
     */
    public function verify(string $code, string $identifier, string $type = 'default'): bool;

    /**
     * Check if an OTP exists for the given identifier and type.
     *
     * @param string $identifier
     * @param string $type
     * @return bool
     */
    public function exists(string $identifier, string $type = 'default'): bool;

    /**
     * Invalidate an OTP.
     *
     * @param string $identifier
     * @param string $type
     * @return bool
     */
    public function invalidate(string $identifier, string $type = 'default'): bool;

    /**
     * Extend the manager with a custom channel.
     *
     * @param string $name
     * @param callable $callback
     * @return $this
     */
    public function extend(string $name, callable $callback): self;

    /**
     * Get a channel instance.
     *
     * @param string $name
     * @return OtpChannelInterface
     */
    public function channel(string $name): OtpChannelInterface;
}
