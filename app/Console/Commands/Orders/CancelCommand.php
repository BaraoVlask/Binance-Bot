<?php

namespace App\Console\Commands\Orders;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Services\BinanceService;
use App\Services\TelegramService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CancelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Para o monitor e cancela todas as ordens em aberto';

    /**
     * Execute the console command.
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        Artisan::call('monitor:stop');
        Order::with(
            [
                'priceRange',
                'priceRange.symbol'
            ]
        )
            ->whereIn(
                'status',
                [
                    OrderStatusEnum::New,
                    OrderStatusEnum::PartiallyFilled
                ]
            )
            ->get()
            ->each(
                function (Order $order) {
                    $binanceOrder = BinanceService::getSpotClient()
                        ->getOrder($order->priceRange->symbol->name, ['origClientOrderId' => $order->id]);

                    $currentStatus = OrderStatusEnum::from($binanceOrder['status']);

                    if (in_array($currentStatus, [OrderStatusEnum::New, OrderStatusEnum::PartiallyFilled])) {
                        BinanceService::getSpotClient()
                            ->cancelOrder($order->priceRange->symbol->name, ['origClientOrderId' => $order->id]);
                        $order->update(['status' => OrderStatusEnum::Cancelled]);
                    }

                    $order->update(['status' => $currentStatus]);

                    TelegramService::sendMessage("Faixa {$order->priceRange->buy_price} encerrda.");

                }
            );
    }
}
