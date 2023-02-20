<?php

namespace App\Listeners;

use App\Events\PriceRangeCreatedEvent;
use App\Jobs\CreateOrderJob;

class PriceRangeCreatedListener
{
    /**
     * Handle the event.
     *
     * @param PriceRangeCreatedEvent $event
     * @return void
     */
    public function handle(PriceRangeCreatedEvent $event): void
    {
        CreateOrderJob::dispatch($event->priceRange);
    }
}
