<?php

namespace App\Jobs\Order\Status;

use App\Enums\OrderStatusEnum;
use App\Jobs\Order\OpenOrderJob;
use App\Models\Order;
use App\Services\TelegramService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CancelledOrderJob implements ShouldQueue
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
        DB::transaction(
            function () {
                try {
                    if ($this->order->isDeleted()) {
                        $symbol = $this->order->symbol;

                        TelegramService::sendMessage(
                            "A ordem $symbol->name[{$this->order->type->value}]:{$this->order->price} foi excluída"
                        );
                    } else {
                        $order = Order::create(
                            [
                                'order_id' => $this->order
                                    ->id,
                                'price_range_id' => $this->order
                                    ->priceRange
                                    ->id,
                                'type' => $this->order
                                    ->type,
                                'quantity' => $this->order
                                    ->quantity,
                                'price' => $this->order
                                    ->price,
                                'side' => $this->order
                                    ->side,
                                'status' => OrderStatusEnum::New,
                            ]
                        );

                        OpenOrderJob::dispatch($order);
                    }
                } catch (Exception) {
                    TelegramService::sendMessage("Erro ao reabrir a faixa Id:{$this->order->id}");
                }
            }
        );
    }
}
