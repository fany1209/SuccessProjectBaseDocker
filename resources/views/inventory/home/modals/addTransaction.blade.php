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
            <x-toggle-switch id="type" name="type" value="Input" :checked="true"
                onLabel="Input" offLabel="Output"
                onColor="emerald-500" offColor="red-700"
                textColor="white" class=""/>
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
                            <x-label for="product_id">Product</x-label>
                            <x-input-1 name="product_id[]" list="products_all" class="product_id">Product</x-input-1>
                            <datalist id="products_all">
                                @foreach ($products_all as $product)
                                    <option value='{{ $product->product_id }}'>{{ $product->name }}</option>
                                @endforeach
                            </datalist>
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
                            <x-input-1 name="warehouse_batch[]" class="warehouse_batch"></x-input-1>
                        </x-wrapper-form-2>
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
                <x-input-1 name="warehouse_batch[]" class="warehouse_batch"></x-input-1>
            </x-wrapper-form-2>
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

        if(isInput){

            $supplierWrapper.removeClass('hidden').css('display','block');
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
        const batch = batchs.find(x => x.inventory_id == inventory_id);
        wrapper.find('.stock').text((inventory_id && batch) ? `Available: ${batch.stock}` : `Available: 0`);
    });

    father.on('input','.product_id',function(){
        let movement = getMovement(father);

        const wrapper = $(this).closest('.wrapper');
        let id = $(this).val();

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
                        wrapper.find('.warehouse_batch').html('<option value="">Select a batch</option>');
                        batchs.forEach(b => {
                            if(b.product_id == id){
                                wrapper.find('.warehouse_batch').append(`<option value="${b.inventory_id}">${b.batch}</option>`);
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
                    message = xhr.responseJSON.error;
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
