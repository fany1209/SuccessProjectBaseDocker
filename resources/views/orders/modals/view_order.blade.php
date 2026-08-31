<x-modal id="view-order-modal">
    <div class="flex flex-col w-full gap-4 p-4">
        <div class="flex justify-between items-center border-b pb-2">
            <x-tittle-form>Detalles del Pedido</x-tittle-form>
            <span id="view_po_badge" class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded"></span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="font-bold text-gray-500">Año / Semana:</p>
                <p id="view_fecha_info" class="text-gray-800"></p>
            </div>
            <div>
                <p class="font-bold text-gray-500">Empresa:</p>
                <p id="view_empresa" class="font-semibold text-gray-800"></p>
            </div>
            
            <div>
                <p class="font-bold text-gray-500">Producto:</p>
                <p id="view_producto" class="text-gray-800"></p>
            </div>
            <div>
                <p class="font-bold text-gray-500">Cantidad:</p>
                <p id="view_cantidad" class="text-green-700 font-bold"></p>
            </div>

            <div>
                <p class="font-bold text-gray-500">Fecha Envío:</p>
                <p id="view_envio" class="text-gray-800"></p>
            </div>
            <div>
                <p class="font-bold text-gray-500">Hora de Envío:</p>
                <p id="view_hora" class="text-gray-800"></p>
            </div>

            <div>
                <p class="font-bold text-gray-500">Transporte / Línea:</p>
                <p id="view_transporte" class="text-gray-800"></p>
            </div>
            
            <div>
                <p class="font-bold text-gray-500">Registrado por:</p>
                <p id="view_creador" class="text-gray-800 font-medium"></p>
            </div>

            <div class="col-span-2 border-t pt-2">
                <p class="font-bold text-gray-500">Documentación Requerida:</p>
                <div id="view_docs" class="flex flex-wrap gap-2 mt-1"></div>
            </div>

            <div class="col-span-2 border-t pt-2">
                <p class="font-bold text-gray-500">Comentarios:</p>
                <p id="view_comentarios" class="italic text-gray-700 bg-gray-50 p-2 rounded min-h-[40px]"></p>
            </div>
        </div>

        <div class="flex justify-end mt-4">
            <x-button-1 class="close-modal" type="button" colorBtn="red">Cerrar</x-button-1>
        </div>
    </div>
</x-modal>