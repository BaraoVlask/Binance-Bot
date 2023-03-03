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
//        $schedule->command('heart:beat')
//            ->everyMinute();
        $schedule->command('accountListenKey:update')
            ->everyThirtyMinutes()
            ->appendOutputTo(storage_path('logs/schedule.worker.out.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        $this->load(__DIR__ . '/Commands/Account');
        $this->load(__DIR__ . '/Commands/Balance');
        $this->load(__DIR__ . '/Commands/Monitor');
        $this->load(__DIR__ . '/Commands/Orders');
        $this->load(__DIR__ . '/Commands/Protection');
        $this->load(__DIR__ . '/Commands/Symbol');

        require_once base_path('routes/console.php');
    }
}
