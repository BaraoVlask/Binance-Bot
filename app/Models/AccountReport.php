<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'stream',
        'report',
    ];
}
