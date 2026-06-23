<x-modal id="add-precio">
    <form class="flex flex-col items-center w-full gap-2" id="add-precio-form">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Agregar Nuevo Precio</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>

            <x-wrapper-form-2>
                <x-label>Insumo / Producto <span class="text-red-500">*</span></x-label>
                <x-input-1 required name="insumo" placeholder="Ej. Tornillo 5/8"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Clave SAT</x-label>
                <x-input-1 name="clave_sat" placeholder="Ej. 43211503"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Proveedor <span class="text-red-500">*</span></x-label>
                <x-input-1 required name="proveedor" placeholder="Ej. Ferretería El Sol"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Precio <span class="text-red-500">*</span></x-label>
                <x-input-1 required type="number" step="0.01" min="0" name="precio" placeholder="0.00"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2 class="flex items-center gap-2 pt-6">
                <input type="checkbox" name="tiene_iva" id="add-tiene-iva" value="1" checked 
                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                <x-label for="add-tiene-iva" class="mb-0 cursor-pointer">Aplica IVA (16%)</x-label>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Moneda</x-label>
                <select name="moneda" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="MXN" selected>MXN - Pesos</option>
                    <option value="USD">USD - Dólares</option>
                    <option value="EUR">EUR - Euros</option>
                </select>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Fecha de Cotización <span class="text-red-500">*</span></x-label>
                <x-input-1 required type="date" name="fecha_cotizacion" value="{{ date('Y-m-d') }}"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 type="button" class="close-modal" colorBtn="red" data-target="add-precio">Cancel</x-button-1>
            <x-button-1 type="submit" id="save-precio" colorBtn="green">Save</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){

    /* GUARDAR PRECIO (AJAX) */
    $('#add-precio-form').on('submit', function(e){
        e.preventDefault();

        const form = $(this);
        const btn = $('#save-precio');
        const btnText = btn.html();
        
        btn.prop('disabled', true).html('Guardando...');

        $.ajax({
            url: "{{ route('precios.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(response){
                $('.close-modal[data-target="add-precio"]').trigger('click');
                form[0].reset();
                $('input[name="fecha_cotizacion"]').val(new Date().toISOString().split('T')[0]);
                
                location.reload(); 
                
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Price saved successfully',
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr){
                btn.prop('disabled', false).html(btnText);
                console.log(xhr.responseText);
                
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    for(let field in errors){
                        message += errors[field][0] + '\n';
                    }
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error de validación',
                        text: message,
                        confirmButtonColor: '#d33'
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al guardar. Revisa la consola.',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function(){
                btn.prop('disabled', false).html(btnText);
            }
        });
    });
});
</script>
@endpush