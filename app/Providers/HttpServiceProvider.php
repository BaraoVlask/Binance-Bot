<?php

namespace App\Providers;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Http::macro(
            'binance',
            function () {
                return Http::withHeaders(
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'X-MBX-APIKEY' => config('services.binance.api.api_key'),
                    ]
                )
                    ->baseUrl(config('services.binance.api.domain'));
            }
        );
    }
}
