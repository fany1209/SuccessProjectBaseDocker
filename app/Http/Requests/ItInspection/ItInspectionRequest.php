<?php

namespace App\Http\Requests\ItInspection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItInspectionRequest extends FormRequest
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
        $fields = [
            'folio',
            'date',
            'brand',
            'model',
            'serial_number',
            'location',
            'area',
            'req1',
            'req2',
            'req3',
            'req4',
            'req5',
            'observations',
            'inspector_name',
        ];

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
     * Reglas de validación para registrar y actualizar inspecciones de TI.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');
        $id = $this->route('id') ?? $this->route('inspeccione');

        return [
            'folio'          => [
                $isPost ? 'required' : 'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('it_inspections', 'folio')->ignore($id),
            ],
            'date'           => [$isPost ? 'required' : 'sometimes', 'required', 'date'],
            'brand'          => ['nullable', 'string', 'max:255'],
            'model'          => ['nullable', 'string', 'max:255'],
            'serial_number'  => ['nullable', 'string', 'max:255'],
            'location'       => ['nullable', 'string', 'max:255'],
            'area'           => ['nullable', 'string', 'max:255'],
            'req1'           => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'in:cumple,nocumple,na'],
            'req2'           => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'in:cumple,nocumple,na'],
            'req3'           => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'in:cumple,nocumple,na'],
            'req4'           => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'in:cumple,nocumple,na'],
            'req5'           => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'in:cumple,nocumple,na'],
            'observations'   => ['nullable', 'string'],
            'inspector_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Mensajes descriptivos en español para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'folio.required'    => 'El folio de inspección es obligatorio.',
            'folio.string'      => 'El folio debe ser una cadena de texto.',
            'folio.max'         => 'El folio no debe superar los 255 caracteres.',
            'folio.unique'      => 'El folio de inspección ya se encuentra registrado.',
            'date.required'     => 'La fecha de inspección es obligatoria.',
            'date.date'         => 'La fecha de inspección debe tener un formato de fecha válido.',
            'brand.string'      => 'La marca debe ser una cadena de texto.',
            'brand.max'         => 'La marca no debe superar los 255 caracteres.',
            'model.string'      => 'El modelo debe ser una cadena de texto.',
            'model.max'         => 'El modelo no debe superar los 255 caracteres.',
            'serial_number.string' => 'El número de serie debe ser una cadena de texto.',
            'serial_number.max'    => 'El número de serie no debe superar los 255 caracteres.',
            'location.string'   => 'La ubicación debe ser una cadena de texto.',
            'location.max'      => 'La ubicación no debe superar los 255 caracteres.',
            'area.string'       => 'El área debe ser una cadena de texto.',
            'area.max'          => 'El área no debe superar los 255 caracteres.',
            'req1.required'     => 'El requisito 1 es obligatorio.',
            'req1.in'           => 'El requisito 1 debe ser cumple, nocumple o na.',
            'req2.required'     => 'El requisito 2 es obligatorio.',
            'req2.in'           => 'El requisito 2 debe ser cumple, nocumple o na.',
            'req3.required'     => 'El requisito 3 es obligatorio.',
            'req3.in'           => 'El requisito 3 debe ser cumple, nocumple o na.',
            'req4.required'     => 'El requisito 4 es obligatorio.',
            'req4.in'           => 'El requisito 4 debe ser cumple, nocumple o na.',
            'req5.required'     => 'El requisito 5 es obligatorio.',
            'req5.in'           => 'El requisito 5 debe ser cumple, nocumple o na.',
            'observations.string' => 'Las observaciones deben ser texto.',
            'inspector_name.string' => 'El nombre del inspector debe ser una cadena de texto.',
            'inspector_name.max'    => 'El nombre del inspector no debe superar los 255 caracteres.',
        ];
    }
}
