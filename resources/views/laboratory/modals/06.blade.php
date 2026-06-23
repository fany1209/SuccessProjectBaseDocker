<x-modal id="06">
<form id="analisis-suelo-form" method="POST" action="{{ route('laboratory.pdf6') }}" enctype="multipart/form-data" target="_blank" class="space-y-6">
  @csrf

  {{-- =================== REPORTE / FECHAS =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">REPORTE Y FECHAS</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-1">
        <label class="block text-sm font-semibold">Fecha de Ingreso</label>
        <input type="date" name="fecha_ingreso" class="w-full border rounded px-2 py-1" value="{{ old('fecha_ingreso') }}">
      </div>
      <div class="md:col-span-1">
        <label class="block text-sm font-semibold">Fecha de Emisión</label>
        <input type="date" name="fecha_emision" class="w-full border rounded px-2 py-1" value="{{ old('fecha_emision') }}">
      </div>
    </div>
  </div>

  {{-- =================== INFORMACIÓN DEL CLIENTE =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">INFORMACIÓN DEL CLIENTE</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-semibold">Nombre del Productor</label>
        <input type="text" name="cliente_nombre" class="w-full border rounded px-2 py-1" value="{{ old('cliente_nombre') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Ciudad</label>
        <input type="text" name="cliente_ciudad" class="w-full border rounded px-2 py-1" value="{{ old('cliente_ciudad') }}">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Dirección</label>
        <input type="text" name="cliente_direccion" class="w-full border rounded px-2 py-1" value="{{ old('cliente_direccion') }}">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Teléfono de contacto</label>
        <input type="text" name="cliente_telefono" class="w-full border rounded px-2 py-1" value="{{ old('cliente_telefono') }}">
      </div>
    </div>
  </div>

  {{-- =================== INFORMACIÓN DE LA MUESTRA =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">INFORMACIÓN DE LA MUESTRA</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-semibold">Tipo de Cultivo</label>
        <input type="text" name="cultivo" class="w-full border rounded px-2 py-1" value="{{ old('cultivo') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Sistema</label>
        <input type="text" name="sistema" class="w-full border rounded px-2 py-1" value="{{ old('sistema') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Tipo</label>
        <input type="text" name="tipo_planta" class="w-full border rounded px-2 py-1" value="{{ old('tipo_planta') }}">
      </div>

      <div>
        <label class="block text-sm font-semibold">Peso de la muestra</label>
        <input type="text" name="peso_muestra" class="w-full border rounded px-2 py-1" value="{{ old('peso_muestra') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Testigo</label>
        <div class="flex items-center gap-2">
          <input type="checkbox" name="testigo" value="1" {{ old('testigo') ? 'checked' : '' }}>
          <span>Marcar si aplica</span>
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold">Ubicación</label>
        <input type="text" name="ubicacion" class="w-full border rounded px-2 py-1" value="{{ old('ubicacion') }}">
      </div>

      <div>
        <label class="block text-sm font-semibold">Tipo de muestreo</label>
        <input type="text" name="tipo_muestreo" class="w-full border rounded px-2 py-1" value="{{ old('tipo_muestreo') }}">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Responsable del muestreo</label>
        <input type="text" name="responsable_muestreo" class="w-full border rounded px-2 py-1" value="{{ old('responsable_muestreo') }}">
      </div>

      <div class="md:col-span-3">
        <label class="block text-sm font-semibold">Propósito</label>
        <input type="text" name="proposito" class="w-full border rounded px-2 py-1" value="{{ old('proposito') }}">
      </div>
    </div>
  </div>

  {{-- =================== ANÁLISIS CONVENCIONALES =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">ANÁLISIS CONVENCIONALES</div>
    <div class="p-3">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-semibold">Variables (tabla)</span>
        <button type="button" id="conv-add" class="px-2 py-1 border rounded text-sm">+ Fila</button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border">
          <thead>
            <tr class="bg-gray-100">
              <th class="border px-2 py-1">Variable</th>
              <th class="border px-2 py-1">Resultados</th>
              <th class="border px-2 py-1">Unidades</th>
              <th class="border px-2 py-1">—</th>
            </tr>
          </thead>
          <tbody id="conv-rows">
            @php
              $defaults = [
                ['v'=>'pH','r'=>'','u'=>'(2:1)'],
                ['v'=>'Conductividad eléctrica','r'=>'','u'=>'µS/cm'],
                ['v'=>'Saturación de humedad','r'=>'','u'=>'%'],
                ['v'=>'Materia Orgánica','r'=>'','u'=>'NA'],
                ['v'=>'Densidad Aparente','r'=>'','u'=>'g/cm³'],
              ];
              $convOld = is_array(old('convencionales')) ? old('convencionales') : $defaults;
            @endphp
            @foreach($convOld as $i => $row)
              <tr>
                <td class="border"><input name="convencionales[{{ $i }}][v]" class="w-full px-2 py-1" value="{{ $row['v'] ?? '' }}"></td>
                <td class="border"><input name="convencionales[{{ $i }}][r]" class="w-full px-2 py-1" value="{{ $row['r'] ?? '' }}"></td>
                <td class="border"><input name="convencionales[{{ $i }}][u]" class="w-full px-2 py-1" value="{{ $row['u'] ?? '' }}"></td>
                <td class="border text-center"><button type="button" class="conv-del px-2">×</button></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- =================== MACRONUTRIENTES =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">MACRONUTRIENTES</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-semibold">Nitrógeno (mg/kg)</label>
        <input type="number" step="0.01" name="n_val" class="w-full border rounded px-2 py-1" value="{{ old('n_val') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Fósforo (mg/kg)</label>
        <input type="number" step="0.01" name="p_val" class="w-full border rounded px-2 py-1" value="{{ old('p_val') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Potasio (mg/kg)</label>
        <input type="number" step="0.01" name="k_val" class="w-full border rounded px-2 py-1" value="{{ old('k_val') }}">
      </div>
    </div>
  </div>

  {{-- =================== TEXTURA DEL SUELO =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DETERMINACIÓN DE TEXTURA</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
      <div>
        <label class="block text-sm font-semibold">Arena (%)</label>
        <input type="number" step="0.01" name="arena" class="w-full border rounded px-2 py-1" value="{{ old('arena') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Limo (%)</label>
        <input type="number" step="0.01" name="limo" class="w-full border rounded px-2 py-1" value="{{ old('limo') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Arcilla (%)</label>
        <input type="number" step="0.01" name="arcilla" class="w-full border rounded px-2 py-1" value="{{ old('arcilla') }}">
      </div>
    </div>
  </div>

   {{-- =================== EVIDENCIA FOTOGRÁFICA =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">EVIDENCIA FOTOGRÁFICA</div>

    <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
      <div class="md:col-span-2">
        <label for="imagen_evidencia" class="block text-sm font-semibold">Añadir imagen</label>
        <input type="file"
               id="imagen_evidencia"
               name="imagen_evidencia"
               accept="image/*"
               class="w-full border rounded px-2 py-1">
        <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG/PNG. Tamaño máx. 3&nbsp;MB.</p>
      </div>

      <div class="md:col-span-1">
        <label class="block text-sm font-semibold">Vista previa</label>
        <div class="border rounded p-2 flex items-center justify-center h-32 bg-gray-50">
          <img id="preview_imagen_evidencia" class="max-h-28 max-w-full object-contain" alt="Sin imagen seleccionada">
        </div>
        <div class="mt-2">
          <button type="button" id="btn-clear-evidencia" class="px-2 py-1 border rounded text-sm">Quitar imagen</button>
        </div>
      </div>
    </div>
  </div>

  <div class="flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="submit" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
  </div>
</form>

{{-- =============== JS MÍNIMO PARA FILAS DINÁMICAS =============== --}}
  <script>
  (function(){
    const tbody = document.getElementById('conv-rows');
    let idx = tbody?.children.length || 0;
    document.getElementById('conv-add')?.addEventListener('click', () => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="border"><input name="convencionales[${idx}][v]" class="w-full px-2 py-1" placeholder="Variable"></td>
        <td class="border"><input name="convencionales[${idx}][r]" class="w-full px-2 py-1" placeholder="Resultado"></td>
        <td class="border"><input name="convencionales[${idx}][u]" class="w-full px-2 py-1" placeholder="Unidades"></td>
        <td class="border text-center"><button type="button" class="conv-del px-2">×</button></td>
      `;
      tbody.appendChild(tr);
      idx++;
    });
    tbody?.addEventListener('click', (e) => {
      if (e.target.classList.contains('conv-del')) {
        e.target.closest('tr')?.remove();
      }
    });

    const input = document.getElementById('imagen_evidencia');
    const preview = document.getElementById('preview_imagen_evidencia');
    const btnClear = document.getElementById('btn-clear-evidencia');
    const MAX_MB = 3;

    input?.addEventListener('change', () => {
      const file = input.files?.[0];
      if (!file) { preview.src = ''; preview.alt = 'No image selected'; return; }

      if (!file.type.startsWith('image/')) {
        alert('The selected file is not an image.');
        input.value = '';
        preview.src = '';
        preview.alt = 'No image selected';
        return;
      }

      const sizeMB = file.size / (1024 * 1024);
      if (sizeMB > MAX_MB) {
        alert('The image exceeds the maximum size of ' + MAX_MB + ' MB.');
        input.value = '';
        preview.src = '';
        preview.alt = 'No image selected';
        return;
      }

      const reader = new FileReader();
      reader.onload = e => {
        preview.src = e.target.result;
        preview.alt = 'Preview';
      };
      reader.readAsDataURL(file);
    });

    btnClear?.addEventListener('click', () => {
      input.value = '';
      preview.src = '';
      preview.alt = 'No image selected';
    });
  })();
  </script>
</x-modal>
