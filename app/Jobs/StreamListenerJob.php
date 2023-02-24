<?php

namespace App\Jobs;

use App\Models\AccountReport;
use App\Models\Order;
use App\Services\BinanceService;
use App\Services\TelegramService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        Cache::forever('monitorIsRunning', true);
        $client = BinanceService::getWebsocketClient();
        $client->combined(
            [
                BinanceService::getAccountListenerKey(),
            ],
            [
                'message' => function ($conn, $payload) {
                    try {
                        Log::channel('binance')
                            ->info('Payload', json_decode($payload, true));

                        $message = json_decode($payload, false);

                        $accountReport = new AccountReport([
                            'stream' => $message->data->e,
                            'report' => $message->data,
                        ]);
                        $accountReport->save();

                        match ($message->data->e) {
                            'balanceUpdate' => HandleBalanceUpdateJob::dispatch($message->data),
                            'executionReport' => ExecutionReportJob::dispatch($accountReport),
                            'outboundAccountPosition' => OutboundAccountPositionJob::dispatch($message->data),
                        };

                    } catch (Exception $exception) {
                        Log::error($exception->getMessage(), $exception->getTrace());
                    }
                },
                'close' => fn($conn) => TelegramService::sendMessage('O robo parou de funcionar')
            ]
        );
    }
}
