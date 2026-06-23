{{--
Warehouse
Warehouse (Show Summary)
Fecha de creación: 21-07-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 28-08-25
--}}
<x-modal id="summary">
    <div class="flex justify-between items-center w-full my-2">
        <h2 class="my-2 text-2xl row-span-1 row-start-1 w-1/2">Summary</h2>
        <div class="flex flex-col items-start w-1/2">
            <label for="" class="mb-1 block font-medium text-sm text-gray-700">Select a warehouse</label>
            <select id="current-warehouse-summary" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                <option value="">None</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="items flex flex-col items-center w-full h-[480px] overflow-y-auto my-2 rounded-md"></div>
    <x-button class="close-modal">Close</x-button>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    const summary = @json($summary);
    function getSummary(){
        $('#current-warehouse-summary').on('change',function(){
            let wh = $(this).val();
            $('.items').empty();
            summary.forEach(product => {
                if(wh == product.warehouse_id){
                    let formattedTotal = product.net_weight.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                    });
                    $('.items').append(`
                        <div class="flex justify-between items-center bg-green-100 border-b-2 border-green-700 px-4 py-2 w-full">
                            <span class="text-blue-800 text-center">${product.name}</span>
                            <span class="text-blue-800 text-center">Total: ${formattedTotal} ${product.unit ? product.unit:''}</span>
                        </div>
                    `);
                }
            });
        });
    }
    getSummary();
});
</script>
@endpush