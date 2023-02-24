<?php

namespace App\Services;

use Binance\Exception\MissingArgumentException;
use Binance\Spot;
use Illuminate\Support\Facades\Cache;
use Ratchet\Client\Connector as ClientConnector;
use React\EventLoop\Loop;
use React\Socket\Connector as SocketConnector;
use Binance\Websocket\Spot as Websocket;

class BinanceService
{
    public static function getSpotClient(): Spot
    {
        return new Spot(
            [
                'key' => config('services.binance.api.api_key'),
                'secret' => config('services.binance.api.api_secret')
            ]
        );
    }

    public static function getMarketLastPrice(string $symbol)
    {
        return self::getSpotClient()
            ->tickerPrice(
                [
                    'symbol' => $symbol
                ]
            )['price'];
    }

    public static function getWebsocketClient(): Websocket
    {
        $loop = Loop::get();
        return new Websocket(
            [
                'key' => config('services.binance.api.api_key'),
                'secret' => config('services.binance.api.api_secret'),
                'wsConnector' => new ClientConnector(
                    $loop,
                    new SocketConnector($loop)
                )
            ]
        );
    }

    public static function getAccountListenerKey(): string
    {
        $key = Cache::get(
            'getAccountListenerKey',
            self::getSpotClient()->newListenKey()['listenKey']
        );
        Cache::forever('getAccountListenerKey', $key);
        return $key;
    }

    /**
     * @throws MissingArgumentException
     */
    public static function updateAccountListenerKey(string $key): bool
    {
        return self::getSpotClient()->renewListenKey($key) === [];
    }

    /**
     * @throws MissingArgumentException
     */
    public static function deleteAccountListenerKey(string $key): bool
    {
        return self::getSpotClient()->closeListenKey($key) === [];
    }

}
