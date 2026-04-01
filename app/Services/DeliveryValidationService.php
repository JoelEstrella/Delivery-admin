<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryValidation;
use App\Repositories\DeliveryValidationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryValidationService
{
    protected $validations;

    protected $inventory;

    protected $activityLogs;

    public function __construct(DeliveryValidationRepository $validations, InventoryService $inventory, ActivityLogService $activityLogs)
    {
        $this->validations = $validations;
        $this->inventory = $inventory;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->validations->paginate(15, $search, ['delivery.cct', 'delivery.direction', 'validator.role'], ['validated_at', 'desc']);
    }

    public function find($id)
    {
        return $this->validations->findOrFail($id, ['delivery.cct', 'delivery.direction', 'delivery.items.plant', 'validator.role']);
    }

    public function save(Delivery $delivery, array $data, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        return DB::transaction(function () use ($delivery, $data, $userId) {
            $delivery->load('items', 'validation');
            $validation = $delivery->validation ?: new DeliveryValidation();

            $oldValues = $validation->exists ? $validation->toArray() : null;
            $validation->delivery_id = $delivery->id;
            $validation->received_quantity = isset($data['received_quantity']) ? (int) $data['received_quantity'] : 0;
            $validation->observations = isset($data['observations']) ? $data['observations'] : null;
            $validation->validated_by = $userId;
            $validation->status = isset($data['status']) ? $data['status'] : 'validated';
            $validation->validated_at = in_array($validation->status, ['validated', 'approved'], true) ? now() : null;
            $validation->save();

            $delivery->status = $validation->status;
            $delivery->save();

            $deliveredQuantity = (int) $delivery->items->sum('quantity');
            $difference = $deliveredQuantity - (int) $validation->received_quantity;
            $shouldRecordMovement = !$validation->exists || $oldValues === null || (int) $oldValues['received_quantity'] !== (int) $validation->received_quantity || (string) $oldValues['status'] !== (string) $validation->status;

            if ($shouldRecordMovement) {
                foreach ($delivery->items as $item) {
                    $this->inventory->recordMovement(
                        $delivery->direction_id,
                        $item->plant_id,
                        'validacion',
                        0,
                        DeliveryValidation::class,
                        $validation->id,
                        'Validación de entrega #' . $delivery->id . '. Entregado: ' . $deliveredQuantity . ', recibido: ' . $validation->received_quantity . ', diferencia: ' . $difference,
                        $userId
                    );
                }
            }

            $this->activityLogs->log('validations', $validation->status, 'Se registró la validación de la entrega #' . $delivery->id, $validation->id, $oldValues, $validation->toArray(), $userId);

            return $validation->fresh(['delivery.cct', 'delivery.direction', 'delivery.items.plant', 'validator.role']);
        });
    }

    public function approve(DeliveryValidation $validation, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        return DB::transaction(function () use ($validation, $userId) {
            $validation->load('delivery.items');
            $oldValues = $validation->toArray();

            $validation->status = 'approved';
            $validation->validated_by = $userId;
            $validation->validated_at = now();
            $validation->save();

            $validation->delivery->status = 'approved';
            $validation->delivery->save();

            $this->activityLogs->log('validations', 'approve', 'Se aprobó la validación de la entrega #' . $validation->delivery_id, $validation->id, $oldValues, $validation->toArray(), $userId);

            return $validation->fresh(['delivery.cct', 'delivery.direction', 'delivery.items.plant', 'validator.role']);
        });
    }

    public function delete(DeliveryValidation $validation, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        $oldValues = $validation->toArray();
        $validation->delete();

        $this->activityLogs->log('validations', 'delete', 'Se eliminó la validación de la entrega #' . $validation->delivery_id, $validation->id, $oldValues, null, $userId);

        return true;
    }
}
