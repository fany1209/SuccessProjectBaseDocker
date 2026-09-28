<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class FinancePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $clean = strip_tags(trim($value));
                $sanitized[$key] = $clean === '' ? null : $clean;
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }

        if ($this->has('comentarios')) {
            $comentarios = $this->input('comentarios');
            $terminacion = ($comentarios === 'Tarjeta') ? $this->input('terminacion') : null;
            $efectivo = ($comentarios === 'Efectivo') ? $this->input('efectivo') : null;

            $this->merge([
                'terminacion' => $terminacion,
                'efectivo'    => $efectivo,
            ]);
        }
    }

    public function rules(): array
    {
        $isPost = $this->isMethod('POST');

        return [
            'empresa'       => ['required', 'string', 'max:150'],
            'cantidad'      => ['required', 'numeric', 'min:0'],
            'motivo'        => ['required', 'string'],
            'banco'         => ['nullable', 'string', 'max:100'],
            'factura'       => [$isPost ? 'required' : 'nullable', 'max:50'],
            'fecha_factura' => ['nullable', 'date'],
            'fecha_pago'    => ['nullable', 'date'],
            'semana'        => ['required', 'integer', 'min:1', 'max:53'],
            'anio'          => ['required', 'integer', 'min:2000', 'max:2100'],
            'estatus'       => ['required', 'in:PENDIENTE,PAGADO,CANCELADO'],
            'comentarios'   => ['nullable', 'string', 'max:255'],
            'terminacion'   => ['nullable', 'string', 'max:4'],
            'efectivo'      => ['nullable', 'string', 'max:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'empresa.required'   => 'La empresa es obligatoria.',
            'empresa.max'        => 'La empresa no puede superar los 150 caracteres.',
            'cantidad.required'  => 'La cantidad es obligatoria.',
            'cantidad.numeric'   => 'La cantidad debe ser un valor numérico.',
            'cantidad.min'       => 'La cantidad no puede ser negativa.',
            'motivo.required'    => 'El motivo es obligatorio.',
            'factura.required'   => 'La factura es obligatoria.',
            'factura.max'        => 'La factura no puede superar los 50 caracteres.',
            'semana.required'    => 'La semana es obligatoria.',
            'semana.integer'     => 'La semana debe ser un número entero.',
            'semana.min'         => 'La semana debe ser entre 1 y 53.',
            'semana.max'         => 'La semana debe ser entre 1 y 53.',
            'anio.required'      => 'El año es obligatorio.',
            'anio.integer'       => 'El año debe ser un número entero.',
            'anio.min'           => 'El año debe ser válido (mínimo 2000).',
            'anio.max'           => 'El año debe ser válido (máximo 2100).',
            'estatus.required'   => 'El estatus es obligatorio.',
            'estatus.in'         => 'El estatus seleccionado no es válido (PENDIENTE, PAGADO, CANCELADO).',
            'terminacion.max'    => 'La terminación no puede exceder 4 caracteres.',
            'efectivo.max'       => 'El campo efectivo no puede exceder 2 caracteres.',
        ];
    }
}
