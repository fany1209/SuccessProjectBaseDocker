{{--
Inventario
Make Transaction
Fecha de creación: 08-10-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 15-01-2026
--}}
<x-modal id="make-transaction">
    <form class="flex flex-col items-center w-full gap-2" id="make-transaction-form">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Make Transaction</x-tittle-form>
            @hasrole('Admin')
            <x-toggle-switch id="type" name="type" value="Input" :checked="true"
                onLabel="Input" offLabel="Output"
                onColor="emerald-500" offColor="red-700"
                textColor="white" class=""/>
            @else
            <div class="flex items-center gap-2">
                <span class="bg-emerald-500 text-white font-semibold px-4 py-1 rounded shadow-sm">Input</span>
                <input type="checkbox" id="type" name="type" value="Input" checked class="hidden" />
            </div>
            @endhasrole
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2 id="supplier-wrapper" class="">
                <x-label for="supplier">Supplier</x-label>
                <x-input-1 required name="supplier" id="supplier" list="suppliers"></x-input-1>
                <datalist id="suppliers">
                    @foreach ($suppliers as $supplier)
                        <option value='{{ $supplier->supplier_id }}'>{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <p id="supplier-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>

            <x-wrapper-form-2 id="customer-wrapper" class="hidden">
                <x-label for="customer">Customer</x-label>
                <x-input-1 disabled required name="customer" id="customer" list="customers"></x-input-1>
                <datalist id="customers">
                    @foreach ($customers as $customer)
                        <option value='{{ $customer->customer_id }}'>{{ $customer->name }}</option>
                    @endforeach
                </datalist>
                <p id="customer-name" class="text-sm text-gray-500"></p>
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
                            <x-input-1 name="product_id[]" list="products_all" class="product_id">Product</x-input-1>
                            <datalist id="products_all">
                                @foreach ($products_all as $product)
                                    <option value='{{ $product->product_id }}'>{{ $product->name }}</option>
                                @endforeach
                            </datalist>
                            <p class="product-name text-sm text-gray-500"></p>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="quantity">Quantity</x-label>
                            <x-input-1 type="number" step="any" min="0" name="quantity[]" class="quantity"></x-input-1>
                            <p class="unit text-sm text-gray-500"></p>
                        </x-wrapper-form-2>

                        <x-wrapper-form-2>
                            <x-label for="weight_per_unit">Weight per unit</x-label>
                            <x-input-1 type="number" step="any" min="0" name="weight_per_unit[]" class="weight_per_unit"></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1 class="location-assignment-wrapper">
                        <x-wrapper-form-2>
                            <x-label for="warehouse">Warehouse (Optional)</x-label>
                            <x-select-1 name="warehouse[]" class="warehouse">
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </x-select-1>
                        </x-wrapper-form-2>

                        <x-wrapper-form-2 class="location-id-wrapper">
                            <x-label for="location">Location</x-label>
                            <x-select-1 name="location_id[]" class="location_id">
                                <option value="">Select a location</option>
                            </x-select-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="warehouse_batch">Warehouse batch</x-label>
                            <x-input-1 name="warehouse_batch[]" class="warehouse_batch"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2><p class="total"></p></x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
                        <x-label># de Barcina para cada BigBag</x-label>
                        <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
                        </div>
                    </x-wrapper-form-1>
                </div>
            </div>
        </x-wrapper-form-1>


        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="transport_line">Transport Line</x-label>
                <x-input-1 name="transport_line" id="transport_line" list="transport_lines"></x-input-1>
                <datalist id="transport_lines">
                    @foreach ($transport_lines as $transport_line)
                        <option value='{{ $transport_line->transport_line_id }}'>{{ $transport_line->name }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="operator">Operator</x-label>
                <x-input-1 name="operator" id="operator" list="operators"></x-input-1>
                <datalist id="operators">
                    @foreach ($operators as $operator)
                        <option value="{{ $operator->name }}">{{ $operator->license }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="license_number">License number</x-label>
                <x-input-1 name="license_number" id="license_number" maxlength="50"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2 id="seller-wrapper" class="hidden">
                <x-label for="vendedor">Vendedor</x-label>
                <x-input-1 disabled name="vendedor" id="vendedor" list="sellers" maxlength="200"></x-input-1>
                <datalist id="sellers">
                    <option value="Nery Medina"></option>
                    <option value="William"></option>
                    <option value="Flor Gutierrez"></option>
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-toggle-decision id="security-seal" label="Security Seal?" buttonText="No"
                class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"></x-toggle-decision>

            <x-input-1 type="hidden" value="0" name="security_seal" id="security_seal"></x-input-1>

            <x-wrapper-form-2 id="security-seal-number-wrapper" class="hidden">
                <x-label for="security_seal_number" id="security_seal_number-tag" class="hidden">Security Seal Number</x-label>
                <x-input-1 disabled name="security_seal_number" id="security_seal_number" class="hidden"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="unit_plates">Unit plates</x-label>
                <x-input-1 name="unit_plates" id="unit_plates" list="vehicles"></x-input-1>
                <datalist id="vehicles"></datalist>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="trailer_plates">Trailer plates</x-label>
                <x-input-1 name="trailer_plates" id="trailer_plates" list="trailers"></x-input-1>
                <datalist id="trailers"></datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>



        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="comments">Comments</x-label>
                <x-textarea-1 name="comments" id="comments"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-button-1 id="clean-form" type="reset" colorBtn="yellow">Clean Form</x-button-1>
                <x-button-1 id="close" class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            </x-wrapper-form-1>
            <x-button-1 id="save-transaction" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>

    <div id="product_input_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
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
                <x-input-1 name="product_id[]" list="products_all" class="product_id">Product</x-input-1>
                <p class="product-name text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 type="number" step="any" min="0" name="quantity[]" class="quantity"></x-input-1>
                <p class="unit text-sm text-gray-500"></p>
            </x-wrapper-form-2>
            
            <x-wrapper-form-2>
                <x-label for="weight_per_unit">Weight per unit</x-label>
                <x-input-1 type="number" step="any" min="0" name="weight_per_unit[]" class="weight_per_unit"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="location-assignment-wrapper">
            <x-wrapper-form-2>
                <x-label for="warehouse">Warehouse (Optional)</x-label>
                <x-select-1 name="warehouse[]" class="warehouse" disabled>
                    <option value="">Select a warehouse</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2 class="location-id-wrapper">
                <x-label for="location">Location</x-label>
                <x-select-1 name="location_id[]" class="location_id" disabled>
                    <option value="">Select a location</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="warehouse_batch">Warehouse batch</x-label>
                <x-input-1 name="warehouse_batch[]" class="warehouse_batch" readonly></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2><p class="total"></p></x-wrapper-form-2>
        </x-wrapper-form-1>
        
        <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
            <x-label># de Barcina para cada BigBag</x-label>
            <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
            </div>
        </x-wrapper-form-1>
    </div>

    <div id="product_output_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
        <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_id">Product</x-label>
                <x-input-1 name="product_id[]" list="products_all" class="product_id">Product</x-input-1>
                <p class="product-name text-sm text-gray-500"></p>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 name="quantity[]" class="quantity"></x-input-1>
                <p class="unit text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="warehouse_batch">Warehouse batch</x-label>
                <x-select-1 name="warehouse_batch[]" class="warehouse_batch">
                    <option value="">Select a batch</option>
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="label_batch">Label batch</x-label>
                <x-input-1 name="label_batch[]" class="label_batch"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        
        <x-wrapper-form-1 class="bag_numbers_wrapper hidden w-full flex-col mt-2">
            <x-label># de Barcina para cada BigBag</x-label>
            <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-1><p class="stock text-sm text-gray-500"></p></x-wrapper-form-1>
    </div>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#make-transaction');
    const batchs = @json($batchs);
    const suppliers = @json($suppliers);
    const customers = @json($customers);
    const operators = @json($operators);
    const vehicles = @json($vehicles);
    const trailers = @json($trailers);
    const warehouses = @json($warehouses ?? []);
    const locations = @json($locations ?? []);
    const product_locations = @json($product_locations ?? []);
    const products_inventory = @json($products_all ?? []); // Using products_all as products list
    let row_index = 1;

    function getMovement($modal){
        const cb = $modal.find('input[type="checkbox"]#type').first();
        return (cb.length && cb.prop('checked')) ? 'Input' : 'Output';
    }

    function setMovement($modal, movement){
        const cb = $modal.find('input[type="checkbox"]#type').first();
        const hd = $modal.find('input[type="hidden"][name="type"]').first();

        if(cb.length) cb.prop('checked', movement === 'Input');
        if(hd.length) hd.val(movement);
    }

    function applyMovement($modal, movement){
        const isInput = (movement === 'Input');
        const $supplierWrapper = $modal.find('#supplier-wrapper-wrapper');
        const $customerWrapper = $modal.find('#customer-wrapper-wrapper');
        const $sellerWrapper   = $modal.find('#seller-wrapper-wrapper');
        const $locationWrapper = $modal.find('#location-assignment-wrapper');

        if(isInput){

            $supplierWrapper.removeClass('hidden').css('display','block');
            $modal.find('.warehouse').prop('disabled', false);
            $modal.find('.location_id').prop('disabled', false);
            $customerWrapper.addClass('hidden').css('display','none');
            $modal.find('#supplier').prop('disabled', false).removeAttr('disabled').prop('required', true);
            $modal.find('#customer').prop('disabled', true).attr('disabled','disabled').prop('required', false);
            $modal.find('#customer').val('');
            $modal.find('#customer-name').text('');
            $sellerWrapper.addClass('hidden').css('display','none');
            $modal.find('#vendedor').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');
            let template = $modal.find('#product_input_template').clone().removeAttr('id').removeClass('hidden').show();
            $modal.find('#products').empty().append(template);

        }else{

            $customerWrapper.removeClass('hidden').css('display','block');
            $supplierWrapper.addClass('hidden').css('display','none');
            $modal.find('.warehouse').prop('disabled', true);
            $modal.find('.location_id').prop('disabled', true);
            $modal.find('#customer').prop('disabled', false).removeAttr('disabled').prop('required', true);
            $modal.find('#supplier').prop('disabled', true).attr('disabled','disabled').prop('required', false);
            $modal.find('#supplier').val('');
            $modal.find('#supplier-name').text('');
            $sellerWrapper.removeClass('hidden').css('display','block');
            $modal.find('#vendedor').prop('disabled', false).removeAttr('disabled').prop('required', true);
            let template = $modal.find('#product_output_template').clone().removeAttr('id').removeClass('hidden').show();
            $modal.find('#products').empty().append(template);
        }

        row_index = 1;
    }

    function initTypeUI(){
        const movement = getMovement(father);
        setMovement(father, movement);
        applyMovement(father, movement);
    }

    $(document).off('change.makeTxType', '#make-transaction input[type="checkbox"]#type')
      .on('change.makeTxType', '#make-transaction input[type="checkbox"]#type', function(){
          const $modal = $(this).closest('#make-transaction');
          const movement = this.checked ? 'Input' : 'Output';

          setMovement($modal, movement);
          applyMovement($modal, movement);
      });

    initTypeUI();

    function renderBagNumbers(wrapper) {
        let product_id = wrapper.find('.product_id').val();
        let quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
        let container = wrapper.find('.bag_numbers_container');
        let bag_wrapper = wrapper.find('.bag_numbers_wrapper');
        let locIdWrapper = wrapper.find('.location-id-wrapper');
        
        if (product_id !== '') {
            let idNum = parseInt(product_id);
            let product = products_inventory.find(i => i.product_id == idNum);
            if (product && product.name.includes('BGBG')) {
                bag_wrapper.removeClass('hidden');
                container.empty();
                let numInputs = Math.floor(quantity);
                let movement = getMovement(wrapper.closest('#make-transaction'));
                
                if (movement === 'Input') {
                    locIdWrapper.addClass('hidden').css('display', 'none');
                } else {
                    locIdWrapper.removeClass('hidden').css('display', 'block');
                }
                
                let availableBags = [];
                if (movement === 'Output') {
                    let inventory_id = wrapper.find('.warehouse_batch').val();
                    let selectedLocId = wrapper.find('.warehouse_batch').find('option:selected').data('location-id');
                    if (inventory_id) {
                        availableBags = product_locations.filter(pl => pl.inventory_id == inventory_id && pl.bag_number && (!selectedLocId || pl.location_id == selectedLocId)).map(pl => pl.bag_number);
                    }
                }

                for (let i = 0; i < numInputs; i++) {
                    if (movement === 'Output' && availableBags.length > 0) {
                        let options = '<option value="">Select a Barcina</option>';
                        // filter out duplicates just in case
                        [...new Set(availableBags)].forEach(bag => {
                            options += `<option value="${bag}">${bag}</option>`;
                        });
                        container.append(`
                            <select class="bag_number_input w-full rounded-lg border border-gray-300 text-gray-700 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2" required>
                                ${options}
                            </select>
                        `);
                    } else {
                        let locOptions = wrapper.find('.location_id').html();
                        let locSelectHTML = movement === 'Input' 
                            ? `<select class="bag_location_input w-full rounded-lg border border-gray-300 text-gray-700 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2" required>${locOptions}</select>`
                            : '';
                        container.append(`
                            <div class="flex flex-col gap-1 w-full bg-gray-50 p-2 rounded-lg border border-gray-200">
                                <input type="text" class="bag_number_input w-full rounded-lg border border-gray-300 text-gray-700 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2" placeholder="Barcina #${i+1}" required>
                                ${locSelectHTML}
                            </div>
                        `);
                    }
                }
            } else {
                locIdWrapper.removeClass('hidden').css('display', 'block');
                bag_wrapper.addClass('hidden');
                container.empty();
            }
        } else {
            locIdWrapper.removeClass('hidden').css('display', 'block');
            bag_wrapper.addClass('hidden');
            container.empty();
        }
    }

    father.on('change', '.warehouse', function(){
        const $wrapper = $(this).closest('.wrapper');
        const $locSelect = $wrapper.find('.location_id');
        const warehouse = parseInt($(this).val());
        
        $locSelect.empty().append('<option value="">Select a location</option>');
        let optionsHtml = '<option value="">Select a location</option>';
        locations.forEach(location => {
            if(location.warehouse_id == warehouse){
                $locSelect.append(`<option value="${location.location_id}">${location.name}</option>`);
                optionsHtml += `<option value="${location.location_id}">${location.name}</option>`;
            }
        });

        const $bagLocSelects = $wrapper.find('.bag_location_input');
        if ($bagLocSelects.length > 0) {
            $bagLocSelects.empty().append(optionsHtml);
        }
    });

    father.on('change', '.location_id, .bag_location_input', function(){
        const wrapper = $(this).closest('.wrapper');
        let hasLocation = wrapper.find('.location_id').val() !== '';
        wrapper.find('.bag_location_input').each(function() {
            if ($(this).val() !== '') hasLocation = true;
        });
        wrapper.find('.concept_id').prop('required', hasLocation);
        wrapper.find('.weight_per_unit').prop('required', hasLocation);
    });

    father.on('click','#add_product',function(){
        setTimeout(() => {
            father.find('#products .wrapper').each(function() {
                let hasLocation = $(this).find('.location_id').val() !== '';
                $(this).find('.bag_location_input').each(function() {
                    if ($(this).val() !== '') hasLocation = true;
                });
                $(this).find('.concept_id').prop('required', hasLocation);
                $(this).find('.weight_per_unit').prop('required', hasLocation);
            });
        }, 50);
    });

    father.on('input','.weight_per_unit, .quantity',function(){
        const wrapper = $(this).closest('.wrapper');
        let weight_unit = wrapper.find('.weight_per_unit').val() || 0;
        let quantity = wrapper.find('.quantity').val() || 0;
        if(getMovement(father) === 'Input') {
            wrapper.find('.total').addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1').text(`Total: ${quantity*weight_unit}`);
        }
        renderBagNumbers(wrapper);
    });

    // REACTIVE
    father.on('input','#operator',function(){
        let operator = $(this).val();
        const item = operators.find(x => x.name == operator);
        if(item){
            father.find('#license_number').val(item.license);
        }
    });

    father.on('input','#transport_line',function(){
        let transport_line = $(this).val();
        father.find('#vehicles').empty();
        vehicles.forEach(vehicle => {
            if(vehicle.transport_line_id == transport_line){
                father.find('#vehicles').append(`<option value="${vehicle.plate}">${vehicle.type}</option>`);
            }
        });
        father.find('#trailers').empty();
        trailers.forEach(trailer => {
            if(trailer.transport_line_id == transport_line){
                father.find('#trailers').append(`<option value="${trailer.plate}">${trailer.type}</option>`);
            }
        });
    });

    father.on('input','#customer',function(){
        let id = $(this).val();
        if (/^\d+$/.test(id)) {
            id = parseInt(id);
            const c = customers.find(x => x.customer_id === id);
            father.find('#customer-name').text(c ? c.name : '');
        } else {
            father.find('#customer-name').text('');
        }
    });

    father.on('input','#supplier',function(){
        let id = $(this).val();
        if (/^\d+$/.test(id)) {
            id = parseInt(id);
            const s = suppliers.find(x => x.supplier_id === id);
            father.find('#supplier-name').text(s ? s.name : '');
        } else {
            father.find('#supplier-name').text('');
        }
    });

    father.on('change','.warehouse_batch',function(){
        const wrapper = $(this).closest('.wrapper');
        let inventory_id = $(this).val();
        let location_id = $(this).find('option:selected').data('location-id');
        const batch = batchs.find(x => x.inventory_id == inventory_id);
        wrapper.find('.stock').text((inventory_id && batch) ? `Available: ${batch.stock}` : `Available: 0`);
        
        let movement = getMovement(father);
        if (movement === 'Output' && location_id) {
            let locationObj = locations.find(l => l.location_id == location_id);
            if (locationObj) {
                wrapper.find('.warehouse').val(locationObj.warehouse_id).trigger('change');
                setTimeout(() => {
                    wrapper.find('.location_id').val(locationObj.location_id).trigger('change');
                }, 100);
            }
        }
        renderBagNumbers(wrapper);
    });

    father.on('change','.product_id',function(){
        let movement = getMovement(father);

        const wrapper = $(this).closest('.wrapper');
        let id = $(this).val();

        renderBagNumbers(wrapper);

        let date = new Date();
        let day = String(date.getDate()).padStart(2, '0');
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let year = String(date.getFullYear()).slice(-2);
        let code = day + month + year;

        const batch = batchs.find(x => x.product_id == id);

        if (/^\d+$/.test(id)) {
            id = parseInt(id);
            $.ajax({
                type: 'GET',
                url: `/catalogs/${id}`,
                success: function(response){
                    wrapper.find('.product-name').text(response.product.name);
                    wrapper.find('.unit').text(`Unit: ${response.product.unit}`);

                    if(movement === 'Output'){
                        if(batch){
                            wrapper.find('.label_batch').val(`${batch.batch_code}${code}`);
                        }
                        wrapper.find('.warehouse_batch').html('<option value="">Select a batch / location</option>');
                        batchs.forEach(b => {
                            if(b.product_id == id && parseFloat(b.stock) > 0){
                                let locs = product_locations.filter(pl => pl.inventory_id == b.inventory_id);
                                if (locs.length > 0) {
                                    let locGroups = {};
                                    locs.forEach(pl => {
                                        if(!locGroups[pl.location_id]) locGroups[pl.location_id] = 0;
                                        locGroups[pl.location_id]++;
                                    });
                                    for(let loc_id in locGroups) {
                                        let count = locGroups[loc_id];
                                        let locationObj = locations.find(l => l.location_id == loc_id);
                                        let locName = locationObj ? locationObj.name : 'Unknown';
                                        let whObj = locationObj ? warehouses.find(w => w.warehouse_id == locationObj.warehouse_id) : null;
                                        let whName = whObj ? whObj.name : '';
                                        wrapper.find('.warehouse_batch').append(`<option value="${b.inventory_id}" data-location-id="${loc_id}">${b.batch} - ${whName} ${locName} (${count} bags)</option>`);
                                    }
                                } else {
                                    wrapper.find('.warehouse_batch').append(`<option value="${b.inventory_id}">${b.batch} - (No location mapped)</option>`);
                                }
                            }
                        });
                    }else{
                        wrapper.find('.warehouse_batch').val(`${response.product.batch_code}${code}`);
                    }
                },
                error: function(){
                    wrapper.find('.product-name').text('');
                    wrapper.find('.unit').text('');
                    wrapper.find('.stock').text('');
                    wrapper.find('.warehouse_batch').val('');
                    wrapper.find('.label_batch').val('');
                }
            });
        } else {
            wrapper.find('.product-name').text('');
            wrapper.find('.unit').text('');
            wrapper.find('.stock').text('');
            wrapper.find('.warehouse_batch').val('');
            wrapper.find('.label_batch').val('');
        }
    });

    father.find('#clean-form, #close').on('click',function(){
        row_index = 1;

        father.find('#security-seal-btn').text('No')
            .removeClass('border-green-400 bg-green-400 text-white')
            .addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');

        father.find('#security_seal_number-tag, #security_seal_number, #security-seal-number-wrapper').addClass('hidden');
        father.find('#security_seal_number').prop('disabled',true);
        father.find('#security_seal').val('0');

        setMovement(father, 'Input');
        applyMovement(father, 'Input');

        father.find('#vendedor').val('').prop('disabled', true);
        father.find('#seller-wrapper-wrapper').addClass('hidden').css('display','none');
    });

    father.on('click','.remove-product', function () {
        if(row_index != 1){
            row_index -= 1;
            $(this).closest('.wrapper').remove();
        }
    });

    father.find('#add_product').on('click',function(){
        row_index += 1;
        let movement = getMovement(father);

        let template = (movement === 'Output')
            ? father.find('#product_output_template').clone().removeAttr('id').removeClass('hidden').show()
            : father.find('#product_input_template').clone().removeAttr('id').removeClass('hidden').show();

        father.find('#products').append(template);
    });

    // SUBMIT
    father.find("#make-transaction-form").submit(function(event) {
        event.preventDefault();
        var form = father.find('#make-transaction-form')[0];
        var data = new FormData(form);
        
        let movement = getMovement(father);
        data.delete('bag_number'); 
        data.delete('bag_location_id'); 
        $('#make-transaction #products .wrapper').each(function(index) {
            $(this).find('.bag_number_input').each(function() {
                data.append('bag_number[' + index + '][]', $(this).val());
            });
            $(this).find('.bag_location_input').each(function() {
                data.append('bag_location_id[' + index + '][]', $(this).val());
            });
            
            if (movement === 'Output') {
                let loc_id = $(this).find('.warehouse_batch').find('option:selected').data('location-id');
                if (loc_id) {
                    data.append('output_location_id[' + index + ']', loc_id);
                }
            }
        });

        father.find('#save-transaction').prop('disabled',true);

        $.ajax({
            type:'POST',
            url:"{{ route('inventory.makeTransaction') }}",
            data:data,
            processData:false,
            contentType:false,
            success: function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'The transaction was added successfully.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) location.reload();
                });
                father.find('#save-transaction').prop('disabled',false);
            },
            error:function(xhr){
                let message = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    if (typeof xhr.responseJSON.error === 'object') {
                        message = Object.values(xhr.responseJSON.error).flat().join('\\n');
                    } else {
                        message = xhr.responseJSON.error;
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
                father.find('#save-transaction').prop('disabled',false);
            }
        });
    });

    function setSecuritySealUI($modal, on){
        const $btn = $modal.find('#security-seal-btn');
        const $hidden = $modal.find('#security_seal');
        const $wrap = $modal.find('#security-seal-number-wrapper, #security-seal-number-wrapper-wrapper');
        const $tag  = $modal.find('#security_seal_number-tag');
        const $inp  = $modal.find('#security_seal_number');

        $hidden.val(on ? '1' : '0');

        if(on){
        
            $btn.text('Yes')
                .removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white')
                .addClass('border-green-400 bg-green-400 text-white');
            $wrap.removeClass('hidden').css('display','block');
            $tag.removeClass('hidden');
            $inp.removeClass('hidden').prop('disabled', false).removeAttr('disabled');
        }else{
            $btn.text('No')
                .removeClass('border-green-400 bg-green-400 text-white')
                .addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');

            $inp.val('').prop('disabled', true).attr('disabled','disabled').addClass('hidden');
            $tag.addClass('hidden');
            $wrap.addClass('hidden').css('display','none');
        }
    }

    function getSecuritySeal($modal){
        return String($modal.find('#security_seal').val() || '0') === '1';
    }

    setSecuritySealUI(father, getSecuritySeal(father));
    $(document).off('click.makeTxSeal', '#make-transaction #security-seal-btn')
      .on('click.makeTxSeal', '#make-transaction #security-seal-btn', function(e){
          e.preventDefault();
          const $modal = $(this).closest('#make-transaction');
          setSecuritySealUI($modal, !getSecuritySeal($modal));
      });

});
</script>
@endpush
