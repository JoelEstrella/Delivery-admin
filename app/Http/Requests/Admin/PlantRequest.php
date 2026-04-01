<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $plant = $this->route('plant');
        $plantId = $plant ? $plant->id : null;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('plants', 'name')->ignore($plantId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('plants', 'slug')->ignore($plantId)],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description_html' => ['nullable', 'string'],
            'care_instructions' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'primary_image_index' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
