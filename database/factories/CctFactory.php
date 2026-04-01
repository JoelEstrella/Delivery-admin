<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CctFactory extends Factory
{
    public function definition()
    {
        return [
            'CLAVECCT' => $this->faker->unique()->bothify('##???####?'),
            'NOMBRECT' => $this->faker->company(),
            'STATUS' => 1,
        ];
    }
}
