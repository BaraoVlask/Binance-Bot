<?php

namespace App\Console\Commands\Orders;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecoverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:recover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->withProgressBar(
            Order::all(),
            function (Order $order) {
                $latestReport = $order->accountReport();
                if ($latestReport) {
                    $order->update(
                        [
                            'amount' => $order['cummulativeQuoteQty'] ?? 0,
                            'status' => OrderStatusEnum::from($latestReport->report->X),
                        ]
                    );
                } else {
                    Log::warning("Ordem sem report: $order->id");
                }
            }
        );
    }
}
