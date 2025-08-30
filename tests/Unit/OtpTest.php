<?php

namespace Litepie\Otp\Tests\Unit;

use Litepie\Otp\Tests\TestCase;
use Litepie\Otp\Facades\Otp;
use Litepie\Otp\Otp as OtpModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Litepie\Otp\Events\OtpGenerated;
use Litepie\Otp\Events\OtpSent;
use Litepie\Otp\Events\OtpVerified;

class OtpTest extends TestCase
{
    public function test_can_generate_otp()
    {
        Event::fake();

        $otp = Otp::generate()
            ->for('user@example.com')
            ->type('login')
            ->send();

        $this->assertInstanceOf(OtpModel::class, $otp);
        $this->assertEquals('user@example.com', $otp->identifier);
        $this->assertEquals('login', $otp->type);
        $this->assertEquals(6, strlen($otp->code));

        Event::assertDispatched(OtpGenerated::class);
        Event::assertDispatched(OtpSent::class);
    }

    public function test_can_verify_otp()
    {
        Event::fake();

        $otp = Otp::generate()
            ->for('user@example.com')
            ->type('login')
            ->send();

        $result = Otp::verify($otp->code, 'user@example.com', 'login');

        $this->assertTrue($result);
        Event::assertDispatched(OtpVerified::class);
    }

    public function test_invalid_otp_fails_verification()
    {
        $otp = Otp::generate()
            ->for('user@example.com')
            ->type('login')
            ->send();

        $result = Otp::verify('invalid', 'user@example.com', 'login');

        $this->assertFalse($result);
    }

    public function test_expired_otp_fails_verification()
    {
        $otp = Otp::generate()
            ->for('user@example.com')
            ->type('login')
            ->expiresIn(-60) // Expired 1 minute ago
            ->send();

        $result = Otp::verify($otp->code, 'user@example.com', 'login');

        $this->assertFalse($result);
    }

    public function test_can_check_if_otp_exists()
    {
        $this->assertFalse(Otp::exists('user@example.com', 'login'));

        Otp::generate()
            ->for('user@example.com')
            ->type('login')
            ->send();

        $this->assertTrue(Otp::exists('user@example.com', 'login'));
    }

    public function test_can_invalidate_otp()
    {
        $otp = Otp::generate()
            ->for('user@example.com')
            ->type('login')
            ->send();

        $this->assertTrue(Otp::exists('user@example.com', 'login'));

        Otp::invalidate('user@example.com', 'login');

        $this->assertFalse(Otp::exists('user@example.com', 'login'));
    }

    public function test_custom_otp_length()
    {
        $otp = Otp::generate()
            ->for('user@example.com')
            ->length(8)
            ->send();

        $this->assertEquals(8, strlen($otp->code));
    }

    public function test_alphanumeric_otp_format()
    {
        $otp = Otp::generate()
            ->for('user@example.com')
            ->format('alphanumeric')
            ->send();

        $this->assertTrue(ctype_alnum($otp->code));
    }

    public function test_email_channel_sends_mail()
    {
        Mail::fake();

        Otp::generate()
            ->for('user@example.com')
            ->via('email')
            ->send();

        Mail::assertSent(\Litepie\Otp\Notifications\OtpNotification::class);
    }
}
