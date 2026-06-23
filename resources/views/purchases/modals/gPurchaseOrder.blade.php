@php
    $imgCls  = 'h-16 w-16 bg-green-400 rounded-md p-1 object-contain pointer-events-none select-none me-2';
    $payment_method = [
        (object)[ "type" => "PUE", "description" => "Pago en una sola exhibición" ],
        (object)[ "type" => "PPD", "description" => "Pago en parcialidades o diferido" ]
    ];
    $method_payment = [
        (object)[ "code" => "01", "description" => "Efectivo" ],
        (object)[ "code" => "02", "description" => "Cheque nominativo" ],
        (object)[ "code" => "03", "description" => "Transferencia electrónica de fondos" ],
        (object)[ "code" => "04", "description" => "Tarjeta de crédito" ],
        (object)[ "code" => "05", "description" => "Monedero electrónico" ],
        (object)[ "code" => "06", "description" => "Dinero electrónico" ],
        (object)[ "code" => "08", "description" => "Vales de despensa" ],
        (object)[ "code" => "12", "description" => "Dación en pago" ],
        (object)[ "code" => "13", "description" => "Pago por subrogación" ],
        (object)[ "code" => "14", "description" => "Pago por consignación" ],
        (object)[ "code" => "15", "description" => "Condonación" ],
        (object)[ "code" => "17", "description" => "Compensación" ],
        (object)[ "code" => "23", "description" => "Novación" ],
        (object)[ "code" => "24", "description" => "Confusión" ],
        (object)[ "code" => "25", "description" => "Remisión de deuda" ],
        (object)[ "code" => "26", "description" => "Prescripción o caducidad" ],
        (object)[ "code" => "27", "description" => "A satisfacción del acreedor" ],
        (object)[ "code" => "28", "description" => "Tarjeta de débito" ],
        (object)[ "code" => "29", "description" => "Tarjeta de servicios" ],
        (object)[ "code" => "30", "description" => "Aplicación de anticipos" ],
        (object)[ "code" => "99", "description" => "Por definir" ]
    ];
    $cfdi = [
        (object)[ "cfdi" => "G01", "description" => "Adquisición de mercancías" ],
        (object)[ "cfdi" => "G02", "description" => "Devoluciones, descuentos o bonificaciones" ],
        (object)[ "cfdi" => "G03", "description" => "Gastos en general" ],
        (object)[ "cfdi" => "I01", "description" => "Construcciones" ],
        (object)[ "cfdi" => "I02", "description" => "Mobiliario y equipo de oficina por inversiones" ],
        (object)[ "cfdi" => "I03", "description" => "Equipo de transporte" ],
        (object)[ "cfdi" => "I04", "description" => "Equipo de computo y accesorios" ],
        (object)[ "cfdi" => "I05", "description" => "Dados, troqueles, moldes, matrices y herramental" ],
        (object)[ "cfdi" => "I06", "description" => "Comunicaciones telefónicas" ],
        (object)[ "cfdi" => "I07", "description" => "Comunicaciones satelitales" ],
        (object)[ "cfdi" => "I08", "description" => "Otra maquinaria y equipo" ],
        (object)[ "cfdi" => "D01", "description" => "Honorarios médicos, dentales y gastos hospitalarios" ],
        (object)[ "cfdi" => "D02", "description" => "Gastos médicos por incapacidad o discapacidad" ],
        (object)[ "cfdi" => "D03", "description" => "Gastos funerales" ],
        (object)[ "cfdi" => "D04", "description" => "Donativos" ],
        (object)[ "cfdi" => "D05", "description" => "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)" ],
        (object)[ "cfdi" => "D06", "description" => "Aportaciones voluntarias al SAR" ],
        (object)[ "cfdi" => "D07", "description" => "Primas por seguros de gastos médicos" ],
        (object)[ "cfdi" => "D08", "description" => "Gastos de transportación escolar obligatoria" ],
        (object)[ "cfdi" => "D09", "description" => "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones" ],
        (object)[ "cfdi" => "D10", "description" => "Pagos por servicios educativos (colegiaturas)" ],
        (object)[ "cfdi" => "S01", "description" => "Sin efectos fiscales" ],
        (object)[ "cfdi" => "CP01", "description" => "Pagos" ],
        (object)[ "cfdi" => "CN01", "description" => "Nómina" ]
    ];
@endphp
<x-modal id="g-purchase-order">
    <form class="flex flex-col items-center w-full gap-2" action="{{ route('purchases.gPurchaseOrder') }}" method="POST" id="g-test-form">
        @csrf
        <x-wrapper-form-1>
            <img src="{{ asset('images/purchases/01.png') }}" alt="Purchase order image" class="{{ $imgCls }}">
            <x-tittle-form class="border-b-2 border-green-700 pb-2">Purchase Order</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="id">Folio Number</x-label>
                <x-input-1 required name="id" placeholder="Enter Folio (e.g. 101)"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Supplier</x-label>
                <x-input-1 required name="supplier_id" list="suppliers" placeholder="Select a supplier"></x-input-1>
                <datalist id="suppliers">
                    @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->name }}</option>
                    @endforeach
                </datalist>
                <x-input-1 type="hidden" name="supplier_name"></x-input-1>
                <x-input-1 required disabled name="name" placeholder="Write name of new supplier" class="hidden"></x-input-1>
                <p id="supplier-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-toggle-decision id="exist" label="Exist?" buttonText="Yes" class="border-green-400 bg-green-400 text-white"></x-toggle-decision>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-2>
            <span class="accordion-header w-full text-left p-1 ps-4 rounded-md tracking-[2px] transition text-gray-700 bg-gray-200 hover:bg-blue-700 hover:text-white cursor-pointer">Information</span>
            <div class="accordion-body w-full hidden">
                <x-wrapper-form-1>
                    <x-wrapper-form-2>
                        <x-label for="email">Email</x-label>
                        <x-input-1 required disabled type="email" name="email" maxlength="150"></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>
                <x-wrapper-form-1>
                    <x-wrapper-form-2>
                        <x-label for="phone">Phone</x-label>
                        <x-input-1 required disabled name="phone" maxlength="20"></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label for="rfc">RFC</x-label>
                        <x-input-1 required disabled name="rfc" minlength="12" maxlength="12"></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>
                <x-wrapper-form-1>
                    <x-wrapper-form-2>
                        <x-label for="state">State</x-label>
                        <x-input-1 disabled name="state" maxlength="80"></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label for="address">Address</x-label>
                        <x-input-1 required disabled name="address" maxlength="200"></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>
                <x-wrapper-form-1>
                    <x-wrapper-form-2>
                        <x-label for="city">City</x-label>
                        <x-input-1 disabled name="city" maxlength="80"></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label for="district">District</x-label>
                        <x-input-1 disabled name="district" maxlength="80"></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>
            </div>
        </x-wrapper-form-2>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="contact">Contact</x-label>
                <x-input-1 required name="contact"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="delivery_time">Delivery time</x-label>
                <x-input-1 required name="delivery_time"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="delivery_date">Delivery date</x-label>
                <x-input-1 required type="date" name="delivery_date"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="guia">Guide</x-label>
                <x-input-1 name="guia" placeholder="Optional"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="cfdi">Use of CFDI</x-label>
                <x-input-1 required name="cfdi" list="descriptions"></x-input-1>
                <datalist id="descriptions">
                    @foreach ($cfdi as $item)
                    <option value="{{ $item->cfdi }}">{{ $item->description }}</option>
                    @endforeach
                </datalist>
                <p id="cfdi-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="payment_method">Payment Method</x-label>
                <x-input-1 required name="payment_method" list="payments"></x-input-1>
                <datalist id="payments">
                    @foreach ($payment_method as $item)
                    <option value="{{ $item->type }}">{{ $item->description }}</option>
                    @endforeach
                </datalist>
                <p id="payment-name" class="text-sm text-gray-500">Payment method:</p>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="method_payment">Method of payment</x-label>
                <x-input-1 required name="method_payment" list="methods"></x-input-1>
                <datalist id="methods">
                    @foreach ($method_payment as $item)
                    <option value="{{ $item->code }}">{{ $item->description }}</option>
                    @endforeach
                </datalist>
                <p id="method-name" class="text-sm text-gray-500">Method of payment:</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-tittle-form class="border-b-2 border-green-700 pb-2">Application details</x-tittle-form>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="application_date">Date</x-label>
                <x-input-1 required type="date" name="application_date"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="price">Price</x-label>
                <x-input-1 required name="price"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="applicant">Applicant</x-label>
                <x-input-1 required name="applicant"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-blue-500 text-white font-semibold px-2 py-1 rounded-md">Products</span>
                <x-button-1 type="button" colorBtn="blue" id="add_product">Add product</x-button-1>
            </x-wrapper-form-1>
            <div id="products" class="w-full">
                <div data-iva="0" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
                    <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="product_name">Product</x-label>
                            <x-input-1 required name="product_name[]" class="product_name"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-toggle-decision id="iva" label="IVA?" buttonText="Yes" class="iva-btn border-green-400 bg-green-400 text-white"></x-toggle-decision>
                            <x-input-1 type="hidden" value="1" name="iva[]" class="iva"></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="unit_price">Unit price</x-label>
                            <x-input-1 required name="unit_price[]" class="unit_price"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label for="quantity">Quantity</x-label>
                            <x-input-1 required type="number" name="quantity[]" class="quantity"></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <p class="text-md mt-2 text-gray-700 tracking-[2px]">Subtotal: <span class="import-total text-md text-white tracking-[2px] bg-gray-700 p-1 rounded-sm">$0.00</span></p>
                </div>
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-2>
            <div class="flex justify-end items-center w-full gap-2">
                <x-label>IVA 16%:</x-label>
                <span id="subtotal-iva" class="text-md text-gray-700 tracking-[2px] p-0.5 rounded-sm">$0.00</span>
            </div>
            <div class="flex justify-end items-center w-full gap-2">
                <x-label>Total:</x-label>
                <span id="grand-total" class="text-md text-white tracking-[2px] bg-green-700 p-0.5 rounded-sm">$0.00</span>
            </div>
        </x-wrapper-form-2>

        <x-wrapper-form-1>
            <x-button-1 colorBtn="red" class="close-modal" type="reset">Close</x-button-1>
            <x-button-1 id="generate-pdf" colorBtn="orange">Generate pdf</x-button-1>
        </x-wrapper-form-1>
    </form>

    <div data-iva="0" id="product_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
        <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_name">Product</x-label>
                <x-input-1 required name="product_name[]" class="product_name"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-toggle-decision id="iva" label="IVA?" buttonText="Yes" class="iva-btn border-green-400 bg-green-400 text-white"></x-toggle-decision>
                <x-input-1 type="hidden" value="1" name="iva[]" class="iva"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="unit_price">Unit price</x-label>
                <x-input-1 required name="unit_price[]" class="unit_price"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 required type="number" name="quantity[]" class="quantity"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <p class="text-md mt-2 text-gray-700 tracking-[2px]">Subtotal: <span class="import-total text-md text-white tracking-[2px] bg-gray-700 p-1 rounded-sm">$0.00</span></p>
    </div>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#g-purchase-order');
    const suppliers = @json($suppliers);
    const items_cfdi = @json($cfdi);
    const items_paymets = @json($payment_method);
    const items_methods = @json($method_payment);
    let row_index = 1;
    function reactivePurchaseOrder(){

        function updateWrapperImport(wrapper) {
            const unit_price = parseFloat(wrapper.find('.unit_price').val()) || 0;
            const quantity = parseFloat(wrapper.find('.quantity').val()) || 0;
            const iva = wrapper.find('.iva').val();
            const importValue = (iva == 1 ? 1.16 : 1) * (unit_price * quantity);
            wrapper.find('.import-total').text(`$${importValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
            const ivaLinea = iva == 1 ? unit_price * quantity * 0.16 : 0;
            wrapper.data('iva', ivaLinea);
        }

        function updateGrandTotal() {
            let total = 0;
            let subtotal_iva = 0;
            father.find('.import-total').each(function () {
                const text = $(this).text().replace('$', '').replace(',', '').trim();
                const val = parseFloat(text);
                if (!isNaN(val)) {
                    total += val;
                }
            });
            father.find('#grand-total').text(`$${total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
            father.find('.wrapper').each(function () {
                const iva = $(this).data('iva') || 0;
                subtotal_iva += iva;
            });
            father.find('#subtotal-iva').text(`$${subtotal_iva.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`);
        }
        
        father.on('input', '.unit_price, .quantity', function () {
            const wrapper = $(this).closest('.wrapper');
            updateWrapperImport(wrapper);
            updateGrandTotal();
        });

        father.on('click','.iva-btn', function(){
            const wrapper = $(this).closest('.wrapper');
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No')
                btn.removeClass('border-green-400 bg-green-400 text-white');
                btn.addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                wrapper.find('.iva').val('0');
            }else{
                btn.text('Yes')
                btn.removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                btn.addClass('border-green-400 bg-green-400 text-white');
                wrapper.find('.iva').val('1');
            }
            updateWrapperImport(wrapper);
            updateGrandTotal();
        });

        father.on('click','.remove-product', function () {
            row_index = parseInt(father.find('#products .wrapper').length);
            if(row_index != 1){
                row_index -= 1;
                $(this).closest('.wrapper').remove();
            }
            updateGrandTotal();
        });

        father.find('#add_product').on('click',function(){
            row_index += 1;
            let template = null;
            template = father.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
            father.find('#products').append(template);
        });

        father.on('keyup','#payment_method',function(){
            const type = $(this).val();
            if (type){
                const item = items_paymets.find(x => x.type == type);
                father.find('#payment-name').text(`Payment method: ${item ? item.description : ''}`);
            }else{
                father.find('#payment-name').text(`Payment method:`);
            }
        });

        father.on('keyup','#method_payment',function(){
            const code = $(this).val();
            if (code){
                const item = items_methods.find(x => x.code == code);
                father.find('#method-name').text(`Method of payment: ${item ? item.description : ''}`);
            }else{
                father.find('#method-name').text(`Method of payment:`);
            }
        });

        father.on('keyup','#cfdi',function(){
            const cfdi = $(this).val();
            if (cfdi){
                const item = items_cfdi.find(x => x.cfdi == cfdi);
                father.find('#cfdi-name').text(`${item ? item.description : ''}`);
            }else{
                father.find('#cfdi-name').text(``);
            }
        });

        father.find("#g-test-form").submit(function(event) {
            event.preventDefault();
            const form = $(this);
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'The PDF was generated successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                form.attr('target', '_blank');
                form[0].submit();
                location.reload();
            });
        });

        father.on('keyup','#name',function(){
            const name = $(this).val();
            const item = suppliers.find(x => x.name === name);
            if(item){
                father.find('#generate-pdf').prop('disabled',true);
                father.find('#supplier-name').removeClass('text-gray-500').addClass('text-red-500');
                father.find('#supplier-name').text(`The supplier alright exist`);
            }else{
                father.find('#generate-pdf').prop('disabled',false);
                father.find('#supplier-name').removeClass('text-red-500').addClass('text-gray-500');
                father.find('#supplier-name').text(``);
            }
            
        });

        father.on('click','#exist-btn', function(){
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No')
                btn.removeClass('border-green-400 bg-green-400 text-white');
                btn.addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                father.find('#supplier_id').prop('disabled',true).addClass('hidden').val('');
                father.find('#name').prop('disabled',false).removeClass('hidden').val('');
                father.find('#email, #phone, #rfc, #state, #address, #city, #district').prop('disabled',false).val('');
                father.find('#supplier-name').text(``);
            }else{
                btn.text('Yes')
                btn.removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                btn.addClass('border-green-400 bg-green-400 text-white');
                father.find('#generate-pdf').prop('disabled',false);
                father.find('#supplier_id').prop('disabled',false).removeClass('hidden').val('');
                father.find('#name').prop('disabled',true).addClass('hidden').val('');
                father.find('#email, #phone, #rfc, #state, #address, #city, #district').prop('disabled',true).val('');
                father.find('#supplier-name').removeClass('text-red-500').addClass('text-gray-500').text(``);
            }
        });

        father.on('click','.accordion-header', function () {
            const $body = $(this).next('.accordion-body');
            father.find('.accordion-body').not($body).slideUp();
            $body.slideToggle();
        });
        function getSupplierById(id) {
            return fetch(`/suppliers/${id}`)
                .then(res => res.json());
        }
        father.on('keyup','#supplier_id',function(){
            const id = $(this).val();
            if (/^\d+$/.test(id)){
                const item = suppliers.find(x => x.supplier_id == id);
                father.find('#supplier-name').text(`${item ? item.name : ''}`);
                getSupplierById(id).then(item => {
                    father.find('#supplier_name').val(item.supplier.name);
                    father.find('#email').val(item.supplier.email);
                    father.find('#phone').val(item.supplier.phone);
                    father.find('#rfc').val(item.supplier.rfc);
                    father.find('#city').val(item.supplier.city);
                    father.find('#address').val(item.supplier.address);
                    father.find('#state').val(item.supplier.state);
                    father.find('#district').val(item.supplier.district);
                });
            }else{
                father.find('#supplier-name').text(``);
            }
        });
    }
    reactivePurchaseOrder();
});
</script>
@endpush