{{-- 
Requisitions
Mostrar Requisitions
Fecha de creación: 15-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 20-01-2026
--}}

<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search_requisitions" placeholder="Search..."></x-input>

            <select id="requisitions_check"
                class="p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                <option value="0">Sin Revisar</option>
                <option value="1">Revisadas</option>
            </select>
        </div>

        <table id="requisitions-table"
            class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th class="px-6 py-4 text-right">CONSECUTIVE</th>
                    <th class="px-6 py-4 text-right">PURCHASE ORDER</th>
                    <th class="px-6 py-4 text-right">APPLICANT</th>
                    <th class="px-6 py-4 text-right">DEPARTMENT</th>
                    <th class="px-6 py-4 text-right">INSUMO</th>
                    <th class="px-6 py-4 text-right">DATA SHEET</th>
                    <th class="px-6 py-4 text-right">SAFETY SHEET</th>
                    <th class="px-6 py-4 text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm"></tbody>
        </table>
    </div>

    @can('purchases.requisitions.show')
        @include('purchases.requisitions.modals.checkRequisition')
        @include('purchases.requisitions.modals.insumos')
    @endcan

@push('js')
<script>
$(document).ready(function () {

    function badge(text, color){
        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded bg-${color}-400">${text}</span>`;
    }

    function getRequisitions() {

        let table = $('#requisitions-table').DataTable({
            ajax: {
                url: "{{ route('requisitions.getRequisitions') }}",
                data: function (d) {
                    d.requisitions_check = $('#requisitions_check').val();
                    d.search_requisitions = $('#search_requisitions').val();
                },
                dataSrc: 'requisitions'
            },

            columns: [
                { data: 'consecutive', defaultContent: 'Pendiente' },
                { data: 'purchase_order', defaultContent: 'Pendiente' },
                { data: 'applicant' },
                { data: 'department' },

                {
                    data: 'insumo_resumen',
                    render: function (data) {
                        if(!data) return '—';
                        if(data === 'directo') return badge('Directo','emerald');
                        if(data === 'indirecto') return badge('Indirecto','purple');
                        if(data === 'mixto') return badge('Mixto','gray');
                        return data;
                    }
                },

                {
                    data: 'data_sheet',
                    render: d => d ? badge('Yes','blue') : badge('No','red')
                },

                {
                    data: 'safety_sheet',
                    render: d => d ? badge('Yes','blue') : badge('No','red')
                },

                { data: null }
            ],

            columnDefs: [{
                targets: 7,
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {

                    let buttons = `
                        <button data-id="${row.id}"
                            data-target="insumos-modal"
                            class="open-modal view-insumos-btn text-sm text-white bg-emerald-600 hover:bg-emerald-700 rounded-sm p-2"
                            title="Ver Insumos">
                            <img width="18" src="{{ asset('images/ver.png') }}">
                        </button>

                        <button
                            type="button"
                            data-id="${row.id}"
                            data-modal="check-requisition"
                            data-consecutive="${row.consecutive ?? ''}"
                            data-po="${row.purchase_order ?? ''}"
                            class="open-modal check-requisition-btn text-sm text-white bg-sky-500 hover:bg-sky-600 rounded-sm p-2"
                            title="Check">
                            <img width="18" src="{{ asset('images/check.png') }}">
                        </button>
                    `;
                    

                    if(row.canUpdate){
                        buttons += `
                            <button data-id="${row.id}"
                                data-target="edit-requisition"
                                class="open-modal edit-requisition-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2"
                                title="Edit">
                                <img width="18" src="{{ asset('images/editar.png') }}">
                            </button>
                        `;
                    }

                    if(row.canDelete){
                        buttons += `
                            <button data-id="${row.id}"
                                class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
                                title="Delete">
                                <img width="18" src="{{ asset('images/borrar.png') }}">
                            </button>
                        `;
                    }

                    buttons += `
                        <button data-id="${row.id}"
                            class="requisition-format-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2"
                            title="Format">
                            <img width="18" src="{{ asset('images/archivo.png') }}">
                        </button>
                    `;

                    return buttons;
                }
            }],

            searching: false,
            pageLength: 10,
            responsive: true,
            lengthChange: false
        });

        $('#requisitions_check').change(() => table.ajax.reload());
        $('#search_requisitions').keyup(() => table.ajax.reload());

        viewInsumos();
        requisitionFormat();
    }

    function viewInsumos(){
        $('#requisitions-table tbody').on('click', '.view-insumos-btn', function(){
            let id = $(this).data('id');
            $('#insumos-body').empty();

            $.ajax({
                type: 'GET',
                url: `/requisitions/${id}`,
                success: function(res){
                    res.products.forEach(p => {

                        let badgeInsumo =
                            p.insumo === 'directo'
                                ? badge('Directo','emerald')
                                : p.insumo === 'indirecto'
                                ? badge('Indirecto','purple')
                                : badge('Mixto','gray');

                        $('#insumos-body').append(`
                            <tr>
                                <td class="border px-2 py-1">${p.description ?? '—'}</td>
                                <td class="border px-2 py-1">${p.supplier ?? '—'}</td>
                                <td class="border px-2 py-1">${p.use ?? '—'}</td>
                                <td class="border px-2 py-1">${p.quantity ?? '—'}</td>
                                <td class="border px-2 py-1 text-center">${badgeInsumo}</td>
                            </tr>
                        `);
                    });
                },
                error(){
                    Swal.fire('Error','No se pudieron cargar los insumos','error');
                }
            });
        });
    }

    function deleteRequisition() {
        $('#requisitions-table tbody').on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            let row = $(this).closest('tr'); 
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', 
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/requisitions/${id}`, 
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}' 
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message || 'Requisition has been deleted.',
                                'success'
                            );
                            $('#requisitions-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            let msg = 'Something went wrong';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', msg, 'error');
                        }
                    });
                }
            })
        });
    }
    function requisitionFormat(){
        $('#requisitions-table tbody').on('click','.requisition-format-btn',function(){
            let id = $(this).data('id');
            window.open(`requisition-format/${id}`,'_blank');
        });
    }

    deleteRequisition();

    getRequisitions();
});
</script>
@endpush
