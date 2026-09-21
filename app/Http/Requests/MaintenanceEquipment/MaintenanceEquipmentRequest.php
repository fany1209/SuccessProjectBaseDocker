<?php

namespace App\Http\Requests\MaintenanceEquipment;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceEquipmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'code'      => is_string($this->code) ? strip_tags(trim($this->code)) : $this->code,
            'name'      => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
            'is_active' => $this->has('is_active') ? filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true : true,
        ]);
    }

    public function rules()
    {
        $equipmentParam = $this->route('equipment') ?? $this->route('id');
        $equipmentId = is_object($equipmentParam) ? $equipmentParam->id : $equipmentParam;

        switch ($this->method()) {
            case 'POST':
                return [
                    'code'      => 'required|string|max:100|unique:equipment,code',
                    'name'      => 'required|string|max:255',
                    'area_id'   => 'required|integer|exists:areas,id',
                    'is_active' => 'sometimes|boolean',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'code'      => 'sometimes|required|string|max:100|unique:equipment,code,' . $equipmentId,
                    'name'      => 'sometimes|required|string|max:255',
                    'area_id'   => 'sometimes|required|integer|exists:areas,id',
                    'is_active' => 'sometimes|boolean',
                ];

            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'code.required'      => 'El código del equipo es obligatorio.',
            'code.string'        => 'El código debe ser una cadena de texto válida.',
            'code.max'           => 'El código no debe superar los 100 caracteres.',
            'code.unique'        => 'Ya existe un equipo registrado con este código.',
            'name.required'      => 'El nombre del equipo es obligatorio.',
            'name.string'        => 'El nombre debe ser una cadena de texto válida.',
            'name.max'           => 'El nombre no debe superar los 255 caracteres.',
            'area_id.required'   => 'El área asignada es obligatoria.',
            'area_id.integer'    => 'El identificador de área debe ser numérico.',
            'area_id.exists'     => 'El área seleccionada no existe en el catálogo.',
            'is_active.boolean'  => 'El estado del equipo debe ser verdadero o falso.',
        ];
    }
}
