<?php

namespace App\Jobs;

use Amp\Loop;
use Amp\Websocket\Client\Rfc6455Connection;
use App\Models\AccountReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Http;
use Amp\Websocket;
use Amp\Websocket\Client;

class StreamListenerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $response = Http::binance()
            ->post('https://api.binance.com/api/v3/userDataStream', null);
        $listenKey = $response->json()['listenKey'];
        Loop::run(function () use ($listenKey) {
            /** @var Rfc6455Connection $connection */
            $connection = yield Client\connect(
                "wss://stream.binance.com:9443/stream?streams=$listenKey/busdbrl@ticker"
            );

            /** @var Websocket\Message $message */
            while ($message = yield $connection->receive()) {
                $payload = yield $message->buffer();

                $json = json_decode($payload, false);

                $accountReport = new AccountReport(
                    [
                        'stream' => $json->stream,
                        'report' => json_encode($json->data),
                    ]
                );
                $accountReport->save();
            }
        });
    }
}
