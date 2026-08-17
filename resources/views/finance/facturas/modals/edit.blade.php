<x-modal id="edit-factura">
    <form class="flex flex-col items-center w-full gap-2" id="edit-factura-form">
        @csrf
        @method('PATCH')

        <input type="hidden" id="edit-factura-id" name="factura_id">

        <x-wrapper-form-1>
            <x-tittle-form>Edit Documento</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Tipo de Documento</x-label>
                <select name="tipo_documento" id="edit-tipo_documento" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="factura">Factura (Fiscal)</option>
                    <option value="nota_venta">Nota de Venta</option>
                </select>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Tipo de Insumo</x-label>
                <select name="insumo" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="directo">Directo</option>
                    <option value="indirecto">Indirecto</option>
                </select>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Empresa / Cliente</x-label>
                <x-input-1 id="edit-empresa" required name="empresa"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Folio</x-label>
                <x-input-1 id="edit-folio_factura" required name="folio_factura"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Fecha Factura</x-label>
                <x-input-1 type="date" id="edit-fecha_factura" required name="fecha_factura"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Método de Pago</x-label>
                <select name="metodo_pago" id="edit-metodo_pago" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Tarjeta de Crédito">Tarjeta</option>
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="banco">Banco</x-label>
                <x-select-1 name="banco" id="edit-banco">
                    <option value="">Selecciona un banco</option>
                    <option value="BBVA">BBVA</option>
                    <option value="Banamex">Banamex</option>
                    <option value="Banorte">Banorte</option>
                    <option value="Santander">Santander</option>
                    <option value="HSBC">HSBC</option>
                    <option value="Scotiabank">Scotiabank</option>
                    <option value="Inbursa">Inbursa</option>
                    <option value="Afirme">Afirme</option>
                    <option value="BanBajío">BanBajío</option>
                    <option value="BanRegio">BanRegio</option>
                    <option value="Hey Banco">Hey Banco</option>
                    <option value="Nu">Nu</option>
                    <option value="Otro">Otro</option>
                </x-select-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>¿Quién paga? (Pagador)</x-label>
                <x-input-1 id="edit-pagador" name="pagador" placeholder="Nombre de la persona"></x-input-1>
            </x-wrapper-form-2>

            <div id="edit-wrapper-terminacion" style="display: none;" class="w-full">
                <x-wrapper-form-2>
                    <x-label>Terminación (4 dígitos)</x-label>
                    <x-input-1 id="edit-terminacion" name="terminacion" maxlength="4" placeholder="1234"></x-input-1>
                </x-wrapper-form-2>
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Departamento</x-label>
                <x-input-1 id="edit-departamento" name="departamento" placeholder="Ej. Operaciones, TI..."></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Descripción / Observaciones</x-label>
                <x-input-1 id="edit-descripcion" name="descripcion" placeholder="Ej. Pago de servicios..."></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1><hr class="w-full my-2 border-gray-300"></x-wrapper-form-1>

        <x-wrapper-form-1>
            <h3 class="font-bold text-gray-700 w-full text-lg">Detalle de Productos</h3>
        </x-wrapper-form-1>

        <div id="edit-productos-container" class="w-full flex flex-col gap-4">
            </div>

        <x-wrapper-form-1>
            <button type="button" id="edit-add-product" class="w-full md:w-auto border-2 border-dashed border-green-500 text-green-600 bg-green-50 hover:bg-green-100 rounded-lg text-sm font-bold py-2 px-4 mt-2 transition-colors">
                + Agregar producto
            </button>
        </x-wrapper-form-1>

        <x-wrapper-form-1><hr class="w-full my-2 border-gray-300"></x-wrapper-form-1>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-2 w-full bg-gray-50 p-3 rounded-lg border border-gray-200">
            
            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-gray-600 uppercase">Subtotal</label>
                <input type="text" id="edit-factura-subtotal" readonly
                       class="w-full rounded border border-gray-300 px-2 py-1 bg-white text-right font-semibold">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-orange-600 uppercase">Descuentos -</label>
                <input type="text" id="edit-factura-descuentos" readonly
                       class="w-full rounded border border-orange-200 px-2 py-1 bg-white text-right text-orange-700">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-gray-600 uppercase">IVA Calc. +</label>
                <input type="text" id="edit-factura-iva-calc" readonly
                       class="w-full rounded border border-gray-300 px-2 py-1 bg-white text-right">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-blue-600 uppercase">Trasl/ILC +</label>
                <input type="text" id="edit-factura-traslados-manual" readonly
                       class="w-full rounded border border-blue-200 px-2 py-1 bg-white text-right text-blue-700">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-red-600 uppercase">Retenciones -</label>
                <input type="text" id="edit-factura-retenciones" readonly
                       class="w-full rounded border border-red-200 px-2 py-1 bg-white text-right text-red-700">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-red-800 uppercase">ISR -</label>
                <input type="text" id="edit-factura-isr" readonly
                       class="w-full rounded border border-red-300 px-2 py-1 bg-white text-right text-red-800">
            </div>

            <div class="flex flex-col col-span-2">
                <label class="text-xs font-bold text-green-700 uppercase">Gran Total</label>
                <input type="text" readonly name="total" id="edit-factura-total" 
                       class="w-full rounded border border-green-500 px-2 py-1 bg-green-100 text-right font-bold text-green-800 text-lg">
            </div>

        </div>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 type="reset" class="close-modal" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" id="update-factura" colorBtn="green">Guardar Factura</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){

    let editIndexNew = 1000; 

    /* =========================
     * CALCULAR TOTALES (EDICIÓN)
     * ========================= */
    function calcularEditTotales(){
        let subtotalGeneral = 0;
        let totalDescuentos = 0;
        let totalIvaCalculado = 0;   
        let totalTrasladoMasIlc = 0;  
        let totalRetenciones = 0;
        let totalIsr = 0;

        $('#edit-productos-container .producto-row').each(function(){
            const row = $(this);

            const cantidad  = parseFloat(row.find('.cantidad').val()) || 0;
            const pUnitario = parseFloat(row.find('.precio-unitario').val()) || 0;
            const descuento = parseFloat(row.find('.descuento').val()) || 0;
            
            const importeBase = cantidad * pUnitario;
            row.find('.precio').val(importeBase.toFixed(5));

            const baseImpuestos = importeBase - descuento;

            const ivaPct  = parseFloat(row.find('.iva-pct').val()) || 0;
            const otroPct = parseFloat(row.find('.otro-pct').val()) || 0;
            
            const montoIva  = baseImpuestos * (ivaPct / 100);
            const montoOtro = baseImpuestos * (otroPct / 100);

            const trasladoManual  = parseFloat(row.find('.traslado').val()) || 0;
            const ilcManual       = parseFloat(row.find('.ilc').val()) || 0;
            const retencionManual = parseFloat(row.find('.retencion').val()) || 0;
            const isrManual       = parseFloat(row.find('.isr').val()) || 0;

            row.find('.aplica-iva-hidden').val(ivaPct > 0 ? 1 : 0);

            subtotalGeneral     += importeBase;
            totalDescuentos     += descuento;
            totalIvaCalculado   += (montoIva + montoOtro); 
            totalTrasladoMasIlc += trasladoManual; 
            totalRetenciones    += (retencionManual + ilcManual); 
            totalIsr            += isrManual;
        });

        const granTotal = subtotalGeneral - totalDescuentos + totalIvaCalculado + totalTrasladoMasIlc - totalRetenciones - totalIsr;

        $('#edit-factura-subtotal').val(subtotalGeneral.toFixed(5));
        $('#edit-factura-descuentos').val(totalDescuentos.toFixed(5));
        $('#edit-factura-iva-calc').val(totalIvaCalculado.toFixed(5));
        $('#edit-factura-traslados-manual').val(totalTrasladoMasIlc.toFixed(5));
        $('#edit-factura-retenciones').val(totalRetenciones.toFixed(5));
        $('#edit-factura-isr').val(totalIsr.toFixed(5));
        $('#edit-factura-total').val(granTotal.toFixed(5));
    }

    $(document).on(
        'input change',
        '#edit-productos-container .cantidad, #edit-productos-container .precio-unitario, #edit-productos-container .descuento, #edit-productos-container .traslado, #edit-productos-container .ilc, #edit-productos-container .retencion, #edit-productos-container .isr, #edit-productos-container .iva-pct, #edit-productos-container .otro-pct',
        calcularEditTotales
    );

    /* =========================
     * AGREGAR PRODUCTO (EDICIÓN)
     * ========================= */
    $('#edit-add-product').on('click', function(){
        $('#edit-productos-container').append(`
            <div class="producto-row bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-sm relative mt-2">
                <input type="hidden" name="productos[${editIndexNew}][aplica_iva]" class="aplica-iva-hidden" value="1">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-3">
                    <div class="md:col-span-4">
                        <label class="text-sm font-medium text-gray-700">Producto</label>
                        <input name="productos[${editIndexNew}][producto]" class="w-full border rounded px-2 py-1 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Clave SAT</label>
                        <input name="productos[${editIndexNew}][clave_sat]" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Unidad</label>
                        <input name="productos[${editIndexNew}][unidad]" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-gray-700">Cant.</label>
                        <input name="productos[${editIndexNew}][cantidad]" type="number" min="0" step="any" class="w-full border rounded px-2 py-1 text-sm cantidad" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-gray-700">P. Unit</label>
                        <input name="productos[${editIndexNew}][precio_unitario]" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm precio-unitario" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Importe Base</label>
                        <input name="productos[${editIndexNew}][precio]" type="number" step="0.00001" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-sm precio bg-gray-200 font-bold text-right" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-8 gap-2 items-end pt-2 border-t border-gray-200">
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-orange-600">Desc. $</label>
                        <input name="productos[${editIndexNew}][descuento]" type="number" step="0.00001" value="0" class="w-full border rounded px-2 py-1 text-sm descuento text-orange-600 font-semibold">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">% IVA</label>
                        <input name="productos[${editIndexNew}][iva_porcentaje]" value="16" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm iva-pct text-center">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">% Otro</label>
                        <input name="productos[${editIndexNew}][otro_impuesto]" value="0" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm otro-pct text-center">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-blue-600">Trasl. $</label>
                        <input name="productos[${editIndexNew}][traslado]" value="0" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm traslado text-center font-semibold text-blue-600">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-purple-600">ILC $</label>
                        <input name="productos[${editIndexNew}][ilc]" value="0" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm ilc text-center font-semibold text-purple-600">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-red-600">Reten. $</label>
                        <input name="productos[${editIndexNew}][retencion]" value="0" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm retencion text-center font-semibold text-red-600">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-red-800">ISR $</label>
                        <input name="productos[${editIndexNew}][isr]" value="0" type="number" step="0.00001" class="w-full border rounded px-2 py-1 text-sm isr text-center font-semibold text-red-800">
                    </div>
                    
                    <div class="flex justify-center md:justify-end pb-1 h-full">
                        <button type="button" class="remove-product w-full md:w-auto text-red-600 font-bold hover:bg-red-100 border border-red-200 bg-white rounded px-3 py-1 text-xs">✕ Eliminar</button>
                    </div>
                </div>
            </div>
        `);
        editIndexNew++;
    });

    $(document).on('click', '#edit-productos-container .remove-product', function(){
        if($('#edit-productos-container .producto-row').length > 0){
             $(this).closest('.producto-row').remove();
             calcularEditTotales();
        }
    });

    $('#edit-metodo_pago').on('change', function() {
        const valor = $(this).val();
        if (valor && valor.includes('Tarjeta')) {
            $('#edit-wrapper-terminacion').fadeIn().find('input').prop('required', true);
        } else {
            $('#edit-wrapper-terminacion').fadeOut().find('input').prop('required', false).val('');
        }
    });

    /* =========================
     * GUARDAR CAMBIOS (UPDATE)
     * ========================= */
    $('#edit-factura-form').on('submit', function(e){
        e.preventDefault();

        const id = $('#edit-factura-id').val();
        const url = "{{ route('facturas.update', ':id') }}".replace(':id', id);
        
        const btn = $('#update-factura');
        const btnText = btn.html();
        btn.prop('disabled', true).html('Guardando...');

        $.ajax({
            url: url,
            method: "PATCH", 
            data: $(this).serialize(),
            success: function(response){
                if(response.success){
                    $('.close-modal').trigger('click');
                    $('#facturas-table').DataTable().ajax.reload();
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: response.message || 'Documento actualizado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function(xhr){
                btn.prop('disabled', false).html(btnText);
                console.log(xhr.responseText);
                
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    for(let field in errors){
                        message += errors[field][0] + '\n';
                    }
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error de validación',
                        text: message,
                        confirmButtonColor: '#d33'
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al actualizar. Revisa la consola.',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function(){
                btn.prop('disabled', false).html(btnText);
            }
        });
    });

});
</script>
@endpush