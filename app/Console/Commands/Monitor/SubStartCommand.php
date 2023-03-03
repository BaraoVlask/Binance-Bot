<?php

namespace App\Console\Commands\Monitor;

use Illuminate\Console\Command;

class SubStartCommand extends Command
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
