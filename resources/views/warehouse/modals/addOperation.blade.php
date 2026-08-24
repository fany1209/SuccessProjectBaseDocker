{{--
Warehouse
Add Cli
Fecha de creación: 28-10-2025
Creado por: Jacob
Actualizado por: fany
Fecha de actualización: 09/03/2026
--}}
<x-modal id="add-cli">
    <form class="flex flex-col items-center w-full gap-2" id="add-cli-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Do Operation</x-tittle-form>
            <x-wrapper-form-2>
                <x-label for="warehouse">Choice a warehouse</x-label>
                <x-select-1 name="warehouse">
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
                <x-select-1 required name="location_id">
                    <option value="">Select a warehouse</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-blue-500 text-white font-semibold px-2 py-1 rounded-md">Products</span>
                <x-button-1 type="button" colorBtn="blue" id="add_product">Add product</x-button-1>
            </x-wrapper-form-1>
            
            <div id="products" class="w-full">
                <div class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
                    <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="concept_id">Concept</x-label>
                            <x-select-1 name="concept_id[]" class="concept_id">
                                <option value="">Select a concept</option>
                                @foreach ($concepts as $concept)
                                    <option value="{{ $concept->concept_id }}">{{ $concept->name }}</option>
                                @endforeach
                            </x-select-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label for="product_id">Product</x-label>
                            <x-input-1 readonly name="product_id[]" class="product_id bg-gray-100" list="products_id"></x-input-1>
                            <datalist id="products_id">
                                @foreach ($products_inventory as $product)
                                    <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                                @endforeach
                            </datalist>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1>
                        <x-wrapper-form-2><p class="unit text-sm text-gray-500">Unit:</p></x-wrapper-form-2>
                        <x-wrapper-form-2><p class="product-name text-sm text-gray-500">Product:</p></x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="batch">Warehouse batch</x-label>
                            <x-select-1 readonly name="inventory_id[]" class="inventory_id bg-gray-100">
                                <option value="">Select a product</option>
                            </x-select-1>
                            <p class="stock text-sm text-gray-500">Available:</p>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="quantity">Quantity</x-label>
                            <x-input-1 readonly type="number" step="any" min="0" name="quantity[]" class="quantity bg-gray-100"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label for="weight_per_unit">Weight per unit</x-label>
                            <x-input-1 readonly type="number" step="any" min="0" name="weight_per_unit[]" class="weight_per_unit bg-gray-100"></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2><p class="total"></p></x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
                        <x-label># de Barcina y Proteína para cada BigBag</x-label>
                        <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
                        </div>
                    </x-wrapper-form-1>
                </div>
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-button-1 id="clean-form" type="reset" colorBtn="yellow">Clean Form</x-button-1>
                <x-button-1 id="close" class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            </x-wrapper-form-1>
            <x-button-1 id="save-operation" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>

    <div id="product_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
    <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
    
    <x-wrapper-form-1>
        <x-wrapper-form-2>
            <x-label for="concept_id">Concept</x-label>
            <x-select-1 name="concept_id[]" class="concept_id">
                <option value="">Select a concept</option>
                @foreach ($concepts as $concept)
                    <option value="{{ $concept->concept_id }}">{{ $concept->name }}</option>
                @endforeach
            </x-select-1>
        </x-wrapper-form-2>
        <x-wrapper-form-2>
            <x-label for="product_id">Product</x-label>
            <x-input-1 readonly name="product_id[]" class="product_id bg-gray-100" list="products_id"></x-input-1>
        </x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
        <x-wrapper-form-2><p class="unit text-sm text-gray-500">Unit:</p></x-wrapper-form-2>
        <x-wrapper-form-2><p class="product-name text-sm text-gray-500">Product:</p></x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
        <x-wrapper-form-2>
            <x-label for="batch">Warehouse batch</x-label>
            <x-select-1 readonly name="inventory_id[]" class="inventory_id bg-gray-100">
                <option value="">Select a product</option>
            </x-select-1>
            <p class="stock text-sm text-gray-500">Available:</p>
        </x-wrapper-form-2>
    </x-wrapper-form-1>
    
    <x-wrapper-form-1>
        <x-wrapper-form-2>
            <x-label for="quantity">Quantity</x-label>
            <x-input-1 readonly type="number" step="any" min="0" name="quantity[]" class="quantity bg-gray-100"></x-input-1>
        </x-wrapper-form-2>
        <x-wrapper-form-2>
            <x-label for="weight_per_unit">Weight per unit</x-label>
            <x-input-1 readonly type="number" step="any" min="0" name="weight_per_unit[]" class="weight_per_unit bg-gray-100"></x-input-1>
        </x-wrapper-form-2>
    </x-wrapper-form-1>
    <x-wrapper-form-1>
        <x-wrapper-form-2><p class="total"></p></x-wrapper-form-2>
    </x-wrapper-form-1>
    <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
        <x-label># de Barcina y Proteína para cada BigBag</x-label>
        <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
        </div>
    </x-wrapper-form-1>
</div>
</x-modal>
@push('js')
<script>
$(function(){
    const locations = @json($locations);
    const products_inventory = @json($products_inventory);
    const inventory = @json($inventory);
    let row_index = 1;
    const father = $('#add-cli');

    function renderBagNumbers(wrapper) {
        let product_id = wrapper.find('.product_id').val();
        let quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
        let container = wrapper.find('.bag_numbers_container');
        let bag_wrapper = wrapper.find('.bag_numbers_wrapper');
        
        if (product_id !== '') {
            let product = products_inventory.find(i => i.product_id == product_id);
            // Check if product name includes BGBG
            if (product && product.name.includes('BGBG')) {
                bag_wrapper.removeClass('hidden');
                container.empty();
                // Number of inputs based on quantity
                let numInputs = Math.floor(quantity);
                for (let i = 0; i < numInputs; i++) {
                    container.append(`
                        <div class="flex flex-col gap-1 w-full bg-gray-50 p-2 rounded-lg border border-gray-200">
                            <input type="text" class="bag_number_input w-full rounded-lg border border-gray-300 text-gray-700 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2" placeholder="Barcina #${i+1}" required>
                            <input type="number" step="any" class="protein_input w-full rounded-lg border border-gray-300 text-gray-700 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2" placeholder="Proteína %" required>
                        </div>
                    `);
                }
            } else {
                bag_wrapper.addClass('hidden');
                container.empty();
            }
        } else {
            bag_wrapper.addClass('hidden');
            container.empty();
        }
    }

    function reactiveDoOperation(){
        father.on('click','#clean-form,#close',function(){
            row_index = 1;
            father.find('#products').empty();
            let template = null;
            template = father.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
            father.find('#products').append(template);
        });

        father.on('input','.weight_per_unit',function(){
            const wrapper = $(this).closest('.wrapper');
            let weight_unit = $(this).val();
            let quantity = wrapper.find('.quantity').val();
            wrapper.find('.total').addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(`Total: ${quantity*weight_unit}`);
        });

        father.on('input','.quantity',function(){
            const wrapper = $(this).closest('.wrapper');
            let quantity = $(this).val();
            let weight_unit = wrapper.find('.weight_per_unit').val();
            wrapper.find('.total').addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(`Total: ${quantity*weight_unit}`);
            renderBagNumbers(wrapper);
        });
        
        father.on('change','.inventory_id',function(){
            const wrapper = $(this).closest('.wrapper');
            const selected_id = $(this).val(); 
            
            if(selected_id){
                const item = inventory.find(i => i.inventory_id == selected_id);
                const availableStock = item ? item.stock_wh : 0;
                wrapper.find('.stock').text(`Available: ${availableStock}`);
            } else {
                wrapper.find('.stock').text(`Available:`);
            }
        });

        father.on('input','.product_id',function(){
            const wrapper = $(this).closest('.wrapper');
            let product_id = $(this).val();            
            if(product_id !== '' && wrapper.find('.concept_id').val() !== ''){
                product_id = parseInt(product_id);
                wrapper.find('.product-name').text(`Product: ${products_inventory.find(i => i.product_id == product_id)?.name ?? ''}`);
                wrapper.find('.unit').text(`Unit: ${products_inventory.find(i => i.product_id == product_id)?.unit ?? ''}`);
                wrapper.find('.inventory_id,.quantity,.weight_per_unit').prop('readonly',false);
                wrapper.find('.inventory_id,.quantity,.weight_per_unit').removeClass('bg-gray-100');
                if(!isNaN(product_id)){
                    wrapper.find('.inventory_id').empty();
                    wrapper.find('.inventory_id').append(`<option value="">Select a batch</option>`);
                    wrapper.find('.stock').text(`Available:`);
                    inventory.forEach(item => {
                        if(item.product_id == product_id){
                            wrapper.find('.inventory_id').append(`<option value="${item.inventory_id}">${item.batch}</option>`);
                        }
                    });
                }
                renderBagNumbers(wrapper);
            }else{
                wrapper.find('.stock').text('Available:');
                wrapper.find('.unit').text('Unit:');
                wrapper.find('.total').removeClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(``);
                wrapper.find('.product-name').text('Product:');
                wrapper.find('.inventory_id').html(`<option value="">Select a product</option>`);
                wrapper.find('.inventory_id,.quantity,.weight_per_unit').prop('readonly',true).addClass('bg-gray-100').val('');
                renderBagNumbers(wrapper);
            }
        });

        father.on('change','.concept_id',function(){
            const wrapper = $(this).closest('.wrapper');
            if($(this).val() !== ''){
                wrapper.find('.product_id').prop('readonly',false).removeClass('bg-gray-100').val('');
            }
            wrapper.find('.inventory_id,.quantity,.weight_per_unit').prop('readonly',true).addClass('bg-gray-100').val('');
            wrapper.find('.inventory_id').html(`<option value="">Select a product</option>`);
            wrapper.find('.total').removeClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(``);
            wrapper.find('.stock').text('Available:');
            wrapper.find('.product-name').text('Product:');
            wrapper.find('.unit').text('Unit:');
            renderBagNumbers(wrapper);
        });

        father.on('change','#warehouse',function(){
            const warehouse = parseInt($(this).val());
            father.find('#location_id').empty();
            father.find('#location_id').append('<option value="">Select a location</option>');
            locations.forEach(location => {
                if(location.warehouse_id == warehouse){
                    father.find('#location_id').append(`<option value="${location.location_id}">${location.name}</option>`);
                }
            });
        });

        father.on('click','.remove-product', function () {
            row_index = parseInt(father.find('#products .wrapper').length);
            if(row_index != 1){
                row_index -= 1;
                $(this).closest('.wrapper').remove();
            }
        });

        father.find('#add_product').on('click',function(){
            row_index += 1;
            let template = null;
            template = father.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
            father.find('#products').append(template);
        });
    }
    function doOperation(){
        father.find("#add-cli-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-cli-form')[0];
            var data = new FormData(form);
            data.delete('bag_number'); // Ensure no stray bag_numbers
            data.delete('protein');
            $('#products .wrapper').each(function(index) {
                $(this).find('.bag_number_input').each(function() {
                    data.append('bag_number[' + index + '][]', $(this).val());
                });
                $(this).find('.protein_input').each(function() {
                    data.append('protein[' + index + '][]', $(this).val());
                });
            });
            $('#save-operation').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/cli",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Operation was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-operation').prop('disabled',false);
                },
                error:function(xhr){
                    let message = 'Ocurrió un error inesperado.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        message = Object.values(errors).map(e => e.join('\n')).join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',	
                        text: message,
                        confirmButtonText: 'OK'
                    });
                    $('#save-operation').prop('disabled',false);
                }
            });
        });
    }
    doOperation();
    reactiveDoOperation();
});
</script>
@endpush