<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryValidationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        if (!$this->filled('status')) {
            $this->merge([
                'status' => 'validated',
            ]);
        }
    }

    public function rules()
    {
        return [
            'delivery_id' => ['required', 'exists:deliveries,id'],
            'received_quantity' => ['required', 'integer', 'min:0'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,validated,approved,rejected'],
        ];
    }
}
