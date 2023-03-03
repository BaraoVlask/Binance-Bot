<?php

namespace App\Models;

use App\Enums\FiltersEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Symbol extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tick_size',
        'round_length',
    ];

    /**
     * @return HasMany|Filter
     */
    public function filters(): HasMany|Filter
    {
        return $this->hasMany(Filter::class, 'symbol_id');
    }

    public function stepSizeRound(): Attribute|int
    {
        return Attribute::make(
            get: fn($value) => max(
                [
                    strpos(
                        $this->filters()
                            ->where('name', FiltersEnum::PriceFilter)
                            ->fields
                            ->stepSize,
                        1
                    ) - 1,
                    1
                ]
            ),
        );
    }
}
