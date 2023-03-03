<?php

namespace App\Console\Commands\Orders;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Symbol;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;

class StatusComparisonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        $rows = [];
        $this->withProgressBar(
            Symbol::all(),
            function (Symbol $symbol) use (&$rows) {
                collect(BinanceService::getSpotClient()->allOrders($symbol->name))
                    ->each(function (array $order) use ($symbol, &$rows) {
                        $dbOrder = Order::where('binance_id', $order['orderId'])
                            ->withTrashed()
                            ->first();
                        if (OrderStatusEnum::from($order['status'])->value !== $dbOrder?->status->value) {
                            $rows[] = [
                                $symbol->name,
                                $dbOrder?->id,
                                $dbOrder?->status->value,
                                $order['orderId'],
                                $order['status'],
                            ];
                        }
                    });
            }
        );
        $this->newLine();
        $this->table(
            [
                'Symbol',
                'OrderId',
                'Status',
                'BinanceId',
                'Status',
            ],
            $rows
        );
    }
}
