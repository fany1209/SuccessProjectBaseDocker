{{--
Inventario
Inventario (Edit format)
Fecha de creación: 25-08-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 15-01-26
--}}

<x-modal id="edit-transaction">
    <form class="flex flex-col items-center w-full gap-2" id="edit-transaction-form">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Edit Transaction</x-tittle-form>
            <x-input-1 type="hidden" name="id" id="transaction_id"></x-input-1>
            <x-input-1 type="hidden" name="type" id="transaction_type"></x-input-1>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2 id="supplier-wrapper">
                <x-label for="supplier">Supplier</x-label>
                <x-input-1 required name="supplier_id" id="supplier" list="suppliers"></x-input-1>
                <datalist id="suppliers">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <p id="supplier-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>

            <x-wrapper-form-2 id="customer-wrapper" class="hidden">
                <x-label for="customer">Customer</x-label>
                <x-input-1 disabled required name="customer_id" id="customer" list="customers"></x-input-1>
                <datalist id="customers">
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->customer_id }}">{{ $customer->name }}</option>
                    @endforeach
                </datalist>
                <p id="customer-name" class="text-sm text-gray-500"></p>
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

        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-blue-500 text-white font-semibold px-2 py-1 rounded-md">Products</span>
            </x-wrapper-form-1>

            <div id="products" class="w-full"></div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="transport_line">Transport Line</x-label>
                <x-input-1 required name="transport_line_id" id="transport_line" list="transport_lines"></x-input-1>
                <datalist id="transport_lines">
                    @foreach ($transport_lines as $transport_line)
                        <option value="{{ $transport_line->transport_line_id }}">{{ $transport_line->name }}</option>
                    @endforeach
                </datalist>
                <p id="transport-line-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="operator">Operator</x-label>
                <x-input-1 required name="operator" id="operator"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="license_number">License number</x-label>
                <x-input-1 required name="license_number" id="license_number"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-toggle-decision
                id="security-seal"
                label="Security Seal?"
                buttonText="No"
                class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"
            ></x-toggle-decision>

            <x-input-1 type="hidden" value="0" name="security_seal" id="security_seal"></x-input-1>

            <x-wrapper-form-2 id="security-seal-number-wrapper" class="hidden">
                <x-label for="security_seal_number" id="security_seal_number-tag" class="hidden">Security Seal Number</x-label>
                <x-input-1 disabled name="security_seal_number" id="security_seal_number" class="hidden"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="unit_plates">Unit plates</x-label>
                <x-input-1 required name="unit_plates" id="unit_plates"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="trailer_plates">Trailer plates</x-label>
                <x-input-1 required name="trailer_plates" id="trailer_plates"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="comments">Comments</x-label>
                <x-textarea-1 name="comments" id="comments"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 id="close" class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="update-transaction" type="submit" colorBtn="blue">Update</x-button-1>
        </x-wrapper-form-1>
    </form>

    <datalist id="products_all">
        @foreach ($products_all as $product)
            <option value='{{ $product->product_id }}'>{{ $product->name }}</option>
        @endforeach
    </datalist>

    <div id="product_input_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_id">Product</x-label>
                <x-input-1 name="product_id[]" class="product_id" readonly list="products_all"></x-input-1>
                <input type="hidden" name="product[]" class="product_hidden">
                <p class="product-name text-sm text-gray-500"></p>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 name="quantity[]" class="quantity" inputmode="decimal" placeholder="0.000"></x-input-1>
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
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_id">Product</x-label>
                <x-input-1 name="product_id[]" list="products_all" class="product_id"></x-input-1>
                <input type="hidden" name="product[]" class="product_hidden">
                <p class="product-name text-sm text-gray-500"></p>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 name="quantity[]" class="quantity" inputmode="decimal" placeholder="0.000"></x-input-1>
                <p class="unit text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="warehouse_batch">Warehouse batch</x-label>
                <x-input-1 name="warehouse_batch[]" class="warehouse_batch"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="label_batch">Label batch</x-label>
                <x-input-1 name="label_batch[]" class="label_batch"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <p class="stock text-sm text-gray-500"></p>
        </x-wrapper-form-1>
    </div>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#edit-transaction');
    const suppliers = @json($suppliers);
    const customers = @json($customers);

    const ROUTES = {
        inputShow:    @json(route('inputs.show',   ['input'  => '__ID__'])),
        inputUpdate:  @json(route('inputs.update', ['input'  => '__ID__'])),
        outputShow:   @json(route('outputs.show',  ['output' => '__ID__'])),
        outputUpdate: @json(route('outputs.update',['output' => '__ID__'])),
    };

    function urlFrom(template, id){
        return String(template).replace('__ID__', id);
    }

    function normalizeDecimal(v){
        if (v === null || v === undefined) return '';
        v = String(v).trim();
        return v.replace(',', '.');
    }

    father.on('input', '.product_id', function(){
        const $wrap = $(this).closest('.wrapper');
        $wrap.find('.product_hidden').val($(this).val());
    });

    function editTransaction(){
        father.find("#edit-transaction-form").on('submit', function(event) {
            event.preventDefault();

            father.find('input.quantity').each(function(){
                $(this).val(normalizeDecimal($(this).val()));
            });

            const form = father.find('#edit-transaction-form')[0];
            const data = new FormData(form);
            const id = father.find('#transaction_id').val();
            const type = (father.find('#transaction_type').val() || '').toLowerCase();

            if(!id || !type){
                Swal.fire({ icon:'error', title:'Error', text:'No se encontró el ID o el tipo de transacción.', confirmButtonText:'OK' });
                return;
            }

            data.append('_method', 'PUT');

            const isInput = (type === 'input');
            const url = isInput ? urlFrom(ROUTES.inputUpdate, id) : urlFrom(ROUTES.outputUpdate, id);

            $.ajax({
                type:'POST',
                url: url,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({ icon:'success', title:'Success', text:'The transaction was updated successfully.', confirmButtonText:'OK' })
                    .then(r => { if(r.isConfirmed) location.reload(); });
                },
                error:function(xhr){
                    let message = 'Ocurrió un error inesperado.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon:'error', title:'Error', text: message, confirmButtonText:'OK' });
                }
            });
        });
    }

    function reactiveEditTransaction(){
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

        father.find('#security-seal-btn').on('click', function(){
            const btn = $(this);
            const $secWrap = father.find('#security-seal-number-wrapper-wrapper, #security-seal-number-wrapper').first();
            const $tag = father.find('#security_seal_number-tag');
            const $inp = father.find('#security_seal_number');

            if(btn.hasClass('border-green-400')){
                btn.text('No')
                  .removeClass('border-green-400 bg-green-400 text-white')
                  .addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');

                $secWrap.addClass('hidden').hide();
                $tag.addClass('hidden');
                $inp.addClass('hidden').prop('disabled', true).val('');
                father.find('#security_seal').val('0');
            }else{
                btn.text('Yes')
                  .removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white')
                  .addClass('border-green-400 bg-green-400 text-white');

                $secWrap.removeClass('hidden').show();
                $tag.removeClass('hidden');
                $inp.removeClass('hidden').prop('disabled', false);
                father.find('#security_seal').val('1');
            }
        });
    }

    window.fillEditTransactionModal = function(id, typeLower){
        const modal = $('#edit-transaction');

        typeLower = String(typeLower || '').trim().toLowerCase();
        if(typeLower === 'inputs') typeLower = 'input';
        if(typeLower === 'outputs') typeLower = 'output';

        modal.find('#transaction_id').val(id);
        modal.find('#transaction_type').val(typeLower);
        modal.find('#products').empty();

        const $supplierWrapper = modal.find('#supplier-wrapper-wrapper, #supplier-wrapper').first();
        const $customerWrapper = modal.find('#customer-wrapper-wrapper, #customer-wrapper').first();
        const $sellerWrapper   = modal.find('#seller-wrapper-wrapper, #seller-wrapper').first();

        const isInputByType = (typeLower === 'input');
        const url = isInputByType ? urlFrom(ROUTES.inputShow, id) : urlFrom(ROUTES.outputShow, id);

        $.ajax({
            type: 'GET',
            url: url,
            success: function(r){
                const finalIsInput = !!r.input;

                if(finalIsInput){
                    // INPUT
                    $supplierWrapper.removeClass('hidden').show();
                    $customerWrapper.addClass('hidden').hide();
                    $sellerWrapper.addClass('hidden').hide();

                    modal.find('#supplier').prop('disabled', false).removeAttr('disabled').prop('required', true);
                    modal.find('#customer').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');
                    modal.find('#customer-name').text('');

                    modal.find('#vendedor').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');

                    modal.find('#supplier').val(r.input.supplier_id);
                    modal.find('#supplier-name').text(r.input.sName);

                }else{
                    // OUTPUT
                    $customerWrapper.removeClass('hidden').show();
                    $supplierWrapper.addClass('hidden').hide();
                    $sellerWrapper.removeClass('hidden').show();

                    modal.find('#customer').prop('disabled', false).removeAttr('disabled').prop('required', true);
                    modal.find('#supplier').prop('disabled', true).attr('disabled','disabled').prop('required', false).val('');
                    modal.find('#supplier-name').text('');
                    modal.find('#vendedor').prop('disabled', false).removeAttr('disabled').prop('required', true);
                    modal.find('#customer').val(r.output.customer_id);
                    modal.find('#customer-name').text(r.output.cName);
                    modal.find('#vendedor').val(r.output.vendedor || '');
                }

                const header = finalIsInput ? r.input : r.output;
                const sec = header.security_seal;
                const $secWrap = modal.find('#security-seal-number-wrapper-wrapper, #security-seal-number-wrapper').first();
                const $tag = modal.find('#security_seal_number-tag');
                const $inp = modal.find('#security_seal_number');

                if(sec == 1){
                    modal.find('#security-seal-btn').text('Yes')
                        .removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white')
                        .addClass('border-green-400 bg-green-400 text-white');

                    $secWrap.removeClass('hidden').show();
                    $tag.removeClass('hidden');
                    $inp.removeClass('hidden').prop('disabled', false);

                    modal.find('#security_seal').val('1');
                    $inp.val(header.security_seal_number || '');
                }else{
                    modal.find('#security-seal-btn').text('No')
                        .removeClass('border-green-400 bg-green-400 text-white')
                        .addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');

                    $secWrap.addClass('hidden').hide();
                    $tag.addClass('hidden');
                    $inp.addClass('hidden').prop('disabled', true).val('');

                    modal.find('#security_seal').val('0');
                }

                // Header
                modal.find('#transport_line').val(header.transport_line_id);
                modal.find('#transport-line-name').text(header.tName);
                modal.find('#operator').val(header.operator);
                modal.find('#license_number').val(header.license_number);
                modal.find('#unit_plates').val(header.unit_plates);
                modal.find('#trailer_plates').val(header.trailer_plates);
                modal.find('#comments').val(header.comments);

                // Products
                r.products.forEach(item => {
                    let tpl = finalIsInput
                        ? modal.find('#product_input_template').clone()
                        : modal.find('#product_output_template').clone();

                    tpl.removeAttr('id').removeClass('hidden').show();
                    tpl.find('input,select,textarea').prop('disabled', false);
                    tpl.find('.product_id').val(item.product_id);
                    tpl.find('.product_hidden').val(item.product_id);
                    tpl.find('.product-name').text(item.name);
                    tpl.find('.quantity').val(normalizeDecimal(item.quantity));
                    tpl.find('.unit').text(`Old quantity: ${item.quantity} ${item.unit}`);
                    tpl.find('.warehouse_batch').val(item.warehouse_batch);

                    if(!finalIsInput){
                        tpl.find('.label_batch').val(item.label_batch);
                    }

                    modal.find('#products').append(tpl);
                });
            },
            error: function(){
                Swal.fire({ icon:'error', title:'Error', text:'An error occurred while getting the transaction.', confirmButtonText:'OK' });
            }
        });
    };

    // INIT
    editTransaction();
    reactiveEditTransaction();

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
