<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Jobs\OpenOrderJob;
use Illuminate\Console\Command;

class OrdersOpenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:open';

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
        Order::with('priceRange')
            ->where('status', OrderStatusEnum::New)
            ->whereNull('binance_id')
            ->get()
            ->each(fn(Order $order) => OpenOrderJob::dispatch($order));
    }
}
