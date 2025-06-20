<?php

namespace App\Jobs;

use App\Models\Coin;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OutboundAccountPositionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected object $data)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        collect($this->data->B)
            ->each(
                function (object $asset) {
                    $coin = Coin::firstWhere('name', $asset->a);
                    if (is_null($coin)) {
                        $coin = new Coin(['name' => $asset->a]);
                        $coin->save();
                    }

                    $wallet = Wallet::where('coin_id', $coin->id)->first();
                    if (is_null($wallet)) {
                        $wallet = new Wallet(['coin_id' => $coin->id]);
                    }

                    $wallet->free = $asset->f;
                    $wallet->locked = $asset->l;
                    $wallet->save();
                }
            );
    }
}
