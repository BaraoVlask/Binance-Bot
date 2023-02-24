<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\AccountReport;
use App\Models\Coin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly AccountReport $accountReport)
    {
    }

    public function handle(): void
    {
        $order = $this->accountReport->order;
        $report = $this->accountReport->report;
        $status = OrderStatusEnum::from($report->X);
        $fields = [
            'status' => $status,
            'amount' => (float)$report->Z,
        ];

        if ($status === OrderStatusEnum::Filled) {
            $fields = array_merge(
                $fields,
                [
                    'commission_amount' => (float)$report->n,
                    'commission_coin' => Coin::firstOrCreate(
                        ['name' => $report->N],
                        ['name' => $report->N]
                    )->id,
                ]
            );
        }

        $order->update($fields);
    }
}
