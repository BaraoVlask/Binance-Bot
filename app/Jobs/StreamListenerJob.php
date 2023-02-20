<?php

namespace App\Jobs;

use App\Models\AccountReport;
use App\Services\BinanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $client = BinanceService::getWebsocketClient();
        $client->combined(
            [
                BinanceService::getAccountListenerKey(),
                'busdbrl@ticker'
            ],
            [
                'message' => function ($conn, $payload) {
                    $json = json_decode($payload, false);

                    $accountReport = new AccountReport(
                        [
                            'stream' => $json->stream,
                            'report' => json_encode($json->data),
                        ]
                    );
                    $accountReport->save();
                },
                'close' => function ($conn) {}
            ]
        );
    }
}
