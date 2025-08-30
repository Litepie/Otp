<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default OTP Settings
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default settings for OTP generation and validation.
    | These settings will be used when no specific configuration is provided for
    | a particular OTP type.
    |
    */

    'default' => [
        'length' => env('OTP_DEFAULT_LENGTH', 6),
        'format' => env('OTP_DEFAULT_FORMAT', 'numeric'), // numeric, alphanumeric, alphabetic
        'expires_in' => env('OTP_DEFAULT_EXPIRES_IN', 300), // 5 minutes in seconds
        'max_attempts' => env('OTP_DEFAULT_MAX_ATTEMPTS', 3),
        'channels' => explode(',', env('OTP_DEFAULT_CHANNELS', 'email')),
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP Types Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define different OTP types with their specific settings.
    | Each type can have its own length, format, expiration time, and channels.
    |
    */

    'types' => [
        'login' => [
            'length' => 6,
            'format' => 'numeric',
            'expires_in' => 300, // 5 minutes
            'max_attempts' => 3,
            'channels' => ['email'],
            'rate_limit' => [
                'max_attempts' => 5,
                'decay_minutes' => 60,
            ],
        ],

        'email_verification' => [
            'length' => 6,
            'format' => 'numeric',
            'expires_in' => 600, // 10 minutes
            'max_attempts' => 5,
            'channels' => ['email'],
            'rate_limit' => [
                'max_attempts' => 3,
                'decay_minutes' => 30,
            ],
        ],

        'password_reset' => [
            'length' => 8,
            'format' => 'alphanumeric',
            'expires_in' => 900, // 15 minutes
            'max_attempts' => 3,
            'channels' => ['email'],
            'rate_limit' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
            ],
        ],

        'two_factor' => [
            'length' => 6,
            'format' => 'numeric',
            'expires_in' => 180, // 3 minutes
            'max_attempts' => 3,
            'channels' => ['sms', 'email'],
            'rate_limit' => [
                'max_attempts' => 5,
                'decay_minutes' => 30,
            ],
        ],

        'phone_verification' => [
            'length' => 6,
            'format' => 'numeric',
            'expires_in' => 300, // 5 minutes
            'max_attempts' => 3,
            'channels' => ['sms'],
            'rate_limit' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure global rate limiting for OTP generation to prevent abuse.
    | You can also define rate limits per OTP type above.
    |
    */

    'rate_limit' => [
        'max_attempts' => 5,
        'decay_minutes' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP Signing Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how OTPs are digitally signed for enhanced security.
    | The secret key should be kept secure and rotated regularly.
    |
    */

    'signing' => [
        'secret' => env('OTP_SIGNING_SECRET', env('APP_KEY')),
        'algorithm' => 'sha256',
    ],

    /*
    |--------------------------------------------------------------------------
    | Channel Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the various channels through which OTPs can be delivered.
    | Each channel can have its own specific settings and providers.
    |
    */

    'channels' => [
        'email' => [
            'use_notifications' => true,
            'template' => 'otp::emails.otp',
            'subject' => 'Your OTP Code',
            'message' => 'Your verification code is: {code}',
            'greeting' => 'Hello!',
            'footer' => 'If you did not request this code, please ignore this email.',
        ],

        'sms' => [
            'provider' => env('OTP_SMS_PROVIDER', 'log'), // log, nexmo, twilio
            'message' => 'Your OTP code is: {code}',
            
            // Nexmo/Vonage configuration
            'nexmo' => [
                'key' => env('NEXMO_KEY'),
                'secret' => env('NEXMO_SECRET'),
                'from' => env('NEXMO_FROM', 'YourApp'),
            ],
            
            // Twilio configuration
            'twilio' => [
                'sid' => env('TWILIO_SID'),
                'token' => env('TWILIO_TOKEN'),
                'from' => env('TWILIO_FROM'),
            ],
        ],

        'database' => [
            // Database channel stores OTP in database for manual retrieval
            // No additional configuration needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic cleanup of expired OTPs from the database.
    | This helps keep your database clean and improves performance.
    |
    */

    'cleanup' => [
        'enabled' => true,
        'schedule' => 'daily', // daily, hourly, weekly
        'delete_after_days' => 7, // Delete expired OTPs after this many days
    ],

];
