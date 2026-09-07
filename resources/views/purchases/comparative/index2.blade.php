@php
    $mis_insumos = \DB::table('comparative')
        ->where('user_id', auth()->id()) 
        ->orderBy('id', 'desc')
        ->get();
    $mis_grupos = $mis_insumos->groupBy('folio');
@endphp

<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search_my_comparative" placeholder="Search my folios..."></x-input>
        </div>

        <div class="w-full overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
            <table id="my-comparative-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left" style="width:100%">
                <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                    <tr>
                        <th class="px-6 py-4 text-center">My Folio</th>
                        <th class="px-6 py-4 text-center">Items</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm">
                    @forelse($mis_grupos as $folio => $productos)
                    <tr class="hover:bg-blue-50 transition-colors border-b border-gray-100">
                        <td class="px-6 py-4 font-bold text-blue-900 uppercase italic text-[12px]">
                            <i class="ri-file-user-fill mr-2 text-blue-500"></i> {{ empty($folio) ? 'S/F' : $folio }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                {{ $productos->count() }} PRODUCTOS EN LISTA
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-1">
                                <button type="button" class="view-details2 bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-sm shadow-sm transition" 
                                    data-folio="{{ $folio }}" data-items='@json($productos)'>
                                    <img width="18" src="{{ asset('images/ver.png') }}">
                                </button>

                                <button type="button" class="edit-comparative-btn bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-sm shadow-sm transition"
                                    data-folio="{{ $folio }}" data-items='@json($productos)'>
                                    <img width="18" src="{{ asset('images/editar.png') }}">
                                </button>

                                <a href="/purchases/comparative-pdf/{{ $folio }}" target="_blank" 
                                   class="bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-sm shadow-sm flex items-center">
                                    <i class="ri-file-pdf-line text-[18px]"></i>
                                </a>

                                <button type="button" class="delete-comparative bg-red-500 hover:bg-red-600 text-white p-2 rounded-sm shadow-sm transition" 
                                    data-folio="{{ $folio }}">
                                    <img width="18" src="{{ asset('images/borrar.png') }}">
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td></td>
                            <td class="px-6 py-4 text-center text-gray-400 italic">No personal comparisons found.</td>
                            <td></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- MODAL DE DETALLES --}}
<x-modal id="modal-detalle-comp2" class="max-w-7xl">
    <div class="p-6">
        <div class="flex justify-between items-center border-b-2 border-blue-500 pb-4 mb-6">
            <h2 id="modal-titulo2" class="text-2xl font-black text-gray-800 uppercase tracking-tighter italic font-sans"></h2>
            <button class="close-modal text-gray-400 hover:text-red-500 text-2xl"><i class="ri-close-circle-fill"></i></button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left bg-white border-collapse">
                <thead class="bg-gray-100 text-[10px] uppercase font-black text-gray-600 border-b">
                    <tr>
                        <th class="p-4">Vista</th><th class="p-4">Insumo</th><th class="p-4">Descripción</th>
                        <th class="p-4 text-center">Cant.</th><th class="p-4 text-right">Total</th><th class="p-4 text-center">Link</th>
                    </tr>
                </thead>
                <tbody id="modal-tabla-cuerpo2" class="text-xs text-gray-700"></tbody>
            </table>
        </div>
    </div>
</x-modal>

{{-- MODAL DE EDICIÓN --}}
<x-modal id="modal-edit-comparative" class="max-w-4xl">
    <form class="flex flex-col w-full gap-4" action="{{ route('purchases.comparative.update.all') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="p-6">
            <x-tittle-form id="edit-modal-title" class="border-b-2 border-yellow-600 pb-2 w-full text-yellow-700 uppercase italic font-black mb-4">
                Editando Comparativa
            </x-tittle-form>

            <div id="container-edit-insumos" class="w-full max-h-[60vh] overflow-y-auto px-2"></div>

            <div class="flex justify-start w-full mt-4 px-2">
                <x-button-1 type="button" colorBtn="blue" id="add-insumo-edit">
                    <i class="ri-add-line"></i> Agregar otro insumo
                </x-button-1>
            </div>

            <div class="flex justify-end gap-2 border-t pt-4 mt-4">
                <x-button-1 colorBtn="red" class="close-edit-modal" type="button">Cancelar</x-button-1>
                <x-button-1 colorBtn="green" type="submit">Actualizar Todo</x-button-1>
            </div>
        </div>
    </form>
</x-modal>

@push('js')
<script>
$(document).ready(function () {
    let table2 = $('#my-comparative-table').DataTable({
        autoWidth: false,
        dom: 'rtip',
        pageLength: 10,
        responsive: true,
        language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
        columnDefs: [
            { orderable: false, targets: 2 },
            { className: "align-middle", targets: "_all" }
        ],
        order: [[0, "desc"]]
    });

    $('#search_my_comparative').keyup(function(){
        table2.search($(this).val()).draw();
    });

    $('.option-btn').on('click', function() {
        const option = $(this).data('button');
        if(option === 'comparative2') { 
            setTimeout(() => {
                table2.columns.adjust().draw();
            }, 100);
        }
    });

    $(document).on('click', '.view-details2', function() {
        const folio = $(this).data('folio');
        const productos = $(this).data('items');
        $('#modal-titulo2').text('My Details: ' + folio);
        let filas = '';
        productos.forEach(p => {
            filas += `
                <tr class="border-b">
                    <td class="p-4"><img src="${p.imagen}" class="w-12 h-12 object-contain" onerror="this.src='https://via.placeholder.com/150'"></td>
                    <td class="p-4 font-bold uppercase text-blue-900">${p.insumo || ''}</td>
                    <td class="p-4 text-xs italic text-gray-500">${p.descripcion || ''}</td>
                    <td class="p-4 text-center font-bold">${p.cantidad || 0}</td>
                    <td class="p-4 text-right font-bold text-green-700">$${parseFloat(p.precio_total || 0).toLocaleString()}</td>
                    <td class="p-4 text-center">${p.link ? `<a href="${p.link}" target="_blank" class="text-blue-600"><i class="ri-external-link-line text-xl"></i></a>` : '—'}</td>
                </tr>`;
        });
        $('#modal-tabla-cuerpo2').html(filas);
        $('#modal-detalle-comp2').removeClass('hidden').addClass('flex');
    });

    $(document).on('click', '.edit-comparative-btn', function() {
        const folio = $(this).data('folio');
        const productos = $(this).data('items');
        $('#edit-modal-title').text('Editando Folio: ' + folio);
        $('#container-edit-insumos').empty();

        productos.forEach(p => {
            let imgPreviewHtml = (p.imagen && p.imagen.trim() !== '') 
                ? `<img src="${p.imagen}" class="object-contain w-full h-full rounded-md shadow-sm" onerror="this.src='https://via.placeholder.com/250x200?text=URL+de+Imagen+Invalida'">` 
                : `<div class="text-center text-gray-400"><i class="ri-image-add-line text-4xl block"></i><span class="text-xs">Vista previa de imagen</span></div>`;

            let card = `
                <div class="insumo-row border-2 border-dashed border-gray-200 rounded-md p-4 mb-6 relative bg-white shadow-sm">
                    <input type="hidden" name="id[]" value="${p.id}">
                    <span class="remove-insumo absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-2 right-2 rounded-full font-bold cursor-pointer p-1">X</span>
                    
                    <div class="flex flex-row w-full gap-4 mb-2">
                        <div class="flex flex-col w-3/4">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Insumo</label>
                            <input required type="text" name="insumo[]" value="${p.insumo || ''}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Nombre del artículo...">
                        </div>
                        <div class="flex flex-col w-1/4">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Pz / Cant</label>
                            <input required type="number" name="cantidad[]" value="${p.cantidad || ''}" class="cantidad calcular text-center w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="0">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row w-full gap-4 mb-2">
                        <div class="flex flex-col w-full md:w-1/3">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Proveedor</label>
                            <input type="text" name="proveedor[]" value="${p.proveedor || ''}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Nombre de la empresa">
                        </div>
                        <div class="flex flex-col w-full md:w-1/3">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Precio Total</label>
                            <input required type="number" step="0.01" name="precio_total[]" value="${p.precio_total || ''}" class="precio_total calcular font-bold text-green-700 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="0.00">
                        </div>
                        <div class="flex flex-col w-full md:w-1/3">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Precio Unitario</label>
                            <input type="text" name="precio_unt[]" value="${p.precio_unt || '0.00'}" class="precio_unt bg-gray-100 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" readonly placeholder="0.00">
                        </div>
                    </div>

                    <div class="flex flex-col w-full mb-2">
                        <label class="block font-bold text-sm text-gray-700 mb-1">Link de Imagen (URL)</label>
                        <input type="text" name="imagen[]" value="${p.imagen || ''}" class="img-link w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Pegue el link de la imagen aquí...">
                    </div>

                    <div class="flex justify-center w-full mt-2 mb-4">
                        <div class="preview-container w-full max-w-[250px] h-48 border-2 border-gray-100 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 p-2">
                            ${imgPreviewHtml}
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row w-full gap-4 mb-2">
                        <div class="flex flex-col w-full md:w-1/2">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Link del Producto</label>
                            <input type="text" name="link[]" value="${p.link || ''}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="URL de compra">
                        </div>
                        <div class="flex flex-col w-full md:w-1/2">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Entrega Estimada</label>
                            <input type="date" name="entrega_estimada[]" value="${p.entrega_estimada || ''}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row w-full gap-4 mb-2">
                        <div class="flex flex-col w-full md:w-1/2">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Descripción</label>
                            <input type="text" name="descripcion[]" value="${p.descripcion || ''}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Especificaciones técnicas, marca...">
                        </div>
                        <div class="flex flex-col w-full md:w-1/2">
                            <label class="block font-bold text-sm text-gray-700 mb-1">Comentarios</label>
                            <input type="text" name="comentarios[]" value="${p.comentarios || ''}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" placeholder="Notas sobre el envío, urgencia...">
                        </div>
                    </div>
                </div>`;
            $('#container-edit-insumos').append(card);
        });
        $('#modal-edit-comparative').removeClass('hidden').addClass('flex');
    });

    $(document).on('click', '.close-modal, .close-edit-modal', function() {
        $('#modal-detalle-comp2, #modal-edit-comparative').addClass('hidden').removeClass('flex');
    });

    $(document).on('click', '.delete-comparative', function() {
        let folio = $(this).data('folio');
        Swal.fire({
            title: '¿Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/purchases/comparative/${folio}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() { location.reload(); }
                });
            }
        });
    });

    $(document).on('input', '.calcular', function() {
        const row = $(this).closest('.insumo-row');
        const qty = parseFloat(row.find('.cantidad').val()) || 0;
        const total = parseFloat(row.find('.precio_total').val()) || 0;
        
        if (qty > 0) {
            const unit = total / qty;
            row.find('.precio_unt').val(unit.toFixed(2));
        } else {
            row.find('.precio_unt').val('0.00');
        }
    });

    $(document).on('input', '.img-link', function() {
        const url = $(this).val();
        const preview = $(this).closest('.insumo-row').find('.preview-container');

        if (url && url.trim() !== '') {
            preview.html(`<img src="${url}" class="object-contain w-full h-full rounded-md shadow-sm" onerror="this.src='https://via.placeholder.com/250x200?text=URL+de+Imagen+Invalida'">`);
        } else {
            preview.html('<div class="text-center text-gray-400"><i class="ri-image-add-line text-4xl block"></i><span class="text-xs">Vista previa de imagen</span></div>');
        }
    });

    $(document).on('click', '.remove-insumo', function() {
        const row = $(this).closest('.insumo-row');
        const container = row.parent();
        if (container.find('.insumo-row').length > 1) {
            row.remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'La comparativa debe tener al menos un insumo.',
            });
        }
    });

    $('#add-insumo-edit').on('click', function() {
        const container = $('#container-edit-insumos');
        const newRow = container.find('.insumo-row').first().clone();
        
        newRow.find('input').val('');
        newRow.find('.preview-container').html('<div class="text-center text-gray-400"><i class="ri-image-add-line text-4xl block"></i><span class="text-xs">Vista previa de imagen</span></div>');
        container.append(newRow);
        container.animate({ scrollTop: container[0].scrollHeight }, 300);
    });

});
</script>
@endpush