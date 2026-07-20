{{--
Inventario
Inventario disponible
Fecha de creación: 11-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 17-09-2025
--}}
<section class="flex flex-col items-center w-full">
    <div class="flex justify-center items-center gap-2 w-full">
        <x-input class="w-full p-2 my-1" id="search-inventory" placeholder="Search..."></x-input>
    </div>
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex flex-col justify-center items-center w-full p-2 bg-green-700 rounded-sm">
            <h2 class="text-xl font-bold tracking-[5px] text-white">Inventory available</h2>
        </div>
        <table id="inventory-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-green-700 text-white uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-1 text-left">PRODUCT</th>
                    <th scope="col" class="px-6 py-1 text-left">STOCK</th>
                    <th scope="col" class="px-6 py-1 text-left">UNIT</th>
                    <th scope="col" class="px-6 py-1">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>
</section>
@push('js')
<script>
$(document).ready(function(){    
    function getInventory(){
        let table = $('#inventory-table').DataTable({
            ajax: {
                url: "{{ route('inventory.getInventoryAvailable') }}",
                data: function (d) {
                    d.search = $('#search-inventory').val();
                },
                dataSrc: 'products'
            },
            order: [[1, 'desc']],
            columns: [
                { data: 'name' },
                {data: 'stock',
                    render: function(data, type, row) {
                        let value = parseFloat(data);
                        if (isNaN(value)) {
                            return '-';
                        }
                        let formatted = value.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded-lg bg-green-700">${formatted}</span>`;
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
                    targets: 3,
                    orderable: false,
                    searchable: false,
                    className: 'text-start',
                    render: function(data, type, row) {
                        let buttons = ``;
                        buttons += `
                            <button data-id="${row.product_id}" data-name="${row.name}" data-target="view-stock" class="open-modal view-stock-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2" title="View Stock">
                                <img width="18" src="{{ asset('images/ver.png') }}" alt="Edit"/>
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
        $('#search-inventory').on('keyup',function (){
            table.ajax.reload();
        });
        $('#inventory-table tbody').on('click', '.view-stock-btn', function(){
            let id = $(this).data('id');
            let name = $(this).data('name');
            $('#product-title').text(name);
            $('#product-id-batchs').val(id);
        });
    }
    getInventory();
});
</script>
@endpush