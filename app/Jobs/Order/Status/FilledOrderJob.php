<?php

namespace App\Jobs\Order\Status;

use App\Jobs\CalculateProfitJob;
use App\Jobs\Order\CreateInverseOrderJob;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FilledOrderJob implements ShouldQueue
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
        TelegramService::sendMessage("Ordem fechada, {$this->order->tag}");

        CreateInverseOrderJob::dispatch($this->order);

        if ($this->order->parentOrder()->exists()) {
            CalculateProfitJob::dispatch($this->order);
        }
    }
}
