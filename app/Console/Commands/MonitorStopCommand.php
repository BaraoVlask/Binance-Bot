<?php

namespace App\Console\Commands;

use App\Jobs\StreamListenerJob;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorStopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desliga o robo';

    /**
     * Execute the console command.
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        exec("ps aux | grep 'monitor:sub-start' > storage/app/monitor.txt");
        collect(
            explode(
                "\n",
                preg_replace(
                    '/\s\s+/',
                    ' ',
                    Storage::disk('local')
                        ->get('monitor.txt')
                )
            )
        )
            ->transform(fn($linha) => collect(explode(' ', $linha)))
            ->filter(fn($arr) => count($arr) > 10)
            ->each(function ($arr) {
                if (
                    ($arr[10] != 'grep' && !isset($arr[15]))
                    || (isset($arr[15]) && $arr[15] != 'grep' && $arr[10] != 'grep')
                ) {
                    exec("kill $arr[1]");
                }
            });
        Cache::forever('monitorIsRunning', false);
        BinanceService::deleteAccountListenerKey(
            BinanceService::getAccountListenerKey()
        );
        Cache::forget('getAccountListenerKey');
    }
}
