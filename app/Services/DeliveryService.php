<?php

namespace App\Services;

use App\Models\Delivery;
use App\Repositories\DeliveryItemRepository;
use App\Repositories\DeliveryRepository;
use App\Repositories\PlantRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    protected $deliveries;

    protected $deliveryItems;

    protected $plants;

    protected $inventory;

    protected $activityLogs;

    public function __construct(DeliveryRepository $deliveries, DeliveryItemRepository $deliveryItems, PlantRepository $plants, InventoryService $inventory, ActivityLogService $activityLogs)
    {
        $this->deliveries = $deliveries;
        $this->deliveryItems = $deliveryItems;
        $this->plants = $plants;
        $this->inventory = $inventory;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->deliveries->paginate(15, $search, ['cct', 'direction', 'creator.role', 'items.plant', 'validation.validator.role'], ['delivery_date', 'desc']);
    }

    public function find($id)
    {
        return $this->deliveries->findOrFail($id, ['cct', 'direction', 'creator.role', 'items.plant', 'validation.validator.role']);
    }

    public function create(array $data, array $items, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        $items = $this->normalizeItems($items);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Debes agregar al menos una planta a la entrega.',
            ]);
        }

        return DB::transaction(function () use ($data, $items, $userId) {
            $data['created_by'] = $userId;
            if (empty($data['status'])) {
                $data['status'] = 'pending';
            }

            $delivery = $this->deliveries->create($data);

            foreach ($items as $item) {
                $plant = $this->plants->findOrFail($item['plant_id']);
                $quantity = (int) $item['quantity'];

                $this->inventory->decreaseStock(
                    $delivery->direction_id,
                    $plant->id,
                    $quantity,
                    'entrega',
                    Delivery::class,
                    $delivery->id,
                    'Entrega registrada en la entrega #' . $delivery->id,
                    $userId
                );

                $this->deliveryItems->create([
                    'delivery_id' => $delivery->id,
                    'plant_id' => $plant->id,
                    'quantity' => $quantity,
                ]);
            }

            $this->activityLogs->log('deliveries', 'create', 'Se creó la entrega #' . $delivery->id, $delivery->id, null, $delivery->fresh()->toArray(), $userId);

            return $delivery->fresh(['cct', 'direction', 'items.plant', 'creator.role', 'validation.validator.role']);
        });
    }

    public function update(Delivery $delivery, array $data, array $items, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        $items = $this->normalizeItems($items);

        if ($delivery->validation) {
            throw ValidationException::withMessages([
                'delivery' => 'No se puede editar una entrega que ya fue validada.',
            ]);
        }

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Debes agregar al menos una planta a la entrega.',
            ]);
        }

        return DB::transaction(function () use ($delivery, $data, $items, $userId) {
            $delivery->load('items', 'cct', 'direction');
            $oldValues = $delivery->toArray();
            $originalItems = $delivery->items->all();

            foreach ($originalItems as $existingItem) {
                $this->inventory->increaseStock(
                    $delivery->direction_id,
                    $existingItem->plant_id,
                    $existingItem->quantity,
                    'ajuste',
                    Delivery::class,
                    $delivery->id,
                    'Reverso por actualización de entrega #' . $delivery->id,
                    $userId
                );
            }

            $this->deliveryItems->query()->where('delivery_id', $delivery->id)->delete();

            if (empty($data['status'])) {
                $data['status'] = $delivery->status;
            }

            $delivery = $this->deliveries->update($delivery, $data);

            foreach ($items as $item) {
                $plant = $this->plants->findOrFail($item['plant_id']);
                $quantity = (int) $item['quantity'];

                $this->inventory->decreaseStock(
                    $delivery->direction_id,
                    $plant->id,
                    $quantity,
                    'entrega',
                    Delivery::class,
                    $delivery->id,
                    'Actualización de entrega #' . $delivery->id,
                    $userId
                );

                $this->deliveryItems->create([
                    'delivery_id' => $delivery->id,
                    'plant_id' => $plant->id,
                    'quantity' => $quantity,
                ]);
            }

            $this->activityLogs->log('deliveries', 'update', 'Se actualizó la entrega #' . $delivery->id, $delivery->id, $oldValues, $delivery->fresh()->toArray(), $userId);

            return $delivery->fresh(['cct', 'direction', 'items.plant', 'creator.role', 'validation.validator.role']);
        });
    }

    public function delete(Delivery $delivery, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        if ($delivery->validation) {
            throw ValidationException::withMessages([
                'delivery' => 'No se puede eliminar una entrega que ya fue validada.',
            ]);
        }

        return DB::transaction(function () use ($delivery, $userId) {
            $delivery->load('items');
            $oldValues = $delivery->toArray();

            foreach ($delivery->items as $item) {
                $this->inventory->increaseStock(
                    $delivery->direction_id,
                    $item->plant_id,
                    $item->quantity,
                    'ajuste',
                    Delivery::class,
                    $delivery->id,
                    'Cancelación de entrega #' . $delivery->id,
                    $userId
                );
            }

            $delivery->status = 'cancelled';
            $delivery->save();
            $delivery->delete();

            $this->activityLogs->log('deliveries', 'delete', 'Se eliminó la entrega #' . $delivery->id, $delivery->id, $oldValues, null, $userId);

            return true;
        });
    }

    protected function normalizeItems(array $items)
    {
        $normalized = [];

        foreach ($items as $item) {
            $plantId = isset($item['plant_id']) ? (int) $item['plant_id'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;

            if ($plantId > 0 && $quantity > 0) {
                $normalized[] = [
                    'plant_id' => $plantId,
                    'quantity' => $quantity,
                ];
            }
        }

        return $normalized;
    }
}
