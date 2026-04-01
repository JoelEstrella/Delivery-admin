<?php

namespace Database\Seeders;

use App\Models\Direction;
use App\Models\DirectionStock;
use App\Models\Plant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class DirectionStockSeeder extends Seeder
{
    public function run()
    {
        $direction = Direction::where('code', 'DG-001')->first();
        $plant = Plant::where('slug', 'planta-demo')->first();
        $admin = User::where('email', 'admin@admin.com')->first();

        if (!$direction || !$plant) {
            return;
        }

        $stock = DirectionStock::updateOrCreate(
            [
                'direction_id' => $direction->id,
                'plant_id' => $plant->id,
            ],
            [
                'stock' => 100,
            ]
        );

        StockMovement::create([
            'direction_id' => $direction->id,
            'plant_id' => $plant->id,
            'movement_type' => 'entrada',
            'quantity' => 100,
            'reference_type' => 'seed',
            'reference_id' => $stock->id,
            'notes' => 'Stock inicial de demostración.',
            'created_by' => $admin ? $admin->id : null,
        ]);
    }
}
