{{--
Facturas
Fecha de creación: 22-01-2026
Creado por: Stefany
Modificado:
--}}
@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4">

    <section class="col-span-12 w-full flex flex-col items-center px-1">
        <div class="flex flex-col justify-center items-center w-full">

            <div class="mt-6 text-center w-full">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
                    Facturas
                </h1>
                <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
            </div>

            <x-section-1>
                <div class="flex justify-end items-center w-full my-1 gap-2">

                    <x-button data-target="add-factura"
                        class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 text-white px-4 py-2 rounded shadow">
                        Add factura
                    </x-button>

                    @include('finance.facturas.modals.create')
                    @include('finance.facturas.modals.edit')
                    @include('finance.facturas.modals.show')

                    <button id="btn-open-edit-factura" type="button" class="open-modal hidden"
                        data-target="edit-factura"></button>
                    
                    <button id="btn-open-show-factura" type="button" class="open-modal hidden"
                        data-target="show-factura"></button>

                </div>
            </x-section-1>

            <div class="w-full mt-4">
                <table id="facturas-table"
                    class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                        <tr>
                            <th class="w-24">Tipo</th> 
                            <th>Empresa</th>
                            <th>Folio</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th class="w-32 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </section>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {

        // =========================
        // MANEJO DE MODALES
        // =========================
        $(document).on('click', '.open-modal', function() {
            const target = $(this).data('target');
            if (target) $('#' + target).removeClass('hidden');
        });
        $(document).on('click', '.close-modal', function() {
            $(this).closest('.fixed').addClass('hidden');
        });
        $(document).on('click', '.fixed', function(e) {
            if ($(e.target).hasClass('fixed')) {
                $(this).addClass('hidden');
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        /* =========================
         * DATATABLE
         * ========================= */
        const table = $('#facturas-table').DataTable({
            ajax: {
                url: "{{ route('facturas.datatable') }}",
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'tipo_documento',
                    render: function(data) {
                        if(data === 'nota_venta') {
                            return '<span class="bg-gray-200 text-gray-700 py-1 px-2 rounded-full text-xs font-bold">Nota Venta</span>';
                        } else {
                            return '<span class="bg-blue-100 text-blue-700 py-1 px-2 rounded-full text-xs font-bold">Factura</span>';
                        }
                    }
                },
                { data: 'empresa' },
                { data: 'folio_factura' },

                {
                    data: 'subtotal',
                    render: d => `$${(parseFloat(d) || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                },
                {
                    data: 'iva',
                    render: d => `$${(parseFloat(d) || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                },
                {
                    data: 'total',
                    render: (d, type, row) => {
                        const formatted = (parseFloat(d) || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        const moneda = row.moneda || 'MXN';
                        const badgeClass = moneda === 'USD' 
                            ? 'bg-amber-100 text-amber-800 border-amber-300' 
                            : 'bg-emerald-50 text-emerald-700 border-emerald-300';
                        return `
                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                <span class="font-bold text-gray-900">$${formatted}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border ${badgeClass}">${moneda}</span>
                            </div>
                        `;
                    }
                },
                
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: row => `
                        <div class="flex gap-1 justify-center items-center">
                            
                            {{-- BOTÓN VER --}}
                            <button data-id="${row.factura_id}"
                                    class="btn-show-factura text-sm text-white bg-emerald-600 hover:bg-emerald-700 rounded-sm p-2 transition duration-200"
                                    title="Ver Detalles">
                                <img width="18" src="{{ asset('images/ver.png') }}" alt="Ver">
                            </button>

                            {{-- BOTÓN EDITAR --}}
                            <button data-id="${row.factura_id}"
                                    class="btn-edit-factura text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 transition duration-200"
                                    title="Editar">
                                <img width="18" src="{{ asset('images/editar.png') }}" alt="Editar">
                            </button>

                            {{-- BOTÓN ELIMINAR --}}
                            <button data-id="${row.factura_id}"
                                    class="btn-delete-factura text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2 transition duration-200"
                                    title="Eliminar">
                                <img width="18" src="{{ asset('images/borrar.png') }}" alt="Borrar">
                            </button>

                        </div>
                    `
                }
            ],
            pageLength: 15,
            lengthChange: false,
            language: {
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                zeroRecords: "No hay resultados",
                paginate: { next: "Siguiente", previous: "Anterior" }
            }
        });

        /* DETALLES FACTURA*/
        $(document).on('click', '.btn-show-factura', function() {
            const id = $(this).data('id');
            $('#btn-open-show-factura').trigger('click'); 
            const tbody = $('#show-products-body');
            tbody.empty();

            $.get("{{ route('facturas.show', ':id') }}".replace(':id', id), function(res) {
                if (!res.success) return;

                const f = res.factura;
                const moneda = f.moneda || 'MXN';

                const formatoMoneda = new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: moneda,
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                $('#show-empresa').text(f.empresa);
                $('#show-folio').text(f.folio_factura);
                $('#show-fecha').text(f.fecha_factura ? f.fecha_factura : 'Sin fecha');

                const monedaBadge = $('#show-moneda');
                monedaBadge.text(moneda);
                if (moneda === 'USD') {
                    monedaBadge.removeClass('bg-green-50 text-green-700 border-green-200').addClass('bg-amber-100 text-amber-800 border-amber-300');
                    if (f.tipo_cambio && parseFloat(f.tipo_cambio) > 0) {
                        $('#show-tc-badge').text('TC: ' + parseFloat(f.tipo_cambio).toFixed(4)).removeClass('hidden');
                    } else {
                        $('#show-tc-badge').addClass('hidden');
                    }
                } else {
                    monedaBadge.removeClass('bg-amber-100 text-amber-800 border-amber-300').addClass('bg-green-50 text-green-700 border-green-200');
                    $('#show-tc-badge').addClass('hidden');
                }
                
                const tipoBadge = $('#show-tipo');
                if(f.tipo_documento === 'factura'){
                    tipoBadge.text('Factura').addClass('bg-blue-100 text-blue-800').removeClass('bg-gray-200 text-gray-800');
                } else {
                    tipoBadge.text('Nota de Venta').addClass('bg-gray-200 text-gray-800').removeClass('bg-blue-100 text-blue-800');
                }

                const insumoBadge = $('#show-insumo');
                insumoBadge.text(f.insumo || 'No especificado');
                insumoBadge.removeClass('bg-purple-100 text-purple-700 border-purple-200 bg-orange-100 text-orange-700 border-orange-200');
                
                if (f.insumo === 'directo') {
                    insumoBadge.addClass('bg-purple-100 text-purple-700 border-purple-200');
                } else {
                    insumoBadge.addClass('bg-orange-100 text-orange-700 border-orange-200');
                }

                if(f.descripcion){
                    $('#show-descripcion-container').removeClass('hidden');
                    $('#show-descripcion').text(f.descripcion);
                } else {
                    $('#show-descripcion-container').addClass('hidden');
                }

                res.detalles.forEach(p => {
                    const precioU   = parseFloat(p.precio_unitario) || 0;
                    const descuento = parseFloat(p.descuento) || 0;
                    const importe   = parseFloat(p.precio) || 0; 
                    
                    const montoIva   = (importe - descuento) * ((parseFloat(p.iva_porcentaje)||0) / 100);
                    const montoOtro  = (importe - descuento) * ((parseFloat(p.otro_impuesto)||0) / 100);
                    const traslados  = montoIva + montoOtro + (parseFloat(p.traslado)||0) + (parseFloat(p.ilc)||0);
                    
                    const retenciones = (parseFloat(p.retencion)||0) + (parseFloat(p.isr)||0);

                    const totalFila = importe - descuento + traslados - retenciones;

                    tbody.append(`
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2 font-semibold">${p.cantidad} <span class="text-[9px] text-gray-400 font-normal ml-1">${p.unidad||''}</span></td>
                            <td class="px-3 py-2">
                                <span class="font-bold text-gray-800">${p.producto}</span>
                                <div class="text-[10px] text-gray-500">SAT: ${p.clave_sat || 'N/A'}</div>
                            </td>
                            <td class="px-3 py-2 text-right">${formatoMoneda.format(precioU)}</td>
                            <td class="px-3 py-2 text-right text-orange-600">${descuento > 0 ? formatoMoneda.format(descuento) : '-'}</td>
                            <td class="px-3 py-2 text-right font-semibold">${formatoMoneda.format(importe)}</td>
                            <td class="px-3 py-2 text-right text-blue-600">${traslados > 0 ? formatoMoneda.format(traslados) : '-'}</td>
                            <td class="px-3 py-2 text-right text-red-600">${retenciones > 0 ? formatoMoneda.format(retenciones) : '-'}</td>
                            <td class="px-3 py-2 text-right font-bold text-green-700">${formatoMoneda.format(totalFila)}</td>
                        </tr>
                    `);
                });

                const subtotalBase = parseFloat(f.subtotal) || 0;
                const totalDesc    = parseFloat(f.descuento_total) || 0;
                const totalImpPos  = (parseFloat(f.iva) || 0) + (parseFloat(f.traslado_total) || 0);
                const totalImpNeg  = parseFloat(f.retencion_total) || 0;
                const granTotal    = parseFloat(f.total) || 0;

                $('#show-subtotal').text(formatoMoneda.format(subtotalBase) + ' ' + moneda);
                
                if(totalDesc > 0){
                    $('#row-show-descuentos').show();
                    $('#show-descuentos').text(formatoMoneda.format(totalDesc) + ' ' + moneda);
                } else {
                    $('#row-show-descuentos').hide();
                }

                $('#show-impuestos').text(formatoMoneda.format(totalImpPos) + ' ' + moneda);
                $('#show-retenciones').text(formatoMoneda.format(totalImpNeg) + ' ' + moneda);
                $('#show-total').text(formatoMoneda.format(granTotal) + ' ' + moneda);
            });
        });
        /* EDITAR FACTURA  */
        $(document).on('click', '.btn-edit-factura', function() {
            const id = $(this).data('id');
            $('#btn-open-edit-factura').trigger('click');
            const container = $('#edit-productos-container');
            container.empty();
            let editIndex = 0;
            
            $('#edit-factura-subtotal, #edit-factura-descuentos, #edit-factura-iva-calc, #edit-factura-traslados-manual, #edit-factura-retenciones, #edit-factura-isr, #edit-factura-total').val('');

            $.get("{{ route('facturas.show', ':id') }}".replace(':id', id), function(res) {
                if (!res.success) return;

                $('#edit-factura-id').val(res.factura.factura_id);
                $('#edit-tipo_documento').val(res.factura.tipo_documento);
                $('#edit-insumo').val(res.factura.insumo); 
                $('#edit-empresa').val(res.factura.empresa);
                $('#edit-folio_factura').val(res.factura.folio_factura);
                $('#edit-fecha_factura').val(res.factura.fecha_factura || '');
                $('#edit-descripcion').val(res.factura.descripcion || '');
                $('#edit-moneda').val(res.factura.moneda || 'MXN');
                $('#edit-moneda').trigger('change');
                if (res.factura.moneda === 'USD') {
                    $('#edit-tipo_cambio').val(res.factura.tipo_cambio || '');
                } else {
                    $('#edit-tipo_cambio').val('1.0000');
                }

                res.detalles.forEach(p => {
                    container.append(`
                        <div class="producto-row bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-sm relative mt-2">
                            <input type="hidden" name="productos[${editIndex}][aplica_iva]" class="aplica-iva-hidden" value="${p.aplica_iva}">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-3">
                                <div class="md:col-span-4">
                                    <label class="text-sm font-medium text-gray-700">Producto</label>
                                    <input name="productos[${editIndex}][producto]" value="${p.producto || ''}" class="w-full border rounded px-2 py-1 text-sm" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Clave SAT</label>
                                    <input name="productos[${editIndex}][clave_sat]" value="${p.clave_sat || ''}" class="w-full border rounded px-2 py-1 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Unidad</label>
                                    <input name="productos[${editIndex}][unidad]" value="${p.unidad || ''}" class="w-full border rounded px-2 py-1 text-sm">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="text-sm font-medium text-gray-700">Cant.</label>
                                    <input name="productos[${editIndex}][cantidad]" type="number" min="0" step="any" value="${p.cantidad || 1}" class="w-full border rounded px-2 py-1 text-sm cantidad" required>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="text-sm font-medium text-gray-700">P. Unit</label>
                                    <input name="productos[${editIndex}][precio_unitario]" type="number" step="any" value="${p.precio_unitario || 0}" class="w-full border rounded px-2 py-1 text-sm precio-unitario" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Importe Base</label>
                                    <input name="productos[${editIndex}][precio]" type="number" step="any" value="${p.precio || 0}" readonly class="w-full border border-gray-300 rounded px-2 py-1 text-sm precio bg-gray-200 font-bold text-right" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-9 gap-2 items-end pt-2 border-t border-gray-200">
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-orange-600">Desc. $</label>
                                    <input name="productos[${editIndex}][descuento]" type="number" step="any" value="${p.descuento || 0}" class="w-full border rounded px-2 py-1 text-sm descuento text-orange-600 font-semibold">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-green-600">Base IVA</label>
                                    <input name="productos[${editIndex}][base_iva]" type="number" step="any" value="${p.base_iva || ''}" class="w-full border rounded px-2 py-1 text-sm base-iva text-center font-semibold text-green-600" placeholder="Auto">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-700">% IVA</label>
                                    <input name="productos[${editIndex}][iva_porcentaje]" value="${p.iva_porcentaje || 0}" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm iva-pct text-center">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-700">% Otro</label>
                                    <input name="productos[${editIndex}][otro_impuesto]" value="${p.otro_impuesto_porcentaje || p.otro_impuesto || 0}" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm otro-pct text-center">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-blue-600">Trasl. $</label>
                                    <input name="productos[${editIndex}][traslado]" value="${p.traslado || 0}" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm traslado text-center font-semibold text-blue-600">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-purple-600">ILC $</label>
                                    <input name="productos[${editIndex}][ilc]" value="${p.ilc || 0}" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm ilc text-center font-semibold text-purple-600">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-red-600">Reten. $</label>
                                    <input name="productos[${editIndex}][retencion]" value="${p.retencion || 0}" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm retencion text-center font-semibold text-red-600">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-red-800">ISR $</label>
                                    <input name="productos[${editIndex}][isr]" value="${p.isr || 0}" type="number" step="any" class="w-full border rounded px-2 py-1 text-sm isr text-center font-semibold text-red-800">
                                </div>
                                
                                <div class="flex justify-center md:justify-end pb-1 h-full">
                                    <button type="button" class="remove-product w-full md:w-auto text-red-600 font-bold hover:bg-red-100 border border-red-200 bg-white rounded px-3 py-1 text-xs">✕ Eliminar</button>
                                </div>
                            </div>
                        </div>
                    `);
                    editIndex++;
                });

                if (typeof calcularEditTotales === 'function') {
                    calcularEditTotales();
                }
            });
        });

        /*ELIMINAR */
        $(document).on('click', '.btn-delete-factura', function() {
            const id = $(this).data('id');
            const url = "{{ route('facturas.destroy', ':id') }}".replace(':id', id);

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    response.message,
                                    'success'
                                );
                                $('#facturas-table').DataTable().ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            const res = xhr.responseJSON;
                            const msg = res && res.message ? res.message : 'Ocurrió un problema al intentar eliminar el registro.';

                            if (xhr.status === 422 && res && res.has_payments) {
                                Swal.fire({
                                    title: 'Pagos vinculados detectados',
                                    text: msg + ' ¿Deseas forzar la eliminación de la factura y todos sus registros de pago asociados?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Sí, forzar eliminación',
                                    cancelButtonText: 'Cancelar'
                                }).then((forceResult) => {
                                    if (forceResult.isConfirmed) {
                                        $.ajax({
                                            url: url,
                                            method: 'DELETE',
                                            data: {
                                                _token: $('meta[name="csrf-token"]').attr('content'),
                                                force: 1
                                            },
                                            success: function(forceRes) {
                                                if (forceRes.success) {
                                                    Swal.fire('¡Eliminado!', forceRes.message, 'success');
                                                    $('#facturas-table').DataTable().ajax.reload();
                                                }
                                            },
                                            error: function(forceErr) {
                                                const errText = forceErr.responseJSON && forceErr.responseJSON.message
                                                    ? forceErr.responseJSON.message
                                                    : 'No se pudo forzar la eliminación del documento.';
                                                Swal.fire('Error', errText, 'error');
                                            }
                                        });
                                    }
                                });
                            } else {
                                Swal.fire(
                                    'Atención',
                                    msg,
                                    xhr.status === 422 ? 'warning' : 'error'
                                );
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush