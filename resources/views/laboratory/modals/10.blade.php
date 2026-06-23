<x-modal id="10">
    <form id="formulacion10-form" method="POST" action="{{ route('laboratory.pdf10') }}" target="_blank" class="space-y-6">
  @csrf

  {{-- =================== Encabezado =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Encabezado</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-6 gap-3">
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Fecha de formulación</label>
        <input type="date" name="fecha_formulacion" class="w-full border rounded px-2 py-1" value="{{ old('fecha_formulacion') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">SKU</label>
        <input type="text" name="sku" class="w-full border rounded px-2 py-1" value="{{ old('sku') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Lote</label>
        <input type="text" name="lote" class="w-full border rounded px-2 py-1" value="{{ old('lote') }}">
      </div>
    </div>
  </div>

  {{-- =================== Nombre del producto =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Producto</div>
    <div class="p-3">
      <label class="block text-sm font-semibold">Nombre del producto</label>
      <input type="text" name="nombre_producto" class="w-full border rounded px-2 py-1" value="{{ old('nombre_producto') }}">
    </div>
  </div>

  {{-- =================== Campo de aplicación / Uso =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Campo de aplicación / Uso</div>
    <div class="p-3 space-y-3">
      <div>
        <label class="block text-sm font-semibold">Campo de aplicación</label>
        <div class="flex flex-wrap gap-4 mt-1">
          @php $apOld = old('aplicacion', []); @endphp
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="agricola" {{ in_array('agricola',$apOld) ? 'checked' : '' }}> Agrícola
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="pecuario" {{ in_array('pecuario',$apOld) ? 'checked' : '' }}> Pecuario
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="petfood" {{ in_array('petfood',$apOld) ? 'checked' : '' }}> Petfood
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="otro" {{ in_array('otro',$apOld) ? 'checked' : '' }}> Otro
          </label>
        </div>
        <input type="text" name="ap_otro" class="mt-2 w-full border rounded px-2 py-1" placeholder="Especifique 'Otro' (opcional)" value="{{ old('ap_otro') }}">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label for="uso_especifico" class="block text-sm font-semibold">Uso Específico</label>
          <select name="uso_especifico" id="uso_especifico"
                  class="w-full border rounded px-2 py-1">
            <option value="" disabled {{ old('uso_especifico')===null ? 'selected' : '' }}>Elija un elemento.</option>
            <option value="Avícola"           {{ old('uso_especifico')==='Avícola' ? 'selected' : '' }}>Avícola</option>
            <option value="Porcícola"         {{ old('uso_especifico')==='Porcícola' ? 'selected' : '' }}>Porcícola</option>
            <option value="Acuícola"          {{ old('uso_especifico')==='Acuícola' ? 'selected' : '' }}>Acuícola</option>
            <option value="Carne"             {{ old('uso_especifico')==='Carne' ? 'selected' : '' }}>Carne</option>
            <option value="Leche"             {{ old('uso_especifico')==='Leche' ? 'selected' : '' }}>Leche</option>
            <option value="Bioestimulante"    {{ old('uso_especifico')==='Bioestimulante' ? 'selected' : '' }}>Bioestimulante</option>
            <option value="Nutrición vegetal" {{ old('uso_especifico')==='Nutrición vegetal' ? 'selected' : '' }}>Nutrición vegetal</option>
            <option value="Alimento"          {{ old('uso_especifico')==='Alimento' ? 'selected' : '' }}>Alimento</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold">Otro (uso específico)</label>
          <input type="text" name="uso_otro" class="w-full border rounded px-2 py-1" value="{{ old('uso_otro') }}">
        </div>
      </div>
    </div>
  </div>

  {{-- =================== Fórmula para producción =================== --}}
  <div class="border rounded" x-data>
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Fórmula para producción</div>

    <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
      <div>
        <label class="block text-sm font-semibold">Total producción (kg)</label>
        <input type="number" step="0.001" min="0" id="total_kg" name="total_produccion_kg" class="w-full border rounded px-2 py-1" value="{{ old('total_produccion_kg', 1000) }}">
      </div>
      <div class="md:col-span-3 text-sm text-gray-600">
        Si capturas **%**, el **kg** se calcula automáticamente: <em>kg = % × total / 100</em>.
      </div>
    </div>

    <div class="p-3">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-semibold">Materias primas</span>
        <button type="button" id="mp-add" class="px-2 py-1 border rounded text-sm">+ Ingrediente</button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border" id="mp-table">
          <thead>
            <tr class="bg-gray-100">
              <th class="border px-2 py-1" style="width:40%;">Materia prima</th>
              <th class="border px-2 py-1" style="width:10%;">%</th>
              <th class="border px-2 py-1" style="width:15%;">Cantidad (kg)</th>
              <th class="border px-2 py-1" style="width:35%;">Observaciones</th>
              <th class="border px-2 py-1">—</th>
            </tr>
          </thead>
          <tbody id="mp-rows">
            @php
              $rows = old('materias_primas', [
                ['nombre'=>'Ingrediente A','porcentaje'=>'','cantidad_kg'=>'','obs'=>''],
              ]);
            @endphp
            @foreach($rows as $i => $r)
              <tr>
                <td class="border">
                  <input name="materias_primas[{{ $i }}][nombre]" class="w-full px-2 py-1" value="{{ $r['nombre'] ?? '' }}" placeholder="Nombre de la materia prima">
                </td>
                <td class="border">
                  <input name="materias_primas[{{ $i }}][porcentaje]" class="w-full px-2 py-1 mp-por" type="number" step="0.001" min="0" value="{{ $r['porcentaje'] ?? '' }}" placeholder="%">
                </td>
                <td class="border">
                  <input name="materias_primas[{{ $i }}][cantidad_kg]" class="w-full px-2 py-1 mp-kg" type="number" step="0.001" min="0" value="{{ $r['cantidad_kg'] ?? '' }}" placeholder="kg">
                </td>
                <td class="border">
                  <input name="materias_primas[{{ $i }}][obs]" class="w-full px-2 py-1" value="{{ $r['obs'] ?? '' }}" placeholder="Notas/observaciones">
                </td>
                <td class="border text-center">
                  <button type="button" class="mp-del px-2">×</button>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="font-semibold">
              <td class="border px-2 py-1 text-center">TOTAL</td>
              <td class="border px-2 py-1 text-center"><span id="sum-por">0</span>%</td>
              <td class="border px-2 py-1 text-center"><span id="sum-kg">0</span> kg</td>
              <td class="border px-2 py-1"></td>
              <td class="border px-2 py-1"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- =================== Vida / Observaciones / Recomendaciones =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Vida útil y observaciones</div>
    <div class="p-3 grid grid-cols-1 gap-3">
      <div>
        <label class="block text-sm font-semibold">Vida de anaquel</label>
        <input type="text" name="vida_anaquel" class="w-full border rounded px-2 py-1" value="{{ old('vida_anaquel') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Observaciones del producto terminado</label>
        <textarea name="obs_producto" rows="3" class="w-full border rounded px-2 py-1">{{ old('obs_producto') }}</textarea>
      </div>
      <div>
        <label class="block text-sm font-semibold">Recomendaciones Generales</label>
        <textarea name="recomendaciones" rows="3" class="w-full border rounded px-2 py-1">{{ old('recomendaciones') }}</textarea>
      </div>
      <div>
        <label class="block text-sm font-semibold">Recomendación en la etiqueta</label>
        <textarea name="recom_etiqueta" rows="3" class="w-full border rounded px-2 py-1">{{ old('recom_etiqueta') }}</textarea>
      </div>
    </div>
  </div>

  {{-- =================== Anexos =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Anexos</div>
    <div class="p-3">
      <textarea name="anexos" rows="4" class="w-full border rounded px-2 py-1" placeholder="Referencias, documentos, etc.">{{ old('anexos') }}</textarea>
    </div>
  </div>

  <div class="flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="submit" form="formulacion10-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
  </div>
</form>

{{-- ===== JS: dinámico y cálculos ===== --}}
<script>
(function(){
  const tbody   = document.getElementById('mp-rows');
  const btnAdd  = document.getElementById('mp-add');
  const totalEl = document.getElementById('total_kg');
  const sumPor  = document.getElementById('sum-por');
  const sumKg   = document.getElementById('sum-kg');

  function parse(v){ const n = parseFloat(v); return isNaN(n) ? 0 : n; }

  function recalc(){
    const total = parse(totalEl.value);
    let tPor = 0, tKg = 0;
    tbody.querySelectorAll('tr').forEach(tr=>{
      const porEl = tr.querySelector('.mp-por');
      const kgEl  = tr.querySelector('.mp-kg');
      const por   = parse(porEl?.value || '');
      let  kg     = parse(kgEl?.value || '');

      if(porEl && porEl.value !== ''){
        kg = (por * total) / 100.0;
        if(kgEl){ kgEl.value = (Math.round(kg * 1000) / 1000).toString(); }
      }
      tPor += por;
      tKg  += parse(kgEl?.value || '');
    });
    sumPor.textContent = (Math.round(tPor * 1000) / 1000).toString();
    sumKg.textContent  = (Math.round(tKg  * 1000) / 1000).toString();
  }

  btnAdd?.addEventListener('click', ()=>{
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="border"><input name="materias_primas[${idx}][nombre]" class="w-full px-2 py-1" placeholder="Nombre de la materia prima"></td>
      <td class="border"><input name="materias_primas[${idx}][porcentaje]" class="w-full px-2 py-1 mp-por" type="number" step="0.001" min="0" placeholder="%"></td>
      <td class="border"><input name="materias_primas[${idx}][cantidad_kg]" class="w-full px-2 py-1 mp-kg" type="number" step="0.001" min="0" placeholder="kg"></td>
      <td class="border"><input name="materias_primas[${idx}][obs]" class="w-full px-2 py-1" placeholder="Notas/observaciones"></td>
      <td class="border text-center"><button type="button" class="mp-del px-2">×</button></td>
    `;
    tbody.appendChild(tr);
    recalc();
  });

  tbody?.addEventListener('input', (e)=>{
    if(e.target.matches('.mp-por, .mp-kg')) recalc();
  });

  tbody?.addEventListener('click', (e)=>{
    if(e.target.classList.contains('mp-del')){
      e.target.closest('tr')?.remove();
      recalc();
    }
  });

  totalEl?.addEventListener('input', recalc);

  recalc();
})();
</script>

</x-modal>