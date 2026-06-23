<x-modal id="edit-order-modal">
    <form class="flex flex-col items-center w-full gap-2" id="edit-order-form">
        @csrf
        <input type="hidden" name="id" id="edit_id">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Editar Pedido</x-tittle-form>
                <p id="edit_label_po" class="text-sm text-gray-500 italic">Cargando...</p>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_año">Año</x-label>
                <x-input-1 type="number" name="año" id="edit_año" required></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_semana">Semana</x-label>
                <x-input-1 type="number" name="semana" id="edit_semana" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_empresa">Empresa</x-label>
                <x-input-1 name="empresa" id="edit_empresa" required></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_producto">Producto</x-label>
                <select name="producto" id="edit_producto" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-sm">
                    <option value="">Seleccione un producto...</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->name }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_cantidad">Cantidad</x-label>
                <x-input-1 type="text" name="cantidad" id="edit_cantidad" placeholder="Ej: 500 kg"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_po">PO</x-label>
                <x-input-1 name="po" id="edit_po"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_fecha_de_carga">Fecha Carga</x-label>
                <x-input-1 type="date" name="fecha_de_carga" id="edit_fecha_de_carga"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_hora">Hora</x-label>
                <x-input-1 type="time" name="hora" id="edit_hora"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_fecha_de_envio">Fecha Envío</x-label>
                <x-input-1 type="date" name="fecha_de_envio" id="edit_fecha_de_envio"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_fecha_requerida_por_el_cliente">Fecha Req. Cliente</x-label>
                <x-input-1 type="date" name="fecha_requerida_por_el_cliente" id="edit_fecha_requerida_por_el_cliente"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_transporte">Transporte</x-label>
                <x-input-1 name="transporte" id="edit_transporte"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_estatus_almacen">Almacén</x-label>
                <x-select-1 name="estatus_almacen" id="edit_estatus_almacen">
                    <option value="Pendiente">Pendiente</option>
                    <option value="En Proceso">En Proceso</option>
                    <option value="Terminado">Terminado</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_estatus_calidad">Calidad</x-label>
                <x-select-1 name="estatus_calidad" id="edit_estatus_calidad">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Liberado">Liberado</option>
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_estatus_administrativo">Administrativo</x-label>
                <x-select-1 name="estatus_administrativo" id="edit_estatus_administrativo">
                    <option value="Documentacion Pendiente">Documentación Pendiente</option>
                    <option value="Documentacion Completa">Documentación Completa</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Documentación Requerida</x-label>
                <div class="flex flex-wrap gap-4 p-2 bg-gray-50 rounded-md border w-full">
                    @foreach(['PO', 'R', 'F', 'CoA', 'CT'] as $doc)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="documentacion_requerida[]" value="{{$doc}}" class="edit-doc-check rounded text-green-600">
                            <span class="text-sm">{{$doc}}</span>
                        </label>
                    @endforeach
                </div>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_comentarios">Comentarios</x-label>
                <x-input-1 name="comentarios" id="edit_comentarios"></x-input-1>
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
            <x-button-1 class="close-modal" type="button" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 id="update-order" type="button" colorBtn="green">Actualizar</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>