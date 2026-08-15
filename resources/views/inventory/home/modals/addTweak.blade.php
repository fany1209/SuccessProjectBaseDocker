{{--
Inventario
Agregar Tweak
Fecha de creación: 12-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 17-09-2025
--}}
<x-modal id="add-tweak">
    <form class="flex flex-col items-center w-full gap-2" id="add-tweak-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Make Tweak</x-tittle-form>
            <x-toggle-switch id="type" name="type" value="Input" :checked="true" onLabel="Input" offLabel="Output" onColor="emerald-500" offColor="red-700" textColor="white" class=""/>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="product_id">Product</x-label>
                <x-input-1 required name="product_id" list="products"></x-input-1>
                <datalist id="products">
                    @foreach ($products_warehouse as $product)
                        <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                    @endforeach
                </datalist>
                <p id="product-name" class="text-sm text-gray-500"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="batch">Batch</x-label>
                <x-select-1 required disabled name="inventory_id" id="batch" class="batch">
                    <option value="">Select a batch</option>
                </x-select-1>
                <p id="available-stock" class="product-name text-sm text-gray-500">Available Stock: 0</p>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="quantity">Quantity</x-label>
                <x-input-1 required name="quantity" min="0"></x-input-1>
                <p class="text-sm text-gray-400 font-light" id="total">Total: 0</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="reason">Motivo</x-label>
                <x-select-1 required id="reason" name="reason">
                    <option value="">Selecciona un motivo</option>
                    <option value="Omisión de registro de salida">Omisión de registro de salida</option>
                    <option value="Omisión de registro de entrada">Omisión de registro de entrada</option>
                    <option value="Error de captura">Error de captura</option>
                    <option value="Devolución no procesada">Devolución no procesada</option>
                    <option value="Cruce de referencias">Cruce de referencias</option>
                    <option value="Ajuste por conteo físico">Ajuste por conteo físico</option>
                    <option value="Diferencia de recepción">Diferencia de recepción</option>
                    <option value="Merma o daño">Merma o daño</option>
                    <option value="Extravío">Extravío</option>
                    <option value="Consumo interno no registrado">Consumo interno no registrado</option>
                    <option value="Otro">Otro</option>
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="comments">Comments</x-label>
                <x-textarea-1 id="comments" name="comments" placeholder="Comentarios adicionales (opcional)"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-button-1 id="clean-tweak-form" type="reset" colorBtn="yellow">Clean Form</x-button-1>
                <x-button-1 id="close" class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            </x-wrapper-form-1>
            <x-button-1 id="save-tweak" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#add-tweak');
    const batchs = @json($batchs);
    const products_warehouse = @json($products_warehouse);
    function resetFormTweaks(){
        $('#product_id').val('');
        $('#batch').val('');
        $('#quantity').val('');
        $('#comments').val('');
        $('#batch').prop('disabled', true);
        $('#batch').html('<option value="">Select a batch</option>');
        $('#product-name').text(``);
        $('#available-stock').text(`Available Stock: 0`);
        $('#total').text(`Total: 0`);
        $('#reason').val('');
    }

    $('#clean-tweak-form').on('click',function(){
        resetFormTweaks();
    });

    function reactiveFormTweaks(){

        let togMovement = $('#type').is(':checked');        
        let product_id = null;

        $('#clean-tweak-form').on('click',function(){
            reactiveFormTweaks();
        });

        $('#type').on('change', function () {
            togMovement= $(this).is(':checked');
            resetFormTweaks();
        });

        father.on('keyup','#product_id',function(){
            id = $(this).val();
            $('#batch').empty().append('<option value="">Select a batch</option>');
            $('#available-stock').text(`Available Stock: 0`);
            $('#quantity').val('');
            $('#total').text(`Total: 0`);
            if (/^\d+$/.test(id)){
                const item = products_warehouse.find(x => x.product_id == id);
                $('#product-name').text(`${item ? item.name : ''}`);
                $('#batch').prop('disabled', item ? false : true);
                batchs.forEach(batch => {
                    if(batch.product_id == id){
                        $('#batch').append(`<option value="${batch.inventory_id}">${batch.batch}</option>`);
                    }
                });
            }else{
                $('#product-name').text(``);
                $('#batch').prop('disabled', true);
            }
        });

        father.on('change','#batch',function(){
            $('#quantity').val('');
            let inventory_id = $(this).val();
            batchs.forEach(batch => {
                if(inventory_id == batch.inventory_id){
                    let formatted = batch.stock.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 3
                    });
                    $('#available-stock').text(`Available Stock: ${formatted}`);
                }else{
                    if(inventory_id == 0){
                        $('#available-stock').text(`Available Stock: 0`);
                    }
                }
            });
        });

        $('#reason').on('change', function() {
            if($(this).val() === 'Otro') {
                $('#comments').prop('required', true);
                $('#comments').attr('placeholder', 'Especifique el motivo...');
            } else {
                $('#comments').prop('required', false);
                $('#comments').attr('placeholder', 'Comentarios adicionales (opcional)');
            }
        });
        
        $('#quantity').on('input',function(){            
            let quantity = parseFloat($(this).val() || 0);
            let value = parseFloat($('#available-stock').text().match(/[\d,.]+/)[0].replace(/,/g, ''));
            if(togMovement){
                total = value + quantity;
            }else{
                total = value - quantity;
            }
            let formatted = total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 3
            });
            if(quantity !== 0){
                $('#total').text(`Total: ${formatted}`);
            }else{
                $('#total').text(`Total: 0`);
            }
        });
    }
    function addTweak(){
        $("#add-tweak-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-tweak-form')[0];
            var data = new FormData(form);
            if(!$('#type').is(':checked')){
                data.append('type', 'Output');
            }
            
            let reason = $('#reason').val();
            let comments = data.get('comments') || '';
            if(reason && reason !== 'Otro'){
                let combined = '[' + reason + ']';
                if(comments.trim() !== '') {
                    combined += ' ' + comments.trim();
                }
                data.set('comments', combined);
            }
            
            $('#save-tweak').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/tweaks",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Tweak was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-tweak').prop('disabled',false);
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the tweak.',
                        confirmButtonText: 'OK'
                    });
                    $('#save-tweak').prop('disabled',false);
                }
            });
        });
    }
    reactiveFormTweaks();
    addTweak();
});
</script>
@endpush