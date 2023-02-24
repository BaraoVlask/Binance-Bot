<?php

namespace App\Console\Commands;

use App\Jobs\StreamListenerJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MonitorStartCommand extends Command
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
