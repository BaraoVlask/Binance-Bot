<?php

namespace App\Jobs;

use App\Enums\OrderDatabaseTypeEnum;
use App\Enums\OrderSideEnum;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateProfitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param Order $order
     * @param Order $parentOrder
     */
    public function __construct(protected Order $order, protected Order $parentOrder)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
//        $balance = 0;
//        if (
//            $this->order->type === OrderDatabaseTypeEnum::Normal
//            && $this->order->side === OrderSideEnum::Sell
//        ) {
//            $balance = $this->parentOrder->amount - $this->order->amount;
//        }
//        if (
//            $this->order->type === OrderDatabaseTypeEnum::Protection
//            && $this->order->side === OrderSideEnum::Buy
//        ) {
//            $balance = $this->order->amount - $this->parentOrder->amount;
//        }
//        TelegramService::sendMessage("Profit {$this->order->id}:$balance");
    }

}
