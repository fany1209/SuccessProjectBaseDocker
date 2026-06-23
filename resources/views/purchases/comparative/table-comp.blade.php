<x-modal id="modal-insumos">
    <form class="flex flex-col items-center w-full gap-4" action="{{ route('comparative.store') }}" method="POST" id="form-insumos">
        @csrf
        
        <x-wrapper-form-1>
            <x-tittle-form class="border-b-2 border-blue-600 pb-2 w-full">Registro Comparativo de Insumos</x-tittle-form>
        </x-wrapper-form-1>

        <div id="container-insumos" class="w-full">
            <div class="insumo-row border-2 border-dashed border-gray-200 rounded-md p-4 mb-6 relative bg-white shadow-sm">
                <span class="remove-insumo absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-2 right-2 rounded-full font-bold cursor-pointer p-1">X</span>
                
                <div class="flex flex-row w-full gap-4 mb-2">
                    <div class="flex flex-col w-3/4">
                        <x-label>Insumo</x-label>
                        <x-input-1 required name="insumo[]" placeholder="Nombre del artículo..."></x-input-1>
                    </div>
                    <div class="flex flex-col w-1/4">
                        <x-label>Pz / Cant</x-label>
                        <x-input-1 required type="number" name="cantidad[]" class="cantidad calcular text-center" placeholder="0"></x-input-1>
                    </div>
                </div>

                <x-wrapper-form-1>
                    <x-wrapper-form-2>
                        <x-label>Proveedor</x-label>
                        <x-input-1 name="proveedor[]" placeholder="Nombre de la empresa"></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label>Precio Total</x-label>
                        <x-input-1 required type="number" step="0.01" name="precio_total[]" class="precio_total calcular font-bold text-green-700" placeholder="0.00"></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label>Precio Unitario</x-label>
                        <x-input-1 name="precio_unt[]" class="precio_unt bg-gray-100" readonly placeholder="0.00"></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>

                <x-wrapper-form-2>
                    <x-label>Link de Imagen (URL)</x-label>
                    <x-input-1 name="imagen[]" class="img-link w-full" placeholder="Pegue el link de la imagen aquí..."></x-input-1>
                </x-wrapper-form-2>

                <div class="flex justify-center w-full mt-2">
                    <div class="preview-container w-full max-w-[250px] h-48 border-2 border-gray-100 rounded-lg flex items-center justify-center overflow-hidden bg-gray-50 p-2">
                        <div class="text-center text-gray-400">
                            <i class="ri-image-add-line text-4xl block"></i>
                            <span class="text-xs">Vista previa de imagen</span>
                        </div>
                    </div>
                </div>

                <x-wrapper-form-1 class="mt-4">
                    <x-wrapper-form-2>
                        <x-label>Link del Producto</x-label>
                        <x-input-1 name="link[]" placeholder="URL de compra"></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label>Entrega Estimada</x-label>
                        <x-input-1 type="date" name="entrega_estimada[]"></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>

                <x-wrapper-form-1>
                    <x-wrapper-form-2>
                        <x-label>Descripción</x-label>
                        <x-input-1 name="descripcion[]" placeholder="Especificaciones técnicas, marca..."></x-input-1>
                    </x-wrapper-form-2>
                    <x-wrapper-form-2>
                        <x-label>Comentarios</x-label>
                        <x-input-1 name="comentarios[]" placeholder="Notas sobre el envío, urgencia..."></x-input-1>
                    </x-wrapper-form-2>
                </x-wrapper-form-1>
            </div>
        </div>

        <x-wrapper-form-1 class="justify-start">
            <x-button-1 type="button" colorBtn="blue" id="add-insumo">
                <i class="ri-add-line"></i> Agregar otro insumo
            </x-button-1>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="justify-end gap-2 border-t pt-4">
            <x-button-1 colorBtn="red" class="close-modal" type="reset">Cancelar</x-button-1>
            <x-button-1 colorBtn="green" type="submit">Guardar Cambios</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const container = $('#container-insumos');

    // Agregar nueva fila
    $('#add-insumo').on('click', function() {
        const newRow = container.find('.insumo-row').first().clone();
        newRow.find('input').val('');
        newRow.find('.preview-container').html('<div class="text-center text-gray-400"><i class="ri-image-add-line text-4xl block"></i><span class="text-xs">Vista previa de imagen</span></div>');
        container.append(newRow);
    });

    // Eliminar fila
    container.on('click', '.remove-insumo', function() {
        if (container.find('.insumo-row').length > 1) {
            $(this).closest('.insumo-row').remove();
        }
    });

    container.on('input', '.calcular', function() {
        const row = $(this).closest('.insumo-row');
        const qty = parseFloat(row.find('.cantidad').val()) || 0;
        const total = parseFloat(row.find('.precio_total').val()) || 0;
        
        if (qty > 0) {
            const unit = total / qty;
            row.find('.precio_unt').val(unit.toFixed(2));
        } else {
            row.find('.precio_unt').val('0.00');
        }
    });

    container.on('input', '.img-link', function() {
        const url = $(this).val();
        const preview = $(this).closest('.insumo-row').find('.preview-container');

        if (url && url.trim() !== '') {
            preview.html(`<img src="${url}" class="object-contain w-full h-full rounded-md shadow-sm" onerror="this.src='https://via.placeholder.com/250x200?text=URL+de+Imagen+Invalida'">`);
        } else {
            preview.html('<div class="text-center text-gray-400"><i class="ri-image-add-line text-4xl block"></i><span class="text-xs">Vista previa de imagen</span></div>');
        }
    });
});
</script>
@endpush