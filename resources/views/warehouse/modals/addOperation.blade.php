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
                    <x-wrapper-form-1 class="packing-list-wrapper hidden w-full">
                        <x-wrapper-form-2>
                            <x-label for="packing_list"><i class="ri-file-list-3-line mr-1 text-emerald-600"></i>Packing List</x-label>
                            <x-input-1 type="number" step="1" min="1" name="packing_list[]" class="packing_list" placeholder="Número de Packing List"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2 class="flex items-end pb-2">
                            <p class="text-xs text-gray-500 italic">Número de Packing List para identificar este lote de BigBags.</p>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
                        <x-label># de Barcina, Proteína y Peso para cada BigBag</x-label>
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
    <x-wrapper-form-1 class="packing-list-wrapper hidden w-full">
        <x-wrapper-form-2>
            <x-label for="packing_list"><i class="ri-file-list-3-line mr-1 text-emerald-600"></i>Packing List</x-label>
            <x-input-1 type="number" step="1" min="1" name="packing_list[]" class="packing_list" placeholder="Número de Packing List"></x-input-1>
        </x-wrapper-form-2>
        <x-wrapper-form-2 class="flex items-end pb-2">
            <p class="text-xs text-gray-500 italic">Número de Packing List para identificar este lote de BigBags.</p>
        </x-wrapper-form-2>
    </x-wrapper-form-1>
    <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
        <x-label># de Barcina, Proteína y Peso para cada BigBag</x-label>
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

    function recalculateBagWeights(wrapper) {
        let totalWeight = 0;
        let filledCount = 0;
        wrapper.find('.bag_weight_input').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val) && val > 0) {
                totalWeight += val;
                filledCount++;
            }
        });

        let quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
        if (filledCount > 0) {
            let formatted = totalWeight.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            wrapper.find('.total')
                .addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1')
                .text(`Total: ${formatted} kg`);

            if (quantity > 0) {
                let avg = (totalWeight / quantity).toFixed(3);
                wrapper.find('.weight_per_unit').val(avg);
            }
        } else {
            let weight_unit = parseFloat(wrapper.find('.weight_per_unit').val()) || 0;
            if (quantity > 0 && weight_unit > 0) {
                let formatted = (quantity * weight_unit).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                wrapper.find('.total')
                    .addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1')
                    .text(`Total: ${formatted} kg`);
            } else {
                wrapper.find('.total').text('Total: 0 kg');
            }
        }
    }

    function renderBagNumbers(wrapper) {
        try {
            let product_id = wrapper.find('.product_id').val();
            let quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
            let container = wrapper.find('.bag_numbers_container');
            let bag_wrapper = wrapper.find('.bag_numbers_wrapper');
            let packingWrapper = wrapper.find('.packing-list-wrapper');
            
            let idNum = parseInt(product_id);
            let product = null;
            if (!isNaN(idNum) && idNum > 0) {
                product = products_inventory.find(i => i.product_id == idNum);
            }
            if (!product && product_id) {
                let searchStr = String(product_id).trim().toLowerCase();
                product = products_inventory.find(i => i.name && i.name.toLowerCase() === searchStr);
            }

            let prodName = (product && product.name ? product.name : '') || wrapper.find('.product-name').text() || '';
            let prodUnit = (product && product.unit ? product.unit : '') || wrapper.find('.unit').text() || '';
            let isBigBag = prodName.toUpperCase().includes('BGBG') || 
                           prodName.toUpperCase().includes('BIGBAG') || 
                           prodUnit.toUpperCase().includes('BIGBAG');
            
            if (isBigBag) {
                bag_wrapper.show().css('display', 'flex').removeClass('hidden');
                packingWrapper.show().css('display', 'flex').removeClass('hidden');
                packingWrapper.find('.packing_list').prop('disabled', false);

                // Preserve existing user inputs
                let existingBags = [];
                container.find('.bag-card-row').each(function() {
                    existingBags.push({
                        bag_number: $(this).find('.bag_number_input').val() || '',
                        protein: $(this).find('.protein_input').val() || '',
                        weight: $(this).find('.bag_weight_input').val() || ''
                    });
                });

                container.empty();
                let numInputs = Math.floor(quantity);
                let defaultUnitWeight = wrapper.find('.weight_per_unit').val() || '';

                for (let i = 0; i < numInputs; i++) {
                    let saved = existingBags[i] || {};
                    let savedBagNum = saved.bag_number || '';
                    let savedProtein = saved.protein || '';
                    let savedWeight = saved.weight || defaultUnitWeight;

                    container.append(`
                        <div class="bag-card-row flex flex-col gap-1.5 w-full bg-white p-2.5 rounded-lg border border-gray-200 shadow-sm hover:border-emerald-300 transition-colors">
                            <div class="flex items-center justify-between text-xs font-bold text-emerald-800">
                                <span><i class="ri-inbox-line mr-1"></i>Barcina #${i+1}</span>
                            </div>
                            <input type="text" class="bag_number_input w-full rounded-md border border-gray-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 px-2.5 py-1.5" placeholder="Barcina #${i+1}" value="${savedBagNum}" required>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase">Proteína %</label>
                                    <input type="number" step="any" min="0" class="protein_input w-full rounded-md border border-gray-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 px-2 py-1" placeholder="Proteína %" value="${savedProtein}" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-emerald-700 uppercase">Peso Total (kg)</label>
                                    <input type="number" step="any" min="0" class="bag_weight_input w-full rounded-md border border-emerald-300 text-emerald-900 bg-emerald-50/40 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 px-2 py-1" placeholder="Peso (kg)" value="${savedWeight}" required>
                                </div>
                            </div>
                        </div>
                    `);
                }
                recalculateBagWeights(wrapper);
            } else {
                bag_wrapper.hide().css('display', 'none').addClass('hidden');
                container.empty();
                packingWrapper.hide().css('display', 'none').addClass('hidden');
                packingWrapper.find('.packing_list').prop('disabled', true);
            }
        } catch (err) {
            console.error('Error in renderBagNumbers:', err);
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
            let weight_unit = parseFloat($(this).val()) || 0;
            let $bagWeights = wrapper.find('.bag_weight_input');
            if ($bagWeights.length > 0 && weight_unit > 0) {
                $bagWeights.each(function() {
                    if (!$(this).val() || $(this).data('auto-filled')) {
                        $(this).val(weight_unit).data('auto-filled', true);
                    }
                });
            }
            recalculateBagWeights(wrapper);
        });

        father.on('change', '.bag_weight_input', function(){
            $(this).removeData('auto-filled');
        });

        father.on('input', '.bag_weight_input', function(){
            const wrapper = $(this).closest('.wrapper');
            recalculateBagWeights(wrapper);
        });

        father.on('input','.quantity',function(){
            const wrapper = $(this).closest('.wrapper');
            renderBagNumbers(wrapper);
            recalculateBagWeights(wrapper);
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
            data.delete('bag_weight');
            data.delete('packing_list');
            $('#products .wrapper').each(function(index) {
                let plVal = $(this).find('.packing_list').val() || '';
                data.append('packing_list[' + index + ']', plVal);

                $(this).find('.bag_number_input').each(function() {
                    data.append('bag_number[' + index + '][]', $(this).val());
                });
                $(this).find('.protein_input').each(function() {
                    data.append('protein[' + index + '][]', $(this).val());
                });
                $(this).find('.bag_weight_input').each(function() {
                    data.append('bag_weight[' + index + '][]', $(this).val());
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