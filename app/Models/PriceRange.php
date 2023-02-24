<?php

namespace App\Models;

use App\Jobs\CreateOrderJob;
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
     * @return BelongsTo|Symbol
     */
    public function protectionSymbol(): BelongsTo|Symbol
    {
        return $this->belongsTo(Symbol::class, 'protection_symbol_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function quantity(): Attribute
    {
        return Attribute::make(
            get: fn($value) => round($this->amount / $this->buy_price, 1, PHP_ROUND_HALF_DOWN),
        );
    }
}
