<?php

namespace App\Models;

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
}
