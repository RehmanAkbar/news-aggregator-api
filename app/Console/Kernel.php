<?php

namespace App\Console;

use App\Jobs\SyncArticlesJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         // Schedule article syncing to run daily at midnight
        $schedule->job(new SyncArticlesJob())
            ->dailyAt('00:00')
            ->onQueue('articles')
            ->appendOutputTo(storage_path('logs/article-sync.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
