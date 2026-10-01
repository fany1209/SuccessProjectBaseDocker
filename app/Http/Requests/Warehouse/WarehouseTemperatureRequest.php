<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseTemperatureRequest extends FormRequest
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
        $sanitized = [];

        if ($this->has('measurements') && is_array($this->input('measurements'))) {
            $measurements = $this->input('measurements');
            foreach ($measurements as $index => $item) {
                if (is_array($item)) {
                    if (isset($item['hour']) && is_string($item['hour'])) {
                        $measurements[$index]['hour'] = strip_tags(trim($item['hour']));
                    }
                    if (isset($item['temperature']) && is_string($item['temperature'])) {
                        $measurements[$index]['temperature'] = trim($item['temperature']);
                    }
                    if (isset($item['humidity']) && is_string($item['humidity'])) {
                        $measurements[$index]['humidity'] = trim($item['humidity']);
                    }
                }
            }
            $sanitized['measurements'] = $measurements;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para registro de mediciones de temperatura y humedad en almacén.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'warehouse_id'               => ['required', 'integer', 'exists:warehouses,warehouse_id'],
            'measurements'               => ['required', 'array', 'min:1'],
            'measurements.*.hour'        => ['required', 'string', 'max:20'],
            'measurements.*.temperature' => ['required', 'numeric'],
            'measurements.*.humidity'    => ['required', 'numeric'],
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
            'warehouse_id.required'               => 'El almacén es obligatorio.',
            'warehouse_id.integer'                => 'El ID de almacén debe ser un número entero.',
            'warehouse_id.exists'                 => 'El almacén seleccionado no existe.',
            'measurements.required'               => 'Las mediciones son obligatorias.',
            'measurements.array'                  => 'Las mediciones deben enviarse en formato de arreglo.',
            'measurements.min'                    => 'Debe registrar al menos una medición.',
            'measurements.*.hour.required'        => 'La hora de medición es obligatoria.',
            'measurements.*.hour.string'          => 'La hora de medición debe ser texto.',
            'measurements.*.hour.max'             => 'La hora no debe exceder 20 caracteres.',
            'measurements.*.temperature.required' => 'La temperatura es obligatoria.',
            'measurements.*.temperature.numeric'  => 'La temperatura debe ser un valor numérico.',
            'measurements.*.humidity.required'    => 'La humedad es obligatoria.',
            'measurements.*.humidity.numeric'     => 'La humedad debe ser un valor numérico.',
        ];
    }
}
