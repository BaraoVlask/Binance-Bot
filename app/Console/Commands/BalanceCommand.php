<?php

namespace App\Console\Commands;

use App\Enums\OrderDatabaseTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class BalanceCommand extends Command
{
    protected $signature = 'balance';

    protected $description = 'Command description';

    public function handle(): void
    {
        $orders = Order::where('status', OrderStatusEnum::Filled)
            ->where('type', OrderDatabaseTypeEnum::Normal)
            ->get()
            ->toArray();

        $orders = collect($orders)
            ->groupBy('side')
            ->transform(function (Collection $side){
                return $side->sum('executed_value');
            })
            ->dd();
    }
}
