<section id="add-sale-blade" class="flex flex-col items-center w-full lg:w-3/4 rounded-lg bg-gray-50 shadow-md px-4 py-2 hidden">
    <div class="flex justify-between items-center w-full gap-2">
        <h2 class="my-2 text-2xl row-span-1 row-start-1 w-full border-s-4 border-green-700 ps-2">Create Sale {{ $total_sales + 1 }}</h2>
        <x-button id="close-add-sale-blade" class="bg-red-500 hover:bg-red-600 focus:bg-red-600 active:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">Close</x-button>
    </div>

    <form class="flex flex-col lg:flex-row lg:justify-center items-start w-full gap-3 mt-2" id="add-sale-form">
        @csrf
        <input type="hidden" id="sales-status" name="sales_status_id" value="1">

        <div class="flex flex-col items-center w-full lg:w-3/4">
            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="sector" class="mb-1 block font-medium text-md text-gray-700">Sale Type</label>
                    <select required id="sector" name="sector_id" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        <option value="">Select a sector</option>
                        <option value="1">Comercial</option>
                        <option value="2">Gobierno</option>
                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->sector_id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2 border-b-2 border-gray-500 pb-4">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="seller" class="mb-1 block font-medium text-md text-gray-700">Seller</label>
                    @if ($user_admin)
                        <select required id="seller" name="user_id" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            <option value="">Select a seller</option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->seller_number }}">{{ $seller->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <x-input-1 readonly name="seller" value="{{ $user_name }}"></x-input-1>
                        <x-input-1 type="hidden" id="seller" name="user_id" value="{{ $user_id }}"></x-input-1>
                    @endif
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
                <input disabled type="text" id="sale-type-input" name="term" placeholder="Example: 15 days, 30 days, other, etc." class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                <input type="hidden" id="sale-type" name="sale_type" value="Cash">
            </div>

            <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Client Information</h3>
            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="purchase-order" class="mb-1 block font-medium text-md text-gray-700">Purchase Order</label>
                    <input type="text" id="purchase-order" name="purchase_order" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="date" class="mb-1 block font-medium text-md text-gray-700">Date</label>
                    <input required type="date" id="date" name="date" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2 border-b-2 border-gray-200 pb-2">
                <div id="invoice-opt" class="flex flex-col items-start gap-1 w-full">
                    <label for="" id="invoice-tag" class="mb-1 block font-medium text-md text-gray-700">Invoice?</label>
                    <span id="invoice-btn" class="flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="invoice" class="mb-1 block font-medium text-md text-gray-700">Invoice #</label>
                    <input disabled type="text" id="invoice" name="invoice" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <div id="first-time-opt" class="flex flex-col items-start gap-1 w-full">
                    <label for="" id="first-time-tag" class="mb-1 block font-medium text-md text-gray-700">First Time?</label>
                    <span id="first-time-btn" class="flex justify-center items-center w-full transition border-2 border-green-400 bg-green-400 text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">Yes</span>
                    <input type="hidden" name="first_time" id="first-time" value="1">
                </div>
                <div id="customer-opt" class="flex flex-col items-start gap-1 w-full hidden">
                    <label for="" id="customer-tag" class="mb-1 block font-medium text-md text-gray-700">Customer?</label>
                    <span id="customer-btn" class="flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                    <input type="hidden" name="is_customer" id="is-customer" value="0">
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="name" id="client-tag" class="mb-1 block font-medium text-md text-gray-700">Prospect name</label>

                    <input required disabled type="text" id="prospect-1" name="name" maxlength="200"
                           class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2 hidden">

                    <input required type="text" id="prospect-2"
                           placeholder="Select a sector for view Prospects"
                           list="prospects" maxlength="200"
                           class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    
                    <datalist id="prospects">
                        @foreach ($prospects as $prospect)
                            <option value="{{ $prospect->prospect_id }} - {{ $prospect->name }}"></option>
                        @endforeach
                    </datalist>
                    
                    <input type="hidden" id="prospect-2-id" name="prospect_id" value="">

                    <input required disabled type="text" id="customer-1"
                           placeholder="Select a sector for view Customer"
                           list="customers" maxlength="200"
                           class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2 hidden">
                    <datalist id="customers"></datalist>
                    <input type="hidden" id="customer-1-id" name="customer_id" value="">

                    <p id="client-name" class="text-sm text-gray-500"></p>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2 mt-1 border-b-2 border-gray-500 pb-4">
                <div class="flex flex-col items-start gap-1 w-full">                    
                    <span class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Information</span>
                    <div class="accordion-body w-full hidden">
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="email" class="mb-1 block font-medium text-md text-gray-700">Email</label>
                                <input type="email" id="email" name="email" maxlength="150" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="phone" class="mb-1 block font-medium text-md text-gray-700">Phone</label>
                                <input type="text" id="phone" name="phone" maxlength="20" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="rfc" class="mb-1 block font-medium text-md text-gray-700">RFC</label>
                                <input type="text" id="rfc" name="rfc" maxlength="13" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="address" class="mb-1 block font-medium text-md text-gray-700">Address</label>
                                <input type="text" id="address" name="address" maxlength="200" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="city" class="mb-1 block font-medium text-md text-gray-700">City</label>
                                <input type="text" id="city" name="city" maxlength="80" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="district" class="mb-1 block font-medium text-md text-gray-700">District</label>
                                <input type="text" id="district" name="district" maxlength="80" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="state" class="mb-1 block font-medium text-md text-gray-700">State</label>
                                <input type="text" id="state" name="state" maxlength="80" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="country" class="mb-1 block font-medium text-md text-gray-700">Country</label>
                                <input type="text" id="country" name="country" maxlength="100" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Products</h3>
                <span id="add-product-sales" class="px-2 py-1 rounded-md text-white tracking-[2px] cursor-pointer bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Product +</span>
            </div>

            <div id="products-sales" class="flex flex-col items-center w-full gap-2">
                <div class="wrapper flex flex-col items-start w-full gap-2 rounded-md p-2 border-2 border-dashed border-gray-300 relative">
                    <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="product-id" class="mb-1 block font-medium text-md text-gray-700">Product</label>
                                <input required type="text" name="product_id[]" list="products" class="product-id w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                <datalist id="products">
                                    @foreach ($products as $product)
                                        <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="public-batch" class="mb-1 block font-medium text-md text-gray-700">Public batch</label>
                                <input type="text" id="public-batch" name="public_batch[]" maxlength="100" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="quantity" class="quantity-tag mb-1 block font-medium text-md text-gray-700">Quantity</label>
                                <input required type="text" value="0" name="quantity[]" class="quantity w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="cost" class="cost-tag mb-1 block font-medium text-md text-gray-700">Cost</label>
                                <input type="number" value="0" step="any" min="0" name="cost[]" class="cost w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label class="mb-1 block font-medium text-md text-gray-700">Invoice Value?</label>
                                <select name="invoice_val[]" class="invoice-val-select w-full rounded-lg border border-gray-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                    <option value="0">MSVC</option>
                                    <option value="1">Valor Factura</option>
                                </select>
                            </div>

                            <div class="flex flex-col items-start gap-1 w-full">
                                <label class="mb-1 block font-medium text-md text-gray-700">IVA</label>
                                <span class="tax-btn flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                                <input type="hidden" class="has-tax" name="has_tax[]" value="0">
                            </div>

                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="public-product-name" class="mb-1 block font-medium text-md text-gray-700">Invoice product name</label>
                                <input type="text" name="public_product_name[]" maxlength="100" class="public-product-name w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center w-full gap-2">
                        <div class="flex justify-between items-center gap-2">
                            <p class="product-name text-sm text-gray-500"></p>
                            <p class="product-sku text-sm text-gray-500"></p>
                        </div>
                        <p class="text-md text-gray-700 tracking-[2px]">Subtotal: <span class="import-total text-md text-white tracking-[2px] bg-gray-700 p-1 rounded-sm">$00.00</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col justify-between items-end w-full gap-4 lg:w-1/4">
            <div class="flex flex-col items-center w-full gap-4 shadow-md rounded-md px-4 py-1">
                <h3 class="my-2 text-xl text-left border-b-2 border-green-700 pb-1 px-2">Sale Summary</h3>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Sector</p>
                    <span id="sector-selected" class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md"></span>
                </div>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Client name</p>
                    <span id="client-selected" class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md"></span>
                </div>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Seller name</p>
                    <span id="seller-selected" class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md">{{ $user_admin ? '' : $user_name }}</span>
                </div>
                <div class="flex justify-between items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Products</p>
                    <span id="products-count" class="tracking-[2px] text-md bg-gray-700 text-white px-1 rounded-md">1</span>
                </div>
                <div class="flex flex-col items-start w-full gap-2 border-b-2 border-dashed border-gray-400 pb-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Payment method</p>
                    <span id="payment-method-summary" class="tracking-[2px] transition text-md bg-green-700 text-white px-1 rounded-md"><i class="ri-cash-line text-lg"></i>Cash</span>
                </div>
                <div class="flex justify-between items-start w-full gap-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">IVA (16%)</p>
                    <span id="iva-total-amount" class="tracking-[2px] text-md text-gray-700 px-1 rounded-md">$0.00</span>
                </div>
                <div class="flex justify-between items-start w-full gap-2">
                    <p class="text-lg text-gray-700 tracking-[2px]">Total</p>
                    <span id="grand-total" class="tracking-[2px] text-md bg-green-500 text-white px-1 rounded-md">$0.00</span>
                </div>
            </div>

            <div class="flex justify-end items-start w-full gap-2">
                <x-button type="button" id="reset-sale-form" class="bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">Reset Sale</x-button>
                <x-button type="submit" id="save-sale" class="bg-blue-500 hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Finish</x-button>
            </div>
        </div>
    </form>

    <div id="product-template" class="hidden">
        <div class="wrapper flex flex-col items-start w-full gap-2 rounded-md p-2 border-2 border-dashed border-gray-300 relative">
            <span class="remove-btn absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs cursor-pointer">X</span>

            <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                <div class="flex justify-between items-center w-full gap-2">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="product-id" class="mb-1 block font-medium text-md text-gray-700">Product<span></span></label>
                        <input required type="text" name="product_id[]" list="products" class="product-id w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>

                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="public-batch" class="mb-1 block font-medium text-md text-gray-700">Public batch</label>
                        <input type="text" name="public_batch[]" maxlength="100" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>

                <div class="flex justify-between items-center w-full gap-2">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="quantity" class="quantity-tag mb-1 block font-medium text-md text-gray-700">Quantity</label>
                        <input required type="text" value="0" name="quantity[]" class="quantity w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="cost" class="cost-tag mb-1 block font-medium text-md text-gray-700">Cost</label>
                        <input type="number" value="0" step="any" min="0" name="cost[]" class="cost w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                <div class="flex justify-between items-center w-full gap-2">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label class="mb-1 block font-medium text-md text-gray-700">Invoice Value?</label>
                        <select name="invoice_val[]" class="invoice-val-select w-full rounded-lg border border-gray-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            <option value="0">MSVC</option>
                            <option value="1">Valor Factura</option>
                        </select>
                    </div>

                    <div class="flex flex-col items-start gap-1 w-full">
                        <label class="mb-1 block font-medium text-md text-gray-700">IVA</label>
                        <span class="tax-btn flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                        <input type="hidden" class="has-tax" name="has_tax[]" value="0">
                    </div>

                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="public-product-name" class="mb-1 block font-medium text-md text-gray-700">Invoice product name</label>
                        <input type="text" name="public_product_name[]" maxlength="100" class="public-product-name w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <div class="flex justify-between items-center gap-2">
                    <p class="product-name text-sm text-gray-500"></p>
                    <p class="product-sku text-sm text-gray-500"></p>
                </div>
                <p class="text-md text-gray-700 tracking-[2px]">Subtotal: <span class="import-total text-md text-white tracking-[2px] bg-gray-700 p-1 rounded-sm">$00.00</span></p>
            </div>
        </div>
    </div>
</section>

@push('js')
<script>
$(function(){
    const prospects = @json($prospects);
    const customers = @json($customers);
    const user_admin = @json($user_admin);

    function addSale(){
        $("#add-sale-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-sale-form')[0];
            var data = new FormData(form);

            if(user_admin){
                data.append('seller', $('#seller option:selected').text());
            }

            $('#save-sale').prop('disabled',true);

            $.ajax({
                type:'POST',
                url:"/sales",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The sale was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-sale').prop('disabled',false);
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
                    $('#save-sale').prop('disabled',false);
                }
            });
        });
    }

    function reactiveAddSale(){

        $('#close-add-sale-blade, #reset-sale-form').on('click',function(){
            $('#add-sale-form #sector,#folio,#seller,#purchase-order,#date, #invoice, #email, #phone, #rfc, #address, #city, #address, #state, #country, #district').val('');
            $('#add-sale-form #sector-selected,#seller-selected,#client-selected,#client-name').text('');
            $('#client-tag').text('Prospect name');
            $('#products-count').text('1');
            $('#grand-total').text('$0.00');
            $('#iva-total-amount').text('$0.00');

            $('#payment-metod').prop('checked', true).trigger('change');

            $('#invoice-btn, #customer-btn').text('No');
            $('#invoice-btn, #customer-btn').removeClass('border-green-400 bg-green-400 text-white');
            $('#invoice-btn, #customer-btn').addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');

            $('#first-time-btn').text('Yes');
            $('#first-time-btn').removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
            $('#first-time-btn').addClass('border-green-400 bg-green-400 text-white');
            $('#prospect-2').removeClass('hidden').prop('disabled', false);
            $('#prospect-1, #customer-1, #customer-opt').addClass('hidden');
            $('#invoice, #customer-1').prop('disabled',true);
            $('#email, #phone, #rfc, #address, #city, #address, #state, #country, #district').prop('disabled',false);

            $('#prospect-1').val('');
            $('#prospect-2').val('');
            $('#customer-1').val('');
            $('#prospect-2-id').val('');
            $('#customer-1-id').val('');
            $('datalist#customers').empty();
            
            $('#products-sales').html(`
                <div class="wrapper flex flex-col items-start w-full gap-2 rounded-md p-2 border-2 border-dashed border-gray-300 relative">
                    <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="product-id" class="mb-1 block font-medium text-md text-gray-700">Product</label>
                                <input required type="text" name="product_id[]" list="products" class="product-id w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="public-batch" class="mb-1 block font-medium text-md text-gray-700">Public batch</label>
                                <input type="text" name="public_batch[]" maxlength="100" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="quantity" class="quantity-tag mb-1 block font-medium text-md text-gray-700">Quantity</label>
                                <input required type="text" value="0" name="quantity[]" class="quantity w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label for="cost" class="cost-tag mb-1 block font-medium text-md text-gray-700">Cost</label>
                                <input type="number" value="0" step="any" min="0" name="cost[]" class="cost w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col lg:flex-row lg:justify-between items-center w-full gap-2">
                        <div class="flex justify-between items-center w-full gap-2">
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label class="mb-1 block font-medium text-md text-gray-700">Invoice Value?</label>
                                <select name="invoice_val[]" class="invoice-val-select w-full rounded-lg border border-gray-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                    <option value="0">MSVC</option>
                                    <option value="1">Valor Factura</option>
                                </select>
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label class="mb-1 block font-medium text-md text-gray-700">IVA</label>
                                <span class="tax-btn flex justify-center items-center w-full transition border-2 border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white p-1 tracking-[3px] text-lg cursor-pointer rounded-md">No</span>
                                <input type="hidden" class="has-tax" name="has_tax[]" value="0">
                            </div>
                            <div class="flex flex-col items-start gap-1 w-full">
                                <label class="mb-1 block font-medium text-md text-gray-700">Invoice product name</label>
                                <input type="text" name="public_product_name[]" maxlength="100" class="public-product-name w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center w-full gap-2">
                        <div class="flex justify-between items-center gap-2">
                            <p class="product-name text-sm text-gray-500"></p>
                            <p class="product-sku text-sm text-gray-500"></p>
                        </div>
                        <p class="text-md text-gray-700 tracking-[2px]">Subtotal: <span class="import-total text-md text-white tracking-[2px] bg-gray-700 p-1 rounded-sm">$00.00</span></p>
                    </div>
                </div>
            `);
        });

        $('#add-sale-form #payment-metod').change(function () {
            if ($(this).is(':checked')) {
                $('#sale-type-input').prop('disabled',true).val('');
                $('#sale-type').val('Cash');
                $('#payment-method-summary').removeClass('bg-orange-700').addClass('bg-green-700');
                $('#payment-method-summary').html('<i class="ri-cash-line text-lg"></i>Cash');
            } else {
                $('#payment-method-summary').html('<i class="ri-bank-card-fill"></i>Credit');
                $('#payment-method-summary').removeClass('bg-green-700').addClass('bg-orange-700');
                $('#sale-type-input').prop('disabled',false);
                $('#sale-type').val('Credit');
            }
        });

        $('#add-sale-form #seller').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            $('#seller-selected').text(selectedText);
        });

        $('#add-sale-form #sector').on('change', function() {
            const value = $(this).val();
            const selectedText = $(this).find('option:selected').text();
            $('#sector-selected').text(selectedText);

            $('#customer-1').val('');
            $('#prospect-1').val('');
            $('#prospect-2').val('');
            $('#customer-1-id').val('');
            $('#prospect-2-id').val('');
            $('#client-name').text('');
            $('#client-selected').text('');

            $('datalist#customers').empty();
            customers.forEach(customer => {
                if(value == customer['sector_id']){
                    const id = customer['customer_id'];
                    const name = customer['name'] || '';
                    $('datalist#customers').append(`<option value="${id} - ${name}"></option>`);
                }
            });
        });

        $('#close-add-sale-blade').on('click',function(){
            $('#add-sale-blade').addClass('hidden');
        });

        $('#add-sale-form #invoice-btn').on('click',function () {
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No').removeClass('border-green-400 bg-green-400 text-white').addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                $('#invoice').val('').prop('disabled',true);
            }else{
                btn.text('Yes').removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white').addClass('border-green-400 bg-green-400 text-white');
                $('#invoice').prop('disabled',false);
            }
        });

        $('#add-sale-form #first-time-btn').on('click',function () {
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No').removeClass('border-green-400 bg-green-400 text-white').addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                $('#first-time').val('0');
                $('#customer-opt').removeClass('hidden');
                $('#prospect-1').removeClass('hidden').prop('disabled',false); 
                $('#prospect-2').addClass('hidden').prop('disabled',true).val('');
                $('#email, #phone, #rfc, #address, #city, #district, #state, #country').val('');
                $('#client-name, #client-selected').text('');
                $('#prospect-2-id').val('');
            }else{
                btn.text('Yes').removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white').addClass('border-green-400 bg-green-400 text-white');
                $('#first-time').val('1');
                $('#customer-opt').addClass('hidden');
                $('#customer-1').addClass('hidden').prop('disabled',true);
                $('#prospect-1').addClass('hidden').prop('disabled',true).val('');
                $('#prospect-2').removeClass('hidden').prop('disabled',false);
                $('#email, #phone, #rfc, #address, #city, #district, #state, #country').prop('disabled',false);
                $('#customer-1').val('');
                $('#customer-1-id').val('');
                $('#email, #phone, #rfc, #address, #city, #district, #state, #country').val('');
                $('#client-name, #client-selected').text('');
            }
        });

        $('#add-sale-form #customer-btn').on('click',function () {
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No').removeClass('border-green-400 bg-green-400 text-white').addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                $('#prospect-1').prop('disabled',false).removeClass('hidden');
                $('#customer-1').prop('disabled',true).addClass('hidden');
                $('#is-customer').val('0');
                $('#customer-1').val('');
                $('#customer-1-id').val('');
                $('#client-tag').text('Prospect name');
                $('#client-name, #client-selected').text('');
            }else{
                btn.text('Yes').removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white').addClass('border-green-400 bg-green-400 text-white');
                $('#customer-1').prop('disabled',false).removeClass('hidden');
                $('#prospect-1').prop('disabled',true).addClass('hidden').val('');
                $('#is-customer').val('1');
                $('#client-tag').text('Customer name');
                $('#client-name, #client-selected').text('');
            }
        });

        $('#add-sale-form #prospect-1').on('input',function(){
            $('#client-selected').text($(this).val());
        });

        $('#add-sale-form #prospect-2').on('input',function(){
            const raw = ($(this).val() || '').trim();
            const id = parseInt(raw.split(' - ')[0], 10) || 0;
            $('#prospect-2-id').val(id ? String(id) : '');
            if(id !== 0){
                $.ajax({
                    type: 'GET',
                    url: `/prospects/${id}`,
                    success: function(r){
                        $('#client-name, #client-selected').text(r.prospect['name']);
                        $('#email').val(r.prospect['email']);
                        $('#phone').val(r.prospect['phone']);
                        $('#rfc').val(r.prospect['rfc']);
                        $('#address').val(r.prospect['address']);
                        $('#city').val(r.prospect['city']);
                        $('#district').val(r.prospect['district']);
                        $('#state').val(r.prospect['state']);
                        $('#country').val(r.prospect['country']);
                    },
                    error: function(){
                        $('#prospect-2-id').val('');
                        Swal.fire({ icon: 'warning', title: 'Not Found', text: 'The requested prospect could not be found.', confirmButtonText: 'OK' });
                    }
                });
            }else{
                $('#client-name, #client-selected').text('');
                $('#email, #phone, #rfc, #address, #city, #district, #state, #country').val('');
            }
        });

        $('#add-sale-form #customer-1').on('input',function(){
            const raw = ($(this).val() || '').trim();
            const id = parseInt(raw.split(' - ')[0], 10) || 0;
            $('#customer-1-id').val(id ? String(id) : '');
            if(id !== 0){
                $.ajax({
                    type: 'GET',
                    url: `/customers/${id}`,
                    success: function(r){
                        $('#client-name, #client-selected').text(r.customer['name']);
                        $('#email').val(r.customer['email']);
                        $('#phone').val(r.customer['phone']);
                        $('#rfc').val(r.customer['rfc']);
                        $('#address').val(r.customer['address']);
                        $('#city').val(r.customer['city']);
                        $('#district').val(r.customer['district']);
                        $('#state').val(r.customer['state']);
                        $('#country').val(r.customer['country']);
                    },
                    error: function(){
                        $('#customer-1-id').val('');
                        Swal.fire({ icon: 'warning', title: 'Not Found', text: 'The requested customer could not be found.', confirmButtonText: 'OK' });
                    }
                });
            }else{
                $('#client-name, #client-selected').text('');
                $('#email, #phone, #rfc, #address, #city, #district, #state, #country').val('');
            }
        });

        $('#add-sale-form .accordion-header').on('click', function () {
            const $body = $(this).next('.accordion-body');
            $('.accordion-body').not($body).slideUp();
            $body.slideToggle();
        });

        $(document).on('change', '.invoice-val-select', function () {
            const father = $(this).closest('.wrapper');
            const val = $(this).val();
            if (val === '1') {
                father.find('.cost').addClass('hidden').val('0');
                father.find('.cost-tag').text('Cost: Invoice value');
                father.find('.import-total').text('$00.00');
            } else {
                father.find('.cost').removeClass('hidden');
                father.find('.cost-tag').text('Cost');
            }
            updateWrapperImport(father);
            updateGrandTotal();
        });

        $(document).on('click', '.tax-btn', function () {
            const btn = $(this);
            const wrapper = btn.closest('.wrapper');
            const inputTax = wrapper.find('.has-tax');

            if(btn.hasClass('border-blue-400')){
                btn.text('No').removeClass('border-blue-400 bg-blue-400 text-white').addClass('border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white');
                inputTax.val('0');
            } else {
                btn.text('Yes').removeClass('border-gray-400 hover:bg-blue-300 text-gray-700 hover:text-white').addClass('border-blue-400 bg-blue-400 text-white');
                inputTax.val('1');
            }
            updateWrapperImport(wrapper);
            updateGrandTotal();
        });

        $(document).on('input','.product-id',function(){
            const father = $(this).closest('.wrapper');
            let id = parseInt($(this).val()) || 0;
            if(id !== 0){
                $.ajax({
                    type: 'GET',
                    url: `/catalogs/${id}`,
                    success: function(r){
                        father.find('.public-product-name').val(r.product['name']);
                        father.find('.product-name').text(`Description: ${r.product['name']}`);
                        father.find('.product-sku').text(`SKU: ${r.product['sku']}`);
                        father.find('.quantity-tag').text(`Quantity (${r.product['unit']})`);
                    }
                });
            }else{
                father.find('.public-product-name').val('');
                father.find('.product-name').text('');
                father.find('.product-sku').text('');
                father.find('.quantity-tag').text(`Quantity`);
            }
        });

        function updateWrapperImport(wrapper) {
            const cost = parseFloat(wrapper.find('.cost').val()) || 0;
            const quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
            const hasTax = wrapper.find('.has-tax').val() === '1';
            
            let rowSubtotal = cost * quantity;
            if (hasTax) {
                rowSubtotal = rowSubtotal * 1.16; 
            }
            
            wrapper.find('.import-total').text(`$${rowSubtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
        }

        function updateGrandTotal() {
            let subtotalGeneral = 0;
            let totalIva = 0;

            $('.wrapper').each(function () {
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
            
            $('#iva-total-amount').text(`$${totalIva.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
            $('#grand-total').text(`$${totalFinal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
        }

        $(document).on('input', '.cost, .quantity', function () {
            const wrapper = $(this).closest('.wrapper');
            updateWrapperImport(wrapper);
            updateGrandTotal();
        });

        $('#add-sale-form #add-product-sales').on('click', function () {
            const father = $('#add-sale-form');
            const newProduct = $('#product-template .wrapper').clone();
            let count = father.find('#products-sales .wrapper');
            father.find('#products-count').text(count.length + 1);
            father.find('#products-sales').append(newProduct);
        });

        $(document).on('click', '.remove-btn', function () {
            const father = $('#add-sale-form');
            $(this).closest('.wrapper').remove();
            let count = father.find('#products-sales .wrapper');
            father.find('#products-count').text(count.length);
            updateGrandTotal();
        });
    }

    reactiveAddSale();
    addSale();
});
</script>
@endpush