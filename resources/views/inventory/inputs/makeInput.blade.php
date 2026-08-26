<div id="make-input" class="w-full">
    <form class="flex flex-col items-center w-full gap-2 px-4" id="make-input-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form class="text-md tracking-[2px] font-light border-b-2 border-green-700 pb-2">Transaction's information</x-tittle-form>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="supplier_id">Supplier</x-label>
                <x-input-1 required name="supplier_id" list="make_input_suppliers"></x-input-1>
                <datalist id="make_input_suppliers">
                    @foreach ($suppliers as $supplier)
                         <option value='{{ $supplier->supplier_id }}'>{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <p id="supplier-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="transport_line_id">Transport Line</x-label>
                <x-select-1 required name="transport_line_id">
                    <option value="">Select a Transport Line</option>
                    @foreach ($transport_lines as $transport_line)
                            <option value='{{ $transport_line->transport_line_id }}'>{{ $transport_line->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="operator_id">Operator</x-label>
                <x-select-1 required name="operator_id">
                    <option value="">Select a Operator</option>
                    @foreach ($operators as $operator)
                            <option value='{{ $operator->operator_id }}'>{{ $operator->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="unit_plates">Unit plates</x-label>
                <x-input-1 required name="unit_plates" list="make_input_vehicles"></x-input-1>
                <datalist id="make_input_vehicles">
                    @foreach ($vehicles as $vehicle)
                            <option value='{{ $vehicle->plate }}'>{{ $vehicle->type }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="trailer_plates">Trailer plates</x-label>
                <x-input-1 name="trailer_plates" list="make_input_trailers"></x-input-1>
                <datalist id="make_input_trailers">
                    @foreach ($trailers as $trailer)
                            <option value='{{ $trailer->plate }}'>{{ $trailer->type }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-toggle-decision id="security-seal" label="Security Seal?" buttonText="No" class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"></x-toggle-decision>
            <x-input-1 type="hidden" value="0" name="security_seal"></x-input-1>
            <x-wrapper-form-2 id="security-seal-number" class="hidden">
                <x-label for="security_seal_number" class="hidden">Security Seal Number</x-label>
                <x-input-1 disabled name="security_seal_number" class="hidden"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="comments">Comments</x-label>
                <x-textarea-1 name="comments"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-tittle-form class="text-md tracking-[2px] font-light border-b-2 border-green-700 pb-2">Products</x-tittle-form>
        </x-wrapper-form-1>
        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <x-button-1 type="button" colorBtn="blue" id="add_product">Add product</x-button-1>
            </x-wrapper-form-1>
            <div id="products" class="w-full">
                <div class="wrapper border-2 border-dashed border-gray-600 rounded-md p-2 my-3 relative">
                    <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="product_id">Product</x-label>
                            <x-input-1 name="product_id[]" id="product_id" list="make_input_products_all" class="product_id">Product</x-input-1>
                                <datalist id="make_input_products_all">
                                    @foreach ($products_all as $product)
                                        <option value='{{ $product->product_id }}'>{{ $product->name }}</option>
                                    @endforeach
                                </datalist>
                            <p class="product-name text-sm text-gray-500"></p>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label for="stock">Quantity</x-label>
                            <x-input-1 name="stock[]" id="stock" class="stock"></x-input-1>
                            <p class="unit text-sm text-gray-500"></p>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="warehouse_batch">Warehouse batch</x-label>
                            <x-input-1 name="warehouse_batch[]" class="warehouse_batch"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label for="concept_id">Concept</x-label>
                            <x-select-1 required name="concept_id[]" class="concept_id">
                                <option value="">Select a concept</option>
                                @foreach ($concepts as $concept)
                                    <option value='{{ $concept->concept_id }}'>{{ $concept->name }}</option>
                                @endforeach
                            </x-select-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2 id="platforms">
                            <x-label for="platforms">Platforms</x-label>
                            <x-input-1 required type="number" min="0" max="16" name="platforms[]" class="platforms"></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <div class="flex-col platforms-rows"></div>
                </div>
            </div>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-button-1 id="clean-form" type="reset" colorBtn="yellow">Clean Form</x-button-1>
                <x-button-1 id="close" class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            </x-wrapper-form-1>
            <x-button-1 id="save-input" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
    <div id="product_template" class="wrapper border-2 border-dashed border-gray-600 rounded-md p-2 my-3 relative hidden">
        <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_id">Product</x-label>
                <x-input-1 name="product_id[]" id="product_id" list="make_input_products_all" class="product_id">Product</x-input-1>
                    <datalist id="make_input_products_all">
                        @foreach ($products_all as $product)
                            <option value='{{ $product->product_id }}'>{{ $product->name }}</option>
                        @endforeach
                    </datalist>
                <p class="product-name text-sm text-gray-500"></p>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="stock">Quantity</x-label>
                <x-input-1 name="stock[]" id="stock" class="stock"></x-input-1>
                <p class="unit text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="warehouse_batch">Warehouse batch</x-label>
                <x-input-1 name="warehouse_batch[]" class="warehouse_batch"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="concept_id">Concept</x-label>
                <x-select-1 required name="concept_id[]" class="concept_id">
                    <option value="">Select a concept</option>
                    @foreach ($concepts as $concept)
                         <option value='{{ $concept->concept_id }}'>{{ $concept->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2 id="platforms">
                <x-label for="platforms">Platforms</x-label>
                <x-input-1 required type="number" min="0" name="platforms[]" class="platforms"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <div class="flex-col platforms-rows">
        </div>
    </div>
    <div id="row_template" class="row-wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 hidden">
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="weight_per_unit">Weight per unit</x-label>
                <x-input-1 type="number" min="0" name="weight_per_unit[][]" class="weight_per_unit"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 name="quantity[][]" class="quantity"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <p class="text-sm text-gray-500 my-2">Total: <span class="bg-gray-700 text-white text-md font-semibold tracking-[2px] rounded-md px-2 total-platform">0</span></p>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="location">Location</x-label>
                <x-wrapper-form-1>
                    <x-input-1 readonly name="location_name[][]" class="location_name" value="Inspection"></x-input-1>
                    <x-button-1 type="button" colorBtn="blue" class="open-maps">Search Location</x-button-1>
                </x-wrapper-form-1>
                @include('inventory.inputs.modals.map')
            </x-wrapper-form-2>
        </x-wrapper-form-1>
    </div>
</div>
@push('js')
<script>
$(function(){
    const father = $('#make-input');
    let row_index = 1;

    function makeInputReactive(){
        father.on('click','#clean-form',function(){
            row_index = 1;
            father.find('#products').empty();
            let template = null;
            template = father.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
            father.find('#products').append(template); 
        });

        father.on('input','#supplier_id',function(){
            let id = $(this).val();
            if (/^\d+$/.test(id) && id) {
                $.ajax({
                    type: 'GET',
                    url: `/suppliers/${id}`,
                    success: function(response){
                        father.find('#supplier-name').text(response.supplier.name);
                    },
                    error: function(e){
                        father.find('#supplier-name').text('');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Not Found',
                            text: 'An error occurred while got the supplier.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }else{
                father.find('#supplier-name').text('');
            }
        });

        //Modal para mapas
        father.on('click','.close-modal',function(){
            $(this).closest('.search-location').addClass('hidden');
        });

        father.on('click','.search-location',function(e){
            if ($(e.target).is('.search-location')) {
                $(this).addClass('hidden');
            }
        });

        father.on('click','.open-maps',function(){
            const wrapper = $(this).closest('.row-wrapper');
            wrapper.find('.search-location').removeClass('hidden');
        });

        father.on('input','.weight_per_unit',function(){
            const wrapper = $(this).closest('.row-wrapper');
            let weight_unit = $(this).val();
            let quantity = wrapper.find('.quantity').val();
            wrapper.find('.total-platform').text(quantity*weight_unit);
        });

        father.on('input','.quantity',function(){
            const wrapper = $(this).closest('.row-wrapper');
            let quantity = $(this).val();
            let weight_unit = wrapper.find('.weight_per_unit').val();
            wrapper.find('.total-platform').text(quantity*weight_unit);
        });

        father.on('input','.product_id',function(){
            let movement = father.find('#type').val();
            const wrapper = $(this).closest('.wrapper');
            let id = $(this).val();
            let date = new Date();
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = String(date.getFullYear()).slice(-2);
            let code = day + month + year
            if (/^\d+$/.test(id)) {
                id = parseInt(id)
                $.ajax({
                    type: 'GET',
                    url: `/catalogs/${id}`,
                    success: function(response){
                        wrapper.find('.product-name').text(response.product.name);
                        wrapper.find('.unit').text(`Unit: ${response.product.unit} | Presentation: ${response.product.presentation ? response.product.presentation : 'No presentation'}`);
                        wrapper.find('.warehouse_batch').val(`${response.product.batch_code}${code}`);
                    },
                    error: function(){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while got the product.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }else{
                wrapper.find('.product-name').text('')
                wrapper.find('.unit').text('');
                wrapper.find('.warehouse_batch').val('');
                wrapper.find('.label_batch').val('');
            }
        });

        father.on('input','.platforms',function(){
            const wrapper = $(this).closest('.wrapper');
            const rows = parseInt($(this).val());
            if(rows>16){
                Swal.fire({
                title: "Opps...",
                text: "You only generate 16 platforms",
                icon: "warning"
                });
                wrapper.find('.platforms').val('');
                rows = 1;
            }
            let template = null;
            wrapper.find('.platforms-rows').empty();
            for (let i = 0; i < rows; i++) {
                template = father.find('#row_template').clone().removeAttr('id').removeClass('hidden').show();
                wrapper.find('.platforms-rows').append(template);
            }
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

        father.find('#security-seal-btn').on('click', function(){
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No')
                btn.removeClass('border-green-400 bg-green-400 text-white');
                btn.addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                father.find('#security_seal_number-tag, #security_seal_number, #security-seal-number-wrapper').addClass('hidden');
                father.find('#security_seal_number').prop('disabled',true);
                father.find('#security_seal').val('0');
            }else{
                btn.text('Yes')
                btn.removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                btn.addClass('border-green-400 bg-green-400 text-white');
                father.find('#security_seal_number-tag, #security_seal_number, #security-seal-number-wrapper').removeClass('hidden');
                father.find('#security_seal_number').prop('disabled',false);
                father.find('#security_seal').val('1');
            }
        });
    }

    function makeInput(){
        $("#make-input-form").submit(function(event) {
            event.preventDefault();
            var form = $('#make-input-form')[0];
            var data = new FormData(form);
            $('#save-input').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/inputs",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The input was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-input').prop('disabled',false);
                },
                error:function(xhr){
                    let message = 'Ocurrió un error inesperado.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',	
                        text: message,
                        confirmButtonText: 'OK'
                    });
                    $('#save-input').prop('disabled',false);
                }
            });
        });
    }
    makeInputReactive();
    makeInput();
});
</script>
@endpush