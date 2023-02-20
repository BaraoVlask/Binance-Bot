<?php

namespace App\Jobs;

use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTimeInForceEnum;
use App\Enums\OrderTradePreventionModeEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Models\PriceRange;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected PriceRange $priceRange
    )
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        $binanceOrder = BinanceService::getSpotClient()
            ->newOrder(
                $this->priceRange->symbol->name,
                OrderSideEnum::Buy->value,
                OrderTypeEnum::Limit->value,
                [
                    'timeInForce' => OrderTimeInForceEnum::GoodTilCancel->value,
                    'quantity' => round(
                        $this->priceRange->amount / $this->priceRange->buy_price,
                        1,
                        PHP_ROUND_HALF_DOWN
                    ),
                    'price' => $this->priceRange->buy_price,
                    'selfTradePreventionMode' => OrderTradePreventionModeEnum::ExpireTaker->value,
                ]
            );
        Order::create(
            [
                'price_range_id' => $this->priceRange->id,
                'binance_id' => $binanceOrder['orderId'],
                'quantity' => $binanceOrder['executedQty'],
                'price' => $binanceOrder['price'],
                'side' => OrderSideEnum::from($binanceOrder['side']),
                'status' => OrderStatusEnum::from($binanceOrder['status']),
                'payload' => $binanceOrder,
            ]
        );
    }
}
