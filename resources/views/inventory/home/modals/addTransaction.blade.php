<x-modal id="make-transaction">
    <form class="flex flex-col items-center w-full gap-2" id="make-transaction-form">
        <datalist id="add_tx_products_all">
            @foreach ($products_all as $product)
                <option value='{{ $product->product_id }}'>{{ $product->name }}</option>
            @endforeach
        </datalist>
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Make Transaction</x-tittle-form>
            @hasanyrole('Admin|Warehouse')
            <select id="type" name="type" class="w-full rounded-lg border border-gray-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 px-3 py-2 font-semibold">
                <option value="Input" selected>Input (Entrada)</option>
                <option value="Output">Output (Salida Venta)</option>
                <option value="InternalOutput">Internal Output (Salida Producción)</option>
            </select>
            @else
            <div class="flex items-center gap-2">
                <span class="bg-emerald-500 text-white font-semibold px-4 py-1 rounded shadow-sm">Input</span>
                <input type="hidden" id="type" name="type" value="Input" />
            </div>
            @endhasanyrole
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2 id="supplier-wrapper" class="">
                <x-label for="supplier">Supplier</x-label>
                <x-input-1 required name="supplier" id="supplier" list="add_tx_suppliers"></x-input-1>
                <datalist id="add_tx_suppliers">
                    @foreach ($suppliers as $supplier)
                        <option value='{{ $supplier->supplier_id }}'>{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <p id="supplier-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>

            <x-wrapper-form-2 id="customer-wrapper" class="hidden">
                <x-label for="customer">Customer</x-label>
                <x-input-1 disabled required name="customer" id="customer" list="add_tx_customers"></x-input-1>
                <datalist id="add_tx_customers">
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
                            <x-input-1 name="product_id[]" list="add_tx_products_all" class="product_id">Product</x-input-1>
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
                                @foreach ($warehouses as $warehouse)
                                    @php
                                        $whLocations = $locations->where('warehouse_id', $warehouse->warehouse_id);
                                    @endphp
                                    @if ($whLocations->count() > 0)
                                        <optgroup label="{{ $warehouse->name }}">
                                            @foreach ($whLocations as $loc)
                                                <option value="{{ $loc->location_id }}">{{ $loc->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
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

                    <x-wrapper-form-1 class="packing-list-wrapper w-full" style="display: none;">
                        <x-wrapper-form-2>
                            <x-label for="packing_list"><i class="ri-file-list-3-line mr-1 text-emerald-600"></i>Packing List</x-label>
                            <x-input-1 type="number" step="1" min="1" name="packing_list[]" class="packing_list" placeholder="Número de Packing List"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2 class="flex items-end pb-2">
                            <p class="text-xs text-gray-500 italic">Número de Packing List para identificar este lote de BigBags.</p>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1 class="bag_numbers_wrapper w-full flex-col mt-2" style="display: none;">
                        <x-label class="text-emerald-800 font-bold"># de Barcina, Proteína y Peso para cada BigBag</x-label>
                        <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
                        </div>
                    </x-wrapper-form-1>
                </div>
            </div>
        </x-wrapper-form-1>


        <x-wrapper-form-1 class="transport-info-wrapper">
            <x-wrapper-form-2>
                <x-label for="transport_line">Transport Line</x-label>
                <x-input-1 name="transport_line" id="transport_line" list="add_tx_transport_lines"></x-input-1>
                <datalist id="add_tx_transport_lines">
                    @foreach ($transport_lines as $transport_line)
                        <option value='{{ $transport_line->transport_line_id }}'>{{ $transport_line->name }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="transport-info-wrapper">
            <x-wrapper-form-2>
                <x-label for="operator">Operator</x-label>
                <x-input-1 name="operator" id="operator" list="add_tx_operators"></x-input-1>
                <datalist id="add_tx_operators">
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
                <x-input-1 disabled name="vendedor" id="vendedor" list="add_tx_sellers" maxlength="200"></x-input-1>
                <datalist id="add_tx_sellers">
                    <option value="Nery Medina"></option>
                    <option value="William"></option>
                    <option value="Flor Gutierrez"></option>
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="transport-info-wrapper">
            <x-toggle-decision id="security-seal" label="Security Seal?" buttonText="No"
                class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"></x-toggle-decision>

            <x-input-1 type="hidden" value="0" name="security_seal" id="security_seal"></x-input-1>

            <x-wrapper-form-2 id="security-seal-number-wrapper" class="hidden">
                <x-label for="security_seal_number" id="security_seal_number-tag" class="hidden">Security Seal Number</x-label>
                <x-input-1 disabled name="security_seal_number" id="security_seal_number" class="hidden"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="transport-info-wrapper">
            <x-wrapper-form-2>
                <x-label for="unit_plates">Unit plates</x-label>
                <x-input-1 name="unit_plates" id="unit_plates" list="add_tx_vehicles"></x-input-1>
                <datalist id="add_tx_vehicles"></datalist>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="trailer_plates">Trailer plates</x-label>
                <x-input-1 name="trailer_plates" id="trailer_plates" list="add_tx_trailers"></x-input-1>
                <datalist id="add_tx_trailers"></datalist>
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
                <x-input-1 name="product_id[]" list="add_tx_products_all" class="product_id">Product</x-input-1>
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
                    @foreach ($warehouses as $warehouse)
                        @php
                            $whLocations = $locations->where('warehouse_id', $warehouse->warehouse_id);
                        @endphp
                        @if ($whLocations->count() > 0)
                            <optgroup label="{{ $warehouse->name }}">
                                @foreach ($whLocations as $loc)
                                    <option value="{{ $loc->location_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
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

        <x-wrapper-form-1 class="packing-list-wrapper w-full" style="display: none;">
            <x-wrapper-form-2>
                <x-label for="packing_list"><i class="ri-file-list-3-line mr-1 text-emerald-600"></i>Packing List</x-label>
                <x-input-1 type="number" step="1" min="1" name="packing_list[]" class="packing_list" placeholder="Número de Packing List"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2 class="flex items-end pb-2">
                <p class="text-xs text-gray-500 italic">Número de Packing List para identificar este lote de BigBags.</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        
        <x-wrapper-form-1 class="bag_numbers_wrapper w-full flex-col mt-2" style="display: none;">
            <x-label class="text-emerald-800 font-bold"># de Barcina, Proteína y Peso para cada BigBag</x-label>
            <div class="bag_numbers_container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mt-1 w-full">
            </div>
        </x-wrapper-form-1>
    </div>

    <div id="product_output_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
        <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_id">Product</x-label>
                <x-input-1 name="product_id[]" list="add_tx_products_all" class="product_id">Product</x-input-1>
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
        
        <x-wrapper-form-1 class="bag_numbers_wrapper w-full flex-col mt-2" style="display: none;">
            <x-label class="text-emerald-800 font-bold"># de Barcina, Proteína y Peso para cada BigBag</x-label>
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

    function buildLocationOptions(selectedWarehouseId = null, selectedLocationId = null) {
        try {
            let optionsHtml = '<option value="">Select a location</option>';
            const whId = (selectedWarehouseId !== null && selectedWarehouseId !== '' && !isNaN(parseInt(selectedWarehouseId))) 
                ? parseInt(selectedWarehouseId) 
                : null;

            const locList = Array.isArray(locations) ? locations : Object.values(locations || {});
            const whList = Array.isArray(warehouses) ? warehouses : Object.values(warehouses || {});

            if (whId) {
                const whObj = whList.find(w => w && w.warehouse_id == whId);
                const whName = whObj ? whObj.name : `Almacén #${whId}`;
                locList.forEach(location => {
                    if (location && location.warehouse_id == whId) {
                        const isSelected = (selectedLocationId && selectedLocationId == location.location_id) ? 'selected' : '';
                        const displayName = location.name && location.name.toLowerCase().includes(whName.toLowerCase())
                            ? location.name 
                            : `${whName} - ${location.name}`;
                        optionsHtml += `<option value="${location.location_id}" ${isSelected}>${displayName}</option>`;
                    }
                });
            } else {
                const whMap = {};
                whList.forEach(w => {
                    if (w && w.warehouse_id) whMap[w.warehouse_id] = w.name;
                });

                const grouped = {};
                locList.forEach(loc => {
                    if (!loc) return;
                    const wId = loc.warehouse_id || 'other';
                    if (!grouped[wId]) grouped[wId] = [];
                    grouped[wId].push(loc);
                });

                whList.forEach(w => {
                    const wId = w.warehouse_id;
                    const locs = grouped[wId] || [];
                    if (locs.length > 0) {
                        const groupName = w.name || `Almacén #${wId}`;
                        optionsHtml += `<optgroup label="${groupName}">`;
                        locs.forEach(loc => {
                            const isSelected = (selectedLocationId && selectedLocationId == loc.location_id) ? 'selected' : '';
                            const displayName = loc.name && loc.name.toLowerCase().includes(groupName.toLowerCase())
                                ? loc.name 
                                : `${groupName} - ${loc.name}`;
                            optionsHtml += `<option value="${loc.location_id}" ${isSelected}>${displayName}</option>`;
                        });
                        optionsHtml += `</optgroup>`;
                    }
                });

                for (const wId in grouped) {
                    if (!whList.some(w => w.warehouse_id == wId)) {
                        const groupName = whMap[wId] || `Almacén #${wId}`;
                        optionsHtml += `<optgroup label="${groupName}">`;
                        grouped[wId].forEach(loc => {
                            const isSelected = (selectedLocationId && selectedLocationId == loc.location_id) ? 'selected' : '';
                            const displayName = loc.name && loc.name.toLowerCase().includes(groupName.toLowerCase())
                                ? loc.name 
                                : `${groupName} - ${loc.name}`;
                            optionsHtml += `<option value="${loc.location_id}" ${isSelected}>${displayName}</option>`;
                        });
                        optionsHtml += `</optgroup>`;
                    }
                }
            }
            return optionsHtml;
        } catch (err) {
            console.error("Error in buildLocationOptions:", err);
            return '<option value="">Select a location</option>';
        }
    }

    function buildBigBagLocationOptions(selectedLocationId = null) {
        return buildLocationOptions(null, selectedLocationId);
    }

    function getMovement($modal){
        const cb = $modal.find('#type');
        return cb.length ? cb.val() : 'Input';
    }

    function setMovement($modal, movement){
        const cb = $modal.find('#type');
        if(cb.length) cb.val(movement);
    }

    function applyMovement($modal, movement){
        const isInput = (movement === 'Input');
        const $supplierWrapper = $modal.find('#supplier-wrapper-wrapper');
        const $customerWrapper = $modal.find('#customer-wrapper-wrapper');
        const $sellerWrapper   = $modal.find('#seller-wrapper-wrapper');
        const $locationWrapper = $modal.find('#location-assignment-wrapper');

        let filteredProducts = products_inventory;
        @hasrole('Warehouse')
        if (movement === 'Output') {
            filteredProducts = products_inventory.filter(p => p.category_id == 17);
        } else if (movement === 'InternalOutput') {
            // For internal production output, we allow BGBG / materia prima (or all inputs)
            filteredProducts = products_inventory; 
        }
        @endhasrole

        let optionsHtml = '';
        filteredProducts.forEach(product => {
            optionsHtml += `<option value='${product.product_id}'>${product.name}</option>`;
        });
        $('#add_tx_products_all').empty().append(optionsHtml);

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
            $modal.find('.transport-info-wrapper').removeClass('hidden').css('display','block');
            let template = $modal.find('#product_input_template').clone().removeAttr('id').removeClass('hidden').show();
            template.find('.location_id').html(buildLocationOptions());
            $modal.find('#products').empty().append(template);
            renderBagNumbers(template);

        } else if (movement === 'InternalOutput') {
            $customerWrapper.addClass('hidden').css('display','none');
            $supplierWrapper.addClass('hidden').css('display','none');
            $modal.find('.warehouse').prop('disabled', true);
            $modal.find('.location_id').prop('disabled', true);
            $modal.find('#customer').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');
            $modal.find('#supplier').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');
            $modal.find('#supplier-name').text('');
            $modal.find('#customer-name').text('');
            $sellerWrapper.addClass('hidden').css('display','none');
            $modal.find('#vendedor').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');
            $modal.find('.transport-info-wrapper').addClass('hidden').css('display','none');
            let template = $modal.find('#product_output_template').clone().removeAttr('id').removeClass('hidden').show();
            $modal.find('#products').empty().append(template);

        } else {
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
            $modal.find('.transport-info-wrapper').removeClass('hidden').css('display','block');
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

    $(document).off('change.makeTxType', '#make-transaction #type')
      .on('change.makeTxType', '#make-transaction #type', function(){
          const $modal = $(this).closest('#make-transaction');
          const movement = $(this).val();

          setMovement($modal, movement);
          applyMovement($modal, movement);
      });

    initTypeUI();

    function recalculateBagWeights(wrapper) {
        let movement = getMovement(wrapper.closest('#make-transaction'));
        if (movement === 'Input') {
            let totalWeight = 0;
            let filledCount = 0;
            let bagInputs = wrapper.find('.bag_weight_input');
            
            bagInputs.each(function() {
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
    }

    function renderBagNumbers(wrapper) {
        try {
            let product_id = wrapper.find('.product_id').val();
            let quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
            let container = wrapper.find('.bag_numbers_container');
            let bag_wrapper = wrapper.find('.bag_numbers_wrapper');
            let locIdWrapper = wrapper.find('.location-id-wrapper');
            let packingWrapper = wrapper.find('.packing-list-wrapper');
            let movement = getMovement(wrapper.closest('#make-transaction'));
            
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
            let isBigBag = (idNum === 623) ||
                           (product && product.product_id == 623) ||
                           prodName.toUpperCase().includes('BGBG') || 
                           prodName.toUpperCase().includes('BIGBAG') || 
                           prodUnit.toUpperCase().includes('BIGBAG');
            
            if (isBigBag) {
                bag_wrapper.show().css('display', 'flex').removeClass('hidden');
                if (movement === 'Input') {
                    packingWrapper.show().css('display', 'flex').removeClass('hidden');
                    packingWrapper.find('.packing_list').prop('disabled', false);
                    locIdWrapper.hide().css('display', 'none').addClass('hidden');
                    locIdWrapper.find('.location_id').prop('disabled', true);
                } else {
                    packingWrapper.hide().css('display', 'none').addClass('hidden');
                    packingWrapper.find('.packing_list').prop('disabled', true);
                    locIdWrapper.show().css('display', 'block').removeClass('hidden');
                    locIdWrapper.find('.location_id').prop('disabled', false);
                }

                // Preserve existing user inputs
                let existingBags = [];
                container.find('.bag-card-row').each(function() {
                    existingBags.push({
                        bag_number: $(this).find('.bag_number_input').val() || '',
                        protein: $(this).find('.protein_input').val() || '',
                        weight: $(this).find('.bag_weight_input').val() || $(this).find('.bag_weight_output').val() || '',
                        location_id: $(this).find('.bag_location_input').val() || ''
                    });
                });

                container.empty();
                let numInputs = Math.floor(quantity);
                
                let availableBags = [];
                if (movement === 'Output' || movement === 'InternalOutput') {
                    let inventory_id = wrapper.find('.warehouse_batch').val();
                    let selectedLocId = wrapper.find('.warehouse_batch').find('option:selected').data('location-id');
                    if (inventory_id) {
                        availableBags = product_locations.filter(pl => pl.inventory_id == inventory_id && pl.bag_number && (!selectedLocId || pl.location_id == selectedLocId));
                    }
                }

                let defaultUnitWeight = (parseFloat(wrapper.find('.weight_per_unit').val()) > 10) 
                    ? wrapper.find('.weight_per_unit').val() 
                    : '';

                for (let i = 0; i < numInputs; i++) {
                    let saved = existingBags[i] || {};
                    let savedBagNum = saved.bag_number || '';
                    let savedProtein = saved.protein || '';
                    let savedWeight = (saved.weight !== undefined && saved.weight !== '') 
                        ? saved.weight 
                        : defaultUnitWeight;

                    if ((movement === 'Output' || movement === 'InternalOutput') && availableBags.length > 0) {
                        let options = '<option value="">Select a Barcina</option>';
                        let uniqueBags = [];
                        let seen = new Set();
                        availableBags.forEach(bag => {
                            if (!seen.has(bag.bag_number)) {
                                seen.add(bag.bag_number);
                                uniqueBags.push(bag);
                            }
                        });

                        uniqueBags.forEach(bag => {
                            let isSelected = (savedBagNum && savedBagNum == bag.bag_number) ? 'selected' : '';
                            options += `<option value="${bag.bag_number}" data-protein="${bag.protein || ''}" data-weight="${bag.weight_per_unit || ''}" ${isSelected}>${bag.bag_number}</option>`;
                        });
                        container.append(`
                            <div class="bag-card-row flex flex-col gap-1.5 w-full bg-white p-2.5 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center justify-between text-xs font-bold text-gray-700">
                                    <span><i class="ri-inbox-line mr-1"></i>Barcina #${i+1}</span>
                                </div>
                                <select class="bag_number_input w-full rounded-md border border-gray-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 px-2 py-1.5" required>
                                    ${options}
                                </select>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Proteína %</label>
                                        <input type="number" step="any" class="protein_input w-full rounded-md border border-gray-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 px-2 py-1" placeholder="Proteína %" value="${savedProtein}" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Peso Almacén (kg)</label>
                                        <input type="number" step="any" class="bag_weight_output w-full bg-gray-100 rounded-md border border-gray-300 text-gray-700 text-sm focus:outline-none px-2 py-1" placeholder="Peso (kg)" value="${savedWeight}" readonly>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        let currentWarehouse = wrapper.find('.warehouse').val();
                        let initialLocId = saved.location_id;
                        if (!initialLocId && currentWarehouse) {
                            let locList = Array.isArray(locations) ? locations : Object.values(locations || {});
                            let firstLoc = locList.find(l => l && l.warehouse_id == currentWarehouse);
                            if (firstLoc) initialLocId = firstLoc.location_id;
                        }
                        let locOptions = buildBigBagLocationOptions(initialLocId);
                        let locSelectHTML = movement === 'Input' 
                            ? `<select class="bag_location_input w-full rounded-md border border-gray-300 text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 px-2 py-1.5" required>${locOptions}</select>`
                            : '';
                        
                        let $card = $(`
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
                                <div class="loc-wrapper-slot">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase">Ubicación</label>
                                    ${locSelectHTML}
                                </div>
                            </div>
                        `);

                        if (initialLocId) {
                            $card.find('.bag_location_input').val(initialLocId);
                        }

                        container.append($card);
                    }
                }
                recalculateBagWeights(wrapper);
            } else {
                bag_wrapper.hide().css('display', 'none').addClass('hidden');
                container.empty();
                packingWrapper.hide().css('display', 'none').addClass('hidden');
                packingWrapper.find('.packing_list').prop('disabled', true);
                locIdWrapper.show().css('display', 'block').removeClass('hidden');
                locIdWrapper.find('.location_id').prop('disabled', false);
            }
        } catch (err) {
            console.error('Error in renderBagNumbers:', err);
        }
    }

    father.on('change', '.warehouse', function(){
        const $wrapper = $(this).closest('.wrapper');
        const warehouseVal = $(this).val();
        
        const currentMainLoc = $wrapper.find('.location_id').val();
        $wrapper.find('.location_id').html(buildLocationOptions(warehouseVal, currentMainLoc));

        if (warehouseVal) {
            const locList = Array.isArray(locations) ? locations : Object.values(locations || {});
            const firstWhLoc = locList.find(l => l && l.warehouse_id == warehouseVal);
            if (firstWhLoc) {
                $wrapper.find('.bag_location_input').each(function(){
                    if (!$(this).val()) {
                        $(this).val(firstWhLoc.location_id);
                    }
                });
            }
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

        if ($(this).hasClass('location_id')) {
            const locId = parseInt($(this).val());
            if (locId) {
                const locObj = locations.find(l => l.location_id == locId);
                if (locObj && locObj.warehouse_id) {
                    const $whSelect = wrapper.find('.warehouse');
                    if (!$whSelect.val()) {
                        $whSelect.val(locObj.warehouse_id);
                    }
                }
            }
        }
    });

    father.on('change', '.bag_number_input', function() {
        let selectedOption = $(this).find('option:selected');
        let protein = selectedOption.data('protein');
        let weight = selectedOption.data('weight');
        let container = $(this).closest('div');
        let proteinInput = container.find('.protein_input');
        let weightInput = container.find('.bag_weight_output');
        
        if (protein !== undefined && protein !== '') {
            proteinInput.val(protein);
            proteinInput.prop('readonly', true).addClass('bg-gray-100');
        } else {
            proteinInput.val('');
            proteinInput.prop('readonly', false).removeClass('bg-gray-100');
        }

        if (weightInput.length > 0) {
            if (weight !== undefined && weight !== '') {
                weightInput.val(weight);
            } else {
                weightInput.val('');
            }
        }
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

    father.on('input', '.weight_per_unit', function(){
        const wrapper = $(this).closest('.wrapper');
        let weight_unit = parseFloat($(this).val()) || 0;
        let movement = getMovement(father);
        if (movement === 'Input') {
            let $bagWeights = wrapper.find('.bag_weight_input');
            // Only auto-fill individual bag weights if weight_unit is a substantial real weight (> 10 kg),
            // and NOT when the user enters '1' to represent unit count
            if ($bagWeights.length > 0 && weight_unit > 10) {
                $bagWeights.each(function() {
                    if (!$(this).val() || $(this).data('auto-filled')) {
                        $(this).val(weight_unit).data('auto-filled', true);
                    }
                });
            }
            recalculateBagWeights(wrapper);
        }
    });

    father.on('change', '.bag_weight_input', function(){
        $(this).removeData('auto-filled');
    });

    father.on('input', '.bag_weight_input', function(){
        const wrapper = $(this).closest('.wrapper');
        recalculateBagWeights(wrapper);
    });

    father.on('input change', '.quantity', function(){
        const wrapper = $(this).closest('.wrapper');
        renderBagNumbers(wrapper);
        recalculateBagWeights(wrapper);
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
        father.find('#add_tx_vehicles').empty();
        vehicles.forEach(vehicle => {
            if(vehicle.transport_line_id == transport_line){
                father.find('#add_tx_vehicles').append(`<option value="${vehicle.plate}">${vehicle.type}</option>`);
            }
        });
        father.find('#add_tx_trailers').empty();
        trailers.forEach(trailer => {
            if(trailer.transport_line_id == transport_line){
                father.find('#add_tx_trailers').append(`<option value="${trailer.plate}">${trailer.type}</option>`);
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
        if ((movement === 'Output' || movement === 'InternalOutput') && location_id) {
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

    father.on('input change','.product_id',function(){
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

                    renderBagNumbers(wrapper);

                    if(movement === 'Output' || movement === 'InternalOutput'){
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
                                        let isProdBigBag = (id == 623) || (response.product && response.product.product_id == 623) || (response.product && response.product.name && (response.product.name.includes('BGBG') || response.product.name.toUpperCase().includes('BIGBAG')));
                                        if (isProdBigBag) {
                                            locGroups[pl.location_id]++;
                                        } else {
                                            let qty = parseFloat(pl.quantity);
                                            locGroups[pl.location_id] += isNaN(qty) ? 1 : qty;
                                        }
                                    });
                                    for(let loc_id in locGroups) {
                                        let count = locGroups[loc_id];
                                        let locationObj = locations.find(l => l.location_id == loc_id);
                                        let locName = locationObj ? locationObj.name : 'Unknown';
                                        let whObj = locationObj ? warehouses.find(w => w.warehouse_id == locationObj.warehouse_id) : null;
                                        let whName = whObj ? whObj.name : '';
                                        wrapper.find('.warehouse_batch').append(`<option value="${b.inventory_id}" data-location-id="${loc_id}">${b.batch} - ${whName} ${locName}</option>`);
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
                    renderBagNumbers(wrapper);
                }
            });
        } else {
            wrapper.find('.product-name').text('');
            wrapper.find('.unit').text('');
            wrapper.find('.stock').text('');
            wrapper.find('.warehouse_batch').val('');
            wrapper.find('.label_batch').val('');
            renderBagNumbers(wrapper);
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

        father.find('.packing_list').val('');
        father.find('.packing-list-wrapper').addClass('hidden').css('display', 'none');

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

        let template = (movement === 'Output' || movement === 'InternalOutput')
            ? father.find('#product_output_template').clone().removeAttr('id').removeClass('hidden').show()
            : father.find('#product_input_template').clone().removeAttr('id').removeClass('hidden').show();

        if (movement === 'Input') {
            template.find('.location_id').html(buildLocationOptions());
        }

        father.find('#products').append(template);
        renderBagNumbers(template);
    });

    // SUBMIT
    father.find("#make-transaction-form").submit(function(event) {
        event.preventDefault();
        var form = father.find('#make-transaction-form')[0];
        var data = new FormData(form);
        
        let movement = getMovement(father);
        data.delete('bag_number'); 
        data.delete('protein'); 
        data.delete('bag_location_id'); 
        data.delete('bag_weight'); 
        data.delete('packing_list');
        $('#make-transaction #products .wrapper').each(function(index) {
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
            $(this).find('.bag_location_input').each(function() {
                data.append('bag_location_id[' + index + '][]', $(this).val());
            });
            
            if (movement === 'Output' || movement === 'InternalOutput') {
                let loc_id = $(this).find('.warehouse_batch').find('option:selected').data('location-id');
                if (loc_id) {
                    data.append('output_location_id[' + index + ']', loc_id);
                }
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        const reqId = urlParams.get('req_id');
        if (reqId) {
            data.append('req_id', reqId);
        }

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
                    if (result.isConfirmed) {
                        window.location.href = window.location.pathname + "?tab=" + (urlParams.get('tab') || 'inventory');
                    }
                });
                father.find('#save-transaction').prop('disabled',false);
            },
            error:function(xhr){
                let message = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors && typeof xhr.responseJSON.errors === 'object') {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON.error) {
                        if (typeof xhr.responseJSON.error === 'object') {
                            message = Object.values(xhr.responseJSON.error).flat().join('\n');
                        } else {
                            message = xhr.responseJSON.error;
                        }
                    } else if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
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
