<!-- Edit Details Modal -->
<x-modal id="edit-cxc">
  <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
      <i class="ri-edit-2-fill text-[#198754]"></i> Editar Cuenta por Cobrar
    </h3>
    <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
      <i class="ri-close-line text-2xl"></i>
    </button>
  </div>
  
  <div class="p-6">
    <form id="edit-cxc-form">
      <input type="hidden" id="edit-cxc-id">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Documento -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
          <select id="edit-documento" class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-4 py-2 text-gray-700">
            <option value="Factura">Factura</option>
            <option value="Complemento de Pago">Complemento de Pago</option>
            <option value="N/A">N/A</option>
          </select>
        </div>

        <!-- Método de pago -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
          <select id="edit-metodo-pago" class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-4 py-2 text-gray-700">
            <option value="N/A">N/A</option>
            <option value="PUE">PUE (Pago en una sola exhibición)</option>
            <option value="PPD">PPD (Pago en parcialidades o diferido)</option>
          </select>
        </div>



        <!-- Fecha Conclusión -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Conclusión</label>
          <input type="date" id="edit-fecha-conclusion" class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-4 py-2 text-gray-700">
        </div>

        <!-- Descripción -->
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea id="edit-descripcion" rows="3" class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-4 py-2 text-gray-700" placeholder="Añadir descripción o notas..."></textarea>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button type="button" class="close-modal px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancelar</button>
        <button type="submit" id="btn-save-cxc" class="px-4 py-2 bg-[#198754] text-white rounded-lg hover:bg-[#157347] transition">Guardar Cambios</button>
      </div>
    </form>
  </div>
</x-modal>

<!-- Payments Modal -->
<x-modal id="payments-cxc">
  <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
      <i class="ri-money-dollar-circle-fill text-[#198754]"></i> Complementos de Pago
    </h3>
    <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
      <i class="ri-close-line text-2xl"></i>
    </button>
  </div>
  
  <div class="p-6">
    <!-- Header with balance -->
    <div class="mb-6 flex justify-between items-center bg-gray-50 rounded-lg p-4 border border-gray-200">
      <div>
        <p class="text-sm text-gray-500">Folio</p>
        <p id="pay-folio" class="font-bold text-gray-800">---</p>
      </div>
      <div>
        <p class="text-sm text-gray-500">Total Venta</p>
        <p id="pay-total" class="font-bold text-blue-600">---</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-gray-500">Saldo Restante</p>
        <p id="pay-saldo" class="font-bold text-red-600 text-lg">---</p>
      </div>
    </div>

    <!-- Form to add payment -->
    <form id="add-payment-form" class="mb-6 bg-green-50 p-4 rounded-lg border border-green-100" enctype="multipart/form-data">
      <h4 class="font-semibold text-green-800 mb-3 text-sm">Añadir Nuevo Pago</h4>
      <input type="hidden" id="pay-cxc-id">
      <div class="flex gap-3 items-end">
        <div class="flex-1">
          <label class="block text-xs font-medium text-gray-700 mb-1">Monto a pagar</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
            <input type="number" step="0.01" min="0.01" id="pay-amount" required class="w-full pl-8 rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-3 py-2 text-gray-700">
          </div>
        </div>
        <div class="flex-1">
          <label class="block text-xs font-medium text-gray-700 mb-1">Fecha de pago</label>
          <input type="date" id="pay-date" required class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-3 py-2 text-gray-700">
        </div>
      </div>
      <div class="mt-3 flex gap-3 items-end">
        <div class="flex-1">
          <label class="block text-xs font-medium text-gray-700 mb-1">Comprobante (Opcional, max 25MB)</label>
          <input type="file" id="pay-comprobante" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
        </div>
        <div>
          <button type="submit" id="btn-save-payment" class="px-4 py-2 bg-[#198754] text-white font-medium rounded-lg hover:bg-[#157347] transition flex items-center gap-1">
            <i class="ri-add-line"></i> Añadir
          </button>
        </div>
      </div>
    </form>

    <!-- Table of payments -->
    <div class="max-h-60 overflow-y-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Fecha</th>
            <th class="px-4 py-2">Comprobante</th>
            <th class="px-4 py-2 text-right">Monto</th>
          </tr>
        </thead>
        <tbody id="payments-list" class="divide-y divide-gray-100">
          <!-- Populated via JS -->
        </tbody>
      </table>
      <div id="no-payments-msg" class="hidden text-center text-gray-500 py-4 text-sm">
        No hay pagos registrados aún.
      </div>
    </div>

  </div>
</x-modal>
