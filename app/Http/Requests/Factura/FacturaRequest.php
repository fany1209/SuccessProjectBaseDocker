<?php

namespace App\Http\Requests\Factura;

use Illuminate\Foundation\Http\FormRequest;

class FacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $productos = $this->productos;
        if (is_array($productos)) {
            foreach ($productos as $k => $p) {
                if (is_array($p)) {
                    if (isset($p['producto']) && is_string($p['producto'])) {
                        $productos[$k]['producto'] = strip_tags(trim($p['producto']));
                    }
                    if (isset($p['clave_sat']) && is_string($p['clave_sat'])) {
                        $productos[$k]['clave_sat'] = strip_tags(trim($p['clave_sat']));
                    }
                    if (isset($p['unidad']) && is_string($p['unidad'])) {
                        $productos[$k]['unidad'] = strip_tags(trim($p['unidad']));
                    }
                }
            }
        }

        $this->merge([
            'empresa'        => is_string($this->empresa) ? strip_tags(trim($this->empresa)) : $this->empresa,
            'folio_factura'  => is_string($this->folio_factura) ? strip_tags(trim($this->folio_factura)) : $this->folio_factura,
            'departamento'   => is_string($this->departamento) ? strip_tags(trim($this->departamento)) : $this->departamento,
            'descripcion'    => is_string($this->descripcion) ? strip_tags(trim($this->descripcion)) : $this->descripcion,
            'tipo_documento' => is_string($this->tipo_documento) ? strip_tags(trim($this->tipo_documento)) : $this->tipo_documento,
            'insumo'         => is_string($this->insumo) ? strip_tags(trim($this->insumo)) : $this->insumo,
            'moneda'         => is_string($this->moneda) ? strip_tags(trim($this->moneda)) : $this->moneda,
            'productos'      => $productos,
        ]);
    }

    public function rules(): array
    {
        return [
            'tipo_documento'              => ['required', 'in:factura,nota_venta'],
            'insumo'                      => ['required', 'in:directo,indirecto'],
            'empresa'                     => ['required', 'string', 'max:150'],
            'folio_factura'               => ['required', 'string', 'max:100'],
            'fecha_factura'               => ['required', 'date'],
            'moneda'                      => ['required', 'in:MXN,USD'],
            'tipo_cambio'                 => ['required_if:moneda,USD', 'nullable', 'numeric', 'min:0.0001'],
            'departamento'                => ['nullable', 'string', 'max:255'],
            'descripcion'                 => ['nullable', 'string', 'max:1000'],
            'productos'                   => ['required', 'array', 'min:1'],
            'productos.*.producto'        => ['required', 'string', 'max:255'],
            'productos.*.clave_sat'       => ['nullable', 'string', 'max:50'],
            'productos.*.unidad'          => ['nullable', 'string', 'max:50'],
            'productos.*.cantidad'        => ['required', 'numeric', 'min:0.01'],
            'productos.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'productos.*.aplica_iva'      => ['required'],
            'productos.*.descuento'       => ['nullable', 'numeric', 'min:0'],
            'productos.*.iva_porcentaje'  => ['nullable', 'numeric', 'min:0'],
            'productos.*.otro_impuesto'   => ['nullable', 'numeric', 'min:0'],
            'productos.*.traslado'        => ['nullable', 'numeric', 'min:0'],
            'productos.*.ilc'             => ['nullable', 'numeric', 'min:0'],
            'productos.*.retencion'       => ['nullable', 'numeric', 'min:0'],
            'productos.*.isr'             => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required'              => 'El tipo de documento es obligatorio.',
            'tipo_documento.in'                    => 'El tipo de documento debe ser factura o nota de venta.',
            'insumo.required'                      => 'El tipo de insumo es obligatorio.',
            'insumo.in'                            => 'El insumo debe ser directo o indirecto.',
            'empresa.required'                     => 'El nombre de la empresa es obligatorio.',
            'empresa.max'                          => 'La empresa no puede exceder los 150 caracteres.',
            'folio_factura.required'               => 'El folio de factura es obligatorio.',
            'folio_factura.max'                    => 'El folio de factura no puede exceder los 100 caracteres.',
            'fecha_factura.required'               => 'La fecha de la factura es obligatoria.',
            'fecha_factura.date'                   => 'La fecha de factura debe ser una fecha válida.',
            'moneda.required'                      => 'La moneda es obligatoria.',
            'moneda.in'                            => 'La moneda seleccionada debe ser MXN o USD.',
            'tipo_cambio.required_if'              => 'El tipo de cambio es obligatorio cuando la moneda es USD.',
            'tipo_cambio.numeric'                  => 'El tipo de cambio debe ser un número válido.',
            'tipo_cambio.min'                      => 'El tipo de cambio debe ser mayor a 0.',
            'productos.required'                   => 'Debe incluir al menos un producto en la factura.',
            'productos.array'                      => 'El formato de productos no es válido.',
            'productos.min'                        => 'Debe incluir al menos un producto en la factura.',
            'productos.*.producto.required'        => 'El nombre del producto es obligatorio.',
            'productos.*.cantidad.required'        => 'La cantidad del producto es obligatoria.',
            'productos.*.cantidad.min'             => 'La cantidad del producto debe ser mayor a cero.',
            'productos.*.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'productos.*.precio_unitario.min'      => 'El precio unitario no puede ser negativo.',
            'productos.*.aplica_iva.required'      => 'Debe especificar si el producto aplica IVA.',
        ];
    }
}
