<?php

namespace App\Services;

use App\Models\Direction;
use App\Models\Plant;
use App\Repositories\DirectionStockRepository;
use App\Repositories\StockMovementRepository;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    protected $directionStocks;

    protected $stockMovements;

    public function __construct(DirectionStockRepository $directionStocks, StockMovementRepository $stockMovements)
    {
        $this->directionStocks = $directionStocks;
        $this->stockMovements = $stockMovements;
    }

    public function getStock($directionId, $plantId)
    {
        return $this->directionStocks->findByDirectionAndPlant($directionId, $plantId);
    }

    public function increaseStock($directionId, $plantId, $quantity, $movementType = 'entrada', $referenceType = null, $referenceId = null, $notes = null, $userId = null)
    {
        $quantity = (int) $quantity;
        $stock = $this->directionStocks->findOrCreate($directionId, $plantId);
        $stock->stock = (int) $stock->stock + $quantity;
        $stock->save();

        $this->recordMovement($directionId, $plantId, $movementType, $quantity, $referenceType, $referenceId, $notes, $userId);

        return $stock->fresh(['direction', 'plant']);
    }

    public function decreaseStock($directionId, $plantId, $quantity, $movementType = 'salida', $referenceType = null, $referenceId = null, $notes = null, $userId = null)
    {
        $quantity = (int) $quantity;
        $stock = $this->directionStocks->findOrCreate($directionId, $plantId);

        if ((int) $stock->stock < $quantity) {
            throw ValidationException::withMessages([
                'stock' => 'No existe stock suficiente para completar la operación.',
            ]);
        }

        $stock->stock = (int) $stock->stock - $quantity;
        $stock->save();

        $this->recordMovement($directionId, $plantId, $movementType, $quantity, $referenceType, $referenceId, $notes, $userId);

        return $stock->fresh(['direction', 'plant']);
    }

    public function adjustStock($directionId, $plantId, $quantity, $notes = null, $userId = null)
    {
        $stock = $this->directionStocks->findOrCreate($directionId, $plantId);
        $stock->stock = (int) $quantity;
        $stock->save();

        $this->recordMovement($directionId, $plantId, 'ajuste', (int) $quantity, null, null, $notes, $userId);

        return $stock->fresh(['direction', 'plant']);
    }

    public function recordMovement($directionId, $plantId, $movementType, $quantity, $referenceType = null, $referenceId = null, $notes = null, $userId = null)
    {
        return $this->stockMovements->create([
            'direction_id' => $directionId,
            'plant_id' => $plantId,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => $userId,
        ]);
    }
}
