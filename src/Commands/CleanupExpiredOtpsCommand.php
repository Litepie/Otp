<?php

namespace Litepie\Otp\Commands;

use Litepie\Otp\Otp;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupExpiredOtpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup 
                           {--days=7 : Delete OTPs expired more than this many days ago}
                           {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP records from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');

        $cutoffDate = Carbon::now()->subDays($days);
        
        $expiredCount = Otp::where('expires_at', '<', $cutoffDate)->count();

        if ($expiredCount === 0) {
            $this->info('No expired OTPs found to clean up.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredCount} expired OTP(s) older than {$days} days.");

        if (!$force && !$this->confirm('Do you want to delete these records?')) {
            $this->info('Cleanup cancelled.');
            return Command::SUCCESS;
        }

        $deletedCount = Otp::where('expires_at', '<', $cutoffDate)->delete();

        $this->info("Successfully deleted {$deletedCount} expired OTP record(s).");

        return Command::SUCCESS;
    }
}
