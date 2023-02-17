<?php

namespace App\Console\Commands;

use Amp\Loop;
use Amp\Websocket\Client\Rfc6455Connection;
use App\Jobs\StreamListenerJob;
use App\Models\AccountReport;
use Http;
use Amp\Websocket;
use Amp\Websocket\Client;
use Illuminate\Console\Command;


class Teste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        StreamListenerJob::dispatch();
    }
}
