<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\AccountReport;
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
        $report = $this->accountReport->report;
        $this->accountReport->update(['order_id' => $report->c]);

        if ($report->e === 'executionReport') {
            UpdateOrderJob::dispatch($this->accountReport);

            match (OrderStatusEnum::from($report->X)) {
                OrderStatusEnum::Cancelled => ReopenOrderJob::dispatch($this->accountReport),
                OrderStatusEnum::Filled => HandleFilledOrderJob::dispatch($this->accountReport->order),
                default => null,
            };
        }
    }
}
