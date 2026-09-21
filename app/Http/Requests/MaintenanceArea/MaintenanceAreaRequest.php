<?php

namespace App\Http\Requests\MaintenanceArea;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceAreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name'        => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
            'description' => is_string($this->description) ? strip_tags(trim($this->description)) : null,
        ]);
    }

    public function rules()
    {
        $areaParam = $this->route('area') ?? $this->route('id');
        $areaId = is_object($areaParam) ? $areaParam->id : $areaParam;

        switch ($this->method()) {
            case 'POST':
                return [
                    'name'        => 'required|string|max:255|unique:areas,name',
                    'description' => 'nullable|string|max:1000',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'name'        => 'sometimes|required|string|max:255|unique:areas,name,' . $areaId,
                    'description' => 'nullable|string|max:1000',
                ];

            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'name.required'      => 'El nombre de la categoría o área es obligatorio.',
            'name.string'        => 'El nombre debe ser una cadena de texto válida.',
            'name.max'           => 'El nombre no debe superar los 255 caracteres.',
            'name.unique'        => 'Ya existe una categoría o área registrada con este nombre.',
            'description.string' => 'La descripción debe ser una cadena de texto válida.',
            'description.max'    => 'La descripción no debe superar los 1000 caracteres.',
        ];
    }
}
