<?php

namespace App\Console\Commands\Orders;

use App\Enums\OrderStatusEnum;
use App\Jobs\Order\OpenOrderJob;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class OpenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:open {--id=} {--limit=99}';

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
            Order::with('priceRange')
                ->where('status', OrderStatusEnum::New)
                ->whereNull('binance_id')
                ->when(
                    $this->option('limit'),
                    fn(Builder $builder) => $builder->limit($this->option('limit'))
                )
                ->when(
                    $this->option('id'),
                    fn(Builder $builder) => $builder->where('id', $this->option('id'))
                )
                ->get(),
            fn(Order $order) => OpenOrderJob::dispatch($order)
        );
    }
}
