<?php

namespace App\Console\Commands\Orders;

use App\Models\Order;
use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class HistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:history {symbol} {--filter=*}';

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
        $symbol = Str::upper($this->argument('symbol'));
        $rows = [];
        $this->withProgressBar(
            BinanceService::getSpotClient()->allOrders($symbol),
            function (array $order) use (&$rows) {
                $dbOrder = Order::where('binance_id', $order['orderId'])->first();
                $rows[] = [
                    $order['symbol'],
                    $dbOrder?->id,
                    $order['orderId'],
                    $dbOrder?->price ?? $order['price'],
                    $order['origQty'],
                    $order['executedQty'],
                    $order['cummulativeQuoteQty'],
                    $order['status'],
                    $order['side'],
                    $order['type'],
                ];
            }
        );
        $rows = collect($rows)->sortBy('orderId');

        if ($this->option('filter')) {
            $filter = $this->option('filter');
            $key = $filter[0];
            $value = $filter[1];
            $rows = $rows->filter(fn(array $item) => $item[$key] === $value);
        }
        $this->newLine();
        $this->table(
            [
                'Symbol',
                'ID',
                'OrderId',
                'Price',
                'OQuantity',
                'EQuantity',
                'CQuantity',
                'Status',
                'Side',
                'Type',
            ],
            $rows
        );
    }
}
