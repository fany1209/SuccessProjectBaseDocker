<?php

namespace App\Http\Requests\Sales;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class SaleAlmacenActionRequest extends FormRequest
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
                $sanitized[$key] = strip_tags(trim($value));
            } elseif (is_array($value)) {
                $sanitized[$key] = $value;
            }
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        return [
            'action'                          => 'required|string|in:confirm,postpone,mark_ready,cancel',
            'lot_assignments'                 => 'nullable|array',
            'lot_assignments.*.cli_id'        => 'required|integer',
            'lot_assignments.*.quantity'      => 'required|numeric|min:0.001',
            'reason'                          => 'required_if:action,postpone,cancel|nullable|string|max:1000',
            'date'                            => 'required_if:action,postpone|nullable|date',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('action') === 'confirm') {
                $saleId = $this->route('id');
                if ($saleId) {
                    $hasPhysicalProducts = DB::table('sale_detail')
                        ->where('sale_id', $saleId)
                        ->whereNotIn('product_id', Product::SHIPPING_SERVICE_IDS)
                        ->exists();

                    $lotAssignments = $this->input('lot_assignments', []);
                    if ($hasPhysicalProducts && empty($lotAssignments)) {
                        $validator->errors()->add('lot_assignments', 'Debes seleccionar un lote para cada producto que requiere inventario.');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'action.required'                     => 'La acción de almacén es requerida.',
            'action.in'                           => 'La acción indicada no es válida.',
            'lot_assignments.array'               => 'Las asignaciones de lotes deben ser un listado válido.',
            'lot_assignments.*.cli_id.required'   => 'El lote y ubicación física son obligatorios.',
            'lot_assignments.*.cli_id.integer'    => 'El identificador de lote debe ser un número entero.',
            'lot_assignments.*.quantity.required' => 'La cantidad a descontar es obligatoria.',
            'lot_assignments.*.quantity.numeric'  => 'La cantidad a descontar debe ser numérica.',
            'lot_assignments.*.quantity.min'      => 'La cantidad a descontar debe ser mayor a 0.',
            'reason.required_if'                  => 'El motivo es obligatorio para esta acción.',
            'date.required_if'                    => 'La fecha es obligatoria para posponer la venta.',
        ];
    }
}
