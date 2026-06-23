<x-modal id="edit-material-modal">
  <form id="edit-material-form" class="space-y-4">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" id="edit-mat-id" name="id" value="">

    <x-wrapper-form-1>
      <x-tittle-form>Editar Material de Laboratorio</x-tittle-form>
    </x-wrapper-form-1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Nombre</label>
        <input type="text" id="edit-mat-name" name="name" class="w-full border rounded px-2 py-1" required>
      </div>

      <div>
        <label class="block text-sm font-semibold">UM (Unidad)</label>
        <input type="text" id="edit-mat-um" name="um" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Marca</label>
        <input type="text" id="edit-mat-brand" name="brand" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold text-green-700">Entradas</label>
        <input type="number" step="0.01" min="0" id="edit-mat-entries" name="entries" class="w-full border border-green-300 rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold text-red-700">Salidas</label>
        <input type="number" step="0.01" min="0" id="edit-mat-exits" name="exits" class="w-full border border-red-300 rounded px-2 py-1">
      </div>

      <div class="md:col-span-2 bg-blue-50 p-2 rounded">
        <label class="block text-sm font-bold text-blue-800">Stock Actual</label>
        <input type="number" step="0.01" min="0" id="edit-mat-stock" name="stock" class="w-full border border-blue-300 bg-gray-100 rounded px-2 py-1 font-bold text-blue-700" readonly>
        <p class="text-xs text-gray-500 mt-1">Se recalcula automáticamente (Entradas - Salidas).</p>
      </div>

    </div>

    <div class="mt-4 flex justify-end gap-2">
      <x-button type="button" class="close-mat-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
        Cancelar
      </x-button>

      <x-button type="submit" id="edit-mat-save" class="bg-green-600 hover:bg-green-700 text-white">
        Guardar cambios
      </x-button>
    </div>
  </form>
</x-modal>
