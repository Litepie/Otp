<?php

namespace Litepie\Otp\Facades;

use Illuminate\Support\Facades\Facade;
use Litepie\Otp\Contracts\OtpManagerInterface;

/**
 * @method static \Litepie\Otp\OtpBuilder generate()
 * @method static bool verify(string $code, string $identifier, string $type = 'default')
 * @method static bool exists(string $identifier, string $type = 'default')
 * @method static bool invalidate(string $identifier, string $type = 'default')
 * @method static \Litepie\Otp\Contracts\OtpChannelInterface channel(string $name)
 * @method static \Litepie\Otp\Contracts\OtpManagerInterface extend(string $name, callable $callback)
 *
 * @see \Litepie\Otp\OtpManager
 */
class Otp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return OtpManagerInterface::class;
    }
}
