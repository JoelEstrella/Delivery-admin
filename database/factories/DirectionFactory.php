<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DirectionFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company() . ' Dirección',
            'code' => $this->faker->unique()->bothify('DIR-###'),
            'responsible_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
