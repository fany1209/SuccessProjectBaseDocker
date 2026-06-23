{{-- 
Requisitions
Editar Requisition
Fecha de creación: 16-10-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 20-01-2026
--}}

<x-modal id="edit-requisition">
    <div class="flex flex-col items-center w-full gap-2 my-2">

        <form class="flex flex-col items-center w-full gap-2" id="edit-requisition-form">
            @csrf

            <x-wrapper-form-1>
                <x-tittle-form>Edit Requisition</x-tittle-form>
                <x-input-1 type="hidden" id="req_id" name="req_id"></x-input-1>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Applicant</x-label>
                    <x-input-1 disabled name="applicant_name"></x-input-1>
                    <x-input-1 type="hidden" name="applicant"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label>Department</x-label>
                    <x-select-1 required name="department" id="department">
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
                <x-toggle-decision id="data-sheet" label="Data Sheet?" buttonText="No"
                    class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"/>
                <x-input-1 type="hidden" id="data_sheet" name="data_sheet" value="0"/>

                <x-toggle-decision id="safety-sheet" label="Safety Sheet?" buttonText="No"
                    class="border-gray-400 hover:bg-green-300 text-gray-700 hover:text-white"/>
                <x-input-1 type="hidden" id="safety_sheet" name="safety_sheet" value="0"/>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="edit_comparative_id">Folio Tabla Comparativa</x-label>
                    <x-select-1 
                        name="comparative_id" 
                        id="edit_comparative_id" 
                        required 
                        class="bg-gray-50 font-bold text-blue-700 border-blue-200 focus:ring-blue-500"
                    >
                        <option value="">Cargando folios...</option>
                    </x-select-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2></x-wrapper-form-2> 
            </x-wrapper-form-1>

            <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
                <x-wrapper-form-1>
                    <span class="tracking-[3px] bg-blue-500 text-white font-semibold px-2 py-1 rounded-md">
                        Products
                    </span>
                    <x-button-1 type="button" colorBtn="blue" id="add_product">Add product</x-button-1>
                </x-wrapper-form-1>

                <div id="products" class="w-full"></div>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
                <x-button-1 type="submit" colorBtn="sky">Finish</x-button-1>
            </x-wrapper-form-1>
        </form>

        <div id="product_template"
            class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative hidden">

            <span
                class="remove-product absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer">
                X
            </span>

            <x-input-1 type="hidden" name="id[]" class="id"></x-input-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Description</x-label>
                    <x-input-1 name="description[]" class="description"/>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Product URL</x-label>
                    <x-input-1 name="url[]" class="url"/>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Supplier</x-label>
                    <x-input-1 name="supplier[]" class="supplier"/>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label>Use</x-label>
                    <x-input-1 name="use[]" class="use"/>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label>Insumo</x-label>
                    <x-select-1 name="insumo[]" class="insumo">
                        <option value="">Select</option>
                        <option value="directo">Directo</option>
                        <option value="indirecto">Indirecto</option>
                    </x-select-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Quantity</x-label>
                    <x-input-1 type="number" min="0" name="quantity[]" class="quantity"/>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label>Unit</x-label>
                    <x-select-1 name="unit[]" class="unit">
                        <option value="">Select</option>
                        <option value="Kg">Kg</option>
                        <option value="L">L</option>
                        <option value="Pz">Pz</option>
                    </x-select-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Illustration</x-label>
                    <x-textarea-1 name="image_url[]" class="image_url"/>
                </x-wrapper-form-2>

                <div class="flex justify-center w-full">
                    <img class="image-ilustration w-1/2 hidden"/>
                </div>
            </x-wrapper-form-1>
        </div>
    </div>
</x-modal>

@push('js')
<script>
$(document).ready(function() {

    const modal = $('#edit-requisition');

    $('#requisitions-table tbody').on('click', '.edit-requisition-btn', function() {

        let id = $(this).data('id');

        $.get(`/requisitions/${id}`, function(res) {

            modal.find('#req_id').val(res.requisition.id);
            modal.find('[name="applicant"]').val(res.requisition.applicant);
            modal.find('[name="applicant_name"]').val(res.requisition.applicant);
            modal.find('#department').val(res.requisition.department);
            modal.find('#edit_comparative_id').val(res.requisition.comparative_id);
            modal.find('#data_sheet').val(res.requisition.data_sheet);
            modal.find('#safety_sheet').val(res.requisition.safety_sheet);

            if (res.requisition.data_sheet == 1) {
                 modal.find('#data-sheet-btn').trigger('click'); 
            }
            if (res.requisition.safety_sheet == 1) {
                 modal.find('#safety-sheet-btn').trigger('click');
            }

            modal.find('#products').empty();

            res.products.forEach(p => {
                let tpl = modal.find('#product_template')
                    .clone().removeAttr('id').removeClass('hidden');

                tpl.find('.id').val(p.id);
                tpl.find('.description').val(p.description);
                tpl.find('.url').val(p.url);
                tpl.find('.supplier').val(p.supplier);
                tpl.find('.use').val(p.use);
                tpl.find('.insumo').val(p.insumo);
                tpl.find('.quantity').val(p.qty_number); 
                tpl.find('.unit').val(p.qty_unit);

                tpl.find('.image_url').val(p.image_url);

                if (p.image_url) {
                    tpl.find('.image-ilustration')
                        .attr('src', p.image_url).removeClass('hidden');
                }

                modal.find('#products').append(tpl);
            });
        });
    });

    function loadEditComparativeFolios() {
        const selectFolio = $('#edit_comparative_id');
        $.get('/get-comparative-folios', function(data) {
            selectFolio.empty().append('<option value="">Seleccione un folio</option>');
            $.each(data, function(index, item) {
                selectFolio.append(`<option value="${item.id}">${item.folio}</option>`);
            });
        });
    }

    loadEditComparativeFolios();

    modal.find('#edit-requisition-form').submit(function(e) {
        e.preventDefault();

        let data = new FormData(this);
        let id = modal.find('#req_id').val();
        data.append('_method', 'PUT');

        $.ajax({
            url: `/requisitions/${id}`,
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success() {
                Swal.fire('Success', 'Requisition updated', 'success')
                    .then(() => location.reload());
            },
            error(xhr) {
                let msg = 'Update failed';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    msg += ': ' + xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    modal.on('input', '.image_url', function() {
        let w = $(this).closest('.wrapper');
        let img = w.find('.image-ilustration');
        let url = $(this).val();
        img.attr('src', url).toggle(!!url);
    });

    modal.on('click', '#add_product', function() {
        let tpl = modal.find('#product_template')
            .clone().removeAttr('id').removeClass('hidden');
        
        tpl.find('.id').val('null');
        
        modal.find('#products').append(tpl);
    });

    modal.on('click', '.remove-product', function() {
        $(this).closest('.wrapper').remove();
    });
});
</script>
@endpush
