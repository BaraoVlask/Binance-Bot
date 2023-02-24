<?php

namespace App\Console\Commands;

use App\Jobs\StreamListenerJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MonitorSubStartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:sub-start';
    protected $hidden = true;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitora as variações de preço e as operações efetuadas na conta';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        dispatch((new \App\Jobs\StreamListenerJob())
            ->onQueue('monitor'))
            ->onConnection('sync');
    }
}
