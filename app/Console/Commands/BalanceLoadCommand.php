<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Services\BinanceService;
use Illuminate\Console\Command;

class BalanceLoadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carrega informações de saldo da conta para os symbols (pares) cadastrados';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $account = BinanceService::getSpotClient()
            ->account();
        $balances = collect($account['balances']);
        Wallet::all()
            ->each(function (Wallet $wallet) use ($balances) {
                $balance = $balances->firstWhere('asset', $wallet->coin);
                $wallet->free = $balance['free'];
                $wallet->locked = $balance['locked'];
                $wallet->save();
            });
    }
}
