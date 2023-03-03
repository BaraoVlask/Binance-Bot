<?php

namespace App\Jobs\Order\Status;

use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
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
        $order = $this->order;

        $accountReport = $order->accountReport();

        $order->update([
            'binance_id' => $accountReport->report->i,
            'status' => OrderStatusEnum::from($accountReport->report->X),
        ]);

        $lado = $order->side === OrderSideEnum::Buy ? 'compra' : 'venda';

        $operacao = $order->parentOrder->exists()
            && $order->parentOrder->status === OrderStatusEnum::Cancelled
            ? 're-aberta'
            : 'aberta';

        TelegramService::sendMessage("Ordem de $lado, foi $operacao para $order->tag");
    }
}
