<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva anti Stored-XSS antes de validar los datos.
     */
    protected function prepareForValidation(): void
    {
        $fields = ['type', 'unit_number', 'plate', 'color'];
        $sanitized = [];

        foreach ($fields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para registro y actualización de vehículos.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');
        $id = $this->route('vehicle') ?? $this->route('id') ?? $this->input('vehicle_id');

        return [
            'type'              => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:70'],
            'unit_number'       => ['nullable', 'string', 'max:255', Rule::unique('vehicles', 'unit_number')->ignore($id, 'vehicle_id')],
            'plate'             => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:20', Rule::unique('vehicles', 'plate')->ignore($id, 'vehicle_id')],
            'color'             => ['nullable', 'string', 'max:30'],
            'transport_line_id' => [$isPost ? 'required' : 'sometimes', 'required', 'integer', 'exists:transport_lines,transport_line_id'],
        ];
    }

    /**
     * Mensajes descriptivos en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required'              => 'El tipo de vehículo es obligatorio.',
            'type.string'                => 'El tipo de vehículo debe ser una cadena de texto.',
            'type.max'                   => 'El tipo de vehículo no debe superar los 70 caracteres.',
            'unit_number.string'         => 'El número de unidad debe ser una cadena de texto.',
            'unit_number.max'            => 'El número de unidad no debe superar los 255 caracteres.',
            'unit_number.unique'         => 'El número de unidad ya se encuentra registrado.',
            'plate.required'             => 'La placa es obligatoria.',
            'plate.string'               => 'La placa debe ser una cadena de texto.',
            'plate.max'                  => 'La placa no debe superar los 20 caracteres.',
            'plate.unique'               => 'La placa ya se encuentra registrada.',
            'color.string'               => 'El color debe ser texto.',
            'color.max'                  => 'El color no debe superar los 30 caracteres.',
            'transport_line_id.required' => 'La línea de transporte es obligatoria.',
            'transport_line_id.integer'  => 'El ID de la línea de transporte debe ser un número entero.',
            'transport_line_id.exists'   => 'La línea de transporte seleccionada no existe.',
        ];
    }
}
