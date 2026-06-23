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
                <x-label for="producto">Producto</x-label>
                <select name="producto" id="producto" 
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-sm">
                    <option value="" disabled selected>Seleccione un producto...</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->name }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="cantidad">Cantidad (Ej: 100 kg, 50 L)</x-label>
                <x-input-1 
                    type="text" 
                    name="cantidad" 
                    id="cantidad" 
                    placeholder="Ej: 500 kg"
                ></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="po">PO</x-label>
                <x-input-1 name="po" id="po"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

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
                </div>
            </x-wrapper-form-2>
        </x-wrapper-form-1>>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="comentarios">Comentarios</x-label>
                <x-input-1 name="comentarios" id="comentarios" placeholder="Notas adicionales sobre el envío..."></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_pdf">Subir Documento (PDF)</x-label>
                <input type="file" name="pdf_file" id="edit_pdf" accept="application/pdf" 
                    class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <div id="pdf_status" class="mt-1 text-[10px]"></div>
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
                    text: 'No se pudo guardar el pedido.'
                });
                saveBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush