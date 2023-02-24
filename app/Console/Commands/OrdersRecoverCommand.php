<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Jobs\OpenOrderJob;
use App\Models\Symbol;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;

class OrdersRecoverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:recover';

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
        $this->withProgressBar(
            Symbol::all(),
            function (Symbol $symbol) {
                $orders = collect(
                    BinanceService::getSpotClient()
                        ->allOrders($symbol->name)
                );
                $orders->each(function (array $order) {
                    $dbOrder = Order::where('binance_id', $order['orderId'])
                        ->first();
                    if ($dbOrder instanceof Order) {
                        $dbOrder->update(
                            [
                                'executed_value' => $order['cummulativeQuoteQty'],
                                'status' => OrderStatusEnum::from($order['status']),
                                'payload' => $order,
                            ]
                        );
                    }
                });
            }
        );
    }
}
