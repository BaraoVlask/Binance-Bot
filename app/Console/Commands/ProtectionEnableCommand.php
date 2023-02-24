<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ProtectionEnableCommand extends Command
{
    protected $signature = 'protection:enabled';

    protected $description = 'Ativa a proteção de capital';

    public function handle(): void
    {
        Cache::forever('protectionEnabled', true);
    }
}
