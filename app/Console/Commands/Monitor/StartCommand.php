<?php

namespace App\Console\Commands\Monitor;

use Illuminate\Console\Command;

class StartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:start';

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
        print `echo /usr/bin/php -q /var/www/binance-bot/artisan monitor:sub-start | at now`;
    }
}
