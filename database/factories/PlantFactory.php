<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlantFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'short_description' => $this->faker->sentence(),
            'description_html' => '<p>' . e($this->faker->paragraph()) . '</p>',
            'care_instructions' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
