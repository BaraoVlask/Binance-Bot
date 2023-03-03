<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fields',
        'symbol_id',
    ];

    protected $casts = [
        'fields' => 'object'
    ];
}
