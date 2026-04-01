<?php

namespace App\Repositories;

use App\Models\DirectionStock;

class DirectionStockRepository extends BaseRepository
{
    protected $modelClass = DirectionStock::class;

    public function findByDirectionAndPlant($directionId, $plantId)
    {
        return $this->query()
            ->where('direction_id', $directionId)
            ->where('plant_id', $plantId)
            ->first();
    }

    public function findOrCreate($directionId, $plantId)
    {
        $stock = $this->findByDirectionAndPlant($directionId, $plantId);

        if ($stock) {
            return $stock;
        }

        return $this->create([
            'direction_id' => $directionId,
            'plant_id' => $plantId,
            'stock' => 0,
        ]);
    }
}
