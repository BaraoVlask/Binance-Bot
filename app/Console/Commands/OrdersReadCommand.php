<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Jobs\OpenOrderJob;
use App\Models\Symbol;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;

class OrdersReadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:read {symbol : Par a ser lido} {orderId : Id da ordem }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        $symbol = $this->argument('symbol');
        $orderId = $this->argument('orderId');

        dump(
            BinanceService::getSpotClient()
                ->getOrder($symbol, $orderId ? ['orderId' => $orderId] : [])
        );
    }
}
