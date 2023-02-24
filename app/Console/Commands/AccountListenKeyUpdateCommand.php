<?php

namespace App\Console\Commands;

use App\Services\BinanceService;
use Binance\Exception\MissingArgumentException;
use Illuminate\Console\Command;

class AccountListenKeyUpdateCommand extends Command
{
    protected $signature = 'accountListenKey:update';

    protected $description = 'Atualiza a chave de stream da conta';

    /**
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        BinanceService::updateAccountListenerKey(
            BinanceService::getAccountListenerKey()
        );
    }
}
