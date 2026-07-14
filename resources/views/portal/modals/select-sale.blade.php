<x-modal id="select-sale-modal">
  <div class="px-6 py-5 bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-green-600"></div>

    <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-5 mt-2">
      <div class="flex items-center gap-3">
        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
          <i class="fas fa-list-alt text-lg"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Seleccionar Venta</h3>
          <p class="text-sm text-gray-500 mt-0.5">Selecciona la transacción para adjuntarle archivos.</p>
        </div>
      </div>
      <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="max-h-[22rem] overflow-y-auto rounded-xl border border-gray-200 shadow-sm bg-white mb-2">
        <table class="w-full text-left border-collapse">
            <thead class="sticky top-0 bg-gray-50 z-10 shadow-sm border-b border-gray-200">
                <tr class="text-xs uppercase tracking-wider text-gray-500 font-semibold">
                    <th class="px-4 py-3">Folio</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3 text-right">Acción</th>
                </tr>
            </thead>
            <tbody id="list-client-sales" class="divide-y divide-gray-100 text-sm text-gray-700">
            </tbody>
        </table>
    </div>
    
    <div class="mt-5 flex justify-end pt-5 border-t border-gray-100">
        <button type="button" class="close-modal bg-white text-gray-600 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-gray-800 transition-all font-semibold shadow-sm">
            Cerrar Ventana
        </button>
    </div>
  </div>
</x-modal>