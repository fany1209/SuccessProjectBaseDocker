<x-modal id="12">
  <form id="seguimiento12-form" method="POST" action="{{ route('laboratory.pdf12') }}" target="_blank" class="space-y-6">
    @csrf

    {{-- =================== Datos generales =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Datos generales</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Asesor técnico</label>
          <input type="text" name="asesor" class="w-full border rounded px-2 py-1" value="{{ old('asesor') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Productor</label>
          <select name="productor_id" class="w-full border rounded px-2 py-1" required>
            <option value="">Seleccione un productor</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->customer_id }}">{{ $customer->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold">Cultivo a tratar</label>
          <input type="text" name="cultivo" class="w-full border rounded px-2 py-1" value="{{ old('cultivo') }}">
        </div>
       <div>
          <label class="block text-sm font-semibold">FOLIO</label>
          <select name="folio_muestra" class="w-full border rounded px-2 py-1" required>
            <option value="">Seleccione un folio…</option>
            @foreach(($folios ?? collect()) as $f)
              <option value="{{ $f }}" @selected(old('folio_muestra') === $f)>{{ $f }}</option>
            @endforeach
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Producto para aplicar</label>
          <input type="text" name="producto_aplicar" class="w-full border rounded px-2 py-1" value="{{ old('producto_aplicar') }}">
        </div>
       <div>
        <label class="block text-sm font-semibold">Peso</label>
        <input type="text" name="peso_text"
              class="w-full border rounded px-2 py-1"
              value="{{ old('peso_text') }}"
              placeholder="Ej. 25 kg o 300 g">
      </div>
      </div>
    </div>

    {{-- =================== Objetivo / Condiciones / Ubicación =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Objetivo, condiciones y ubicación</div>
      <div class="p-3 space-y-4">
        <div>
          <label class="block text-sm font-semibold">Objetivo</label>
          <textarea name="objetivo" rows="3" class="w-full border rounded px-2 py-1">{{ old('objetivo') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold">Condiciones del campo</label>
          <textarea name="condiciones" rows="3" class="w-full border rounded px-2 py-1">{{ old('condiciones') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-start">
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold">Coordenadas</label>
            <input type="text" name="ubicacion" class="w-full border rounded px-2 py-1" value="{{ old('ubicacion') }}" placeholder="Ej. 20.5231, -100.8123">
          </div>

          <div class="md:col-span-3">
            <label class="block text-sm font-semibold">Ubicación</label>
            <input type="text" name="ubicacion_nombre" class="w-full border rounded px-2 py-1"
                  value="{{ old('ubicacion_nombre') }}" placeholder="Ej. Rancho El Mirador, Celaya, Gto.">
          </div>
        </div>
      </div>
    </div>

    {{-- =================== División / Tratamiento / Dosis =================== --}}
    <div class="border rounded" x-data>
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Diseño y tratamiento</div>
      <div class="p-3 space-y-4">
        <div>
          <label class="block text-sm font-semibold">División en bloques</label>
          <textarea name="division_bloques" rows="3" class="w-full border rounded px-2 py-1">{{ old('division_bloques') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold">Tratamiento</label>
          <textarea name="tratamiento" rows="3" class="w-full border rounded px-2 py-1">{{ old('tratamiento') }}</textarea>
        </div>

        {{-- Lista dinámica de dosis --}}
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold">Dosis (lista)</label>
            <button type="button" id="dosis-add" class="px-2 py-1 border rounded text-sm">+ Agregar</button>
          </div>
          @php $dosisOld = old('dosis', ['']); @endphp
          <div id="dosis-wrap" class="space-y-2">
            @foreach($dosisOld as $i => $d)
              <div class="flex gap-2">
                <input type="text" name="dosis[{{ $i }}]" class="w-full border rounded px-2 py-1" value="{{ $d }}" placeholder="p. ej., 2 L/ha en riego">
                <button type="button" class="dosis-del px-2 border rounded">×</button>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- =================== Fechas =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Fechas</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
          <label class="block text-sm font-semibold">Fecha de aplicación</label>
          <input type="date" name="fecha_aplicacion" class="w-full border rounded px-2 py-1" value="{{ old('fecha_aplicacion') }}">
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold">Documento resultado de muestreo</label>
          <input type="text" name="doc_muestreo" class="w-full border rounded px-2 py-1" value="{{ old('doc_muestreo') }}" placeholder="ID o nombre del documento">
        </div>

        <div class="md:col-span-4">
          <label class="block text-sm font-semibold">Fechas de muestreo</label>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-1">
            <div>
              <span class="block text-xs text-gray-600 mb-1">Antes de aplicación</span>
              <input type="date" name="fechas_muestreo[antes]" class="w-full border rounded px-2 py-1" value="{{ old('fechas_muestreo.antes') }}">
            </div>
            <div>
              <span class="block text-xs text-gray-600 mb-1">15 días</span>
              <input type="date" name="fechas_muestreo[15]" class="w-full border rounded px-2 py-1" value="{{ old('fechas_muestreo.15') }}">
            </div>
            <div>
              <span class="block text-xs text-gray-600 mb-1">25 días</span>
              <input type="date" name="fechas_muestreo[25]" class="w-full border rounded px-2 py-1" value="{{ old('fechas_muestreo.25') }}">
            </div>
            <div>
              <span class="block text-xs text-gray-600 mb-1">35 días</span>
              <input type="date" name="fechas_muestreo[35]" class="w-full border rounded px-2 py-1" value="{{ old('fechas_muestreo.35') }}">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- =================== Variables / Observaciones =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Variables y observaciones</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Variables agronómicas</label>
          <textarea name="variables_agro" rows="4" class="w-full border rounded px-2 py-1" placeholder="Rendimiento, altura, hojas, tamaño de fruto, etc.">{{ old('variables_agro') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold">Observaciones adicionales</label>
          <textarea name="observaciones" rows="4" class="w-full border rounded px-2 py-1">{{ old('observaciones') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== Botones =================== --}}
    <div class="flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" form="seguimiento12-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">
        Generar PDF
      </x-button>
    </div>
  </form>

  {{-- ===== JS: lista dinámica de dosis ===== --}}
  <script>
    (function(){
      const wrap = document.getElementById('dosis-wrap');
      const add  = document.getElementById('dosis-add');

      add?.addEventListener('click', ()=>{
        const idx = wrap.querySelectorAll('input[name^="dosis["]').length;
        const row = document.createElement('div');
        row.className = 'flex gap-2';
        row.innerHTML = `
          <input type="text" name="dosis[${idx}]" class="w-full border rounded px-2 py-1" placeholder="p. ej., 2 L/ha en riego">
          <button type="button" class="dosis-del px-2 border rounded">×</button>
        `;
        wrap.appendChild(row);
      });

      wrap?.addEventListener('click', (e)=>{
        if(e.target.classList.contains('dosis-del')){
          e.target.closest('div.flex')?.remove();
        }
      });
    })();
  </script>
</x-modal>
