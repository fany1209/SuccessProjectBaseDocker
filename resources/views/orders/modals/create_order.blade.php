<x-modal id="create-order">
    <form class="flex flex-col items-center w-full gap-2" id="create-order-form">
        @csrf

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Nuevo Pedido</x-tittle-form>
                <p class="text-sm text-gray-500">Registro de logística y carga</p>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="año">Año</x-label>
                <x-input-1 type="number" name="año" id="año" value="{{ date('Y') }}" required></x-input-1>
            </x-wrapper-form-2>
            
            <x-wrapper-form-2>
                <x-label for="semana">Semana</x-label>
                <x-input-1 type="number" name="semana" id="semana" value="{{ date('W') }}" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="empresa">Empresa</x-label>
                <x-input-1 name="empresa" id="empresa" required></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="po">PO</x-label>
                <x-input-1 name="po" id="po"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <!-- Sección Dinámica de Productos -->
        <div class="w-full bg-gray-50/90 rounded-xl p-3 border border-gray-200 my-2">
            <div class="flex justify-between items-center mb-2 px-1">
                <div>
                    <h4 class="text-sm font-bold text-gray-700">Productos del Pedido</h4>
                    <p class="text-[11px] text-gray-500">Añade uno o múltiples productos con sus cantidades</p>
                </div>
                <button type="button" id="create-add-product-btn" 
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded-lg transition shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    + Agregar Producto
                </button>
            </div>
            
            <div id="create-order-items-container" class="space-y-2">
                <!-- Filas de productos inyectadas dinámicamente -->
            </div>
        </div>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="fecha_de_carga">Fecha de Carga</x-label>
                <x-input-1 type="date" name="fecha_de_carga" id="fecha_de_carga"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="hora">Hora de Carga</x-label>
                <x-input-1 type="time" name="hora" id="hora"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="fecha_de_envio">Fecha de Envío</x-label>
                <x-input-1 type="date" name="fecha_de_envio" id="fecha_de_envio"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="fecha_requerida_por_el_cliente">Fecha Requerida Cliente</x-label>
                <x-input-1 type="date" name="fecha_requerida_por_el_cliente" id="fecha_requerida_por_el_cliente"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="transporte">Transporte</x-label>
                <x-input-1 name="transporte" id="transporte"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="estatus_almacen">Estatus Almacén</x-label>
                <x-select-1 name="estatus_almacen" id="estatus_almacen">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Terminado">Terminado</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="estatus_calidad">Estatus Calidad</x-label>
                <x-select-1 name="estatus_calidad" id="estatus_calidad">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Liberado">Liberado</option>
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="estatus_administrativo">Estatus Administrativo</x-label>
                <x-select-1 name="estatus_administrativo" id="estatus_administrativo">
                    <option value="Documentacion Pendiente">Documentación Pendiente</option>
                    <option value="Documentacion Completa">Documentación Completa</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Documentación Requerida</x-label>
                <div class="flex flex-wrap gap-4 p-2 bg-gray-50 rounded-md border border-gray-200 w-full">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="PO" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">PO</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="R" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">R</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="F" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">F</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="CoA" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">CoA</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="CT" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">CT</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="Ticket de peso" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">Ticket de peso</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="HS" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700" title="Hoja de Seguridad">HS</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="documentacion_requerida[]" value="CFT" class="rounded text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">CFT</span>
                    </label>
                </div>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="comentarios">Comentarios</x-label>
                <x-input-1 name="comentarios" id="comentarios" placeholder="Notas adicionales sobre el envío..."></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="create_pdf">Subir Documento (PDF)</x-label>
                <input type="file" name="pdf_file" id="create_pdf" accept="application/pdf" 
                    class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 id="save-order" colorBtn="green">Finalizar</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){
    const modal = $('#create-order');
    const availableProducts = @json($productos->pluck('name'));
    let createItemIndex = 0;

    function buildProductOptions(selectedValue = '') {
        let opts = '<option value="" disabled ' + (selectedValue ? '' : 'selected') + '>Seleccione un producto...</option>';
        availableProducts.forEach(name => {
            const isSel = (selectedValue === name) ? 'selected' : '';
            opts += `<option value="${name}" ${isSel}>${name}</option>`;
        });
        return opts;
    }

    function addCreateProductRow(producto = '', cantidad = '') {
        const idx = createItemIndex++;
        const html = `
            <div class="order-item-row flex flex-col md:flex-row items-center gap-2 bg-white p-2.5 rounded-lg border border-gray-200 shadow-xs">
                <div class="w-full md:flex-1">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-0.5">Producto</label>
                    <select name="items[${idx}][producto]" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-xs py-1.5" required>
                        ${buildProductOptions(producto)}
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-0.5">Cantidad</label>
                    <input type="text" name="items[${idx}][cantidad]" value="${cantidad}" placeholder="Ej: 500 kg, 20 L" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-xs py-1.5" required>
                </div>
                <div class="w-full md:w-auto flex md:self-end pt-1 md:pt-0">
                    <button type="button" class="remove-create-item-btn p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-md transition" title="Eliminar producto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        $('#create-order-items-container').append(html);
    }

    // Inicializar con 1 fila
    addCreateProductRow();

    $('#create-add-product-btn').on('click', function(){
        addCreateProductRow();
    });

    $(document).on('click', '.remove-create-item-btn', function(){
        if ($('#create-order-items-container .order-item-row').length > 1) {
            $(this).closest('.order-item-row').remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'El pedido debe contener al menos un producto.',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
        }
    });

    modal.find("#create-order-form").submit(function(event) {
        event.preventDefault();
        const form = this;
        const data = new FormData(form);
        const saveBtn = modal.find('#save-order');

        saveBtn.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: "/orders/store", 
            data: data,
            processData: false,
            contentType: false,
            success: function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'El pedido fue agregado correctamente.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'No se pudo guardar el pedido.'
                });
                saveBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush