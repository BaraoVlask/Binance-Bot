<?php

namespace App\Jobs;

use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTimeInForceEnum;
use App\Enums\OrderTradePreventionModeEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Models\Symbol;
use App\Services\BinanceService;
use App\Services\TelegramService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
     */
    public function handle(): void
    {
        $symbol = $this->order->isDefault()
            ? $this->order->priceRange->symbol
            : $this->order->priceRange->protectionSymbol;

        try {
            if (Cache::get('monitorIsRunning', false) === false) {
                TelegramService::sendMessage("O monitor está parado, ordem não criada!");
                exit;
            }

            $this->open($this->order, $symbol);
        } catch (Exception $exception) {
            TelegramService::sendMessage(
                "Erro ao criar a $symbol->name[{$this->order->type->value}]:{$this->order->price}"
            );
            Log::error($exception->getMessage(), $exception->getTrace());
        }
    }

    private function open(Order $order, Symbol $symbol): void
    {
        try {
            if ($order->side === OrderSideEnum::Buy) {
                $type = $order->price >= BinanceService::getMarketLastPrice($symbol->name)
                    ? OrderTypeEnum::Market
                    : OrderTypeEnum::Limit;
            } else {
                $type = $order->price <= BinanceService::getMarketLastPrice($symbol->name)
                    ? OrderTypeEnum::Market
                    : OrderTypeEnum::Limit;
            }

            $options = [
                'quantity' => $order->quantity,
                'newClientOrderId' => $order->id,
            ];

            if ($type === OrderTypeEnum::Limit) {
                $options = array_merge(
                    $options,
                    [
                        'price' => $order->price,
                        'timeInForce' => OrderTimeInForceEnum::GoodTilCancel->value,
                        'selfTradePreventionMode' => OrderTradePreventionModeEnum::ExpireTaker->value,
                    ]
                );
            }

            $binanceOrder = BinanceService::getSpotClient()
                ->newOrder($symbol->name, $order->side->value, $type->value, $options);

            $order->update([
                'binance_id' => $binanceOrder['orderId'],
                'payload' => $binanceOrder,
                'status' => OrderStatusEnum::from($binanceOrder['status']),
            ]);

            Log::channel('binance')
                ->info('New order', $binanceOrder);

            $lado = $order->side === OrderSideEnum::Buy
                ? 'compra'
                : 'venda';

            $operacao = $order->order_id
                ? 're-aberta'
                : 'aberta';

            TelegramService::sendMessage(
                "Ordem de $lado, $operacao faixa $symbol->name[{$order->type->value}]:$order->price"
            );
        } catch (Exception $exception) {
            TelegramService::sendMessage("Error: {$exception->getMessage()}");
        }
    }
}
