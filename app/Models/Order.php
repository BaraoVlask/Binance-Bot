<?php

namespace App\Models;

use App\Enums\OrderDatabaseTypeEnum;
use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Jobs\CalculateProfitJob;
use App\Jobs\OpenOrderJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'price_range_id',
        'binance_id',
        'order_id',
        'quantity',
        'price',
        'side',
        'type',
        'status',
        'payload',
        'amount',
        'commission_coin',
        'commission_amount',
    ];

    protected $casts = [
        'side' => OrderSideEnum::class,
        'type' => OrderDatabaseTypeEnum::class,
        'status' => OrderStatusEnum::class,
        'payload' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(fn(Order $order) => OpenOrderJob::dispatch($order));
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->type === OrderDatabaseTypeEnum::Normal;
    }

    public function priceRange(): BelongsTo|PriceRange
    {
        return $this->belongsTo(PriceRange::class);
    }

    public function parentOrder(): BelongsTo|Order
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AccountReport::class);
    }

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'commission_coin');
    }
}
