<?php

namespace App\Jobs\Order;

use App\Enums\OrderStatusEnum;
use App\Models\Coin;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Order $order)
    {
    }

    public function handle(): void
    {
        $report = $this->order->accountReport()->report;
        $fields = [
            'status' => OrderStatusEnum::from($report->X),
            'amount' => (float)$report->Z,
        ];

        if ($fields['status'] === OrderStatusEnum::Filled) {
            $fields = array_merge(
                $fields,
                [
                    'commission_amount' => (float)$report->n,
                    'commission_coin' => Coin::where('name', $report->N)->first('id')->id,
                ]
            );
        }

        $this->order->update($fields);
    }
}
