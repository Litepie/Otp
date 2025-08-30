<?php

namespace Litepie\Otp;

use Illuminate\Support\ServiceProvider;
use Litepie\Otp\Commands\CleanupExpiredOtpsCommand;
use Litepie\Otp\Contracts\OtpManagerInterface;
use Litepie\Otp\Contracts\OtpGeneratorInterface;
use Litepie\Otp\Contracts\OtpSignerInterface;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/otp.php', 'otp'
        );

        $this->app->singleton(OtpManagerInterface::class, OtpManager::class);
        $this->app->singleton(OtpGeneratorInterface::class, OtpGenerator::class);
        $this->app->singleton(OtpSignerInterface::class, OtpSigner::class);
        
        $this->app->alias(OtpManagerInterface::class, 'otp');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/otp.php' => config_path('otp.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupExpiredOtpsCommand::class,
            ]);
        }

        // Register channels
        $this->registerChannels();
    }

    /**
     * Register OTP channels.
     */
    protected function registerChannels(): void
    {
        $manager = $this->app->make(OtpManagerInterface::class);

        $manager->extend('email', function ($app) {
            return new Channels\EmailChannel();
        });

        $manager->extend('sms', function ($app) {
            return new Channels\SmsChannel();
        });

        $manager->extend('database', function ($app) {
            return new Channels\DatabaseChannel();
        });
    }
}
