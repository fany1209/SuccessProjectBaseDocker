<x-modal id="add-factura">
    <form class="flex flex-col items-center w-full gap-2" id="add-factura-form">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Add Documento</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Tipo de Documento</x-label>
                <select name="tipo_documento" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
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
                <x-input-1 required name="empresa"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Folio</x-label>
                <x-input-1 required name="folio_factura"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Fecha Factura</x-label>
                <x-input-1 type="date" required name="fecha_factura" value="{{ date('Y-m-d') }}"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Descripción / Observaciones</x-label>
                <x-input-1 name="descripcion" placeholder="Ej. Pago de servicios correspondientes al mes de..."></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1><hr class="w-full my-2 border-gray-300"></x-wrapper-form-1>

        <x-wrapper-form-1>
            <h3 class="font-bold text-gray-700 w-full text-lg">Detalle de Productos</h3>
        </x-wrapper-form-1>

        <div id="productos-container" class="w-full flex flex-col gap-4">

            <div class="producto-row bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-sm relative">
                <input type="hidden" name="productos[0][aplica_iva]" class="aplica-iva-hidden" value="1">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-3">
                    <div class="md:col-span-4">
                        <x-label>Producto</x-label>
                        <input name="productos[0][producto]" class="w-full border rounded px-2 py-1 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <x-label>Clave SAT</x-label>
                        <input name="productos[0][clave_sat]" class="w-full border rounded px-2 py-1 text-sm" placeholder="8411..">
                    </div>
                    <div class="md:col-span-2">
                        <x-label>Unidad</x-label>
                        <input name="productos[0][unidad]" class="w-full border rounded px-2 py-1 text-sm" placeholder="H87">
                    </div>
                    <div class="md:col-span-1">
                        <x-label>Cant.</x-label>
                        <input name="productos[0][cantidad]" type="number" step="any" min="0.000001" class="w-full border rounded px-2 py-1 text-sm cantidad" required>
                    </div>
                    <div class="md:col-span-1">
                        <x-label>P. Unit</x-label>
                        <input name="productos[0][precio_unitario]" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm precio-unitario" required>
                    </div>
                    <div class="md:col-span-2">
                        <x-label>Importe Base</x-label>
                        <input name="productos[0][precio]" type="number" step="any" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-sm precio bg-gray-200 font-bold text-right" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-9 gap-2 items-end pt-2 border-t border-gray-200">
                    <div class="flex flex-col">
                        <x-label class="text-orange-600">Desc. $</x-label>
                        <input name="productos[0][descuento]" type="number" step="any" value="0" class="w-full border rounded px-2 py-1 text-sm descuento text-orange-600 font-semibold" placeholder="$">
                    </div>
                    <div class="flex flex-col">
                        <x-label class="text-green-600">Base IVA</x-label>
                        <input name="productos[0][base_iva]" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm base-iva text-center font-semibold text-green-600" placeholder="Auto">
                    </div>
                    <div class="flex flex-col">
                        <x-label>% IVA</x-label>
                        <input name="productos[0][iva_porcentaje]" value="16" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm iva-pct text-center">
                    </div>
                    <div class="flex flex-col">
                        <x-label>% Otro</x-label>
                        <input name="productos[0][otro_impuesto]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm otro-pct text-center">
                    </div>
                    <div class="flex flex-col">
                        <x-label class="text-blue-600">Trasl. $</x-label>
                        <input name="productos[0][traslado]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm traslado text-center font-semibold text-blue-600">
                    </div>
                    <div class="flex flex-col">
                        <x-label class="text-purple-600">ILC $</x-label>
                        <input name="productos[0][ilc]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm ilc text-center font-semibold text-purple-600">
                    </div>
                    <div class="flex flex-col">
                        <x-label class="text-red-600">Reten. $</x-label>
                        <input name="productos[0][retencion]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm retencion text-center font-semibold text-red-600">
                    </div>
                    <div class="flex flex-col">
                        <x-label class="text-red-800">ISR $</x-label>
                        <input name="productos[0][isr]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm isr text-center font-semibold text-red-800">
                    </div>
                    
                    <div class="flex justify-center md:justify-end pb-1 h-full">
                        <button type="button" class="remove-product w-full md:w-auto text-red-600 font-bold hover:bg-red-100 border border-red-200 bg-white rounded px-3 py-1 text-xs">✕ Eliminar</button>
                    </div>
                </div>
            </div>

        </div>

        <x-wrapper-form-1>
            <button type="button" id="add-product" class="w-full md:w-auto border-2 border-dashed border-green-500 text-green-600 bg-green-50 hover:bg-green-100 rounded-lg text-sm font-bold py-2 px-4 mt-2 transition-colors">
                + Agregar producto
            </button>
        </x-wrapper-form-1>

        <x-wrapper-form-1><hr class="w-full my-2 border-gray-300"></x-wrapper-form-1>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-2 w-full bg-gray-50 p-3 rounded-lg border border-gray-200">
            
            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-gray-600 uppercase">Subtotal</label>
                <input type="text" id="factura-subtotal" readonly
                       class="w-full rounded border border-gray-300 px-2 py-1 bg-white text-right font-semibold">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-orange-600 uppercase">Descuentos -</label>
                <input type="text" id="factura-descuentos" readonly
                       class="w-full rounded border border-orange-200 px-2 py-1 bg-white text-right text-orange-700">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-gray-600 uppercase">IVA Calc. +</label>
                <input type="text" id="factura-iva-calc" readonly
                       class="w-full rounded border border-gray-300 px-2 py-1 bg-white text-right">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-blue-600 uppercase">Trasl/ILC +</label>
                <input type="text" id="factura-traslados-manual" readonly
                       class="w-full rounded border border-blue-200 px-2 py-1 bg-white text-right text-blue-700">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-red-600 uppercase">Retenciones -</label>
                <input type="text" id="factura-retenciones" readonly
                       class="w-full rounded border border-red-200 px-2 py-1 bg-white text-right text-red-700">
            </div>

            <div class="flex flex-col">
                <label class="text-[11px] font-semibold text-red-800 uppercase">ISR -</label>
                <input type="text" id="factura-isr" readonly
                       class="w-full rounded border border-red-300 px-2 py-1 bg-white text-right text-red-800">
            </div>

            <div class="flex flex-col col-span-2">
                <label class="text-xs font-bold text-green-700 uppercase">Gran Total</label>
                <input type="text" readonly name="total" id="factura-total" 
                       class="w-full rounded border border-green-500 px-2 py-1 bg-green-100 text-right font-bold text-green-800 text-lg">
            </div>

        </div>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 type="reset" class="close-modal" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" id="save-factura" colorBtn="green">Guardar Factura</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

    
@push('js')
<script>
$(function(){

    let index = 1;

    /* =========================
     * CALCULAR TOTALES 
     * ========================= */
    function calcularTotales(){

        let subtotalGeneral = 0;
        let totalDescuentos = 0;
        let totalIvaCalculado = 0;   
        let totalTrasladoMasIlc = 0;  
        let totalRetenciones = 0;
        let totalIsr = 0;

        $('.producto-row').each(function(){
            const row = $(this);

            const cantidad  = parseFloat(row.find('.cantidad').val()) || 0;
            const pUnitario = parseFloat(row.find('.precio-unitario').val()) || 0;
            const descuento = parseFloat(row.find('.descuento').val()) || 0;
            
            const importeBase = cantidad * pUnitario;
            row.find('.precio').val(importeBase.toFixed(6));

            const baseImpuestos = importeBase - descuento;

            const baseIvaInput = row.find('.base-iva');
            let baseIvaVal = parseFloat(baseIvaInput.val());
            let baseParaIva = baseImpuestos;

            if (!isNaN(baseIvaVal)) {
                baseParaIva = baseIvaVal;
            } else {
                baseIvaInput.attr('placeholder', baseImpuestos.toFixed(2));
            }

            const ivaPct  = parseFloat(row.find('.iva-pct').val()) || 0;
            const otroPct = parseFloat(row.find('.otro-pct').val()) || 0;
            
            const montoIva  = baseParaIva * (ivaPct / 100);
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

        $('#factura-subtotal').val(subtotalGeneral.toFixed(6));
        $('#factura-descuentos').val(totalDescuentos.toFixed(6));
        $('#factura-iva-calc').val(totalIvaCalculado.toFixed(6));
        $('#factura-traslados-manual').val(totalTrasladoMasIlc.toFixed(6));
        $('#factura-retenciones').val(totalRetenciones.toFixed(6));
        $('#factura-isr').val(totalIsr.toFixed(6));
        $('#factura-total').val(granTotal.toFixed(6));
    }

    $(document).on(
        'input change',
        '.cantidad, .precio-unitario, .descuento, .base-iva, .traslado, .ilc, .retencion, .isr, .iva-pct, .otro-pct',
        calcularTotales
    );

    /* =========================
     * AGREGAR PRODUCTO
     * ========================= */
    $('#add-product').on('click', function(){

        $('#productos-container').append(`
            <div class="producto-row bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-sm relative mt-2">
                <input type="hidden" name="productos[${index}][aplica_iva]" class="aplica-iva-hidden" value="1">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-3">
                    <div class="md:col-span-4">
                        <label class="text-sm font-medium text-gray-700">Producto</label>
                        <input name="productos[${index}][producto]" class="w-full border rounded px-2 py-1 text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Clave SAT</label>
                        <input name="productos[${index}][clave_sat]" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Unidad</label>
                        <input name="productos[${index}][unidad]" class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-gray-700">Cant.</label>
                        <input name="productos[${index}][cantidad]" type="number" step="any" min="0.000001" class="w-full border rounded px-2 py-1 text-sm cantidad" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-gray-700">P. Unit</label>
                        <input name="productos[${index}][precio_unitario]" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm precio-unitario" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Importe Base</label>
                        <input name="productos[${index}][precio]" type="number" step="any" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-sm precio bg-gray-200 font-bold text-right" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-9 gap-2 items-end pt-2 border-t border-gray-200">
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-orange-600">Desc. $</label>
                        <input name="productos[${index}][descuento]" type="number" step="any" value="0" class="w-full border rounded px-2 py-1 text-sm descuento text-orange-600 font-semibold">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-green-600">Base IVA</label>
                        <input name="productos[${index}][base_iva]" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm base-iva text-center font-semibold text-green-600" placeholder="Auto">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">% IVA</label>
                        <input name="productos[${index}][iva_porcentaje]" value="16" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm iva-pct text-center">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700">% Otro</label>
                        <input name="productos[${index}][otro_impuesto]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm otro-pct text-center">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-blue-600">Trasl. $</label>
                        <input name="productos[${index}][traslado]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm traslado text-center font-semibold text-blue-600">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-purple-600">ILC $</label>
                        <input name="productos[${index}][ilc]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm ilc text-center font-semibold text-purple-600">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-red-600">Reten. $</label>
                        <input name="productos[${index}][retencion]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm retencion text-center font-semibold text-red-600">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-red-800">ISR $</label>
                        <input name="productos[${index}][isr]" value="0" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm isr text-center font-semibold text-red-800">
                    </div>
                    
                    <div class="flex justify-center md:justify-end pb-1 h-full">
                        <button type="button" class="remove-product w-full md:w-auto text-red-600 font-bold hover:bg-red-100 border border-red-200 bg-white rounded px-3 py-1 text-xs">✕ Eliminar</button>
                    </div>
                </div>
            </div>
        `);

        index++;
    });

    $(document).on('click', '.remove-product', function() {
        if($('.producto-row').length > 1){
            $(this).closest('.producto-row').remove();
            calcularTotales();
        } else {
            const row = $(this).closest('.producto-row');
            row.find('input[type="text"], input[type="number"]').not('.iva-pct').val('');
            row.find('.precio, .descuento, .base-iva, .traslado, .ilc, .retencion, .isr').val(0);
            row.find('.iva-pct').val(16);
            calcularTotales();
        }
    });

    /* =========================
     * GUARDAR 
     * ========================= */
    $('#add-factura-form').on('submit', function(e){
        e.preventDefault();

        const btn = $('#save-factura');
        const btnText = btn.html();
        btn.prop('disabled', true).html('Guardando...');

        $.ajax({
            url: "{{ route('facturas.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response){
                if(response.success){
                    $('.close-modal').trigger('click');
                    
                    $('#add-factura-form')[0].reset();
                    
                    $('#productos-container .producto-row:not(:first)').remove();
                    
                    calcularTotales(); 
                    index = 1; 
                    
                    $('#facturas-table').DataTable().ajax.reload();
                    
                    // Notificar a otras pestañas que deben recargar (ej. tabla de precios)
                    localStorage.setItem('factura_updated', Date.now());
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: response.message || 'Documento guardado correctamente',
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
                        text: 'Ocurrió un error al guardar. Revisa la consola.',
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
