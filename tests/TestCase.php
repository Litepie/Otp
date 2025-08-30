<?php

namespace Litepie\Otp\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Litepie\Otp\OtpServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            OtpServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Otp' => \Litepie\Otp\Facades\Otp::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('mail.default', 'array');
    }
}
