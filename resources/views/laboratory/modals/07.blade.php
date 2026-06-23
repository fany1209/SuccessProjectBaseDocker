<x-modal id="07">
<form id="instructivo-muestreo-form" method="POST" action="{{ route('laboratory.pdf7') }}" target="_blank" class="space-y-6">
  @csrf

  {{-- =================== Tabla de Recomendaciones =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Tabla de recomendaciones</div>

    <div class="p-3">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-semibold">Filas (carga, rango, total, min, max)</span>
        <button type="button" id="rec-add" class="px-2 py-1 border rounded text-sm">+ Fila</button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border">
          <thead>
            <tr class="bg-gray-100">
              <th class="border px-2 py-1" style="width:26%;">Carga (ton)</th>
              <th class="border px-2 py-1" style="width:18%;">Rango de muestreo</th>
              <th class="border px-2 py-1" style="width:22%;">Total en almacén</th>
              <th class="border px-2 py-1" style="width:17%;">Peso mín (g)</th>
              <th class="border px-2 py-1" style="width:17%;">Peso máx (g)</th>
              <th class="border px-2 py-1">—</th>
            </tr>
          </thead>
          <tbody id="rec-rows">
            @php
              $rows = old('tabla_recomendaciones', [
                ['carga'=>'0.5–1.0','rango'=>'0.5%','total'=>'—','min'=>'500','max'=>'1000'],
              ]);
            @endphp
            @foreach($rows as $i => $r)
              <tr>
                <td class="border">
                  <input name="tabla_recomendaciones[{{ $i }}][carga]" class="w-full px-2 py-1" value="{{ $r['carga'] ?? '' }}" placeholder="p. ej. 0.5–1.0">
                </td>
                <td class="border">
                  <input name="tabla_recomendaciones[{{ $i }}][rango]" class="w-full px-2 py-1" value="{{ $r['rango'] ?? '' }}" placeholder="p. ej. 0.5%">
                </td>
                <td class="border">
                  <input name="tabla_recomendaciones[{{ $i }}][total]" class="w-full px-2 py-1" value="{{ $r['total'] ?? '' }}" placeholder="p. ej. 2 kg">
                </td>
                <td class="border">
                  <input name="tabla_recomendaciones[{{ $i }}][min]" class="w-full px-2 py-1" value="{{ $r['min'] ?? '' }}" placeholder="p. ej. 500">
                </td>
                <td class="border">
                  <input name="tabla_recomendaciones[{{ $i }}][max]" class="w-full px-2 py-1" value="{{ $r['max'] ?? '' }}" placeholder="p. ej. 1000">
                </td>
                <td class="border text-center">
                  <button type="button" class="rec-del px-2">×</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <p class="text-xs text-gray-500 mt-2">* Esta tabla alimenta <code>$tabla_recomendaciones</code> del PDF.</p>
    </div>
  </div>

  {{-- =================== Acciones =================== --}}
  <div class="flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="submit" form="instructivo-muestreo-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
  </div>
</form>

{{-- ===== JS mínimo para filas dinámicas ===== --}}
<script>
(function(){
  const tbody = document.getElementById('rec-rows');
  let idx = tbody?.children.length || 0;

  document.getElementById('rec-add')?.addEventListener('click', () => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="border"><input name="tabla_recomendaciones[${idx}][carga]" class="w-full px-2 py-1" placeholder="p. ej. 1–5"></td>
      <td class="border"><input name="tabla_recomendaciones[${idx}][rango]" class="w-full px-2 py-1" placeholder="p. ej. 0.5%"></td>
      <td class="border"><input name="tabla_recomendaciones[${idx}][total]" class="w-full px-2 py-1" placeholder="p. ej. 3 kg"></td>
      <td class="border"><input name="tabla_recomendaciones[${idx}][min]" class="w-full px-2 py-1" placeholder="p. ej. 500"></td>
      <td class="border"><input name="tabla_recomendaciones[${idx}][max]" class="w-full px-2 py-1" placeholder="p. ej. 1500"></td>
      <td class="border text-center"><button type="button" class="rec-del px-2">×</button></td>
    `;
    tbody.appendChild(tr);
    idx++;
  });

  tbody?.addEventListener('click', (e) => {
    if (e.target.classList.contains('rec-del')) {
      e.target.closest('tr')?.remove();
    }
  });
})();
</script>
</x-modal>