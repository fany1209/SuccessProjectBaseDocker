<x-modal id="sp">
  <form id="salida10-form" method="POST" action="{{ route('quality.pdf10') }}" enctype="multipart/form-data" class="w-full space-y-6">
    @csrf
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Datos Generales</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <x-label value="Cliente"/>
          @php
            $sorted = collect($customers)->sortBy(
              fn($c) => mb_strtolower($c->name ?? '', 'UTF-8'),
              SORT_NATURAL
            );
          @endphp

          <select id="customer" name="customer_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
            <option value="">-- Selecciona un cliente --</option>
            @foreach($sorted as $c)
              <option value="{{ $c->customer_id }}"
                      data-code="{{ $c->customer_code ?? '' }}"
                      {{ (string)old('customer_id') === (string)$c->customer_id ? 'selected' : '' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>

          <input type="hidden" name="customer_code" id="customer_code">
          @error('customer_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <x-label value="Fecha de inspección"/>
          <input type="date" name="fecha_inspeccion" value="{{ old('fecha_inspeccion') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
          @error('fecha_inspeccion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    <!-- DATOS DEL PRODUCTO -->
    <div class="border rounded-lg p-4 space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Datos del producto</h2>
        <button type="button" id="add-row" class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 text-sm">
          + Agregar producto
        </button>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm text-center">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-2 py-2 w-[6%]">N°</th>
              <th class="px-2 py-2 w-[36%]">Producto</th>
              <th class="px-2 py-2 w-[16%]">N° de lote</th>
              <th class="px-2 py-2 w-[14%]">Presentación</th>
              <th class="px-2 py-2 w-[12%]">Cantidad</th>
              <th class="px-2 py-2 w-[16%]">Empaque</th>
              <th class="px-2 py-2 w-[8%]">Acción</th>
            </tr>
          </thead>
          <tbody id="rows">
            <tr class="data-row">
              <td class="px-2 py-2"><span class="row-num">1</span></td>
              <td class="px-2 py-2">
                <select name="items[0][product_id]" class="w-full border rounded px-2 py-1" required>
                  <option value="">Seleccione…</option>
                  @foreach($products as $p)
                    <option value="{{ $p->product_id }}">{{ $p->name }}</option>
                  @endforeach
                </select>
              </td>
              <td class="px-2 py-2">
                <input type="text" name="items[0][lote]" class="w-full border rounded px-2 py-1" required>
              </td>
              <td class="px-2 py-2">
                <input type="text" name="items[0][presentacion]" class="w-full border rounded px-2 py-1">
              </td>
              <td class="px-2 py-2">
                <input type="number" name="items[0][cantidad]" min="0" step="any" class="w-full border rounded px-2 py-1 text-right">
              </td>
              <td class="px-2 py-2">
                <input type="text" name="items[0][empaque]" class="w-full border rounded px-2 py-1">
              </td>
              <td class="px-2 py-2">
                <button type="button" class="remove-row text-red-600">Quitar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <template id="row-tpl">
        <tr class="data-row">
          <td class="px-2 py-2"><span class="row-num">#</span></td>
          <td class="px-2 py-2">
            <select name="items[0][product_id]" class="w-full border rounded px-2 py-1" required>
              <option value="">Seleccione…</option>
              @foreach($products as $p)
                <option value="{{ $p->product_id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </td>
          <td class="px-2 py-2"><input type="text" name="items[0][lote]" class="w-full border rounded px-2 py-1" required></td>
          <td class="px-2 py-2"><input type="text" name="items[0][presentacion]" class="w-full border rounded px-2 py-1"></td>
          <td class="px-2 py-2"><input type="number" name="items[0][cantidad]" min="0" step="any" class="w-full border rounded px-2 py-1 text-right"></td>
          <td class="px-2 py-2"><input type="text" name="items[0][empaque]" class="w-full border rounded px-2 py-1"></td>
          <td class="px-2 py-2"><button type="button" class="remove-row text-red-600">Quitar</button></td>
        </tr>
      </template>
    </div>

    <!-- LIBERACIÓN DE PRODUCTO -->
    <div class="border rounded-lg p-4 space-y-3">
      <h2 class="font-semibold">Liberación de producto</h2>
      <p class="text-sm text-gray-600">Marca una opción por requisito: <b>Cumple</b>, <b>No aplica</b> o deja sin marcar (se interpretará como “No cumple”).</p>

      @php
        $reqs_limpieza = [
          'fauna_nociva' => 'Libre de fauna nociva',
          'empaque_limpio' => 'Empaque o emplaye limpio',
          'empaque_sin_rupturas' => 'Empaque,envase y/o emplaye limpio, sin rasgaduras y/o rotos',
          'libre_materia_extrana' => 'Libre de materia extraña (basura, piedras, tierra, cenizas, lodo, etc.)',
          'mezcla_homogenea' => 'Mezcla homogénea / sin aglomeraciones',
          'envase_sellado' => 'Envase sellado (sin derrames)',
          'marca_volumen' => 'Correcto llevado a la marca de volumen',
        ];
        $reqs_etiqueta = [
          'etiq_lote' => 'Lote',
          'etiq_nombre' => 'Nombre del producto',
          'etiq_caducidad' => 'Fecha de caducidad',
          'etiq_contenido' => 'Contenido neto',
          'etiq_recomendaciones' => 'Recomendaciones',
        ];
      @endphp

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-xs">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-2 py-2 w-[70%] text-left">Requisito</th>
              <th class="px-2 py-2 w-[15%]">Cumple</th>
              <th class="px-2 py-2 w-[15%]">No aplica</th>
            </tr>
          </thead>
          <tbody>
            <tr><td class="px-2 py-2 font-semibold bg-gray-50" colspan="3">Limpieza</td></tr>
            @foreach($reqs_limpieza as $key => $label)
              <tr>
                <td class="px-2 py-2 text-left">{{ $label }}</td>
                <td class="px-2 py-2"><input type="radio" name="lib_{{ $key }}" value="cumple"></td>
                <td class="px-2 py-2"><input type="radio" name="lib_{{ $key }}" value="no_aplica"></td>
              </tr>
            @endforeach

            <tr><td class="px-2 py-2 font-semibold bg-gray-50" colspan="3">Datos especificados en etiqueta</td></tr>
            @foreach($reqs_etiqueta as $key => $label)
              <tr>
                <td class="px-2 py-2 text-left">{{ $label }}</td>
                <td class="px-2 py-2"><input type="radio" name="lib_{{ $key }}" value="cumple"></td>
                <td class="px-2 py-2"><input type="radio" name="lib_{{ $key }}" value="no_aplica"></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- OBSERVACIONES -->
    <div class="border rounded-lg p-4">
      <h3 class="text-sm font-semibold">Observaciones</h3>
      <textarea name="observaciones" rows="5" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="Notas, hallazgos, acuerdos, acciones…">{{ old('observaciones') }}</textarea>
    </div>

    <!-- VERIFICACIÓN DEL TRANSPORTE -->
    <div class="border rounded-lg p-4 space-y-3">
      <h2 class="font-semibold">Verificación del transporte</h2>

      @php
        $transp = [
          'fumigacion' => 'Certificado de fumigación',
          'limpieza'   => 'Limpieza (libre de basura, material o equipo extraño, sin vómito, sin heces fecales)',
          'aromas'     => 'Libre de aromas atípicos',
          'puertas'    => 'Puertas herméticas',
          'piso'       => 'Piso (sin orificios, sin desprendimiento de maderas, sellados)',
          'techo'      => 'Techo (sellado, sin desprendimiento de pintura)',
          'paredes'    => 'Paredes sin astillas o filos cortantes',
          'otro'       => 'Otro (especifique)',
        ];
      @endphp

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-xs">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-2 py-2 w-[40%] text-left">Concepto</th>
              <th class="px-2 py-2 w-[10%]">Cumple</th>
              <th class="px-2 py-2 w-[13%]">No aplica</th>
              <th class="px-2 py-2 w-[37%] text-left">Observaciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transp as $key => $label)
              <tr>
                <td class="px-2 py-2 text-left">{{ $label }}</td>
                <td class="px-2 py-2 text-center"><input type="radio" name="tr_{{ $key }}" value="cumple"></td>
                <td class="px-2 py-2 text-center"><input type="radio" name="tr_{{ $key }}" value="no_aplica"></td>
                <td class="px-2 py-2"><input type="text" name="tr_{{ $key }}_obs" class="w-full border rounded px-2 py-1" placeholder="Observaciones"></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- EVIDENCIA FOTOGRÁFICA -->
    <div class="border rounded-lg p-4 space-y-3">
      <h2 class="font-semibold">Evidencia Fotográfica</h2>
      <p class="text-sm text-gray-600">Sube hasta 3 imágenes como evidencia (opcional).</p>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @for($i = 0; $i < 3; $i++)
          <div class="flex flex-col space-y-2 border border-gray-100 p-2 rounded-md bg-gray-50/50">
            <x-label value="Imagen {{ $i + 1 }}"/>
            <input type="file" name="evidencias[]" accept="image/*" class="evidencia-input w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white">
            <div class="flex justify-center items-center h-36 bg-gray-100 rounded-md border border-dashed border-gray-300 overflow-hidden">
              <img id="preview-{{ $i }}" src="#" alt="Vista previa {{ $i + 1 }}" class="hidden max-h-full max-w-full object-contain p-1">
              <span id="placeholder-{{ $i }}" class="text-xs text-gray-400 text-center px-2">Sin archivo seleccionado</span>
            </div>
          </div>
        @endfor
      </div>
    </div>

    <!-- FIRMAS -->
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-2">Nombre</h2>
      <x-label value="Nombre de quien realizó la inspección"/>
      <input type="text" name="inspector_nombre" class="w-full border rounded px-3 py-2" value="{{ old('inspector_nombre') }}">
    </div>

    <div class="flex justify-end">
        <x-button type="button" class="close-modal bg-gray-800 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
        <x-button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Generar PDF</x-button>
    </div>
  </form>
</x-modal>

@push('js')
<script>
(function() {
  const form = document.getElementById('salida10-form');
  if (!form) return;

  // 1. Manejo del código de cliente
  const customerSelect = form.querySelector('#customer');
  const customerCodeInput = form.querySelector('#customer_code');
  
  if (customerSelect) {
    customerSelect.addEventListener('change', function() {
      const code = this.selectedOptions[0]?.getAttribute('data-code') || '';
      if (customerCodeInput) customerCodeInput.value = code;
    });
  }

  // 2. Previsualizaciones de Imagen
  form.querySelectorAll('.evidencia-input').forEach((input, index) => {
    input.addEventListener('change', function() {
      const imgPreview = document.getElementById(`preview-${index}`);
      const txtPlaceholder = document.getElementById(`placeholder-${index}`);
      const file = this.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          imgPreview.src = e.target.result;
          imgPreview.classList.remove('hidden');
          txtPlaceholder.classList.add('hidden');
        }
        reader.readAsDataURL(file);
      } else {
        imgPreview.src = '#';
        imgPreview.classList.add('hidden');
        txtPlaceholder.classList.remove('hidden');
      }
    });
  });

  // 3. Gestión dinámicas de filas de la Tabla
  const tbody  = form.querySelector('#rows');
  const addBtn = form.querySelector('#add-row');
  const tpl    = form.querySelector('#row-tpl');

  function clearRow(row) {
    row.querySelectorAll('input, select, textarea').forEach(el => {
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else el.value = '';
    });
  }

  function reindexRows() {
    const rows = tbody.querySelectorAll('tr.data-row');
    rows.forEach((row, idx) => {
      const num = row.querySelector('.row-num');
      if (num) num.textContent = idx + 1;

      row.querySelectorAll('input, select, textarea').forEach(el => {
        const name = el.getAttribute('name');
        if (!name) return;
        el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + idx + ']'));
      });
    });
  }

  function addRow() {
    if (!tpl || !tpl.content) {
      const last = tbody.querySelector('tr.data-row:last-of-type');
      if (!last) return;
      const clone = last.cloneNode(true);
      clearRow(clone);
      tbody.appendChild(clone);
    } else {
      const frag = tpl.content.cloneNode(true);
      const row  = frag.querySelector('tr.data-row');
      clearRow(row);
      tbody.appendChild(frag);
    }
    reindexRows();
  }

  function removeRow(btn) {
    const rows = tbody.querySelectorAll('tr.data-row');
    const tr = btn.closest('tr.data-row');
    if (!tr) return;

    if (rows.length <= 1) {
      clearRow(tr);
      reindexRows();
    } else {
      tr.remove();
      reindexRows();
    }
  }

  if (addBtn) addBtn.addEventListener('click', addRow);

  form.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-row');
    if (btn) {
      e.preventDefault();
      removeRow(btn);
    }
  });

  reindexRows();
})();
</script>
@endpush