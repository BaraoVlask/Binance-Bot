<?php

namespace App\Console\Commands\Orders;

use App\Jobs\Order\CreateOrderJob;
use App\Models\PriceRange;
use Illuminate\Console\Command;

class RecreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:recreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create orders for price ranges that does not have';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->withProgressBar(
            PriceRange::whereDoesntHave('orders')->get(),
            fn(PriceRange $priceRange) => CreateOrderJob::dispatch($priceRange)
        );
    }
}
