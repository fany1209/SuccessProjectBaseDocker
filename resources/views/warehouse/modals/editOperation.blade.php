{{--
Warehouse
Edit Cli
Fecha de creación: 28-10-2025
Creado por: Jacob
Actualizado por: fany
Fecha de actualización: 09/03/2026
--}}
<x-modal id="edit-cli">
    <form class="flex flex-col items-center w-full gap-2" id="edit-cli-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Edit Operation</x-tittle-form>
            <x-input-1 type="hidden" id="cli_id" name="cli_id"></x-input-1>
            <x-wrapper-form-2>
                <x-label for="warehouse">Choice a warehouse</x-label>
                <x-select-1 name="warehouse" id="warehouse">
                    <option value="">Select a warehouse</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="location">Choice a location</x-label>
                <x-select-1 name="location_id" id="location_id">
                    <option value="">Select a warehouse</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="concept_id">Concept</x-label>
                    <x-select-1 name="concept_id" id="concept_id">
                        <option value="">Select a concept</option>
                        @foreach ($concepts as $concept)
                            <option value="{{ $concept->concept_id }}">{{ $concept->name }}</option>
                        @endforeach
                    </x-select-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label for="product_id">Product</x-label>
                    <x-input-1 name="product_id" id="product_id" list="products_id"></x-input-1>
                    <datalist id="products_id">
                        @foreach ($products_inventory as $product)
                            <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                        @endforeach
                    </datalist>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <p class="unit text-sm text-gray-500">Unit:</p>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <p class="product-name text-sm text-gray-500">Product:</p>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="batch">Warehouse batch</x-label>
                    <x-select-1 name="inventory_id" id="inventory_id">
                        <option value="">Select a product</option>
                    </x-select-1>
                    <p class="stock text-sm text-gray-500">Available:</p>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="quantity">Quantity</x-label>
                    <x-input-1 type="number" step="any" min="0" name="quantity" id="quantity"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label for="weight_per_unit">Weight per unit</x-label>
                    <x-input-1 type="number" step="any" min="0" name="weight_per_unit" id="weight_per_unit"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <p class="total"></p>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 id="close" class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="update-operation" colorBtn="blue">Update</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const locations = @json($locations);
    const products_inventory = @json($products_inventory);
    const inventory = @json($inventory);
    const father = $('#edit-cli');

    function reactiveEditOperation(){
        father.on('click','#close',function(){
            father.find('.total').removeClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(``);
            father.find('.stock').text('Available:');
            father.find('.product-name').text('Product:');
            father.find('.unit').text('Unit:');
        });

        father.on('input','#weight_per_unit',function(){
            let weight_unit = $(this).val();
            let quantity = father.find('#quantity').val();
            father.find('.total').addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(`Total: ${quantity*weight_unit}`);
        });

        father.on('input','#quantity',function(){
            let quantity = $(this).val();
            let weight_unit = father.find('#weight_per_unit').val();
            father.find('.total').addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(`Total: ${quantity*weight_unit}`);
        });
        
        father.on('change','#inventory_id',function(){
            const inventory_id = parseInt($(this).val());
            if(!isNaN(inventory_id)){
                father.find('.stock').text(`Available: ${inventory.find(i => i.inventory_id === inventory_id)?.stock_wh ?? 0.0}`);
            }else{
                father.find('.stock').text(`Available:`);
            }
        });

        father.on('input','#product_id',function(){
            let product_id = $(this).val();            
            if(product_id !== '' && father.find('#concept_id').val() !== ''){
                product_id = parseInt(product_id);
                father.find('.product-name').text(`Product: ${products_inventory.find(i => i.product_id === product_id)?.name ?? ''}`);
                father.find('.unit').text(`Unit: ${products_inventory.find(i => i.product_id === product_id)?.unit ?? ''}`);
                if(!isNaN(product_id)){
                    father.find('#inventory_id').empty();
                    father.find('#inventory_id').append(`<option value="">Select a batch</option>`);
                    father.find('.stock').text(`Available:`);
                    inventory.forEach(item => {
                        if(item.product_id === product_id){
                            father.find('#inventory_id').append(`<option value="${item.inventory_id}">${item.batch}</option>`);
                        }
                    });
                }
            }else{
                father.find('.stock').text('Available:');
                father.find('.unit').text('Unit:');
                father.find('.total').removeClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(``);
                father.find('.product-name').text('Product:');
                father.find('#inventory_id').html(`<option value="">Select a product</option>`);
                father.find('#inventory_id,#quantity,#weight_per_unit').val('');
            }
        });

        father.on('change','#warehouse',function(){
            const warehouse = parseInt($(this).val());
            father.find('#location_id').empty();
            father.find('#location_id').append('<option value="">Select a location</option>');
            locations.forEach(location => {
                if(location.warehouse_id === warehouse){
                    father.find('#location_id').append(`<option value="${location.location_id}">${location.name}</option>`);
                }
            });
        });
    }
    function updateCli(){
        $("#edit-cli-form").submit(function(event) {
            event.preventDefault();
            var form = $('#edit-cli-form')[0];
            var data = new FormData(form);
            var id = $('#cli_id').val();
            data.append('_method', 'PUT');
            $.ajax({
                type:'POST',
                url:`/cli/${id}`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Operation was updated successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updated the cli.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
    updateCli();
    reactiveEditOperation();
});
</script>
@endpush