<?php

namespace App\Jobs\Order;

use App\Enums\OrderTimeInForceEnum;
use App\Enums\OrderTradePreventionModeEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Report;
use App\Models\Order;
use App\Models\Symbol;
use App\Services\BinanceService;
use App\Services\TelegramService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class OpenOrderJob implements ShouldQueue
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
        $symbol = $this->order->symbol;

        if (Cache::get('monitorIsRunning', false) === false) {
            TelegramService::sendMessage("O monitor está parado, ordem não criada!");
            exit;
        }

        $this->open($this->order, $symbol);
    }

    /**
     * @throws MissingArgumentException
     */
    private function open(Order $order, Symbol $symbol): void
    {
        $binanceOrder = BinanceService::getSpotClient()
            ->setRequestTag("$symbol->name[{$this->order->type->value}]:{$this->order->price}")
            ->newOrder(
                $symbol->name,
                $order->side->value,
                OrderTypeEnum::Limit->value,
                [
                    'quantity' => $order->quantity,
                    'newClientOrderId' => $order->id,
                    'price' => $order->price,
                    'timeInForce' => OrderTimeInForceEnum::GoodTilCancel->value,
                    'selfTradePreventionMode' => OrderTradePreventionModeEnum::ExpireTaker->value,
                ]
            );

        (new Report(
            [
                'order_id' => $order->id,
                'stream' => 'newOrder',
                'report' => $binanceOrder
            ]
        ))->save();

        TelegramService::sendMessage("Ordem enviada, $order->tag");
    }
}
