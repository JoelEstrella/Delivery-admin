<?php

namespace Database\Seeders;

use App\Models\Plant;
use Illuminate\Database\Seeder;

class PlantSeeder extends Seeder
{
    public function run()
    {
        Plant::updateOrCreate(
            ['slug' => 'planta-demo'],
            [
                'name' => 'Planta Demo',
                'short_description' => 'Planta de ejemplo para validar el flujo de inventario.',
                'description_html' => '<p>Planta utilizada para demostrar el módulo de inventario y entregas.</p>',
                'care_instructions' => 'Mantener en un espacio ventilado y con riego moderado.',
                'is_active' => true,
            ]
        );
    }
}
