<!-- Edit Cuentas Por Pagar Modal -->
<x-modal id="edit-cxp">
  <div class="px-6 py-4">
    <form id="edit-cxp-form">
      <input type="hidden" id="edit-cxp-id">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Programada de Pago</label>
          <input type="date" id="edit-fecha-pago" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Semana</label>
          <input type="number" id="edit-semana" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
          <input type="number" id="edit-anio" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
          <input type="text" id="edit-banco" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
          <select id="edit-metodo-pago" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
            <option value="N/A">N/A</option>
            <option value="PUE">PUE</option>
            <option value="PPD">PPD</option>
          </select>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button type="button" class="close-modal bg-white text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">Cancelar</button>
        <button type="submit" id="btn-save-cxp" class="bg-[#198754] text-white px-4 py-2 rounded-lg hover:bg-[#157347] transition font-medium">Guardar Cambios</button>
      </div>
    </form>
  </div>
</x-modal>

<!-- Payments Modal -->
<x-modal id="payments-cxp">
  <div class="px-6 py-4">
    <input type="hidden" id="pay-cxp-id">
    
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 flex justify-between items-center">
      <div>
        <p class="text-sm text-gray-500 font-medium">Factura / Empresa</p>
        <p class="text-lg font-bold text-gray-800" id="pay-folio">-</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-gray-500 font-medium">Total Factura</p>
        <p class="text-lg font-bold text-gray-800" id="pay-total">$0.00</p>
      </div>
      <div class="text-right border-l pl-4 border-gray-300">
        <p class="text-sm text-gray-500 font-medium">Saldo Pendiente</p>
        <p class="text-xl font-bold text-red-600" id="pay-saldo">$0.00</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Listado de Pagos -->
      <div class="md:col-span-2">
        <h3 class="text-md font-bold text-gray-800 mb-3 border-b pb-2">Historial de Abonos</h3>
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden max-h-[300px] overflow-y-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
              <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Notas</th>
                <th class="px-4 py-2 text-right">Monto</th>
              </tr>
            </thead>
            <tbody id="payments-list" class="divide-y divide-gray-100">
              <!-- Pagos cargados via AJAX -->
            </tbody>
          </table>
          <div id="no-payments-msg" class="hidden py-6 text-center text-gray-500">
            No hay abonos registrados para esta factura.
          </div>
        </div>
      </div>

      <!-- Nuevo Pago -->
      <div class="md:col-span-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
        <h3 class="text-md font-bold text-gray-800 mb-3">Registrar Abono</h3>
        <form id="add-payment-form">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Monto ($)</label>
              <input type="number" step="0.01" min="0.01" id="pay-amount" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]" required>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha del Pago</label>
              <input type="date" id="pay-date" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Comprobante</label>
              <input type="text" id="pay-comprobante" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]" placeholder="Opcional">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
              <textarea id="pay-notas" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754] text-sm" placeholder="Opcional"></textarea>
            </div>
          </div>

          <div class="mt-5">
            <button type="submit" id="btn-save-payment" class="w-full bg-[#198754] text-white px-4 py-2 rounded-lg hover:bg-[#157347] transition font-medium flex justify-center items-center gap-2">
              <i class="ri-add-line"></i> Guardar Abono
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</x-modal>
