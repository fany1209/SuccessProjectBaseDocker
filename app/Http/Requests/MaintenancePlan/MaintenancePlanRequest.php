<?php

namespace App\Http\Requests\MaintenancePlan;

use Illuminate\Foundation\Http\FormRequest;

class MaintenancePlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $checklistItems = $this->checklist_items;
        if (is_array($checklistItems)) {
            $checklistItems = array_map(function ($item) {
                return is_string($item) ? strip_tags(trim($item)) : $item;
            }, $checklistItems);
        }

        $this->merge([
            'name'            => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
            'type'            => is_string($this->type) ? trim($this->type) : $this->type,
            'checklist_items' => $checklistItems,
        ]);
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'equipment_id'      => 'required|integer|exists:equipment,id',
                    'name'              => 'required|string|max:255',
                    'frequency_days'    => 'required|integer|min:1',
                    'type'              => 'required|string|in:frequent,deep',
                    'checklist_items'   => 'nullable|array',
                    'checklist_items.*' => 'nullable|string|max:500',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'equipment_id'      => 'sometimes|required|integer|exists:equipment,id',
                    'name'              => 'sometimes|required|string|max:255',
                    'frequency_days'    => 'sometimes|required|integer|min:1',
                    'type'              => 'sometimes|required|string|in:frequent,deep',
                    'checklist_items'   => 'nullable|array',
                    'checklist_items.*' => 'nullable|string|max:500',
                ];

            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'equipment_id.required'     => 'El equipo asociado es obligatorio.',
            'equipment_id.integer'      => 'El identificador del equipo debe ser numérico.',
            'equipment_id.exists'       => 'El equipo seleccionado no existe en el catálogo.',
            'name.required'             => 'El nombre del plan de mantenimiento es obligatorio.',
            'name.string'               => 'El nombre debe ser una cadena de texto válida.',
            'name.max'                  => 'El nombre no debe superar los 255 caracteres.',
            'frequency_days.required'   => 'La frecuencia en días es obligatoria.',
            'frequency_days.integer'    => 'La frecuencia debe ser un número entero.',
            'frequency_days.min'        => 'La frecuencia mínima debe ser de al menos 1 día.',
            'type.required'             => 'El tipo de mantenimiento es obligatorio.',
            'type.in'                   => 'El tipo de mantenimiento debe ser frecuente (frequent) o profundo (deep).',
            'checklist_items.array'     => 'Los elementos del checklist deben ser una lista válida.',
            'checklist_items.*.string'  => 'Cada elemento del checklist debe ser texto.',
            'checklist_items.*.max'     => 'Cada elemento del checklist no debe superar los 500 caracteres.',
        ];
    }
}
