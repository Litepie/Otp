# Laravel OTP Package

[![Tests](https://github.com/litepie/otp/workflows/Tests/badge.svg)](https://github.com/litepie/otp/actions)
[![Code Style](https://github.com/litepie/otp/workflows/Code%20Style/badge.svg)](https://github.com/litepie/otp/actions)
[![Static Analysis](https://github.com/litepie/otp/workflows/Static%20Analysis/badge.svg)](https://github.com/litepie/otp/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/litepie/otp.svg?style=flat-square)](https://packagist.org/packages/litepie/otp)
[![Latest Stable Version](https://img.shields.io/packagist/v/litepie/otp.svg?style=flat-square)](https://packagist.org/packages/litepie/otp)
[![License](https://img.shields.io/packagist/l/litepie/otp.svg?style=flat-square)](https://packagist.org/packages/litepie/otp)

A comprehensive Laravel package for generating, signing and managing OTP (One-Time Password) codes with multiple channels support.

## 📋 Requirements

- PHP 8.2 or higher
- Laravel 10.0, 11.0, or 12.0

## 🚀 Features

- ✅ **Secure OTP Generation** - Generate cryptographically secure OTP codes
- ✅ **Digital Signing** - Sign OTP codes for enhanced security and verification
- ✅ **Multiple Delivery Channels** - Email, SMS, Database, and custom channels
- ✅ **Flexible Configuration** - Customizable length, format, and expiration
- ✅ **Rate Limiting** - Built-in protection against abuse
- ✅ **Multiple OTP Types** - Login, email verification, password reset, 2FA, etc.
- ✅ **Event System** - Complete lifecycle events for monitoring and logging
- ✅ **Queue Support** - Background processing for sending OTPs
- ✅ **Auto-cleanup** - Automatic removal of expired OTPs
- ✅ **Laravel 12 Ready** - Full compatibility with the latest Laravel versions
- ✅ **Production Ready** - Thoroughly tested and optimized for production use

## 📦 Installation

You can install the package via Composer:

```bash
composer require litepie/otp
```

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Litepie\Otp\OtpServiceProvider" --tag="config"
```

### Run Migrations

Run the migrations to create the OTPs table:

```bash
php artisan migrate
```

### Set Up Automatic Cleanup (Optional)

Add the following to your `app/Console/Kernel.php` file to automatically clean up expired OTPs:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('otp:cleanup')->daily();
}
```

## 🔧 Configuration

The configuration file `config/otp.php` allows you to customize:

- **Default OTP Settings** - Length, format, expiration, channels
- **OTP Types** - Specific settings for different use cases
- **Rate Limiting** - Prevent abuse with configurable limits
- **Digital Signing** - Secure OTP verification
- **Channel Configuration** - Email, SMS, and custom channel settings
- **Automatic Cleanup** - Keep your database clean

### Environment Variables

Add these to your `.env` file:

```env
# OTP Signing Secret (defaults to APP_KEY)
OTP_SIGNING_SECRET=your-secret-key

# SMS Provider Configuration
OTP_SMS_PROVIDER=log  # Options: log, nexmo, twilio

# Nexmo/Vonage
NEXMO_KEY=your-nexmo-key
NEXMO_SECRET=your-nexmo-secret
NEXMO_FROM=YourApp

# Twilio
TWILIO_SID=your-twilio-sid
TWILIO_TOKEN=your-twilio-token
TWILIO_FROM=your-twilio-number
```

## 📖 Usage

### Quick Start

```php
use Litepie\Otp\Facades\Otp;

// Generate and send OTP
$otp = Otp::generate()
    ->for('user@example.com')
    ->type('login')
    ->send();

// Verify OTP
$isValid = Otp::verify('123456', 'user@example.com', 'login');

if ($isValid) {
    // OTP is valid, proceed with authentication
    return response()->json(['message' => 'Login successful']);
}
```

### Advanced Usage

```php
// Custom OTP with specific settings
$otp = Otp::generate()
    ->for('user@example.com')
    ->type('password_reset')
    ->length(8)                    // 8 digits
    ->format('alphanumeric')       // Letters and numbers
    ->expiresIn(900)              // 15 minutes
    ->via(['email', 'sms'])       // Multiple channels
    ->with(['user_id' => 123])    // Additional data
    ->send();

// Check if OTP exists before generating new one
if (!Otp::exists('user@example.com', 'login')) {
    $otp = Otp::generate()
        ->for('user@example.com')
        ->type('login')
        ->send();
}

// Invalidate existing OTP
Otp::invalidate('user@example.com', 'login');
```

### Exception Handling

```php
use Litepie\Otp\Exceptions\TooManyAttemptsException;
use Litepie\Otp\Exceptions\RateLimitExceededException;

try {
    $isValid = Otp::verify($code, $email, 'login');
} catch (TooManyAttemptsException $e) {
    return response()->json(['error' => 'Too many failed attempts'], 429);
} catch (RateLimitExceededException $e) {
    return response()->json(['error' => 'Rate limit exceeded'], 429);
}
```

## 🎯 OTP Types

The package supports multiple OTP types with individual configurations:

### Built-in Types

| Type | Use Case | Default Length | Default Expiry |
|------|----------|----------------|----------------|
| `login` | User authentication | 6 digits | 5 minutes |
| `email_verification` | Email verification | 6 digits | 10 minutes |
| `password_reset` | Password reset | 8 characters | 15 minutes |
| `two_factor` | 2FA authentication | 6 digits | 3 minutes |
| `phone_verification` | Phone verification | 6 digits | 5 minutes |

### Custom Types

Define custom OTP types in your configuration:

```php
// config/otp.php
'types' => [
    'transaction_verify' => [
        'length' => 8,
        'format' => 'alphanumeric',
        'expires_in' => 600, // 10 minutes
        'max_attempts' => 3,
        'channels' => ['email', 'sms'],
        'rate_limit' => [
            'max_attempts' => 2,
            'decay_minutes' => 30,
        ],
    ],
],
```

## 📡 Delivery Channels

### Email Channel
Sends OTP via email using Laravel's notification system or traditional mail.

```php
Otp::generate()
    ->for('user@example.com')
    ->via('email')
    ->send();
```

### SMS Channel
Send OTPs via SMS using various providers:

```php
Otp::generate()
    ->for('+1234567890')
    ->via('sms')
    ->send();
```

**Supported SMS Providers:**
- **Log** (for testing)
- **Nexmo/Vonage**
- **Twilio**
- **Custom providers** (extensible)

### Database Channel
Store OTP in database for manual retrieval:

```php
Otp::generate()
    ->for('user@example.com')
    ->via('database')
    ->send();

// Retrieve from database
$otpRecord = \Litepie\Otp\Otp::where('identifier', 'user@example.com')
    ->where('type', 'login')
    ->valid()
    ->first();
```

### Multiple Channels
Send via multiple channels simultaneously:

```php
Otp::generate()
    ->for('user@example.com')
    ->via(['email', 'sms', 'database'])
    ->send();
```

### Custom Channels
Create custom delivery channels:

```php
use Litepie\Otp\Contracts\OtpChannelInterface;

class SlackChannel implements OtpChannelInterface
{
    public function send(string $identifier, string $code, array $data = []): bool
    {
        // Implementation for Slack delivery
        return true;
    }

    public function canHandle(string $identifier): bool
    {
        return str_starts_with($identifier, '@slack:');
    }
}

// Register the custom channel
Otp::extend('slack', function () {
    return new SlackChannel();
});
```

## 📊 Events

The package fires comprehensive events for monitoring and logging:

### Available Events

- **`OtpGenerated`** - When an OTP is generated
- **`OtpSent`** - When an OTP is sent via a channel
- **`OtpVerified`** - When an OTP is successfully verified
- **`OtpFailed`** - When OTP verification fails

### Event Listeners

```php
// In EventServiceProvider
protected $listen = [
    \Litepie\Otp\Events\OtpGenerated::class => [
        \App\Listeners\LogOtpGenerated::class,
    ],
    \Litepie\Otp\Events\OtpVerified::class => [
        \App\Listeners\LogOtpVerified::class,
        \App\Listeners\SendWelcomeEmail::class,
    ],
    \Litepie\Otp\Events\OtpFailed::class => [
        \App\Listeners\LogFailedOtpAttempt::class,
    ],
];
```

### Example Listener

```php
class LogOtpGenerated
{
    public function handle(\Litepie\Otp\Events\OtpGenerated $event)
    {
        Log::info('OTP generated', [
            'identifier' => $event->otp->identifier,
            'type' => $event->otp->type,
            'expires_at' => $event->otp->expires_at,
        ]);
    }
}
```

## 🛠️ Artisan Commands

### Cleanup Expired OTPs

```bash
# Clean up expired OTPs (default: 7 days)
php artisan otp:cleanup

# Clean up OTPs older than specific days
php artisan otp:cleanup --days=3

# Force cleanup without confirmation
php artisan otp:cleanup --force
```

## 🔒 Security Features

- **Digital Signing** - All OTPs are digitally signed using HMAC-SHA256
- **Rate Limiting** - Configurable rate limiting per identifier and type
- **Secure Generation** - Cryptographically secure random code generation
- **Attempt Tracking** - Track and limit verification attempts
- **Automatic Cleanup** - Remove expired OTPs automatically
- **Timing Attack Protection** - Use `hash_equals()` for secure comparisons

## 🧪 Testing

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test
vendor/bin/phpunit tests/Unit/OtpTest.php
```

### Test Example

```php
use Litepie\Otp\Facades\Otp;
use Illuminate\Support\Facades\Mail;

public function test_otp_generation_and_verification()
{
    Mail::fake();

    // Generate OTP
    $otp = Otp::generate()
        ->for('test@example.com')
        ->type('login')
        ->send();

    // Verify OTP
    $this->assertTrue(
        Otp::verify($otp->code, 'test@example.com', 'login')
    );

    // Assert mail was sent
    Mail::assertSent(\Litepie\Otp\Notifications\OtpNotification::class);
}
```

## 📚 Documentation

- **[Examples](EXAMPLES.md)** - Comprehensive usage examples
- **[Contributing](CONTRIBUTING.md)** - How to contribute
- **[Security](SECURITY.md)** - Security policy
- **[Changelog](CHANGELOG.md)** - Version history

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

```bash
git clone https://github.com/litepie/otp.git
cd otp
composer install
composer test
```

## 🔐 Security

If you discover a security vulnerability, please send an email to security@litepie.com. All security vulnerabilities will be promptly addressed.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 💖 Support

- **⭐ Star this repo** if you find it helpful
- **🐛 Report issues** on [GitHub Issues](https://github.com/litepie/otp/issues)
- **💡 Request features** via [GitHub Discussions](https://github.com/litepie/otp/discussions)
- **📧 Contact us** at support@litepie.com

---

<p align="center">
Made with ❤️ by <a href="https://github.com/litepie">Litepie</a>
</p>
