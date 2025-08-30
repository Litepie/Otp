# OTP Package Usage Examples

This file contains various usage examples for the Laravel OTP package.

## Basic Usage Examples

### Generate and Send OTP

```php
<?php

use Litepie\Otp\Facades\Otp;

// Basic OTP generation and sending
$otp = Otp::generate()
    ->for('user@example.com')
    ->type('login')
    ->send();

echo "OTP sent successfully! ID: " . $otp->id;
```

### Verify OTP

```php
<?php

use Litepie\Otp\Facades\Otp;

// Verify an OTP
$code = request('otp_code'); // Get from user input
$email = 'user@example.com';

try {
    $isValid = Otp::verify($code, $email, 'login');
    
    if ($isValid) {
        // OTP is valid, proceed with login
        echo "OTP verified successfully!";
    } else {
        // OTP is invalid
        echo "Invalid OTP code.";
    }
} catch (\Litepie\Otp\Exceptions\TooManyAttemptsException $e) {
    echo "Too many failed attempts. Please try again later.";
}
```

## Advanced Usage Examples

### Email Verification Flow

```php
<?php

use Litepie\Otp\Facades\Otp;

// During user registration
class UserController extends Controller
{
    public function register(Request $request)
    {
        // Create user account (but mark as unverified)
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        // Send verification OTP
        Otp::generate()
            ->for($user->email)
            ->type('email_verification')
            ->length(6)
            ->expiresIn(600) // 10 minutes
            ->send();

        return redirect()->route('verify.email')
            ->with('message', 'Please check your email for verification code.');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        try {
            $isValid = Otp::verify($request->code, $request->email, 'email_verification');
            
            if ($isValid) {
                // Mark user as verified
                User::where('email', $request->email)
                    ->update(['email_verified_at' => now()]);
                
                return redirect()->route('dashboard')
                    ->with('success', 'Email verified successfully!');
            }
        } catch (\Litepie\Otp\Exceptions\TooManyAttemptsException $e) {
            return back()->withErrors(['code' => 'Too many failed attempts.']);
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }
}
```

### Password Reset Flow

```php
<?php

use Litepie\Otp\Facades\Otp;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users']);

        try {
            Otp::generate()
                ->for($request->email)
                ->type('password_reset')
                ->length(8)
                ->format('alphanumeric')
                ->expiresIn(900) // 15 minutes
                ->send();

            return back()->with('status', 'Password reset code sent!');
        } catch (\Litepie\Otp\Exceptions\RateLimitExceededException $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:8',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $isValid = Otp::verify($request->code, $request->email, 'password_reset');
            
            if ($isValid) {
                User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);
                
                return redirect()->route('login')
                    ->with('success', 'Password reset successfully!');
            }
        } catch (\Litepie\Otp\Exceptions\TooManyAttemptsException $e) {
            return back()->withErrors(['code' => 'Too many failed attempts.']);
        }

        return back()->withErrors(['code' => 'Invalid reset code.']);
    }
}
```

### Two-Factor Authentication

```php
<?php

use Litepie\Otp\Facades\Otp;

class TwoFactorController extends Controller
{
    public function enable(Request $request)
    {
        $user = auth()->user();
        
        // Send OTP to verify phone number
        Otp::generate()
            ->for($user->phone)
            ->type('two_factor')
            ->length(6)
            ->expiresIn(180) // 3 minutes
            ->via(['sms']) // Use SMS channel
            ->send();

        return response()->json(['message' => 'Verification code sent to your phone.']);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        
        $user = auth()->user();
        
        try {
            $isValid = Otp::verify($request->code, $user->phone, 'two_factor');
            
            if ($isValid) {
                $user->update(['two_factor_enabled' => true]);
                return response()->json(['message' => 'Two-factor authentication enabled.']);
            }
        } catch (\Litepie\Otp\Exceptions\TooManyAttemptsException $e) {
            return response()->json(['error' => $e->getMessage()], 429);
        }

        return response()->json(['error' => 'Invalid verification code.'], 422);
    }
}
```

### Custom Channel Usage

```php
<?php

use Litepie\Otp\Facades\Otp;

// Send OTP via multiple channels
$otp = Otp::generate()
    ->for('user@example.com')
    ->type('login')
    ->via(['email', 'sms', 'database']) // Multiple channels
    ->with(['user_id' => 123]) // Additional data
    ->send();

// Check if OTP exists before sending a new one
if (!Otp::exists('user@example.com', 'login')) {
    $otp = Otp::generate()
        ->for('user@example.com')
        ->type('login')
        ->send();
} else {
    echo "OTP already sent. Please check your email.";
}

// Invalidate existing OTP
Otp::invalidate('user@example.com', 'login');
```

### Event Listeners

```php
<?php

use Litepie\Otp\Events\OtpGenerated;
use Litepie\Otp\Events\OtpVerified;
use Litepie\Otp\Events\OtpFailed;

// In EventServiceProvider
protected $listen = [
    OtpGenerated::class => [
        LogOtpGenerated::class,
    ],
    OtpVerified::class => [
        LogOtpVerified::class,
        SendWelcomeEmail::class,
    ],
    OtpFailed::class => [
        LogFailedOtpAttempt::class,
    ],
];

// Example listener
class LogOtpGenerated
{
    public function handle(OtpGenerated $event)
    {
        Log::info('OTP generated', [
            'identifier' => $event->otp->identifier,
            'type' => $event->otp->type,
            'expires_at' => $event->otp->expires_at,
        ]);
    }
}
```

### Artisan Commands

```bash
# Clean up expired OTPs
php artisan otp:cleanup

# Clean up OTPs older than 3 days
php artisan otp:cleanup --days=3

# Force cleanup without confirmation
php artisan otp:cleanup --force
```

### Configuration Examples

```php
<?php

// In config/otp.php

// Custom OTP type configuration
'types' => [
    'custom_verification' => [
        'length' => 8,
        'format' => 'alphanumeric',
        'expires_in' => 1800, // 30 minutes
        'max_attempts' => 5,
        'channels' => ['email'],
        'rate_limit' => [
            'max_attempts' => 3,
            'decay_minutes' => 60,
        ],
    ],
],

// Custom channel configuration
'channels' => [
    'email' => [
        'subject' => 'Your Custom App Verification Code',
        'message' => 'Your verification code for Custom App is: {code}',
        'greeting' => 'Welcome to Custom App!',
    ],
    'sms' => [
        'provider' => 'twilio',
        'message' => 'CustomApp: Your code is {code}',
    ],
],
```

## Testing Examples

```php
<?php

use Litepie\Otp\Facades\Otp;
use Illuminate\Support\Facades\Mail;

class OtpTest extends TestCase
{
    public function test_otp_generation_and_verification()
    {
        Mail::fake();

        // Generate OTP
        $otp = Otp::generate()
            ->for('test@example.com')
            ->type('test')
            ->send();

        // Verify OTP
        $this->assertTrue(
            Otp::verify($otp->code, 'test@example.com', 'test')
        );

        // Verify mail was sent
        Mail::assertSent(\Litepie\Otp\Notifications\OtpNotification::class);
    }
}
```
