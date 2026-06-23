{{--
Warehouse
Warehouse (do Operation)
Fecha de creación: 26-07-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 02-09-25
--}}
<!-- Modal Operation -->
<x-modal id="do-operation">
    <div class="flex justify-between items-center w-full my-2">
        <h2 class="my-2 text-2xl row-span-1 row-start-1 w-1/2">Do Operation</h2>
        <div class="flex flex-col items-start w-1/2">
            <label for="" class="mb-1 block font-medium text-sm text-gray-700">Select a warehouse</label>
            <select id="current-warehouse" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                <option value="">None</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex flex-col justify-center items-center bg-gray-50 w-full my-2 p-2 rounded-md gap-2">
        <div class="mb-1 w-full">
            <label for="location" class="mb-1 block font-medium text-md text-gray-700">Locations</label>
            <select id="location" name="location" class="location w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                <option value="">Select a warehouse</option>
            </select>
        </div>
        <div class="flex justify-between items-center w-full rounded-md border border-gray-700 bg-white text-gray-700 font-bold py-1 px-2">
            <h2>PRODUCTS</h2>
            <div class="flex justify-between items-center gap-2">
                <p class="no-items text-white text-md font-bold rounded-lg bg-green-500 pb-1 px-3">1</p>
                <button class="add-item text-white text-xl font-bold w-8 h-8 rounded-lg bg-green-500 hover:bg-green-400 pb-1">+</button>
            </div>
        </div>
    </div>
    <div class="forms flex flex-col justify-start items-start gap-2 h-[300px] w-full overflow-y-auto border p-2 bg-white rounded-md">
        <div data-index="1" class="form-item flex flex-col items-center w-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
            <div class="flex justify-between items-center gap-2 w-full my-1">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label class="concept block mb-1 text-sm font-medium text-gray-700">Concept</label>
                    <input type="text" name="concept" list="concepts" class="concept w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    <datalist id="concepts"></datalist>
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="" class="product text-sm font-medium text-gray-700">Product</label>
                    <input type="text" name="product" list="products-1" class="product w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    <datalist id="products-1"></datalist>
                </div>
            </div>
            <div class="flex justify-between items-center gap-2 w-full my-1">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label class="batch block mb-1 text-sm font-medium text-gray-700">Batch</label>
                    <select name="batch" class="batch-1 w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2"></select>
                </div>
            </div>
            <div class="flex justify-between items-center gap-2 w-full my-1">
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="" class="text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" min="0" name="quantity" class="quantity-1 w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
                <div class="flex flex-col items-start gap-1 w-full">
                    <label for="" class="total text-sm font-medium text-gray-700">Weight per Unit</label>
                    <input type="number" min="0" name="weight-unit" class="weight-unit w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                </div>
            </div>
        </div>
    </div>
    <div class="flex justiify-between items-center w-full mt-1">
        <x-button class="close-modal bg-red-500 hover:bg-red-400">Close</x-button>
        <div class="flex justify-end items-center w-full gap-2">
            <x-button class="clean-form-op bg-yellow-500 hover:bg-yellow-400">Clean form</x-button>
            <x-button class="create-op bg-green-500 hover:bg-green-400">Create</x-button>
        </div>
    </div>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    const dOpModal = $('#do-op-modal');
    const slctWarehouse = $('#current-warehouse');
    const listConcept = $('datalist#concepts');
    const slctLocation = $('.location');
    const elmForm = $('#form-operation');
    let items_count = 1;

    function doOperation(){
        $('.create-op').on('click',function(){
            let products = [];
            let location = parseInt($('select[name="location"]').val());
            const noProducts = parseInt($('.no-items').text());
            $('.forms .form-item').each(function(){
                let stock = parseFloat($(this).find('.batch').text().trim().match(/[\d,.]+/)?.[0].replace(/,/g, '') || 0);
                let concept = $(this).find('input[name="concept"]').val();
                let batch = $(this).find('select[name="batch"]').val();
                let quantity = $(this).find('input[name="quantity"]').val();
                let weight_unit = $(this).find('input[name="weight-unit"]').val();
                total = parseFloat(quantity * weight_unit);
                if(stock<total){
                    $(this).find('.total').addClass('text-red-500');
                }else{
                    $(this).find('.total').removeClass('text-red-500');
                    if(batch!==null&&concept.trim() !== ''&&!isNaN(concept.trim())&&quantity.trim() !== ''&&!isNaN(quantity.trim())&&weight_unit.trim() !== ''&&!isNaN(weight_unit.trim())){
                        quantityRaw = parseFloat(quantity);
                        weight_unitRaw = parseFloat(weight_unit);
                        products.push({concept:concept,inventory_id:batch,quantity:quantityRaw,weight_unit:weight_unitRaw});
                    }
                }
            });
            if(location === ''||products.length !== noProducts){
                Swal.fire({
                    icon: 'error',
                    title: 'Error al registrar operación',
                    text: 'Revisa que los datos sean correctos y no estén incompletos',
                    confirmButtonText: 'Entendido'
                });
            }else{
                $.ajax({
                url: "{{ route('warehouse.doOperation') }}",
                method: 'POST',
                data:{
                    location: location,
                    products: products
                },
                success: function(response) {
                    location.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro exitoso',
                        text: 'La operación se completó correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    cleanFormOp();
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', status, error);
                }
                });
            }
        });
    }
    function cleanFormOp(){
        items_count = 1;
        $('.no-items').text('1');
        $('#current-warehouse').prop('selectedIndex', 0);
        $('#location').empty();
        $('.forms').empty();
        $('.forms').append(`
            <div data-index="1" class="form-item flex flex-col items-center w-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
                <div class="flex justify-between items-center gap-2 w-full my-1">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label class="concept block mb-1 text-sm font-medium text-gray-700">Concept</label>
                        <input type="text" name="concept" list="concepts" class="concept w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        <datalist id="concepts"></datalist>
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="" class="product text-sm font-medium text-gray-700">Product</label>
                        <input type="text" name="product" list="products-1" class="product w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        <datalist id="products-1"></datalist>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-2 w-full my-1">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label class="batch block mb-1 text-sm font-medium text-gray-700">Batch</label>
                        <select name="batch" class="batch-1 w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2"></select>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-2 w-full my-1">
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="" class="text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" min="0" name="quantity" class="quantity-1 w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                    <div class="flex flex-col items-start gap-1 w-full">
                        <label for="" class="total text-sm font-medium text-gray-700">Weight per Unit</label>
                        <input type="number" min="0" name="weight-unit" class="weight-unit w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    </div>
                </div>
            </div>
        `);
    }    

    function calculatedTotal(){
        $('.forms').on('input','.weight-unit',function(){
            let item = $(this).closest('.form-item');
            let index = item.data('index');
            let quantityRaw = parseInt(item.find(`.quantity-${index}`).val());
            let quantity = isNaN(quantityRaw) ? 0 : quantityRaw;
            let valueRaw = parseInt($(this).val());
            let value = isNaN(valueRaw) ? 0 : valueRaw;
            let total = value * quantity;
            item.find(`label.total`).text(`Weight per Unit (Total: ${total})`)
        });
    }

    function fillBatchPerProduct(batchs){
        $('.forms').on('input','.product',function(){
            let item = $(this).closest('.form-item');
            let index = item.data('index');
            let product = parseInt(item.find('input.product').val());
            let select = item.find(`.batch-${index}`);
            let value = item.find('label.product');
            let infoStock = item.find('label.batch');
            select.empty();
            select.append(`<option value=""> Select a batch </option>`);
            batchs.forEach(batch => {
                if(product == batch.product_id){
                    value.text('Product: '+batch.name);
                    select.append(`<option value="${batch.inventory_id}">${batch.batch}</option>`);
                }
            });
            select.off('change').on('change',function(){
                batchs.forEach(batch => {
                    if($(this).val() == batch.inventory_id){
                        if(batch.inv_wh <= 0){
                            infoStock.text(`Batch (Available stock: Out of Stock)`);
                            if(batch.inv_wh === null){
                                let formattedStock = batch.stock.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                                });
                                infoStock.text(`Batch (Available stock: ${formattedStock})`);
                            }
                        }else{
                            let formattedStock = batch.inv_wh.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                            });
                            infoStock.text(`Batch (Available stock: ${formattedStock})`);
                        }
                    }
                });
            });
        })
    }

    function fillProductsPerConcept(products){
        $(document).on('input','.concept',function(){
            let item = $(this).closest('.form-item');
            let index = item.data('index');
            let concept = parseInt(item.find('input.concept').val());
            let datalist = item.find(`#products-${index}`);
            let value = item.find('label.concept');      
            datalist.empty();
            products.forEach(product => {
                if(concept == product.concept_id){
                    value.text('Concept: '+product.cName);
                    datalist.append(`<option value="${product.product_id}">${product.name}</option>`);
                }
            });
        })
    }

    function fillLocations(locations){
        slctWarehouse.on('change',function(){
            let warehouse = $(this).val();
            slctLocation.empty();
            slctLocation.append('<option value="">Select a Location</option>')
            locations.forEach(location => {
                if(warehouse == location.warehouse_id){
                    slctLocation.append(`<option value="${location.location_id}">${location.name}</option>`);
                }
            });
        });
    }

    function getData(){
        $.ajax({
            url: "{{ route('warehouse.getData') }}",
            method: 'GET',
            success: function(response) {
                fillLocations(response.locations);
                const datalist = $('#concepts');
                datalist.empty();
                response.concepts.forEach(concept => {
                    datalist.append(`<option value="${concept.concept_id}">${concept.name}</option>`)
                });  
                fillProductsPerConcept(response.products);
                fillBatchPerProduct(response.batchs);
                calculatedTotal();
            },
            error: function(xhr, status, error) {
                alert('Error al cargar los datos: ' + error);
            }
        });
    }

    function deleteFormItem(){
        $('.forms').on('click','.close-item',function(){
            $(this).closest('.form-item').remove();
            if (parseInt($('.no-items').text()) <= 1){
                $('.no-items').text('1');
            }else{
                items_count -= 1;
                $('.no-items').text(items_count);
            }
        });
        $('.close-item').on('click', function () {
            $('.forms').children('.form-item').last().remove();
        });
    }

    function addFormItem(){
        $('.add-item').on('click',function(){
            items_count +=1 ;
            $('.no-items').text(items_count);
            $('.forms').append(`
                <div data-index="${items_count}" class="form-item flex flex-col items-center w-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md relative">
                    <button class="close-item absolute top-1 right-1 text-white text-xl font-bold w-8 h-8 rounded-full bg-red-500 hover:bg-red-400 pb-1">x</button>
                    <div class="flex justify-between items-center gap-2 w-full my-1">
                        <div class="flex flex-col items-start gap-1 w-full">
                            <label class="concept block mb-1 text-sm font-medium text-gray-700">Concept</label>
                            <input type="text" name="concept" list="concepts" class="concept w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            <datalist id="concepts"></datalist>
                        </div>
                        <div class="flex flex-col items-start gap-1 w-full">
                            <label for="" class="product text-sm font-medium text-gray-700">Product</label>
                            <input type="text" name="product" list="products-${items_count}" class="product w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                            <datalist id="products-${items_count}"></datalist>
                        </div>
                    </div>
                    <div class="flex justify-between items-center gap-2 w-full my-1">
                        <div class="flex flex-col items-start gap-1 w-full">
                            <label class="batch block mb-1 text-sm font-medium text-gray-700">Batch</label>
                            <select name="batch" class="batch-${items_count} w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2"></select>
                        </div>
                    </div>
                    <div class="flex justify-between items-center gap-2 w-full my-1">
                        <div class="flex flex-col items-start gap-1 w-full">
                            <label for="" class="text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" min="0" name="quantity" class="quantity-${items_count} w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        </div>
                        <div class="flex flex-col items-start gap-1 w-full">
                            <label for="" class="total text-sm font-medium text-gray-700">Weight per Unit</label>
                            <input type="number" min="0" name="weight-unit" class="weight-unit w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                        </div>
                    </div>
                </div>
            `);
        });
    }

    function cleanDoOperation(){
        $(document).on('click','.clean-form-op',function(){
            cleanFormOp();
            getData();
        });
        $(document).on('click','.close-modal',function(){
            cleanFormOp();
        });
    }

    dOpModal.off('click').on('click',function(){
        getData();
    });

    doOperation();
    cleanDoOperation();
    addFormItem();
    deleteFormItem();
});
</script>
@endpush