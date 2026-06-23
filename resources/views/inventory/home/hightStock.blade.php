{{--
Inventario - Insumos
--}}
<section class="flex flex-col items-center w-full overflow-y-auto h-[500px]">
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex flex-col justify-center items-center w-full p-2 bg-sky-400 rounded-sm">
            <h2 class="text-xl font-bold tracking-[5px] text-white">Insumos</h2>
        </div>
        <table id="hight-stock-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-sky-400 text-white uppercase text-md">
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
    function getHightStock(){
        let table = $('#hight-stock-table').DataTable({
            ajax: {
                url: "{{ route('inventory.getInventoryAvailable') }}",
                data: function (d) {
                    d.search = $('#search').val();
                },
                dataSrc: 'productsHight'
            },
            order: [[1, 'desc']],
            columns: [
                { data: 'name' },
              
                { data: 'stock', 
                    render: function(data, type, row) {
                        let value = parseFloat(data);
                        if (isNaN(value)) return '0.00';
                        
                        let formatted = value.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        return `<span class="text-xs tracking-[2px] font-bold text-white px-2 py-1 rounded-lg bg-sky-400">${formatted}</span>`;
                    }
                },
                { data: 'unit',
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
                        return `
                            <button data-id="${row.product_id}" data-name="${row.name}" data-target="view-stock" class="open-modal view-stock-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2" title="View Stock">
                                <img width="18" src="{{ asset('images/ver.png') }}" alt="Edit"/>
                            </button>
                        `;
                    }
                }
            ],
            responsive: true,
            autoWidth: true,
            lengthChange: false,
            searching: false,
            paging: false,
            serverSide: false,
            language: {
                info: "Mostrando _TOTAL_ insumos",
                infoEmpty: "No hay insumos disponibles",
                zeroRecords: "No se encontraron resultados",
                paginate: {
                    next: "Sig",
                    previous: "Ant"
                },
            }
        });

        $('#search').on('keyup',function (){
            table.ajax.reload();
        });

        $('#hight-stock-table tbody').on('click', '.view-stock-btn', function(){
            let id = $(this).data('id');
            let name = $(this).data('name');
            $('#product-title').text(name);
            $('#product-id-batchs').val(id);
        });
    }
    getHightStock();
});
</script>
@endpush