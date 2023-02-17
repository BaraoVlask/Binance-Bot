<?php

namespace App\Models\DTO;

use Spatie\LaravelData\Data;

class FilterFields extends Data
{
    public function __construct(
        public array $fields
    )
    {
    }
}
