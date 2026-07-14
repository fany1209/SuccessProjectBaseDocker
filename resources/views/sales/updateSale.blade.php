{{--
Sales
updateSales
Fecha de creación: 24-09-2025
Actualizado por: Stefany
Fecha de actualización: 27-02-2026
Actualizado por: Emilio
Fecha de actualización: 15-06-2026
--}}
<section id="update-sale-blade"
    class="flex flex-col items-center w-full lg:w-3/4 rounded-lg bg-gray-50 shadow-md px-4 py-2 hidden">
    <div class="flex justify-between items-center w-full gap-2">
        <h2 class="my-2 text-2xl row-span-1 row-start-1 w-full border-s-4 border-green-700 ps-2">Update Sale</h2>
        <x-button id="close-update-sale-blade"
            class="bg-red-500 hover:bg-red-600 focus:bg-red-600 active:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">Close</x-button>
    </div>
    <form class="flex flex-col lg:flex-row lg:justify-center items-start w-full gap-3 mt-2" id="update-sale-form">
        @csrf
        <input type="hidden" id="sale-id" name="sale_id">
        <input type="hidden" id="sales-status" name="sales_status_id" value="">
        <div class="flex flex-col items-center w-full lg:w-3/4">
            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="sector" class="mb-1 block font-medium text-md text-gray-700">Sale Type</label>
                    <select required id="sector" name="sector_id"
                        class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        <option value="">Select a sector</option>
                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->sector_id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-between items-center w-full gap-2 border-b-2 border-gray-500 pb-4">
               <div class="flex flex-col items-start gap-1 w-full">
                    <label for="folio" class="mb-1 block font-medium text-md text-gray-700">Folio</label>
                    <input type="text" id="folio" name="folio" maxlength="150" readonly class="w-full rounded-lg border border-gray-300 bg-gray-200 text-gray-500 cursor-not-allowed focus:outline-none px-2 py-2">
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="seller" class="mb-1 block font-medium text-md text-gray-700">Seller</label>
                    <x-input-1 name="seller"></x-input-1>
                    <x-input-1 type="hidden" id="user_id" name="user_id"></x-input-1>
                </div>
            </div>
            <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Payment Method</h3>
            <div class="flex justify-start items-center w-full gap-2 border-b-2 border-gray-500 pb-4">
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" id="payment-metod" class="peer sr-only" checked />
                    <div class="payment-method peer flex w-full items-center justify-center py-2 px-3 rounded-md bg-orange-700 
                                after:absolute after:left-0 after:h-full after:w-1/2 after:rounded-md after:bg-white/40 
                                after:transition-all after:translate-x-full after:content-[''] peer-checked:bg-emerald-500 
                                peer-checked:after:translate-x-0 duration-300 text-sm text-white gap-2">
                        <span><i class="ri-cash-line text-xl"></i>Cash</span>
                        <span class="ms-4"><i class="ri-bank-card-fill text-xl"></i>Credit</span>
                    </div>
                </label>
                <input disabled type="text" id="sale-type-input" name="term"
                    placeholder="Example: 15 days, 30 days, other, etc."
                    class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                <input type="hidden" id="sale-type" name="sale_type" value="Cash">
            </div>
            <div id="payment-status-container" class="hidden flex justify-start items-center w-full gap-2 border-b-2 border-gray-500 pb-4">
                <div class="flex flex-col items-start gap-1 w-1/2">
                    <label for="payment-status" class="mb-1 block font-medium text-md text-gray-700">Payment Status</label>
                    <select id="payment-status" name="payment_status" class="w-full rounded-lg border border-gray-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        <option value="PENDING">PENDING</option>
                        <option value="PAID">PAID</option>
                    </select>
                </div>
            </div>
            <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Client Information</h3>
            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="purchase-order" class="mb-1 block font-medium text-md text-gray-700">Purchase
                        Order</label>
                    <input type="text" id="purchase-order" name="purchase_order"
                        class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="date" class="mb-1 block font-medium text-md text-gray-700">Date</label>
                    <input required type="date" id="date" name="date"
                        class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
            </div>
            <div class="flex justify-between items-center w-full gap-2 border-b-2 border-gray-200 pb-2">
                <div id="invoice-opt" class="flex flex-col items-start gap-1 w-full">
                    <label for="" id="invoice-tag" class="mb-1 block font-medium text-md text-gray-700">Invoice?</label>
                    <span id="invoice-btn"
                        class="flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="invoice" class="mb-1 block font-medium text-md text-gray-700">Invoice #</label>
                    <input disabled type="text" id="invoice" name="invoice"
                        class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
                <div class="hidden flex-col items-start gap-1 w-full" id="customer-opt">
                    <label class="mb-1 block font-medium text-md text-gray-700">Customer?</label>
                    <span id="customer-btn"
                        class="flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="name" id="client-tag" class="mb-1 block font-medium text-md text-gray-700">Customer
                        name</label>

                    <input type="hidden" id="prospect-2-id" name="prospect_id" value="">

                    <input required type="text" id="customer-1" placeholder="Double click to show all" list="customers"
                        maxlength="200"
                        class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    <datalist id="customers">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->customer_id }} - {{ $customer->name }}"></option>
                        @endforeach
                    </datalist>
                    <input type="hidden" id="customer-1-id" name="customer_id" value="">

                    <p id="client-name" class="text-sm text-gray-500"></p>
                    <input type="hidden" name="is_customer" id="is-customer" value="0">
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2 mt-1 border-b-2 border-gray-500 pb-4">
                <div class="flex flex-col items-start gap-1 w-full">
                    <span
                        class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Information</span>
                    <div class="accordion-body w-full hidden">
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="email" class="mb-1 block font-medium text-md text-gray-700">Email</label>
                                <input disabled type="email" id="email" name="email" maxlength="150"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="phone" class="mb-1 block font-medium text-md text-gray-700">Phone</label>
                                <input disabled type="text" id="phone" name="phone" maxlength="20"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="rfc" class="mb-1 block font-medium text-md text-gray-700">RFC</label>
                                <input disabled type="text" id="rfc" name="rfc" maxlength="13"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="address"
                                    class="mb-1 block font-medium text-md text-gray-700">Address</label>
                                <input disabled type="text" id="address" name="address" maxlength="200"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="city" class="mb-1 block font-medium text-md text-gray-700">City</label>
                                <input disabled type="text" id="city" name="city" maxlength="80"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="district"
                                    class="mb-1 block font-medium text-md text-gray-700">District</label>
                                <input disabled type="text" id="district" name="district" maxlength="80"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="state" class="mb-1 block font-medium text-md text-gray-700">State</label>
                                <input disabled type="text" id="state" name="state" maxlength="80"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="country"
                                    class="mb-1 block font-medium text-md text-gray-700">Country</label>
                                <input disabled type="text" id="country" name="country" maxlength="100"
                                    class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center w-full gap-2">
                <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Products</h3>
                <span id="add-product-sales"
                    class="px-2 py-1 rounded-md text-white tracking-[2px] cursor-pointer bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add
                    Product +</span>
            </div>
            <div id="products-sales" class="flex flex-col items-center w-full gap-2"></div>
        </div>
        <div class="flex flex-col justify-between items-end w-full gap-4 lg:w-1/4">
            <div class="flex flex-col items-center w-full gap-4 shadow-md rounded-md px-4 py-1">
                <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Sale Summary</h3>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Sector</p>
                    <span id="sector-selected"
                        class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md"></span>
                </div>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Client name</p>
                    <span id="client-selected"
                        class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md"></span>
                </div>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Seller name</p>
                    <span id="seller-selected"
                        class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md"></span>
                </div>
                <div
                    class="flex justify-between items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Products</p>
                    <span id="products-count"
                        class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md">0</span>
                </div>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Payment method</p>
                    <span id="payment-method-summary"
                        class="tracking-[2px] transition text-md bg-green-700 text-white px-1 rounded-md"><i
                            class="ri-cash-line text-lg"></i>Cash</span>
                </div>
                <div class="flex justify-between items-start w-full gap-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">IVA (16%)</p>
                    <span id="iva-total-amount"
                        class="tracking-[2px] text-md text-gray-700 px-1 rounded-md">$0.00</span>
                </div>
                <div class="flex justify-between items-start w-full gap-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Total</p>
                    <span id="grand-total"
                        class="tracking-[2px] text-md bg-green-500 text-white px-1 rounded-md">$0.00</span>
                </div>
            </div>
            <div class="flex justify-end items-start w-full gap-2">
                <x-button type="submit" id="update-sale"
                    class="bg-blue-500 hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Finish</x-button>
            </div>
        </div>
    </form>
    <div id="product-template-update" class="hidden">
        <div
            class="wrapper flex flex-col items-start w-full gap-2 rounded-md p-2 border-2 border-dashed border-gray-300 relative">
            <span
                class="remove-btn-2 absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs cursor-pointer">X</span>
            <input type="hidden" name="sale_detail[]" class="sale-detail" value="0">
            <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                <div class="flex justify-between items-center w-full gap-2">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="product-id"
                            class="mb-1 block font-medium text-md text-gray-700">Product<span></span></label>
                        <input required type="text" name="product_id[]" list="products"
                            class="product-id w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        <datalist id="products">
                            @foreach ($products as $product)
                                <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="public-batch" class="mb-1 block font-medium text-md text-gray-700">Public
                            batch</label>
                        <input type="text" id="" name="public_batch[]" maxlength="100"
                            class="public-batch w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>
                <div class="flex justify-between items-center w-full gap-2">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="quantity"
                            class="quantity-tag mb-1 block font-medium text-md text-gray-700">Quantity</label>
                        <input required type="text" value="0" id="" name="quantity[]"
                            class="quantity w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="cost" class="cost-tag mb-1 block font-medium text-md text-gray-700">Cost</label>
                        <input type="number" value="0" step="any" min="0" id="" name="cost[]"
                            class="cost w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                <div class="flex justify-between items-center w-full gap-2">
                    <div id="invoice-value-opt" class="flex flex-col items-start gap-1 w-full">
                        <label for="" id="invoice-value-tag"
                            class="mb-1 block font-medium text-md text-gray-700">Invoice Value?</label>
                        <span id=""
                            class="invoice-value-btn-2 flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                        <input type="hidden" class="invoice-val" name="invoice_val[]" value="0">
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label class="mb-1 block font-medium text-md text-gray-700">IVA</label>
                        <span
                            class="tax-btn-2 flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                        <input type="hidden" class="has-tax" name="has_tax[]" value="0">
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="public-product-name" class="mb-1 block font-medium text-md text-gray-700">Invoice
                            product name</label>
                        <input type="text" id="" name="public_product_name[]" maxlength="100"
                            class="public-product-name w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex justify-between items-center gap-2">
                    <p class="product-name text-sm text-gray-500"></p>
                    <p class="product-sku text-sm text-gray-500"></p>
                </div>
                <p class="text-md text-gray-700 tracking-[2px]">Subtotal: <span
                        class="import-total text-md text-white tracking-[2px] bg-gray-700 p-1 rounded-sm">$00.00</span>
                </p>
            </div>
        </div>
    </div>
</section>
@push('js')
    <script>
        window.updateWrapperUpdate = function (wrapper) {
            const cost = parseFloat(wrapper.find('.cost').val()) || 0;
            const quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
            const hasTax = wrapper.find('.has-tax').val() === '1';

            let rowSubtotal = cost * quantity;
            if (hasTax) {
                rowSubtotal = rowSubtotal * 1.16;
            }
            wrapper.find('.import-total').text(`$${rowSubtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
        };

        window.updateGrandTotalUpdate = function () {
            const father = $('#update-sale-form');
            let subtotalGeneral = 0;
            let totalIva = 0;

            father.find('.wrapper:visible').each(function () {
                const cost = parseFloat($(this).find('.cost').val()) || 0;
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                const hasTax = $(this).find('.has-tax').val() === '1';

                const filaBase = cost * quantity;
                subtotalGeneral += filaBase;

                if (hasTax) {
                    totalIva += (filaBase * 0.16);
                }
            });

            const totalFinal = subtotalGeneral + totalIva;

            father.find('#iva-total-amount').text(`$${totalIva.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
            father.find('#grand-total').text(`$${totalFinal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
        };

        $(function () {
            function reactiveUpdateSale() {
                $('#update-sale-form #payment-metod').change(function () {
                    const father = $('#update-sale-form');
                    if ($(this).is(':checked')) {
                        father.find('#sale-type-input').prop('disabled', true).val('');
                        father.find('#sale-type').val('Cash');
                        father.find('#payment-method-summary').removeClass('bg-orange-700').addClass('bg-green-700').html('<i class="ri-cash-line text-lg"></i>Cash');
                        father.find('#payment-status-container').addClass('hidden');
                    } else {
                        father.find('#payment-method-summary').html('<i class="ri-bank-card-fill"></i>Credit').removeClass('bg-green-700').addClass('bg-orange-700');
                        father.find('#sale-type-input').prop('disabled', false);
                        father.find('#sale-type').val('Credit');
                        father.find('#payment-status-container').removeClass('hidden');
                    }
                });

                // SOLO ACTUALIZA EL TEXTO, YA NO BORRA LOS DATALISTS
                $('#update-sale-form #sector').on('change', function () {
                    $('#update-sale-form #sector-selected').text($(this).find('option:selected').text());
                });

                $('#close-update-sale-blade').on('click', function () {
                    $('#update-sale-blade').addClass('hidden');
                });

                $('#update-sale-form #invoice-btn').on('click', function () {
                    const father = $('#update-sale-form');
                    const btn = $(this);
                    if (btn.hasClass('border-green-400')) {
                        btn.text('No').removeClass('border-green-400 bg-green-400 text-white').addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                        father.find('#invoice').val('').prop('disabled', true);
                    } else {
                        btn.text('Yes').removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white').addClass('border-green-400 bg-green-400 text-white');
                        father.find('#invoice').prop('disabled', false);
                    }
                });

                // Removed customer-btn click handler

                // --- FIX UX: Doble Clic para limpiar y ver todos ---
                $('#update-sale-form #customer-1').on('dblclick', function () {
                    $(this).val('');
                    $('#update-sale-form #customer-1-id').val('');
                    $('#update-sale-form #prospect-2-id').val('');
                    $('#update-sale-form #is-customer').val('1'); // if cleared, next selection will be a customer
                    $('#update-sale-form #client-name, #update-sale-form #client-selected').text('');
                    $('#update-sale-form #email, #update-sale-form #phone, #update-sale-form #rfc, #update-sale-form #address, #update-sale-form #city, #update-sale-form #district, #update-sale-form #state, #update-sale-form #country').val('');
                });

                $('#update-sale-form #customer-1').on('input', function () {
                    const father = $('#update-sale-form');
                    const raw = ($(this).val() || '').trim();
                    const id = parseInt(raw.split(' - ')[0], 10) || 0;
                    father.find('#customer-1-id').val(id ? String(id) : '');

                    if (id !== 0) {
                        father.find('#prospect-2-id').val('');
                        father.find('#is-customer').val('1');
                        $.ajax({
                            type: 'GET',
                            url: `/customers/${id}`,
                            success: function (r) {
                                father.find('#client-name, #client-selected').text(r.customer['name']);
                                father.find('#email').val(r.customer['email']);
                                father.find('#phone').val(r.customer['phone']);
                                father.find('#rfc').val(r.customer['rfc']);
                                father.find('#address').val(r.customer['address']);
                                father.find('#city').val(r.customer['city']);
                                father.find('#district').val(r.customer['district']);
                                father.find('#state').val(r.customer['state']);
                                father.find('#country').val(r.customer['country']);
                            }
                        });
                    } else {
                        father.find('#client-name, #client-selected').text('');
                        father.find('#email, #phone, #rfc, #address, #city, #district, #state, #country').val('');
                    }
                });

                $('#update-sale-form .accordion-header').on('click', function () {
                    const $body = $(this).next('.accordion-body');
                    $('#update-sale-form .accordion-body').not($body).slideUp();
                    $body.slideToggle();
                });

                $(document).on('click', '.invoice-value-btn-2', function () {
                    const father = $(this).closest('.wrapper');
                    const btn = $(this);
                    if (btn.hasClass('border-green-400')) {
                        btn.text('No').removeClass('border-green-400 bg-green-400 text-white').addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                        father.find('.cost').removeClass('hidden');
                        father.find('.cost-tag').text('Cost');
                        father.find('.invoice-val').val('0');
                    } else {
                        btn.text('Yes').removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white').addClass('border-green-400 bg-green-400 text-white');
                        father.find('.cost').addClass('hidden').val('');
                        father.find('.cost-tag').text('Cost: Invoice value');
                        father.find('.invoice-val').val('1');
                    }
                    window.updateWrapperUpdate(father);
                    window.updateGrandTotalUpdate();
                });

                $(document).on('click', '.tax-btn-2', function () {
                    const btn = $(this);
                    const wrapper = btn.closest('.wrapper');
                    const inputTax = wrapper.find('.has-tax');

                    if (btn.hasClass('border-blue-400')) {
                        btn.text('No').removeClass('border-blue-400 bg-blue-400 text-white').addClass('border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white');
                        inputTax.val('0');
                    } else {
                        btn.text('Yes').removeClass('border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white').addClass('border-blue-400 bg-blue-400 text-white');
                        inputTax.val('1');
                    }
                    window.updateWrapperUpdate(wrapper);
                    window.updateGrandTotalUpdate();
                });

                $(document).on('input', '.product-id', function () {
                    const father = $(this).closest('.wrapper');
                    let id = parseInt($(this).val()) || 0;
                    if (id !== 0) {
                        $.ajax({
                            type: 'GET',
                            url: `/catalogs/${id}`,
                            success: function (r) {
                                father.find('.public-product-name').val(r.product['name']);
                                father.find('.product-name').text(`Description: ${r.product['name']}`);
                                father.find('.product-sku').text(`SKU: ${r.product['sku']}`);
                                father.find('.quantity-tag').text(`Quantity (${r.product['unit']})`);
                            }
                        });
                    } else {
                        father.find('.public-product-name, .product-name, .product-sku').val('').text('');
                        father.find('.quantity-tag').text(`Quantity`);
                    }
                });

                $(document).on('input', '.cost, .quantity', function () {
                    const wrapper = $(this).closest('.wrapper');
                    window.updateWrapperUpdate(wrapper);
                    window.updateGrandTotalUpdate();
                });

                $('#update-sale-form #add-product-sales').on('click', function () {
                    const father = $('#update-sale-form');
                    const newProduct = $('#product-template-update .wrapper').clone();
                    father.find('#products-sales').append(newProduct);
                    father.find('#products-count').text(father.find('#products-sales .wrapper').length);
                });

                $(document).on('click', '.remove-btn-2', function () {
                    const father = $('#update-sale-form');
                    const wrapper = $(this).closest('.wrapper');
                    Swal.fire({
                        title: '¿Eliminar producto?',
                        text: 'Esta acción quitará el producto de la venta.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            wrapper.remove();
                            father.find('#products-count').text(father.find('#products-sales .wrapper').length);
                            window.updateGrandTotalUpdate();
                        }
                    });
                });
            }

            function updateSale() {
                $("#update-sale-form").submit(function (event) {
                    event.preventDefault();
                    var form = $('#update-sale-form')[0];
                    var data = new FormData(form);
                    var id = $('#sale-id').val();
                    data.append('_method', 'PUT');
                    $.ajax({
                        type: 'POST',
                        url: `/sales/${id}`,
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function () {
                            Swal.fire({
                                icon: 'success', title: 'Success', text: 'The sale was updated successfully.'
                            }).then((result) => {
                                if (result.isConfirmed) location.reload();
                            });
                        }
                    });
                });
            }

            updateSale();
            reactiveUpdateSale();
        });
    </script>
@endpush