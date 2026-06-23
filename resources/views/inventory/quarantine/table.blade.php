{{--
Inventario
Quarantine
Fecha de creación: 22-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 22-09-2025
--}}
<section class="flex flex-col items-center w-full overflow-y-auto h-[500px]">
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex flex-col justify-center items-center w-full p-2 bg-orange-400 rounded-sm">
            <h2 class="text-xl font-bold tracking-[5px] text-white">Quarantine</h2>
        </div>
        <table id="quarantine-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-orange-400 text-white uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-1 text-left">PRODUCT</th>
                    <th scope="col" class="px-6 py-1 text-left">BATCH</th>
                    <th scope="col" class="px-6 py-1 text-left">QUANTITY</th>
                    <th scope="col" class="px-6 py-1 text-left">UNIT</th>
                    <th scope="col" class="px-6 py-1">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>
    @include('inventory.quarantine.modals.updateQuarantine')
</section>
@push('js')
<script>
$(document).ready(function(){    
    function getQuarantine(){
        let table = $('#quarantine-table').DataTable({
            ajax: {
                url: "{{ route('inventory.getInventoryAvailable') }}",
                data: function (d) {
                    d.search = $('#search').val();
                },
                dataSrc: 'quarantine'
            },
            order: [[2, 'desc']],
            columns: [
                { data: 'name' },
                { data: 'batch' },
                {data: 'quantity',
                    render: function(data, type, row) {
                        let value = parseFloat(data);
                        if (isNaN(value)) {
                            return '-';
                        }
                        let formatted = value.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded-lg bg-sky-400">${formatted}</span>`;
                    }
                },
                {data: 'unit',
                    render: function(data, type, row) {
                        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded-lg bg-blue-400">${data}</span>`;
                    }
                }
            ],
            columnDefs: [
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                    className: 'text-start',
                    render: function(data, type, row) {
                        let buttons = ``;
                        buttons += `
                            <button
                                data-id="${row.quarantine_id}"
                                data-product="${row.name}"
                                data-batch="${row.batch}"
                                data-notes="${row.notes}"
                                data-quantity="${row.quantity}"
                                data-unit="${row.unit}"
                                data-target="edit-quarantine" 
                                class="open-modal edit-quarantine-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2">
                                <img width="18" src="{{ asset('images/ver.png') }}"/>
                            </button>
                        `;
                        return buttons;
                    }
                }
            ],
            responsive: true,
            autoWidth: true,
            lengthChange: false,
            searching: false,
            paging: false,
            pageLength: 10,
            serverSide: false,
            responsive: true,
            autoWidth: false,
            language: {
                info: "Show _START_ to _END_ of _TOTAL_ products",
                lengthMenu: "Show _MENU_ inventory",
                infoEmpty: "There aren't products available",
                zeroRecords: "No results found",
                infoFiltered: "(filtered on _MAX_ total records)",
                paginate: {
                    first:      "First",
                    last:       "Last",
                    next:       "Next",
                    previous:   "Previous"
                },
            }
        });
        $('#search').on('keyup',function (){
            table.ajax.reload();
        });
        $('#quarantine-table tbody').on('click','.edit-quarantine-btn',function(){
            let id = $(this).data('id');
            let product = $(this).data('product');
            let batch = $(this).data('batch');
            let notes = $(this).data('notes');
            let quantity = $(this).data('quantity');
            let unit = $(this).data('unit');
            $('#edit-quarantine #product-tittle').text(`${product} /`);
            $('#edit-quarantine #batch-tittle').text(`${batch}`);
            $('#edit-quarantine-form #quarantine-id').val(id);
            quantity = quantity.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 3
                    });
            $('#edit-quarantine-form #quantity-available').text(`Quantity: ${quantity} ${unit}`);
            $('#edit-quarantine-form #notes-area').text(`${notes}`);
        });
    }
    getQuarantine();
});
</script>
@endpush