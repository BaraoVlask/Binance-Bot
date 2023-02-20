<?php

namespace App\Console\Commands;

use App\Jobs\StreamListenerJob;
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
