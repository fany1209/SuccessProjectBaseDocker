<x-modal id="view-warehouse">
  <div class="space-y-4">

    <h3 class="text-base font-semibold">Información de la inspección</h3>

    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-medium">Responsable</label>
        <input type="text"
               id="vw-responsable"
               class="w-full rounded-md border px-3 py-2 bg-gray-100"
               readonly>
      </div>

      <div>
        <label class="text-sm font-medium">Inspector</label>
        <input type="text"
               id="vw-inspector"
               class="w-full rounded-md border px-3 py-2 bg-gray-100"
               readonly>
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Comentarios</label>
      <textarea id="vw-comentarios"
                rows="4"
                class="w-full rounded-md border px-3 py-2 bg-gray-100"
                readonly></textarea>
    </div>

    <div>
      <h4 class="font-semibold mb-2">Observaciones</h4>

      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left">Observación</th>
              <th class="px-3 py-2 text-left">Ubicación</th>
              <th class="px-3 py-2 text-left">Fecha</th>
              <th class="px-3 py-2 text-left">Evidencia</th>
            </tr>
          </thead>

          <tbody id="vw-observaciones">
            {{-- Se llena por JS --}}
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex justify-end">
      <x-button type="button" class="close-modal bg-gray-600 text-white">
        Cerrar
      </x-button>
    </div>

  </div>
</x-modal>
