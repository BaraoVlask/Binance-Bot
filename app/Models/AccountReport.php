<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'stream',
        'report',
    ];

    protected $casts = [
        'report' => 'object'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
