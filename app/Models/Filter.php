<?php

namespace App\Models;

use App\Models\DTO\FilterFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fields'
    ];

    protected $casts = [
        'fields' => FilterFields::class
    ];
}
