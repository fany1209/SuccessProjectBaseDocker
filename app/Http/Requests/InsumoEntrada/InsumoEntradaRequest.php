<?php

namespace App\Http\Requests\InsumoEntrada;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;

class InsumoEntradaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitización defensiva contra Stored-XSS antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $fields = [
            'proveedor',
            'supplier_name',
            'categoria',
            'descripcion',
            'unidad',
            'insumo',
            'lote',
            'lote_salida',
            'moneda',
        ];

        $sanitized = [];
        foreach ($fields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $sanitized[$field] = strip_tags(trim($this->input($field)));
            }
        }

        if ($this->has('supplier_id') && is_string($this->input('supplier_id'))) {
            $val = trim($this->input('supplier_id'));
            $sanitized['supplier_id'] = ($val === '__other__') ? '__other__' : $val;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Reglas de validación para la entrada de insumos.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        $rules = [
            'fecha_llegada' => [$isPost ? 'required' : 'sometimes', 'required', 'date'],
            'fecha_salida'  => ['nullable', 'date'],
            'categoria'     => [
                $isPost ? 'required' : 'sometimes',
                'required',
                'string',
                'in:warehouse,purchases,laboratory,quality,Human resources,finance',
            ],
            'supplier_id'   => [
                $isPost ? 'required_without:proveedor' : 'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '' && $value !== '__other__') {
                        if (!Supplier::where('supplier_id', $value)->exists()) {
                            $fail('El proveedor seleccionado no existe en el catálogo.');
                        }
                    }
                },
            ],
            'supplier_name' => ['required_if:supplier_id,__other__', 'nullable', 'string', 'max:255'],
            'sector_id'     => ['required_if:supplier_id,__other__', 'nullable', 'integer', 'exists:sectors,sector_id'],
            'proveedor'     => [$isPost ? 'required_without:supplier_id' : 'sometimes', 'nullable', 'string', 'max:255'],
            'descripcion'   => ['nullable', 'string', 'max:2000'],
            'cantidad'      => [$isPost ? 'required' : 'sometimes', 'required', 'numeric', 'min:0'],
            'unidad'        => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:20'],
            'insumo'        => [$isPost ? 'required' : 'sometimes', 'required', 'string', 'max:255'],
            'lote'          => ['nullable', 'string', 'max:255'],
            'lote_salida'   => ['nullable', 'string', 'max:255'],
            'costo'         => ['nullable', 'numeric', 'min:0'],
            'moneda'        => ['nullable', 'string', 'max:3'],
        ];

        return $rules;
    }

    /**
     * Mensajes descriptivos en español para errores de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_llegada.required'         => 'La fecha de llegada es obligatoria.',
            'fecha_llegada.date'             => 'La fecha de llegada debe ser una fecha válida.',
            'fecha_salida.date'              => 'La fecha de salida debe ser una fecha válida.',
            'categoria.required'             => 'La categoría es obligatoria.',
            'categoria.in'                   => 'La categoría seleccionada no es válida.',
            'supplier_id.required_without'   => 'Debe seleccionar un proveedor o ingresar el nombre del mismo.',
            'supplier_name.required_if'      => 'El nombre del nuevo proveedor es obligatorio cuando se selecciona "Otro".',
            'supplier_name.max'              => 'El nombre del proveedor no debe exceder 255 caracteres.',
            'sector_id.required_if'          => 'El sector es obligatorio al registrar un nuevo proveedor.',
            'sector_id.exists'               => 'El sector seleccionado no existe.',
            'proveedor.required_without'     => 'El nombre del proveedor es obligatorio.',
            'proveedor.max'                  => 'El nombre del proveedor no debe exceder 255 caracteres.',
            'descripcion.max'                => 'La descripción no debe exceder los 2000 caracteres.',
            'cantidad.required'              => 'La cantidad es obligatoria.',
            'cantidad.numeric'               => 'La cantidad debe ser un valor numérico.',
            'cantidad.min'                   => 'La cantidad no puede ser negativa.',
            'unidad.required'                => 'La unidad de medida es obligatoria.',
            'unidad.max'                     => 'La unidad no debe exceder 20 caracteres.',
            'insumo.required'                => 'El insumo es obligatorio.',
            'insumo.max'                     => 'El campo insumo no debe exceder 255 caracteres.',
            'lote.max'                       => 'El lote no debe exceder 255 caracteres.',
            'lote_salida.max'                => 'El lote de salida no debe exceder 255 caracteres.',
            'costo.numeric'                  => 'El costo debe ser un valor numérico.',
            'costo.min'                      => 'El costo no puede ser negativo.',
            'moneda.max'                     => 'El código de moneda no debe exceder 3 caracteres.',
        ];
    }
}
