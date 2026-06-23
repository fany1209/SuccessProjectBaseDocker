     <x-modal id="insumos-modal">
        <div class="flex flex-col gap-4 w-full">
            <h2 class="text-xl font-bold text-center text-gray-700">
                Insumos de la Requisición
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">Descripción</th>
                            <th class="border px-3 py-2">Proveedor</th>
                            <th class="border px-3 py-2">Uso</th>
                            <th class="border px-3 py-2">Cantidad</th>
                            <th class="border px-3 py-2 text-center">Insumo</th>
                        </tr>
                    </thead>
                    <tbody id="insumos-body"></tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <button type="button"
                    class="close-modal bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                    Cerrar
                </button>
            </div>
        </div>
    </x-modal>
</section>