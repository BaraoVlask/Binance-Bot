<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('heart:beat')
            ->everyMinute();
        $schedule->command('accountListenKey:update')
            ->hourly()
            ->appendOutputTo(storage_path('logs/schedule.worker.out.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require_once base_path('routes/console.php');
    }
}
