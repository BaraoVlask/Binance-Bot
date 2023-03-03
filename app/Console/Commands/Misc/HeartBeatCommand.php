<?php

namespace App\Console\Commands\Misc;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\BufferedOutput;

class HeartBeatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heart:beat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se o robo esta funcionando';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $output = new BufferedOutput();

        Artisan::call('monitor:status', [], $output);

        if (Str::contains($output->fetch(), 'O monitor está parado!')) {
            TelegramService::sendMessage('<b>O ROBO não está funcionando!</b>');
        }

        return 0;
    }
}
