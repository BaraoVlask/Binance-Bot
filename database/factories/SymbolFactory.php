<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SymbolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'round_length' => $this->faker->randomFloat(8),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
