<?php

namespace App\Models;

use App\Events\PriceRangeCreatedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRange extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(fn (PriceRange $priceRange) => new PriceRangeCreatedEvent($priceRange));
    }

    /**
     * @return BelongsTo|Symbol
     */
    public function symbol(): BelongsTo|Symbol
    {
        return $this->belongsTo(Symbol::class);
    }
}
