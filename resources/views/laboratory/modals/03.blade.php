<x-modal id="03">
  <form id="salida-muestras-form" method="POST" action="{{ route('laboratory.pdf3') }}" target="_blank" class="space-y-4">
    @csrf

   {{-- ======= Folio ======= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-semibold">Folio muestra *</label>

        <select name="folio_muestra" id="folio_muestra" class="w-full border rounded px-2 py-1" required>
          <option value="">Seleccione folio…</option>
          
          @foreach ($samples->sortByDesc('folio') as $s)
            <option value="{{ $s->folio }}"
                    data-product-id="{{ $s->product_id ?? '' }}"
                    data-sku="{{ $s->sku ?? '' }}"
                    data-producto="{{ $s->producto ?? '' }}"
                    @selected(old('folio_muestra') == $s->folio)>
              
              {{ $s->folio }} — {{ $s->producto ?? 'Sin producto' }}
              
            </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- ======= Información de la muestra ======= --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">INFORMACIÓN DE LA MUESTRA</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- === Producto (select) === --}}
        <div class="lg:col-span-2">
          <label for="product_id" class="block text-sm font-semibold">Producto</label>
          <select name="product_id" id="product_id"
                  class="w-full border rounded px-2 py-1"
                  required>
            <option value="">— Selecciona un producto —</option>
            @foreach($products as $p)
              <option value="{{ $p->product_id }}"
                      data-sku="{{ $p->sku }}"
                      {{ (int)old('product_id', $model->product_id ?? 0) === (int)$p->product_id ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold">Fecha de salida</label>
          <input type="date" name="fecha_salida" class="w-full border rounded px-2 py-1" value="{{ old('fecha_salida') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Nombre comercial</label>
          <input type="text" name="nombre_comercial" class="w-full border rounded px-2 py-1" value="{{ old('nombre_comercial') }}">
        </div>

        {{-- === SKU === --}}
        <div>
          <label class="block text-sm font-semibold">SKU</label>
          <input type="text" name="sku" id="sku"
                 class="w-full border rounded px-2 py-1"
                 value="{{ old('sku', $model->sku ?? '') }}"
                 readonly>
        </div>

        {{-- === Lote=== --}}
        <div>
          <label class="block text-sm font-semibold">Lote</label>
          <input type="text" name="lote" id="lote" list="lote-list" class="w-full border rounded px-2 py-1" required autocomplete="off" placeholder="Seleccione o escriba un lote" value="{{ old('lote', $model->lote ?? '') }}">
          <datalist id="lote-list"></datalist>
        </div>

        <div class="lg:col-span-2">
          <label class="block text-sm font-semibold">Unidad de medida (UM)</label>
          @php $um = old('um', $model->um ?? null); @endphp
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="g"  {{ $um==='g'?'checked':'' }}> g</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="kg" {{ $um==='kg'?'checked':'' }}> kg</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="ml" {{ $um==='ml'?'checked':'' }}> ml</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="l"  {{ $um==='l'?'checked':'' }}> l</label>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold">Cantidad</label>
          <input type="text" name="cantidad" class="w-full border rounded px-2 py-1" placeholder="p. ej., 2 kg" value="{{ old('cantidad', $model->cantidad ?? '') }}">
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Descripción</label>
          <textarea name="descripcion" rows="2" class="w-full border rounded px-2 py-1">{{ old('descripcion', $model->descripcion ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- ======= Datos de salida ======= --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE SALIDA</div>
      <div class="p-3 grid grid-cols-1 gap-3">
        <div>
          <label class="block text-sm font-semibold">Motivo de salida</label>
          @php $mot = old('motivo_salida', $model->motivo_salida ?? null); @endphp
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="cliente"    {{ $mot==='cliente'?'checked':'' }}> Muestra a cliente</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="analisis"    {{ $mot==='analisis'?'checked':'' }}> Análisis de laboratorio</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="desarrollo"  {{ $mot==='desarrollo'?'checked':'' }}> Pruebas desarrollo</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="caducado"    {{ $mot==='caducado'?'checked':'' }}> Producto caducado</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="motivo_salida" value="exposicion"  {{ $mot==='exposicion'?'checked':'' }}> Exposición</label>
            <label class="inline-flex items-center gap-2"><input id="mot_otro_radio" type="radio" name="motivo_salida" value="otro" {{ $mot==='otro'?'checked':'' }}> Otro</label>
            <input id="mot_otro_txt" type="text" name="motivo_otro" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('motivo_otro', $model->motivo_otro ?? '') }}" style="min-width:240px;">
          </div>
        </div>

        {{-- Tipo de entrega --}}
        <div>
          <label class="block text-sm font-semibold">Tipo de entrega</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_paqueteria" value="1" {{ old('entrega_paqueteria', $model->entrega_paqueteria ?? false) ? 'checked':'' }}> Paquetería</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_recoleccion_planta" value="1" {{ old('entrega_recoleccion_planta', $model->entrega_recoleccion_planta ?? false) ? 'checked':'' }}> Recolección en planta</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_personal_empresa" value="1" {{ old('entrega_personal_empresa', $model->entrega_personal_empresa ?? false) ? 'checked':'' }}> Personal de la empresa</label>
            <label class="inline-flex items-center gap-2"><input id="entrega_otro_chk" type="checkbox" name="entrega_otro" value="1" {{ old('entrega_otro', $model->entrega_otro ?? false) ? 'checked':'' }}> Otro</label>
            <input id="entrega_otro_txt" type="text" name="entrega_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('entrega_otro_txt', $model->entrega_otro_txt ?? '') }}" style="min-width:240px;">
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
          <input type="text" name="paq_empresa" class="w-full border rounded px-2 py-1" value="{{ old('paq_empresa', $model->paq_empresa ?? '') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Guía</label>
          <input type="text" name="paq_guia" class="w-full border rounded px-2 py-1" value="{{ old('paq_guia', $model->paq_guia ?? '') }}">
        </div>
      </div>
    </div>

    {{-- ======= Datos de destinatario ======= --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE DESTINATARIO (si aplica)</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Nombre</label>
          <input type="text" name="dest_nombre" class="w-full border rounded px-2 py-1" value="{{ old('dest_nombre', $model->dest_nombre ?? '') }}">
        </div>
        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Dirección</label>
          <input type="text" name="dest_direccion" class="w-full border rounded px-2 py-1" value="{{ old('dest_direccion', $model->dest_direccion ?? '') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Nombre de quien recibe</label>
          <input type="text" name="dest_recibe" class="w-full border rounded px-2 py-1" value="{{ old('dest_recibe', $model->dest_recibe ?? '') }}">
        </div>
        <div>
          <label class="block text sm font-semibold">Correo electrónico</label>
          <input type="email" name="dest_correo" class="w-full border rounded px-2 py-1" value="{{ old('dest_correo', $model->dest_correo ?? '') }}">
        </div>
        <div class="lg:col-span-2">
          <label class="block text-sm font-semibold">Teléfono</label>
          <input type="text" name="dest_telefono" class="w-full border rounded px-2 py-1" value="{{ old('dest_telefono', $model->dest_telefono ?? '') }}">
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Documentación anexa</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_cc" value="1" {{ old('docs_cc', $model->docs_cc ?? false) ? 'checked':'' }}> CC</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_ft" value="1" {{ old('docs_ft', $model->docs_ft ?? false) ? 'checked':'' }}> FT</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_hs" value="1" {{ old('docs_hs', $model->docs_hs ?? false) ? 'checked':'' }}> HS</label>
            <label class="inline-flex items-center gap-2"><input id="docs_otro_chk" type="checkbox" name="docs_otro" value="1" {{ old('docs_otro', $model->docs_otro ?? false) ? 'checked':'' }}> Otro</label>
            <input id="docs_otro_txt" type="text" name="docs_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('docs_otro_txt', $model->docs_otro_txt ?? '') }}" style="min-width:240px;">
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="mt-4 flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="button" id="salida-muestras-submit" class="bg-green-600 hover:bg-green-700 text-white">Generar PDF</x-button>
  </div>

  {{-- ======= JS ====== --}}
  <script>
    (function(){
      // Submit
      document.getElementById('salida-muestras-submit')?.addEventListener('click', function(){
        const modal = this.closest('[role="dialog"], .modal, [data-modal]') || document;
        const form  = modal.querySelector('#salida-muestras-form');
        if (form) form.submit();
      });

      function toggleText(enable, el){ if(!el) return; el.disabled = !enable; if(!enable) el.value=''; }

      // Motivo
      function syncMotivo(){
        const v = (document.querySelector('input[name="motivo_salida"]:checked')||{}).value;
        toggleText(v === 'otro', document.getElementById('mot_otro_txt'));
      }
      document.querySelectorAll('input[name="motivo_salida"]').forEach(r=>r.addEventListener('change', syncMotivo));
      syncMotivo();

      // Entrega otro
      const entOtroChk = document.getElementById('entrega_otro_chk');
      const entOtroTxt = document.getElementById('entrega_otro_txt');
      function syncEntrega(){ toggleText(!!entOtroChk?.checked, entOtroTxt); }
      entOtroChk?.addEventListener('change', syncEntrega); syncEntrega();

      // Docs otro
      const docsOtroChk = document.getElementById('docs_otro_chk');
      const docsOtroTxt = document.getElementById('docs_otro_txt');
      function syncDocs(){ toggleText(!!docsOtroChk?.checked, docsOtroTxt); }
      docsOtroChk?.addEventListener('change', syncDocs); syncDocs();

      // =======================
      //  Producto / SKU / Lotes
      // =======================
      const modalRoot     = document.getElementById('03');
      const folioSelect   = modalRoot?.querySelector('select[name="folio_muestra"]');
      const productSelect = modalRoot?.querySelector('select[name="product_id"]');
      const skuInput      = modalRoot?.querySelector('input[name="sku"]');
      const loteSelect    = modalRoot?.querySelector('input[name="lote"]');

      const BATCHES_BY_PRODUCT = @json($batchesByProduct ?? []);
      const PREV_LOTE = @json(old('lote', $model->lote ?? ''));

      function setSkuFromProduct() {
        const opt = productSelect?.selectedOptions?.[0];
        const sku = opt ? (opt.getAttribute('data-sku') || '') : '';
        if (skuInput) skuInput.value = sku;
      }

      function fillLotes(pid) {
        const loteList = document.getElementById('lote-list');
        const lotes = BATCHES_BY_PRODUCT?.[pid] || [];
        let html = '';

        for (const lote of lotes) {
          html += `<option value="${String(lote)}"></option>`;
        }
        if (loteList) loteList.innerHTML = html;
      }

      function onProductChange(){
        const pid = productSelect?.value || '';
        setSkuFromProduct();
        
        if (pid) {
          if (loteSelect) {
            loteSelect.disabled = false;
            loteSelect.placeholder = "Seleccione o escriba un lote";
          }
        } else {
          if (document.getElementById('lote-list')) document.getElementById('lote-list').innerHTML = '';
          if (loteSelect) {
            loteSelect.disabled = true;
            loteSelect.placeholder = "— Selecciona un producto primero —";
            if (!PREV_LOTE) loteSelect.value = '';
          }
        }
        
        fillLotes(pid);
      }

      // ✅ NUEVO: al cambiar folio, selecciona producto automáticamente
      function setProductFromFolio(){
        if (!folioSelect || !productSelect) return;
        const opt = folioSelect.selectedOptions?.[0];
        const pid = opt?.getAttribute('data-product-id') || '';

        if (pid) {
          productSelect.value = String(pid);
          onProductChange();
        }
      }

      // Eventos
      productSelect?.addEventListener('change', onProductChange);
      folioSelect?.addEventListener('change', setProductFromFolio);

      // Inicializa (por si hay old values)
      onProductChange();
      setProductFromFolio();

    })();
  </script>
</x-modal>
