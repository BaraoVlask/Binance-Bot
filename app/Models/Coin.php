<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'coin_id');
    }
}
