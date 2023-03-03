<?php

namespace App\Jobs\Order;

use App\Models\Order;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(protected Order $order)
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
        BinanceService::getSpotClient()
            ->setRequestTag("{$this->order->tag}")
            ->cancelOrder($this->order->symbol->name, ['orderId' => $this->order->binance_id]);
    }
}
