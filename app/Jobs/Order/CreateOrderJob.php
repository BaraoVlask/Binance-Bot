<?php

namespace App\Jobs\Order;

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
        (new Order(
            [
                'price_range_id' => $this->priceRange->id,
                'quantity' => $this->priceRange->quantity,
                'price' => $this->priceRange->buy_price,
                'side' => OrderSideEnum::Buy,
                'status' => OrderStatusEnum::New,
            ]
        ))->save();
    }
}
