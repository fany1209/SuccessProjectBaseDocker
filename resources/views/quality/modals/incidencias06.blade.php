<x-modal id="inc">
<form method="POST" action="{{ route('quality.pdf6') }}" target="_blank" class="space-y-6" enctype="multipart/form-data">
  @csrf
  <div class="border rounded-lg p-4 space-y-4">
    <h3 class="text-sm font-semibold">Información del producto</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Folio</label>
        <input type="text" 
              class="w-full rounded-md border border-gray-300 px-3 py-2 bg-gray-100 cursor-not-allowed" 
              value="Se asignará automáticamente" readonly>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fecha de recepción</label>
        <input type="date" name="fecha_recepcion"
               class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha_recepcion') }}">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Proveedor</label>
        <select name="proveedor" class="w-full rounded-md border border-gray-300 px-3 py-2">
          <option value="">— Selecciona —</option>
          @foreach(($suppliers ?? []) as $s)
            <option value="{{ $s->name }}" {{ old('proveedor')===$s->name ? 'selected' : '' }}>
              {{ $s->name }} — {{ $s->supplier_code }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fecha de reporte</label>
        <input type="date" name="fecha_reporte"
               class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha_reporte') }}">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Estado del producto</label>
        <select name="mpptme" class="w-full rounded-md border border-gray-300 px-3 py-2">
          <option value="">— Selecciona —</option>
          @foreach (['MP'=>'MP','PT'=>'PT','PP'=>'PP'] as $k=>$v)
            <option value="{{ $k }}" {{ old('mpptme')===$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>

    {{-- Tipo de lote + valor --}}
    <div class="md:col-span-2">
      <span class="block text-sm font-medium mb-1">Tipo de lote</span>
      <label class="inline-flex items-center mr-4">
        <input type="radio" name="lote_tipo" value="interno" class="mr-2"
              {{ old('lote_tipo','interno') === 'interno' ? 'checked' : '' }}>
        <span>Interno</span>
      </label>
      <label class="inline-flex items-center">
        <input type="radio" name="lote_tipo" value="proveedor" class="mr-2"
              {{ old('lote_tipo') === 'proveedor' ? 'checked' : '' }}>
        <span>Proveedor</span>
      </label>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">
        Lote <span id="lote-label-extra" class="text-gray-500"></span>
      </label>
      <input type="text" name="lote" placeholder="SPLC280525"
            class="w-full rounded-md border border-gray-300 px-3 py-2"
            value="{{ old('lote') }}">
    </div>
      <div>
        <label class="block text-sm font-medium mb-1">Producto</label>
      <select name="product_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
          <option value="">— Selecciona —</option>
          @foreach(($products ?? []) as $p)
              <option value="{{ $p->product_id }}" {{ old('product_id') == $p->product_id ? 'selected' : '' }}>
                  {{ $p->name }}
              </option>
          @endforeach
      </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Cantidad Recibida</label>
        <input type="text" name="remitidos" placeholder="Ej. 6000 KG o 6000 LTS"
              class="w-full rounded-md border border-gray-300 px-3 py-2"
              value="{{ old('remitidos') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fecha de incidencia</label>
        <input type="date" name="fecha_incidencia"
               class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha_incidencia') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Incidencia (resumen)</label>
        <input type="text" name="incidencia" placeholder="Tarimas mal emplayadas y presionadas"
               class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('incidencia') }}">
      </div>
    </div>
  </div>

  {{-- Descripción + Imagen (una tabla, una imagen por descripción) --}}
  <div class="border rounded-lg p-4">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-sm font-semibold">Descripción de la incidencia (con evidencia)</h3>
      <button type="button" id="btn-add-descimg"
              class="rounded-lg border px-3 py-1 text-sm hover:bg-gray-50">+ Agregar fila</button>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="text-left">
            <th class="px-3 py-2 w-[60%]">Descripción</th>
            <th class="px-3 py-2 w-[25%]">Imagen</th>
            <th class="px-3 py-2 w-[10%] text-center">Acción</th>
          </tr>
        </thead>
        <tbody id="descimg-rows">
          @php
            // Soporta old() como arreglo de strings o como arreglo de objetos {texto, imagen}
            $oldDesc = old('descripcion', [['texto' => '']]);
            $rows = [];
            if (is_array($oldDesc)) {
                foreach ($oldDesc as $v) {
                    $rows[] = [
                        'texto' => is_array($v) ? ($v['texto'] ?? '') : (string)$v,
                    ];
                }
            } else {
                $rows = [['texto' => '']];
            }
          @endphp

          @foreach($rows as $i => $r)
            <tr class="border-t descimg-row align-top">
              <td class="px-3 py-2">
                <input type="text"
                      name="descripcion[{{ $i }}][texto]"
                      value="{{ $r['texto'] }}"
                      placeholder="Ej.: Emplayado deficiente en sacos de levadura"
                      class="w-full rounded-md border border-gray-300 px-3 py-2">
              </td>

              <td class="px-3 py-2">
                <input type="file"
                      name="descripcion[{{ $i }}][imagen]"
                      accept="image/*"
                      class="block w-full text-xs descimg-file">
                <div class="preview mt-2"></div>
              </td>

              <td class="px-3 py-2 text-center">
                <button type="button"
                        class="btn-del-descimg text-red-600 hover:underline">Quitar</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  <p class="text-xs text-gray-500 mt-2">
    Sube una imagen por cada descripción. Tamaño recomendado ≤ 2&nbsp;MB (JPG o PNG).
  </p>
</div>

  {{-- Comentarios --}}
  <div>
    <label class="block text-sm font-medium mb-1">Comentarios</label>
    <textarea name="comentarios" rows="4"
              class="w-full rounded-md border border-gray-300 px-3 py-2"
              placeholder="Recomendaciones, acciones sugeridas, notas...">{{ old('comentarios') }}</textarea>
  </div>

    {{-- Nombre del responsable (para firmar) --}}
    <div class="mt-6">
    <label class="block text-sm font-medium mb-1">Nombre del responsable</label>
    <input type="text" name="firma_nombre"
            class="w-full rounded-md border border-gray-300 px-3 py-2"
            placeholder="Ej. Juan Pérez">
    </div>

  <div class="flex items-center gap-3">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-2">Generar PDF</x-button>
  </div>
</form>

{{-- JS --}}
<script>
(function(){
  const tbody = document.getElementById('descimg-rows');
  const btnAdd = document.getElementById('btn-add-descimg');
  if(!tbody) return; 

  let idx = tbody.querySelectorAll('.descimg-row').length || 0;

  function makeRow(i, texto = ''){
    const tr = document.createElement('tr');
    tr.className = 'border-t descimg-row align-top';
    tr.innerHTML = `
      <td class="px-3 py-2">
        <input type="text"
               name="descripcion[${i}][texto]"
               value="${texto?.replaceAll('"','&quot;') || ''}"
               placeholder="Ej.: Se observaron tarimas con presión irregular"
               class="w-full rounded-md border border-gray-300 px-3 py-2">
      </td>
      <td class="px-3 py-2">
        <input type="file"
               name="descripcion[${i}][imagen]"
               accept="image/*"
               class="block w-full text-xs descimg-file">
        <div class="preview mt-2"></div>
      </td>
      <td class="px-3 py-2 text-center">
        <button type="button" class="btn-del-descimg text-red-600 hover:underline">Quitar</button>
      </td>
    `;
    return tr;
  }

  function bindRowEvents(tr){
    const fileInput = tr.querySelector('.descimg-file');
    const preview = tr.querySelector('.preview');

    fileInput?.addEventListener('change', function(){
      preview.innerHTML = '';
      const file = this.files?.[0];
      if (file && file.type.startsWith('image/')) {
        const url = URL.createObjectURL(file);
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'evidencia';
        img.style.width = '110px';
        img.style.height = '110px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '4px';
        img.style.border = '1px solid #ddd';
        preview.appendChild(img);
      }
    });
  }

  if (idx === 0) {
    const tr = makeRow(idx++);
    tbody.appendChild(tr);
    bindRowEvents(tr);
  } else {
    Array.from(tbody.querySelectorAll('.descimg-row')).forEach(bindRowEvents);
  }

  btnAdd?.addEventListener('click', () => {
    const tr = makeRow(idx++);
    tbody.appendChild(tr);
    bindRowEvents(tr);
  });

  tbody.addEventListener('click', (e) => {
    if (e.target?.classList.contains('btn-del-descimg')) {
      const rows = tbody.querySelectorAll('.descimg-row');
      if (rows.length > 1) {
        e.target.closest('.descimg-row')?.remove();
      }
    }
  });
})();
</script>
</x-modal>