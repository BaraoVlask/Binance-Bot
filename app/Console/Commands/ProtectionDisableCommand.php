<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ProtectionDisableCommand extends Command
{
    protected $signature = 'protection:disable';

    protected $description = 'Desativa a proteção de capital';

    public function handle(): void
    {
        Cache::forever('protectionEnabled', false);
    }
}
