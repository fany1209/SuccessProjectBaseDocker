@php
    $imgCls  = 'h-16 w-16 bg-blue-400 rounded-md p-1 object-contain pointer-events-none select-none me-2';
@endphp

<x-modal id="modal-oEdit">
    <form class="flex flex-col items-center w-full gap-2" method="POST" id="form-edit-order">
        @csrf
        @method('PUT')
        
        <x-wrapper-form-1>
            <img src="{{ asset('images/purchases/01.png') }}" alt="Edit order image" class="{{ $imgCls }}">
            <x-tittle-form class="border-b-2 border-blue-700 pb-2">Edit Purchase Order</x-tittle-form>
        </x-wrapper-form-1>

        {{-- NUEVO: Campo de Folio Manual en Edición --}}
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_id">Folio Number</x-label>
                <x-input-1 required name="id" id="edit_id" placeholder="Folio manual"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Supplier</x-label>
                <x-input-1 required name="supplier_id" id="edit_supplier_id" list="edit_suppliers" placeholder="Select a supplier"></x-input-1>
                <datalist id="edit_suppliers">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <p id="edit-supplier-name-text" class="text-sm text-blue-600 font-bold mt-1"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_contact">Contact</x-label>
                <x-input-1 required name="contact" id="edit_contact"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_delivery_time">Delivery time</x-label>
                <x-input-1 required name="delivery_time" id="edit_delivery_time"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_delivery_date">Delivery date</x-label>
                <x-input-1 required type="date" name="delivery_date" id="edit_delivery_date"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_guia">Guide</x-label>
                <x-input-1 name="guia" id="edit_guia" placeholder="Optional"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_cfdi">Use of CFDI</x-label>
                <x-input-1 required name="cfdi" id="edit_cfdi" list="edit_descriptions"></x-input-1>
                <datalist id="edit_descriptions">
                    @foreach ($cfdi as $item)
                        <option value="{{ $item->cfdi }}">{{ $item->description }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_payment_method">Payment Method</x-label>
                <x-input-1 required name="payment_method" id="edit_payment_method" list="edit_payments"></x-input-1>
                <datalist id="edit_payments">
                    @foreach ($payment_method as $item)
                        <option value="{{ $item->type }}">{{ $item->description }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_method_payment">Method of payment</x-label>
                <x-input-1 required name="method_payment" id="edit_method_payment" list="edit_methods"></x-input-1>
                <datalist id="edit_methods">
                    @foreach ($method_payment as $item)
                        <option value="{{ $item->code }}">{{ $item->description }}</option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-tittle-form class="border-b-2 border-blue-700 pb-2">Application details</x-tittle-form>
        
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_application_date">Date</x-label>
                <x-input-1 required type="date" name="application_date" id="edit_application_date"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_applicant">Applicant</x-label>
                <x-input-1 required name="applicant" id="edit_applicant"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-blue-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-blue-700 text-white font-semibold px-2 py-1 rounded-md">Products</span>
                <x-button-1 type="button" colorBtn="blue" id="add_product_edit">Add product</x-button-1>
            </x-wrapper-form-1>
            
            <div id="edit-products-container" class="w-full">
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-2>
            <div class="flex justify-end items-center w-full gap-2">
                <x-label>Total:</x-label>
                <x-input-1 name="price" id="edit_total_price" readonly class="w-32 bg-gray-100 font-bold text-blue-700"></x-input-1>
            </div>
        </x-wrapper-form-2>

        <x-wrapper-form-1>
            <x-button-1 colorBtn="red" type="button" onclick="document.getElementById('modal-oEdit').classList.add('hidden')">Cancel</x-button-1>
            <x-button-1 type="submit" colorBtn="blue">Update Order</x-button-1>
        </x-wrapper-form-1>
    </form>

    <div id="product_template_edit" class="hidden">
        <div class="wrapper border-2 border-dashed border-blue-200 rounded-md p-2 my-1 relative">
            <span class="remove-product-edit absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Product</x-label>
                    <x-input-1 required name="product_name[]" class="product_name"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label>IVA?</x-label>
                    <x-input-1 type="hidden" name="iva[]" class="iva_val" value="1"></x-input-1>
                    <button type="button" class="edit-iva-btn border-green-400 bg-green-400 text-white rounded px-2 py-1 text-xs">Yes</button>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Unit price</x-label>
                    <x-input-1 required name="unit_price[]" class="unit_price"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label>Quantity</x-label>
                    <x-input-1 required type="number" name="quantity[]" class="quantity"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
        </div>
    </div>
</x-modal>

<script>
$(function(){
    const modalEdit = $('#modal-oEdit');

    window.fillEditProductsTable = function(details) {
        const container = $('#edit-products-container');
        container.empty();
        
        details.forEach(item => {
            let row = $('#product_template_edit').children().clone();
            row.find('.product_name').val(item.product_name);
            row.find('.unit_price').val(item.unit_price);
            row.find('.quantity').val(item.quantity);
            
            if(item.has_iva == 0) {
                const btn = row.find('.edit-iva-btn');
                btn.text('No').removeClass('bg-green-400 border-green-400').addClass('bg-gray-400 border-gray-400');
                row.find('.iva_val').val('0');
            }
            container.append(row);
        });
        updateEditTotals();
    };

    $('#add_product_edit').click(function() {
        let row = $('#product_template_edit').children().clone();
        $('#edit-products-container').append(row);
    });

    modalEdit.on('click', '.remove-product-edit', function() {
        if($('#edit-products-container .wrapper').length > 1) {
            $(this).closest('.wrapper').remove();
            updateEditTotals();
        }
    });

    modalEdit.on('click', '.edit-iva-btn', function() {
        const btn = $(this);
        const input = btn.siblings('.iva_val');
        if(input.val() == "1") {
            btn.text('No').removeClass('bg-green-400 border-green-400').addClass('bg-gray-400 border-gray-400');
            input.val("0");
        } else {
            btn.text('Yes').removeClass('bg-gray-400 border-gray-400').addClass('bg-green-400 border-green-400');
            input.val("1");
        }
        updateEditTotals();
    });

    modalEdit.on('input', '.unit_price, .quantity', function() {
        updateEditTotals();
    });

    function updateEditTotals() {
        let total = 0;
        $('#edit-products-container .wrapper').each(function() {
            const price = parseFloat($(this).find('.unit_price').val()) || 0;
            const qty = parseFloat($(this).find('.quantity').val()) || 0;
            const iva = $(this).find('.iva_val').val() == "1" ? 1.16 : 1;
            total += (price * qty * iva);
        });
        $('#edit_total_price').val(total.toFixed(2));
    }

    $('#form-edit-order').submit(function(e) {
        e.preventDefault();
        const url = $(this).attr('action');
        const formData = $(this).serialize();

        $.ajax({
            url: url,
            type: 'POST', 
            data: formData,
            success: function(response) {
                Swal.fire('Updated!', 'The order has been updated successfully.', 'success')
                .then(() => {
                    location.reload(); 
                });
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message || 'Error updating order', 'error');
            }
        });
    });
});
</script>