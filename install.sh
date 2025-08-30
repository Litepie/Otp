#!/bin/bash

# Laravel OTP Package Installation Script

echo "🚀 Installing Laravel OTP Package..."

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This doesn't appear to be a Laravel project (no artisan file found)"
    exit 1
fi

echo "📦 Installing package dependencies..."
composer install

echo "📄 Publishing configuration..."
php artisan vendor:publish --provider="Litepie\Otp\OtpServiceProvider" --tag="config"

echo "🗄️  Running migrations..."
php artisan migrate

echo "🧹 Setting up cleanup schedule..."
echo ""
echo "Add the following to your app/Console/Kernel.php schedule() method:"
echo ""
echo "    \$schedule->command('otp:cleanup')->daily();"
echo ""

echo "✅ Installation complete!"
echo ""
echo "📚 Next steps:"
echo "1. Configure your OTP settings in config/otp.php"
echo "2. Set up your email and SMS providers"
echo "3. Add OTP cleanup to your scheduler"
echo "4. Check the EXAMPLES.md file for usage examples"
echo ""
echo "🎉 Happy coding!"
