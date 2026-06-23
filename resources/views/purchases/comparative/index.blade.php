@php
    if(!isset($grupos)){
        $insumos_db = \DB::table('comparative')->orderBy('id', 'desc')->get();
        $grupos = $insumos_db->groupBy('folio');
    }
@endphp

<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search_comparative" placeholder="Search by folio or product count..."></x-input>
        </div>

        <table id="comparative-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th class="px-6 py-4 text-center">Folio de Comparativa</th>
                    <th class="px-6 py-4 text-center">Opciones Registradas</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm">
                @foreach($grupos as $folio => $productos)
                <tr>
                    <td class="px-6 py-4 font-bold text-blue-900 uppercase italic text-[12px]">
                        <i class="ri-file-list-3-fill mr-2 text-blue-500"></i> {{ $folio ?? 'S/F' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                            {{ $productos->count() }} PRODUCTOS EN LISTA
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-1">
                            <button type="button" class="view-details bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-sm shadow-sm transition" 
                                data-folio="{{ $folio }}" 
                                data-items='@json($productos)'
                                title="Ver Detalle">
                                <img width="18" src="{{ asset('images/ver.png') }}">
                            </button>

                            <a href="/purchases/comparative-pdf/{{ $folio }}" target="_blank"
                                class="bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-sm shadow-sm transition" 
                                title="Download PDF">
                                <i class="ri-file-pdf-line text-[18px]"></i>
                            </a>

                            <button type="button" class="delete-comparative bg-red-500 hover:bg-red-600 text-white p-2 rounded-sm shadow-sm transition"
                                data-folio="{{ $folio }}" title="Delete">
                                <img width="18" src="{{ asset('images/borrar.png') }}">
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<x-modal id="modal-detalle-comp" class="max-w-7xl">
    <div class="p-6">
        <div class="flex justify-between items-center border-b-2 border-blue-500 pb-4 mb-6">
            <h2 id="modal-titulo" class="text-2xl font-black text-gray-800 uppercase tracking-tighter italic"></h2>
            <button class="close-modal text-gray-400 hover:text-red-500 text-2xl"><i class="ri-close-circle-fill"></i></button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left bg-white border-collapse">
                <thead class="bg-gray-100 text-[10px] uppercase font-black text-gray-600 border-b">
                    <tr>
                        <th class="p-4">Vista</th><th class="p-4">Insumo</th><th class="p-4">Proveedor</th>
                        <th class="p-4 text-center">Cantidad</th><th class="p-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody id="modal-tabla-cuerpo" class="text-xs text-gray-700"></tbody>
            </table>
        </div>
    </div>
</x-modal>

@push('js')
<script>
$(document).ready(function () {
    let table = $('#comparative-table').DataTable({
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

    $('#search_comparative').keyup(function(){
        table.search($(this).val()).draw();
    });

    $('.option-btn').on('click', function() {
        const option = $(this).data('button');
        if(option === 'comparative') {
            setTimeout(() => {
                table.columns.adjust().draw();
            }, 50);
        }
    });

    $(document).on('click', '.view-details', function() {
        const folio = $(this).data('folio');
        const productos = $(this).data('items');
        $('#modal-titulo').text('Detalles: ' + folio);
        
        let filas = '';
        productos.forEach(p => {
            filas += `
                <tr class="border-b">
                    <td class="p-2"><img src="${p.imagen}" class="w-10 h-10 object-contain" onerror="this.src='https://via.placeholder.com/150'"></td>
                    <td class="p-2 font-bold uppercase text-blue-900">${p.insumo}</td>
                    <td class="p-2">${p.proveedor || 'N/A'}</td>
                    <td class="p-2 text-center font-black">${p.cantidad}</td>
                    <td class="p-2 text-right font-black text-green-700">$${parseFloat(p.precio_total).toLocaleString()}</td>
                </tr>`;
        });
        $('#modal-tabla-cuerpo').html(filas);
        $('#modal-detalle-comp').removeClass('hidden').addClass('flex');
    });

    $(document).on('click', '.close-modal', function() {
        $('#modal-detalle-comp').addClass('hidden').removeClass('flex');
    });

    $(document).on('click', '.delete-comparative', function() {
        let folio = $(this).data('folio');
        Swal.fire({
            title: '¿Are you sure?',
            text: `Se eliminará el folio: ${folio}`,
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
                    success: function() {
                        Swal.fire('Deleted!','Folio removed.','success');
                        location.reload(); 
                    }
                });
            }
        });
    });
});
</script>
@endpush