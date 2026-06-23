<x-modal id="almacen">
    <form method="POST" action="{{ route('quality.pdf4') }}" class="space-y-6"  enctype="multipart/form-data">
  @csrf

  {{-- ================= Información de la inspección ================= --}}
  <div>
    <h3 class="text-base font-semibold mb-3">Información de la inspección</h3>
    <div class="grid md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Fecha de inspección</label>
        <input type="date" name="fecha_inspeccion"
               value="{{ old('fecha_inspeccion') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Inspector</label>
        <input type="text" name="inspector" placeholder="Nombre del inspector"
               value="{{ old('inspector') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Hora</label>
        <input type="time" name="hora_turno"
               value="{{ old('hora_turno') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
      </div>
    </div>

    <div class="mt-4 grid md:grid-cols-3 gap-4">
      <div>
        <span class="block text-sm font-medium mb-1">Turno</span>
        <label class="inline-flex items-center">
          <input type="radio" name="turno" value="1" {{ old('turno') === '1' ? 'checked' : '' }}
                 class="mr-2">
          <span>1</span>
        </label>
        <label class="inline-flex items-center">
          <input type="radio" name="turno" value="2" {{ old('turno') === '2' ? 'checked' : '' }}
                 class="mr-2">
          <span>2</span>
        </label>
        <label class="inline-flex items-center">
          <input type="radio" name="turno" value="3" {{ old('turno') === '3' ? 'checked' : '' }}
                 class="mr-2">
          <span>3</span>
        </label>
         <label class="inline-flex items-center">
          <input type="radio" name="turno" value="mixto" {{ old('turno') === 'mixto' ? 'checked' : '' }}
                 class="mr-2">
          <span>Mixto</span>
        </label>
      </div>

      <div class="md:col-span-2">
        <span class="block text-sm font-medium mb-1">Área</span>
        <label class="inline-flex items-center mr-4">
          <input type="checkbox" name="area[]" value="nave1"
                 {{ collect(old('area', []))->contains('nave1') ? 'checked' : '' }} class="mr-2">
          <span>Nave 1</span>
        </label>
        <label class="inline-flex items-center mr-4">
          <input type="checkbox" name="area[]" value="nave2"
                 {{ collect(old('area', []))->contains('nave2') ? 'checked' : '' }} class="mr-2">
          <span>Nave 2</span>
        </label>
        <label class="inline-flex items-center">
          <input id="area-otro-check" type="checkbox" name="area[]" value="otro"
                 {{ collect(old('area', []))->contains('otro') ? 'checked' : '' }} class="mr-2">
          <span>Otro</span>
        </label>
        <input id="area-otro-text" type="text" name="area_otro" placeholder="Especifique"
               value="{{ old('area_otro') }}"
               class="mt-2 w-full md:w-1/2 rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
      </div>
    </div>

    {{--<div class="mt-4 grid md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Responsable del área</label>
        <input type="text" name="responsable" placeholder="Nombre del responsable"
               value="{{ old('responsable') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
      </div>
    </div>--}}
  </div>

{{-- === Tabla de observaciones=== --}}
    <div class="mt-6">
      <div class="flex items-center justify-between mb-2">
        <h4 class="text-sm font-semibold">Observaciones</h4>
        <button type="button" id="btn-add-obs"
                class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50">
          + Agregar fila
        </button>
      </div>

      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left">
             <th class="px-3 py-2 w-[18%]">Observación</th>
              <th class="px-3 py-2 w-[22%]">Evidencia (imagen o texto)</th>
              <th class="px-3 py-2 w-[18%]">Ubicación</th>
              <th class="px-3 py-2 w-[14%]" colspan="2">Revisión</th>
              <th class="px-3 py-2 w-[6%] text-center">Acción</th>
            </tr>
          </thead>
          <tbody id="obs-rows">
            @php
              $catalogoObs = [
                'Producto sucio','Insectos','Producto mal identificado',
                'Lugar de trabajo desordenado y sucio','Material o producto fuera de su lugar',
                'Producto expuesto','Piso sucio','Tarima o rack sin identificación',
                'Tarima rota','Producto sin identificación FIFO','Producto con emplaye roto o maltratado',
              ];
              $rows = collect(old('obs', [['name'=>'','evidencia'=>'','rev'=>null,'fecha'=>'','ev_corr'=>'']]));
            @endphp

            @foreach($rows as $i => $r)
              @php
                $name = $r['name'] ?? '';
                $isOther = $name !== '' && !in_array($name, $catalogoObs, true);
              @endphp
              <tr class="border-t dyn-obs">
                <td class="px-3 py-2">
                  <input type="hidden" name="obs[{{ $i }}][name]" value="{{ $name }}" class="obs-name-hidden">
                  <select class="obs-name-select w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                    <option value="" {{ $name==='' ? 'selected' : '' }}>Elige…</option>
                    @foreach($catalogoObs as $opt)
                      <option value="{{ $opt }}" {{ $opt===$name ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                    <option value="__otro__" {{ $isOther ? 'selected' : '' }}>Otro…</option>
                  </select>
                  <input type="text"
                        class="obs-name-other mt-2 w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"
                        placeholder="Especifica otra observación"
                        value="{{ $isOther ? $name : '' }}"
                        style="{{ $isOther ? '' : 'display:none;' }}">
                </td>

                {{-- Evidencia: imagen--}}
                <td class="px-3 py-2">
                  <input type="file" name="obs[{{ $i }}][evidencia_file]" accept="image/*"
                        class="mb-2 block w-full text-xs">
                </td>

                {{-- Ubicación --}}
                <td class="px-3 py-2">
                  <input type="text" name="obs[{{ $i }}][ubicacion]" placeholder="Ej. Rack A-3 / Andén 2"
                         value="{{ $r['ubicacion'] ?? '' }}"
                         class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </td>

                {{-- Revisión --}}
                <td class="px-3 py-2">
                  <label class="inline-flex items-center">
                    <input type="radio" name="obs[{{ $i }}][rev]" value="cumple"
                          {{ ($r['rev'] ?? '') === 'cumple' ? 'checked' : '' }} class="mr-2"> Cumple
                  </label>
                </td>
                <td class="px-3 py-2">
                  <label class="inline-flex items-center">
                    <input type="radio" name="obs[{{ $i }}][rev]" value="no_cumple"
                          {{ ($r['rev'] ?? '') === 'no_cumple' ? 'checked' : '' }} class="mr-2"> No cumple
                  </label>
                </td>
                <td class="px-3 py-2 text-center">
                  <button type="button" class="btn-remove-obs text-red-600 hover:underline">Quitar</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  {{-- ================= Comentarios de calidad ================= --}}
  <div>
    <h3 class="text-base font-semibold mb-2">Comentarios de calidad</h3>
    <textarea name="comentarios_q" id="comentarios_q" rows="5"
              placeholder="Observaciones generales, acuerdos, notas…"
              class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">{{ old('comentarios_q') }}</textarea>
  </div>

  <div class="flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    @can('quality.buttons.show')
      <x-button type="submit"
              formaction="{{ route('quality.almacen.store') }}"
              formmethod="POST"
              formenctype="multipart/form-data"
              class="bg-emerald-600 hover:bg-emerald-700 text-white">
              Guardar</x-button>
    @endcan
  </div>
</form>

<script>
(function(){
    const CATALOG = @json($catalogoObs);
    const tbody   = document.getElementById('obs-rows');
    const btnAdd  = document.getElementById('btn-add-obs');
    let idx       = {{ count($rows) }};

    function bindRow(row){
        const sel   = row.querySelector('.obs-name-select');
        const other = row.querySelector('.obs-name-other');
        const hid   = row.querySelector('.obs-name-hidden');
        const fileInput = row.querySelector('input[type="file"]');

        function syncFromSelect(){
            if(sel.value === '__otro__'){
                other.style.display = '';
                hid.value = other.value.trim();
            } else {
                other.style.display = 'none';
                hid.value = sel.value;
            }
        }

        function syncFromOther(){
            if(sel.value === '__otro__') hid.value = other.value.trim();
        }

        sel.addEventListener('change', syncFromSelect);
        other.addEventListener('input', syncFromOther);
        syncFromSelect();

        if(fileInput){
            const previewContainer = document.createElement('div');
            previewContainer.className = 'preview-container mt-2';
            fileInput.insertAdjacentElement('afterend', previewContainer);

            fileInput.addEventListener('change', function(){
                previewContainer.innerHTML = '';
                const file = this.files[0];
                if(file && file.type.startsWith('image/')){
                    const reader = new FileReader();
                    reader.onload = function(e){
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '80px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '4px';
                        previewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    Array.from(tbody.querySelectorAll('tr.dyn-obs')).forEach(bindRow);

    function rowTemplate(i){
        const options = ['<option value="">Elige…</option>']
            .concat(CATALOG.map(o => `<option value="${o}">${o}</option>`))
            .concat(['<option value="__otro__">Otro…</option>'])
            .join('');

        return `
        <tr class="border-t dyn-obs">
            <td class="px-3 py-2">
                <input type="hidden" name="obs[${i}][name]" class="obs-name-hidden" value="">
                <select class="obs-name-select w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                    ${options}
                </select>
                <input type="text" class="obs-name-other mt-2 w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"
                       placeholder="Especifica otra observación" style="display:none;">
            </td>
            <td class="px-3 py-2">
                <input type="file" name="obs[${i}][evidencia_file]" accept="image/*" class="mb-2 block w-full text-xs">
            </td>
            <td class="px-3 py-2">
                <input type="text" name="obs[${i}][ubicacion]" placeholder="Ej. Rack A-3 / Andén 2"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
            </td>
            <td class="px-3 py-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="obs[${i}][rev]" value="cumple" class="mr-2"> Cumple
                </label>
            </td>
            <td class="px-3 py-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="obs[${i}][rev]" value="no_cumple" class="mr-2"> No cumple
                </label>
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="btn-remove-obs text-red-600 hover:underline">Quitar</button>
            </td>
        </tr>`;
    }

    btnAdd?.addEventListener('click', () => {
        tbody.insertAdjacentHTML('beforeend', rowTemplate(idx));
        bindRow(tbody.lastElementChild);
        idx++;
    });

    tbody.addEventListener('click', (e) => {
        if(e.target.classList.contains('btn-remove-obs')){
            const all = tbody.querySelectorAll('tr.dyn-obs');
            if(all.length > 1) e.target.closest('tr')?.remove();
        }
    });
})();
</script>
</x-modal>

