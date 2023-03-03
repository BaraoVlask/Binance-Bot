<?php

namespace App\Jobs\Order;

use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateInverseOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Order $order)
    {
    }

    public function handle(): void
    {
        $priceRange = $this->order->priceRange;

        $sideIsBuy = $this->order->side === OrderSideEnum::Buy;

        (new Order([
            'price_range_id' => $priceRange->id,
            'order_id' => $this->order->id,
            'quantity' => $priceRange->quantity,
            'status' => OrderStatusEnum::New,
            'price' => $sideIsBuy ? $priceRange->sell_price : $priceRange->buy_price,
            'side' => $sideIsBuy ? OrderSideEnum::Sell : OrderSideEnum::Buy,
            'type' => $this->order->type,
        ]))->save();
    }
}
