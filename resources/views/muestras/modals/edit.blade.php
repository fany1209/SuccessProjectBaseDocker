<x-modal id="edit-muestra">
    <form id="edit-muestra-form" class="space-y-4">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" id="edit-muestra-id">

      {{-- ======= Información de la muestra ======= --}}
      <div class="border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">INFORMACIÓN DE LA MUESTRA</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

          {{-- === Producto (select) === --}}
          <div class="lg:col-span-2">
            <label for="edit-product_id" class="block text-sm font-semibold">Producto</label>
            <select name="product_id" id="edit-product_id" class="w-full border rounded px-2 py-1" required>
              <option value="">— Selecciona un producto —</option>
              @if(isset($products))
                  @foreach($products as $p)
                    <option value="{{ $p->product_id }}" data-sku="{{ $p->sku }}">
                      {{ $p->name }}
                    </option>
                  @endforeach
              @endif
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold">Fecha de salida</label>
            <input type="date" name="fecha_salida" id="edit-fecha_salida" class="w-full border rounded px-2 py-1">
          </div>

          <div>
            <label class="block text-sm font-semibold">Nombre comercial</label>
            <input type="text" name="nombre_comercial" id="edit-nombre_comercial" class="w-full border rounded px-2 py-1">
          </div>

          {{-- === SKU === --}}
          <div>
            <label class="block text-sm font-semibold">SKU</label>
            <input type="text" name="sku" id="edit-sku" class="w-full border rounded px-2 py-1" readonly>
          </div>

          {{-- === Lote === --}}
          <div>
            <label class="block text-sm font-semibold">Lote</label>
            <input type="text" name="lote" id="edit-lote" list="edit-lote-list" class="w-full border rounded px-2 py-1" required autocomplete="off" placeholder="Seleccione o escriba un lote">
            <datalist id="edit-lote-list"></datalist>
          </div>

          <div class="lg:col-span-2">
            <label class="block text-sm font-semibold">Unidad de medida (UM)</label>
            <div class="flex flex-wrap gap-4 text-sm mt-1">
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="g" id="edit-um-g"> g</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="kg" id="edit-um-kg"> kg</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="ml" id="edit-um-ml"> ml</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="l" id="edit-um-l"> l</label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold">Cantidad</label>
            <input type="text" name="cantidad" id="edit-cantidad" class="w-full border rounded px-2 py-1" placeholder="p. ej., 2">
          </div>

          <div class="lg:col-span-4">
            <label class="block text-sm font-semibold">Descripción</label>
            <textarea name="descripcion" id="edit-descripcion" rows="2" class="w-full border rounded px-2 py-1"></textarea>
          </div>
        </div>
      </div>

      {{-- ======= Datos de salida ======= --}}
      <div class="border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE SALIDA</div>
        <div class="p-3 grid grid-cols-1 gap-3">
          <div>
            <label class="block text-sm font-semibold">Motivo de salida</label>
            <div class="flex flex-wrap gap-4 text-sm mt-1">
              <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="cliente"> Muestra a cliente</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="analisis"> Análisis de laboratorio</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="desarrollo"> Pruebas desarrollo</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="caducado"> Producto caducado</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="exposicion"> Exposición</label>
              <label class="inline-flex items-center gap-2"><input id="edit-mot_otro_radio" type="radio" name="motivo_salida" value="otro"> Otro</label>
              <input id="edit-mot_otro_txt" type="text" name="motivo_otro" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:240px;" disabled>
            </div>
          </div>

          {{-- Tipo de entrega --}}
          <div>
            <label class="block text-sm font-semibold">Tipo de entrega</label>
            <div class="flex flex-wrap gap-4 text-sm mt-1">
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_paqueteria" value="1" id="edit-entrega_paqueteria"> Paquetería</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_recoleccion_planta" value="1" id="edit-entrega_recoleccion_planta"> Recolección en planta</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_personal_empresa" value="1" id="edit-entrega_personal_empresa"> Personal de la empresa</label>
              <label class="inline-flex items-center gap-2"><input id="edit-entrega_otro_chk" type="checkbox" name="entrega_otro" value="1"> Otro</label>
              <input id="edit-entrega_otro_txt" type="text" name="entrega_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:240px;" disabled>
            </div>
          </div>
        </div>
      </div>

      {{-- ======= Datos de paquetería ======= --}}
      <div class="border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE PAQUETERÍA (si aplica)</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold">Empresa paquetería</label>
            <input type="text" name="paq_empresa" id="edit-paq_empresa" class="w-full border rounded px-2 py-1">
          </div>
          <div>
            <label class="block text-sm font-semibold">Guía</label>
            <input type="text" name="paq_guia" id="edit-paq_guia" class="w-full border rounded px-2 py-1">
          </div>
        </div>
      </div>

      {{-- ======= Datos de destinatario ======= --}}
      <div class="border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE DESTINATARIO (si aplica)</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
          <div class="lg:col-span-4">
            <label class="block text-sm font-semibold">Nombre</label>
            <input type="text" name="dest_nombre" id="edit-dest_nombre" class="w-full border rounded px-2 py-1">
          </div>
          <div class="lg:col-span-4">
            <label class="block text-sm font-semibold">Dirección</label>
            <input type="text" name="dest_direccion" id="edit-dest_direccion" class="w-full border rounded px-2 py-1">
          </div>
          <div>
            <label class="block text-sm font-semibold">Nombre de quien recibe</label>
            <input type="text" name="dest_recibe" id="edit-dest_recibe" class="w-full border rounded px-2 py-1">
          </div>
          <div>
            <label class="block text sm font-semibold">Correo electrónico</label>
            <input type="email" name="dest_correo" id="edit-dest_correo" class="w-full border rounded px-2 py-1">
          </div>
          <div class="lg:col-span-2">
            <label class="block text-sm font-semibold">Teléfono</label>
            <input type="text" name="dest_telefono" id="edit-dest_telefono" class="w-full border rounded px-2 py-1">
          </div>

          <div class="lg:col-span-4">
            <label class="block text-sm font-semibold">Documentación anexa</label>
            <div class="flex flex-wrap gap-4 text-sm mt-1">
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_cc" value="1" id="edit-docs_cc"> CC</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_ft" value="1" id="edit-docs_ft"> FT</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_hs" value="1" id="edit-docs_hs"> HS</label>
              <label class="inline-flex items-center gap-2"><input id="edit-docs_otro_chk" type="checkbox" name="docs_otro" value="1"> Otro</label>
              <input id="edit-docs_otro_txt" type="text" name="docs_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:240px;" disabled>
            </div>
          </div>
        </div>
      </div>

    </form>
    
    <div class="mt-4 flex justify-end gap-2">
        <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
            Cancelar
        </x-button>
        <x-button type="submit" form="edit-muestra-form" class="bg-green-600 hover:bg-green-700 text-white">
            Guardar
        </x-button>
    </div>

  <script>
    (function(){
      function toggleText(enable, el){ if(!el) return; el.disabled = !enable; if(!enable) el.value=''; }

      function syncMotivoEdit(){
        const v = (document.querySelector('#edit-muestra-form input[name="motivo_salida"]:checked')||{}).value;
        toggleText(v === 'otro', document.getElementById('edit-mot_otro_txt'));
      }
      document.querySelectorAll('#edit-muestra-form input[name="motivo_salida"]').forEach(r=>r.addEventListener('change', syncMotivoEdit));
      
      const entOtroChkEdit = document.getElementById('edit-entrega_otro_chk');
      const entOtroTxtEdit = document.getElementById('edit-entrega_otro_txt');
      function syncEntregaEdit(){ toggleText(!!entOtroChkEdit?.checked, entOtroTxtEdit); }
      entOtroChkEdit?.addEventListener('change', syncEntregaEdit);

      const docsOtroChkEdit = document.getElementById('edit-docs_otro_chk');
      const docsOtroTxtEdit = document.getElementById('edit-docs_otro_txt');
      function syncDocsEdit(){ toggleText(!!docsOtroChkEdit?.checked, docsOtroTxtEdit); }
      docsOtroChkEdit?.addEventListener('change', syncDocsEdit);

      const productSelectEdit = document.getElementById('edit-product_id');
      const skuInputEdit      = document.getElementById('edit-sku');
      const loteSelectEdit    = document.getElementById('edit-lote');
      const BATCHES_BY_PRODUCT = @json($batchesByProduct ?? []);

      function fillLotesEdit(pid) {
        const loteList = document.getElementById('edit-lote-list');
        const lotes = BATCHES_BY_PRODUCT?.[pid] || [];
        let html = '';
        for (const lote of lotes) {
          html += `<option value="${String(lote)}"></option>`;
        }
        if (loteList) loteList.innerHTML = html;
      }

      function onProductChangeEdit(){
        const pid = productSelectEdit?.value || '';
        const opt = productSelectEdit?.selectedOptions?.[0];
        const sku = opt ? (opt.getAttribute('data-sku') || '') : '';
        if (skuInputEdit) skuInputEdit.value = sku;
        
        if (pid) {
          if (loteSelectEdit) {
            loteSelectEdit.disabled = false;
            loteSelectEdit.placeholder = "Seleccione o escriba un lote";
          }
        } else {
          if (document.getElementById('edit-lote-list')) document.getElementById('edit-lote-list').innerHTML = '';
          if (loteSelectEdit) {
            loteSelectEdit.disabled = true;
            loteSelectEdit.placeholder = "— Selecciona un producto primero —";
          }
        }
        fillLotesEdit(pid);
      }

      productSelectEdit?.addEventListener('change', onProductChangeEdit);
      
      // We will expose a function to trigger syncs after population
      window.syncEditMuestraForm = function() {
        syncMotivoEdit();
        syncEntregaEdit();
        syncDocsEdit();
        onProductChangeEdit();
      };
    })();
  </script>
</x-modal>
