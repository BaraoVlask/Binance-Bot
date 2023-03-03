<?php

namespace App\Jobs\Streams;

use App\Models\Coin;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BalanceUpdateJob implements ShouldQueue
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
        $coin = Coin::where('name', $this->data->a)->first();
        if ($coin) {
            $wallet = Wallet::where('coin_id', $coin->id)->first();
            if (is_null($wallet)) {
                $wallet = new Wallet(['coin_id' => $coin->id]);
            }

            $wallet->free = $this->data->f;
            $wallet->locked = $this->data->l;
            $wallet->save();
        }
    }
}
