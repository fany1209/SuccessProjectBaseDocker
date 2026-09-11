   <x-modal id="show-factura">
    <div class="flex flex-col w-full gap-4 p-2">

        <div class="flex justify-between items-start border-b pb-2">
            <div>
                <h2 class="text-xl font-bold text-gray-800" id="show-empresa"></h2>
                <div class="flex gap-2 mt-1 items-center flex-wrap">
                    <span id="show-tipo" class="text-[10px] font-bold px-2 py-0.5 rounded inline-block uppercase border"></span>
                    <span id="show-insumo" class="text-[10px] font-bold px-2 py-0.5 rounded inline-block uppercase border"></span>
                    <span id="show-moneda" class="text-[10px] font-bold px-2 py-0.5 rounded inline-block uppercase border bg-green-50 text-green-700 border-green-200"></span>
                    <span id="show-tc-badge" class="text-[10px] font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded inline-block border hidden"></span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Folio</p>
                <p class="text-lg font-bold text-green-600" id="show-folio"></p>
                <p class="text-xs text-gray-400 mt-1"><i class="fas fa-calendar-alt"></i> <span id="show-fecha"></span></p>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <div class="bg-blue-50/50 p-3 rounded-lg border border-blue-100 hidden" id="show-descripcion-container">
                <p class="text-[10px] uppercase text-gray-400 font-bold">Descripción / Observaciones</p>
                <p class="text-sm text-gray-700 mt-1" id="show-descripcion"></p>
            </div>
        </div>

        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-xs text-left text-gray-600 min-w-max">
                <thead class="bg-gray-100 uppercase font-bold text-[10px]">
                    <tr>
                        <th class="px-3 py-2">Cant</th>
                        <th class="px-3 py-2">Producto / SAT</th>
                        <th class="px-3 py-2 text-right">P. Unit</th>
                        <th class="px-3 py-2 text-right text-orange-600">Desc.</th>
                        <th class="px-3 py-2 text-right">Base</th>
                        <th class="px-3 py-2 text-right text-blue-600">+ Imp</th>
                        <th class="px-3 py-2 text-right text-red-600">- Ret</th>
                        <th class="px-3 py-2 text-right text-green-700">Total Fila</th>
                    </tr>
                </thead>
                <tbody id="show-products-body" class="divide-y divide-gray-200 bg-white"></tbody>
            </table>
        </div>

        <div class="flex justify-end border-t border-gray-300 pt-3">
            <div class="w-full md:w-1/2 lg:w-1/3 space-y-1 text-sm bg-gray-50 p-3 rounded-lg border">
                <div class="flex justify-between">
                    <span class="font-semibold text-gray-600">Subtotal:</span>
                    <span id="show-subtotal" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between text-orange-600" id="row-show-descuentos">
                    <span>(-) Descuentos:</span>
                    <span id="show-descuentos" class="font-semibold"></span>
                </div>
                <div class="flex justify-between text-blue-600">
                    <span>(+) Impuestos (IVA/Trasl/ILC):</span>
                    <span id="show-impuestos" class="font-semibold"></span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>(-) Retenciones (Ret/ISR):</span>
                    <span id="show-retenciones" class="font-semibold"></span>
                </div>
                <div class="flex justify-between text-lg border-t border-gray-300 pt-2 mt-2">
                    <span class="font-bold text-gray-800 uppercase">Gran Total:</span>
                    <span id="show-total" class="font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded"></span>
                </div>
            </div>
        </div>

        <div class="flex justify-center mt-2">
            <x-button-1 type="button" class="close-modal" colorBtn="gray">
                Cerrar Detalles
            </x-button-1>
        </div>

    </div>
</x-modal>