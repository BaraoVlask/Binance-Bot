<?php

namespace App\Console\Commands\Symbol;

use App\Enums\FiltersEnum;
use App\Models\Filter;
use App\Models\Symbol;
use App\Services\BinanceService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LoadDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'symbol:load {symbol : O symbol(par de moedas) que se deseja carregar as informações.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carrega informações sobre o symbol fornecido';

    /**
     * Execute the console command.
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $argumentSymbol = Str::upper($this->argument('symbol'));

        $symbol = Symbol::updateOrCreate(['name' => $argumentSymbol]);

        $response = BinanceService::getSpotClient()
            ->exchangeInfo(['symbol' => $argumentSymbol]);
        collect($response['symbols'][0]['filters'])
            ->filter(fn($filter) => FiltersEnum::tryFrom($filter['filterType']))
            ->each(
                fn($filter) => (new Filter(
                    [
                        'symbol_id' => $symbol->id,
                        'name' => $filter['filterType'],
                        'fields' => (object)$filter,
                    ]
                ))->save()
            );
    }
}
