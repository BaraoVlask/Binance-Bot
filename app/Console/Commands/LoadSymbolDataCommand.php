<?php

namespace App\Console\Commands;

use App\Models\DTO\FilterFields;
use App\Models\Symbol;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LoadSymbolDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'symbol:load {symbol : O symbolo(par de moedas) que se deseja carregar as informações.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carrega informações sobre o symbolo fornecido';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $argumentSymbol = Str::upper($this->argument('symbol'));

        $response = Http::binance()->get("/exchangeInfo?symbol=$argumentSymbol");

        if ($response->failed()) {
            $this->error($response->json()['msg']);
        }

        $response = $response->json()['symbols'][0];

        $filters = collect($response['filters']);

        $priceFilter = $filters->firstWhere('filterType', 'PRICE_FILTER');

        $symbol = Symbol::firstOrCreate(
            [
                'name' => $argumentSymbol,
                'tick_size' => $priceFilter['tickSize'],
                'round_length' => Str::length(Str::before($priceFilter['tickSize'], '1')) - 1,
            ]
        );

        $filters->each(
            function ($filter) use ($symbol) {
                $name = Str::replace('_', ' ', Str::lower($filter['filterType']));
                $symbol->filters()
                    ->create(
                        [
                            'name' => $name,
                            'fields' => new FilterFields($filter),
                        ]
                    );
            }
        );
    }
}
