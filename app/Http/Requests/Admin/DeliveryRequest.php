<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $plantIds = $this->input('plant_ids', []);
        $quantities = $this->input('quantities', []);
        $items = [];

        if (is_array($plantIds) || is_array($quantities)) {
            $plantIds = is_array($plantIds) ? array_values($plantIds) : [];
            $quantities = is_array($quantities) ? array_values($quantities) : [];
            $max = max(count($plantIds), count($quantities));

            for ($i = 0; $i < $max; $i++) {
                $plantId = isset($plantIds[$i]) ? (int) $plantIds[$i] : 0;
                $quantity = isset($quantities[$i]) ? (int) $quantities[$i] : 0;

                if ($plantId > 0 || $quantity > 0) {
                    $items[] = [
                        'plant_id' => $plantId,
                        'quantity' => $quantity,
                    ];
                }
            }
        }

        $this->merge([
            'items' => $items,
        ]);

        if (!$this->filled('status')) {
            $this->merge([
                'status' => 'pending',
            ]);
        }
    }

    public function rules()
    {
        return [
            'cct_id' => ['required', 'exists:ccts,id'],
            'direction_id' => ['required', 'exists:directions,id'],
            'delivery_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,delivered,cancelled'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'delivered_by' => ['nullable', 'string', 'max:255'],
            'plant_ids' => ['required', 'array', 'min:1'],
            'plant_ids.*' => ['required', 'exists:plants,id'],
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['required', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.plant_id' => ['required', 'exists:plants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
