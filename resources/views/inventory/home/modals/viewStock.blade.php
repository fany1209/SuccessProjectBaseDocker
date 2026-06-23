{{--
Inventario
Mostrar Stock-Lotes
Fecha de creación: 17-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 17-09-2025
--}}
<x-modal id="view-stock">
    <input type="hidden" id="product-id-batchs" name="product_id">
    <h2 class="text-2xl" id="product-title"></h2>
    <div class="grid grid-cols-1 w-full gap-2 p-2 max-h-[500px] overflow-y-auto" id="targets">
        <!--Información del Stock-->
    </div>
    <x-button class="close-modal">Close</x-button>
</x-modal>
@include('inventory.quarantine.modals.addQuarantine')
@push('js')
<script>
$(function(){
    const batchs = @json($batchs);
    $(document).on('click','.view-stock-btn',function(){
        let id = $('#product-id-batchs').val();
        let rows = '';
        batchs.forEach(product => {
            if(product.product_id == id){                
                let formattedStock = product.stock.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 3
                });
                rows += `
                    <div class="flex justify-around items-start w-full">
                        <div class="flex justify-start items-start w-full">
                            <div class="w-8 h-8 flex items-center justify-center rounded">
                                <img src="/images/errorB.png" alt="">
                            </div>
                            <div class="ml-4">
                                <h2 class="text-md text-gray-700">${product.batch}</h2>
                                <p class="text-sm font-light text-purple-500 mt-1">Stock: ${formattedStock} ${product.unit}</p>
                            </div>
                        </div>
                        <button 
                            data-id="${product.inventory_id}"
                            data-stock="${product.stock}"
                            data-batch="${product.batch}"
                            data-unit="${product.unit}"
                            data-target="add-quarantine"
                            class="open-modal add-quarantine-btn bg-red-500 hover:bg-red-600 rounded-full p-2" title="Add Quarantine">
                            <img width="20" src="{{ asset('images/quarantine.svg') }}" alt="Add Quarantine"/>
                        </button>
                    </div>
                `;                    
            }
        });
        $('#targets').html(rows);
    });
    $(document).on('click','.add-quarantine-btn',function(){
        const batch = $(this).data('batch');
        const inventory_id = $(this).data('id');
        let stock = $(this).data('stock');
        const unit = $(this).data('unit');
        $('#add-quarantine #product-tittle').text(`${$('#product-title').text()} /`);
        $('#add-quarantine #batch-tittle').text(`${batch}`);
        $('#add-quarantine-form #inventory-id').val(inventory_id);
        stock = stock.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 3
                });
        $('#add-quarantine-form #stock-available').text(`Stock: ${stock} ${unit}`);
    });

});
</script>
@endpush