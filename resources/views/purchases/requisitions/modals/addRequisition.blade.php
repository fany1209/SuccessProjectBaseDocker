{{--
Requisitions
Agregar Requisition
Fecha de creación: 15-10-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 20-01-2026
--}}
<x-modal id="add-requisition">
    <div class="flex flex-col items-center w-full gap-2 my-2">
        <form class="flex flex-col items-center w-full gap-2" id="add-requisition-form">
            @csrf
            <x-wrapper-form-1>
                <x-tittle-form>Add Requisition</x-tittle-form>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="applicant_name">Applicant</x-label>
                    <x-input-1 required disabled name="applicant_name" value="{{ auth()->user()->name }}"></x-input-1>
                    <x-input-1 type="hidden" name="applicant" value="{{ auth()->user()->name }}"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="department">Department</x-label>
                    <x-select-1 required name="department">
                        <option value="">Select a department</option>
                        <option value="Almacen">Almacen</option>
                        <option value="Recursos Humanos">Recursos Humanos</option>
                        <option value="Ventas">Ventas</option>
                        <option value="Compras">Compras</option>
                        <option value="Calidad">Calidad</option>
                        <option value="Sistemas">Sistemas</option>
                        <option value="Laboratorio">Laboratorio</option>
                        <option value="I+D">I+D</option>
                        <option value="TECNM">TECNM</option>

                    </x-select-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-toggle-decision id="data-sheet" label="Data Sheet?" buttonText="No" class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"></x-toggle-decision>
                <x-input-1 type="hidden" id="data_sheet" name="data_sheet" value="0"></x-input-1>

                <x-toggle-decision id="safety-sheet" label="Safety Sheet?" buttonText="No" class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"></x-toggle-decision>
                <x-input-1 type="hidden" id="safety_sheet" name="safety_sheet" value="0"></x-input-1>
            </x-wrapper-form-1>

             <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="consecutive">Folio Tabla Comparativa</x-label>
                    <x-select-1 
                        name="comparative_id" 
                        id="consecutive" 
                        :required="!auth()->user()->isAdmin()" 
                        class="bg-gray-50 font-bold text-blue-700 border-blue-200 focus:ring-blue-500">
                        <option value="">Cargando folios...</option>
                    </x-select-1>
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
                                <x-label for="description">Description</x-label>
                                <x-input-1 required name="description[]"></x-input-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>

                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label for="url">Product's URL</x-label>
                                <x-input-1 required name="url[]"></x-input-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>

                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label for="supplier">Supplier</x-label>
                                <x-input-1 required name="supplier[]"></x-input-1>
                            </x-wrapper-form-2>

                            <x-wrapper-form-2>
                                <x-label for="use">Use</x-label>
                                <x-input-1 required name="use[]"></x-input-1>
                            </x-wrapper-form-2>

                            <x-wrapper-form-2>
                                <x-label for="insumo">Insumo</x-label>
                                <x-select-1 required name="insumo[]">
                                    <option value="">Select</option>
                                    <option value="directo">Directo</option>
                                    <option value="indirecto">Indirecto</option>
                                </x-select-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>

                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label for="quantity">Quantity</x-label>
                                <x-input-1 required type="number" name="quantity[]" min="0"></x-input-1>
                            </x-wrapper-form-2>
                            <x-wrapper-form-2>
                                <x-label for="unit">Unit</x-label>
                                <x-select-1 required name="unit[]">
                                    <option value="">Select a Unit</option>
                                    <option value="Kg">Kg</option>
                                    <option value="L">L</option>
                                    <option value="Pz">Pz</option>
                                </x-select-1>
                            </x-wrapper-form-2>
                        </x-wrapper-form-1>

                        <x-wrapper-form-1>
                            <x-wrapper-form-2>
                                <x-label for="image_url">Ilustration</x-label>
                                <x-textarea-1 name="image_url[]" class="image-url"></x-textarea-1>
                            </x-wrapper-form-2>
                            <div class="flex justify-center items-center w-full gap-2">
                                <img src="" class="image-ilustration w-1/2" alt="">
                            </div>
                        </x-wrapper-form-1>
                    </div>
                </div>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
                <x-wrapper-form-1>
                    <x-button-1 class="clear-products" type="button" colorBtn="yellow">Clean Products</x-button-1>
                    <x-button-1 id="save-requisition" colorBtn="green">Finish</x-button-1>
                </x-wrapper-form-1>
            </x-wrapper-form-1>
        </form>

        <div id="product_template" class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">
            <span class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="description">Description</x-label>
                    <x-input-1 required name="description[]"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="url">Product's URL</x-label>
                    <x-input-1 required name="url[]"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="supplier">Supplier</x-label>
                    <x-input-1 required name="supplier[]"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="use">Use</x-label>
                    <x-input-1 required name="use[]"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="insumo">Insumo</x-label>
                    <x-select-1 required name="insumo[]">
                        <option value="">Select</option>
                        <option value="directo">Directo</option>
                        <option value="indirecto">Indirecto</option>
                    </x-select-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="quantity">Quantity</x-label>
                    <x-input-1 required type="number" name="quantity[]" min="0"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label for="unit">Unit</x-label>
                    <x-select-1 required name="unit[]">
                        <option value="">Select a Unit</option>
                        <option value="Kg">Kg</option>
                        <option value="L">L</option>
                        <option value="Pz">Pz</option>
                    </x-select-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="image_url">Ilustration</x-label>
                    <x-textarea-1 name="image_url[]" class="image-url"></x-textarea-1>
                </x-wrapper-form-2>
                <div class="flex justify-center items-center w-full gap-2">
                    <img src="" class="image-ilustration w-1/2" alt="">
                </div>
            </x-wrapper-form-1>
        </div>
    </div>
</x-modal>

@push('js')
<script>
$(document).ready(function(){
    const father = $('#add-requisition');
    let row_index = 1;

    function reactiveAddRequisition(){
        father.on('click','.clear-products',function(){
            row_index = 1;
            let template = father.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
            father.find('#products').empty().append(template);
        });

        father.on('input','.image-url',function(){
            const wrapper = $(this).closest('.wrapper');
            let image_url = $(this).val();
            const img = wrapper.find('.image-ilustration');
            img.attr('src', image_url);
            if (image_url.trim() !== '') img.show();
            else img.hide();
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
            let template = father.find('#product_template').clone().removeAttr('id').removeClass('hidden').show();
            father.find('#products').append(template);
        });

        father.find('#data-sheet-btn').on('click', function(){
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No')
                btn.removeClass('border-green-400 bg-green-400 text-white');
                btn.addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                father.find('#data_sheet').val('0');
            }else{
                btn.text('Yes')
                btn.removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                btn.addClass('border-green-400 bg-green-400 text-white');
                father.find('#data_sheet').val('1');
            }
        });

        father.find('#safety-sheet-btn').on('click', function(){
            const btn = $(this);
            if(btn.hasClass('border-green-400')){
                btn.text('No')
                btn.removeClass('border-green-400 bg-green-400 text-white');
                btn.addClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                father.find('#safety_sheet').val('0');
            }else{
                btn.text('Yes')
                btn.removeClass('border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white');
                btn.addClass('border-green-400 bg-green-400 text-white');
                father.find('#safety_sheet').val('1');
            }
        });
    }
    function loadComparativeFolios() {
        const selectFolio = $('#consecutive');

        $.ajax({
            url: '/get-comparative-folios',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                selectFolio.empty();
                selectFolio.append('<option value="">Seleccione un folio</option>');

                $.each(data, function(index, item) {
                    selectFolio.append(`<option value="${item.id}">${item.folio}</option>`);
                });
                
            },
            error: function(xhr) {
                console.error("Error al cargar folios:", xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        loadComparativeFolios();

        $('#consecutive').on('change', function() {
            const folio = $(this).val();
            if (!folio) return;

            $.ajax({
                url: '/get-comparative-products/' + folio,
                type: 'GET',
                dataType: 'json',
                success: function(products) {
                    const father = $('#add-requisition');
                    father.find('.clear-products').click(); // Limpiar productos existentes

                    $.each(products, function(index, product) {
                        let wrapper;
                        if (index === 0) {
                            wrapper = father.find('#products .wrapper').first();
                        } else {
                            father.find('#add_product').click();
                            wrapper = father.find('#products .wrapper').last();
                        }

                        wrapper.find('input[name="description[]"]').val(product.descripcion || '');
                        wrapper.find('input[name="url[]"]').val(product.link || '');
                        wrapper.find('input[name="supplier[]"]').val(product.proveedor || '');
                        wrapper.find('input[name="use[]"]').val(product.comentarios || '');
                        
                        // Seleccionar insumo basado en el valor
                        let insumoVal = (product.insumo || '').toLowerCase();
                        if (insumoVal.includes('directo')) insumoVal = 'directo';
                        else if (insumoVal.includes('indirecto')) insumoVal = 'indirecto';
                        wrapper.find('select[name="insumo[]"]').val(insumoVal);
                        
                        wrapper.find('input[name="quantity[]"]').val(product.cantidad || '');
                        wrapper.find('textarea[name="image_url[]"]').val(product.imagen || '').trigger('input');
                    });
                },
                error: function(xhr) {
                    console.error("Error al obtener productos:", xhr.responseText);
                }
            });
        });
    });
        function addRequisition(){
            $("#add-requisition-form").submit(function(event) {
                event.preventDefault();
                var form = $('#add-requisition-form')[0];
                var data = new FormData(form);
                $('#save-requisition').prop('disabled',true);

                $.ajax({
                    type:'POST',
                    url:"/requisitions",
                    data:data,
                    processData:false,
                    contentType:false,
                    success: function(){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'The requisition was added successfully.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) location.reload();
                        });
                        $('#save-requisition').prop('disabled',false);
                    },
                    error:function(xhr){
                        let message = 'Ocurrió un error inesperado.';
                        if (xhr.responseJSON && xhr.responseJSON.error) message = xhr.responseJSON.error;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonText: 'OK'
                        });
                        $('#save-requisition').prop('disabled',false);
                    }
                });
            });
        }

        reactiveAddRequisition();
        addRequisition();
    });
</script>
@endpush
