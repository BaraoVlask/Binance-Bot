<?php

namespace App\Jobs\Streams;

use App\Enums\OrderStatusEnum;
use App\Jobs\Order\Status\CancelledOrderJob;
use App\Jobs\Order\Status\FilledOrderJob;
use App\Jobs\Order\Status\NewOrderJob;
use App\Jobs\Order\UpdateOrderJob;
use App\Models\Report;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecutionReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Report $accountReport)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $report = $this->accountReport
            ->report;

        $orderId = null;

        if (is_numeric($report->c)) {
            $orderId = $report->c;
        }

        if (is_numeric($report->C)) {
            $orderId = $report->C;
        }

        if ($orderId) {
            $this->accountReport->update(['order_id' => $orderId]);

            if ($report->e === 'executionReport') {
                UpdateOrderJob::dispatch($this->accountReport->order);

                match (OrderStatusEnum::from($report->X)) {
                    OrderStatusEnum::New => NewOrderJob::dispatch($this->accountReport->order),
                    OrderStatusEnum::Cancelled => CancelledOrderJob::dispatch($this->accountReport->order),
                    OrderStatusEnum::Filled => FilledOrderJob::dispatch($this->accountReport->order),
                    default => null,
                };
            }
        } else {
            TelegramService::sendMessage(
                "Report de ordem não identificada \r\n" . print_r($report, true)
            );
        }

    }
}
