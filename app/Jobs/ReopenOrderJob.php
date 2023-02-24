<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\AccountReport;
use App\Models\Order;
use App\Services\TelegramService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReopenOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param AccountReport $accountReport
     */
    public function __construct(protected AccountReport $accountReport)
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
                $report = $this->accountReport->report;

                try {
                    $oldOrder = Order::find($report->C);

                    $order = Order::create(
                        [
                            'order_id' => $oldOrder->id,
                            'price_range_id' => $oldOrder->priceRange->id,
                            'type' => $oldOrder->type,
                            'quantity' => $oldOrder->quantity,
                            'price' => $oldOrder->price,
                            'side' => $oldOrder->side,
                            'status' => OrderStatusEnum::New,
                            'payload' => [],
                        ]
                    );

                    OpenOrderJob::dispatch($order);
                } catch (Exception) {
                    TelegramService::sendMessage("Erro ao reabrir a faixa Id:$report->C");
                }
            }
        );
    }
}
