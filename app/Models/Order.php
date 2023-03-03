<?php

namespace App\Models;

use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use App\Jobs\Order\CancelOrderJob;
use App\Jobs\Order\OpenOrderJob;
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
        'amount',
        'commission_coin',
        'commission_amount',
    ];

    protected $casts = [
        'side' => OrderSideEnum::class,
        'status' => OrderStatusEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(fn(Order $order) => OpenOrderJob::dispatch($order));
        static::deleted(fn(Order $order) => CancelOrderJob::dispatch($order));
    }

    public function tag(): Attribute|string
    {
        return Attribute::make(
            get: fn($value) => "{$this->symbol->name}[{$this->type->value}]:$this->price",
        );
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return !empty($this->deleted_at);
    }

    public function priceRange(): BelongsTo|PriceRange
    {
        return $this->belongsTo(PriceRange::class);
    }

    public function parentOrder(): BelongsTo|Order
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function accountReport(): Report|null
    {
        return $this->accountReports()
            ->orderByDesc('id')
            ->first();
    }

    public function accountReports(): HasMany
    {
        return $this->hasMany(Report::class, 'order_id');
    }

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'commission_coin');
    }
}
