<?php

namespace App\Jobs;

use App\Enums\OrderDatabaseTypeEnum;
use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\PriceRange;
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
     * @param PriceRange $priceRange
     */
    public function __construct(protected PriceRange $priceRange)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->default($this->priceRange);
        if ($this->priceRange?->protectionSymbol) {
            $this->protection($this->priceRange);
        }
    }

    private function default(PriceRange $priceRange): void
    {
        $order = new Order(
            [
                'price_range_id' => $priceRange->id,
                'quantity' => $priceRange->quantity,
                'price' => $priceRange->buy_price,
                'side' => OrderSideEnum::Buy,
                'status' => OrderStatusEnum::New,
            ]
        );
        $order->save();
    }

    protected function protection(PriceRange $priceRange): void
    {
        $order = new Order(
            [
                'price_range_id' => $priceRange->id,
                'quantity' => $priceRange->quantity,
                'price' => $priceRange->sell_price,
                'side' => OrderSideEnum::Sell,
                'type' => OrderDatabaseTypeEnum::Protection,
                'status' => OrderStatusEnum::New,
            ]
        );
        $order->save();
    }
}
