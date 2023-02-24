<?php

namespace App\Jobs;

use App\Enums\OrderDatabaseTypeEnum;
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

        $fields = [
            'price_range_id' => $priceRange->id,
            'quantity' => $priceRange->quantity,
            'price' => $this->order->side === OrderSideEnum::Buy
                ? $priceRange->sell_price
                : $priceRange->buy_price,
            'side' => $this->order->side === OrderSideEnum::Buy
                ? OrderSideEnum::Sell
                : OrderSideEnum::Buy,
            'status' => OrderStatusEnum::New,
        ];

        if ($this->order->type === OrderDatabaseTypeEnum::Protection) {
            $fields['type'] = OrderDatabaseTypeEnum::Protection;
            if ($this->order->side === OrderSideEnum::Buy) {
                $fields['order_id'] = $this->order->id;
            }
        } elseif ($this->order->side === OrderSideEnum::Sell) {
            $fields['order_id'] = $this->order->id;
        }

        (new Order($fields))->save();
    }
}
