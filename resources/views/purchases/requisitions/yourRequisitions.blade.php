{{--
Requisitions
Mostrar Requisitions (Mis Requisiciones)
Fecha de creación: 15-10-2025
Creado por: Jacob
Actualizado por:stefany
Fecha de creación: 24-02-2026

--}}

<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        <table id="your-requisitions-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4 text-right">DATE</th>
                    <th scope="col" class="px-6 py-4 text-right">PRODUCTS</th>
                    <th scope="col" class="px-6 py-4 text-right">DATA SHEET</th>
                    <th scope="col" class="px-6 py-4 text-right">SAFETY SHEET</th>
                    <th scope="col" class="px-6 py-4 text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>
</section>

@include('purchases.requisitions.modals.insumos')
@include('purchases.requisitions.modals.editRequisition')

@push('js')
<script>
$(document).ready(function(){

    function badge(text, color){
        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded-lg bg-${color}-400">${text}</span>`;
    }

    function getYourRequisitions(){
        let table = $('#your-requisitions-table').DataTable({
            ajax: {
                url: "{{ route('requisitions.getYourRequisitions') }}",
                dataSrc: 'requisitions'
            },
            columns: [
                { data: 'date_formatted' },
                { 
                    data: 'products',
                    render: function(data) {
                        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded-lg bg-blue-400">${data}</span>`;
                    }
                },
                { 
                    data: 'data_sheet',
                    render: d => d != 0 ? badge('Yes','blue') : badge('No','red')
                },
                { 
                    data: 'safety_sheet',
                    render: d => d != 0 ? badge('Yes','blue') : badge('No','red')
                },
                { data: null } 
            ],
            columnDefs: [
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row) {
                        let buttons = ``;

                        buttons += `
                            <button data-id="${row.id}" data-target="insumos-modal" class="open-modal view-insumos-btn text-sm text-white bg-emerald-600 hover:bg-emerald-700 rounded-sm p-2" title="Ver Insumos">
                                <img width="18" src="{{ asset('images/ver.png') }}" alt="Ver"/>
                            </button>
                        `;

                        if(row.canUpdate){
                            buttons += `
                                <button data-id="${row.id}" data-target="edit-requisition" class="open-modal edit-requisition-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                    <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                            `;
                        }

                        if(row.canDelete){
                            buttons += `
                                <button data-id="${row.id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
                                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                                </button>
                            `;
                        }
                        
                        return buttons || '—';
                    }
                }
            ],
            lengthChange: false,
            searching: false,
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ requisitions",
                zeroRecords: "No results found",
                infoEmpty: "No requisitions available",
                paginate: {
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        deleteYourRequisition(table);
        editYourRequisition(table);
        viewInsumos(); 
    }

    function viewInsumos(){
        $('#your-requisitions-table tbody').on('click', '.view-insumos-btn', function(){
            let id = $(this).data('id');
            $('#insumos-body').empty(); 

            $.ajax({
                type: 'GET',
                url: `/requisitions/${id}`,
                success: function(res){
                    res.products.forEach(p => {
                        let badgeInsumo = '';
                        if(p.insumo === 'directo') badgeInsumo = `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded bg-emerald-400">Directo</span>`;
                        else if(p.insumo === 'indirecto') badgeInsumo = `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded bg-purple-400">Indirecto</span>`;
                        else badgeInsumo = `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded bg-gray-400">Mixto</span>`;

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
                error: () => Swal.fire('Error', 'No se pudieron cargar los detalles', 'error')
            });
        });
    }

    function editYourRequisition(table){
        $('#your-requisitions-table tbody').on('click', '.edit-requisition-btn', function(){
            const modal = $('#edit-requisition');
            let id = $(this).data('id');
            $.ajax({
                type: 'GET',
                url: `/requisitions/${id}`,
                success: function(response){
                    modal.find('#req_id').val(response.requisition.id);
                    modal.find('#applicant_name,#applicant').val(response.requisition.applicant);
                    modal.find('#department').val(response.requisition.department);
                    
                    if(response.requisition.data_sheet === 1){
                        modal.find('#data-sheet-btn').text('Yes').addClass('border-green-400 bg-green-400 text-white').removeClass('border-gray-400 text-gray-700');
                    } else {
                        modal.find('#data-sheet-btn').text('No').removeClass('border-green-400 bg-green-400 text-white').addClass('border-gray-400 text-gray-700');
                    }

                    modal.find('#products').empty();
                    response.products.forEach(product => {
                        let template = modal.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
                        template.find('.id').val(product.id);
                        template.find('.description').val(product.description);
                        template.find('.quantity').val(product.quantity.replace(/[^0-9]/g, ''));
                        template.find('.unit').val(product.quantity.replace(/[0-9]/g, ''));
                        template.find('.image-ilustration').attr('src', product.image_url);
                        modal.find('#products').append(template);
                    });
                }
            });
        });
    }

    function deleteYourRequisition(table){
        $('#your-requisitions-table tbody').on('click', '.delete-btn', function(){
            let id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "This requisition will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/requisitions/${id}`,
                        method: "DELETE",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function () {
                            table.ajax.reload();
                            Swal.fire("Deleted!", "The requisition has been removed.", "success");
                        }
                    });
                }                
            });
        });
    }

    getYourRequisitions();
});
</script>
@endpush