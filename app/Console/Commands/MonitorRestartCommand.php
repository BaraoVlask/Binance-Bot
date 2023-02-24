<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MonitorRestartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reinicia o robo';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Artisan::call('monitor:stop');
        Artisan::call('monitor:start');
    }
}
