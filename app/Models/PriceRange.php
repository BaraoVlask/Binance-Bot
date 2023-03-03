<?php

namespace App\Models;

use App\Jobs\Order\CreateOrderJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceRange extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(fn(PriceRange $priceRange) => CreateOrderJob::dispatch($priceRange));
    }

    /**
     * @return BelongsTo|Symbol
     */
    public function symbol(): BelongsTo|Symbol
    {
        return $this->belongsTo(Symbol::class);
    }

    /**
     * @return HasMany|Order
     */
    public function orders(): HasMany|Order
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return Attribute|float
     */

    public function quantity(): Attribute|float
    {
        return Attribute::make(
            get: fn($value) => round(
                $this->amount / $this->buy_price,
                $this->symbol->stepSizeRound,
                PHP_ROUND_HALF_DOWN
            ),
        );
    }
}
