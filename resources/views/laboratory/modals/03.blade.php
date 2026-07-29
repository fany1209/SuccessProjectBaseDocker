<x-modal id="03">
  <form id="salida-muestras-form" method="POST" action="{{ route('laboratory.pdf3') }}" target="_blank" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
      <div>
        <label class="block text-sm font-semibold">Folio muestra (Inventario) *</label>
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
      <div>
        <label class="block text-sm font-semibold text-blue-700">Vincular con Solicitud (Opcional)</label>
        <select id="folio_solicitud" class="w-full border border-blue-400 rounded px-2 py-1">
          <option value="">Seleccione una solicitud para autocompletar datos…</option>
          @if(isset($customerRequests))
              @foreach($customerRequests as $req)
                <option value="{{ $req->folio }}">
                  {{ $req->folio }} — {{ $req->cliente_nombre }} ({{ $req->producto }})
                </option>
              @endforeach
          @endif
        </select>
        <p class="text-xs text-gray-500 mt-1">Al seleccionar una solicitud se autocompletarán los datos de contacto y paquetería.</p>
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
      document.getElementById('salida-muestras-submit')?.addEventListener('click', function(){
        const modal = this.closest('[role="dialog"], .modal, [data-modal]') || document;
        const form  = modal.querySelector('#salida-muestras-form');
        if (form) form.submit();
      });

      function toggleText(enable, el){ if(!el) return; el.disabled = !enable; if(!enable) el.value=''; }

      function syncMotivo(){
        const v = (document.querySelector('input[name="motivo_salida"]:checked')||{}).value;
        toggleText(v === 'otro', document.getElementById('mot_otro_txt'));
      }
      document.querySelectorAll('input[name="motivo_salida"]').forEach(r=>r.addEventListener('change', syncMotivo));
      syncMotivo();

      const entOtroChk = document.getElementById('entrega_otro_chk');
      const entOtroTxt = document.getElementById('entrega_otro_txt');
      function syncEntrega(){ toggleText(!!entOtroChk?.checked, entOtroTxt); }
      entOtroChk?.addEventListener('change', syncEntrega); syncEntrega();

      const docsOtroChk = document.getElementById('docs_otro_chk');
      const docsOtroTxt = document.getElementById('docs_otro_txt');
      function syncDocs(){ toggleText(!!docsOtroChk?.checked, docsOtroTxt); }
      docsOtroChk?.addEventListener('change', syncDocs); syncDocs();

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

      let customerRequestsCache = null;
      async function getCustomerRequests() {
          if (customerRequestsCache) return customerRequestsCache;
          try {
              const res = await fetch("{{ route('laboratory.customer_requests.json') }}");
              const json = await res.json();
              customerRequestsCache = json.data || [];
              return customerRequestsCache;
          } catch (e) {
              console.error("Error fetching customer requests:", e);
              return [];
          }
      }

      function setProductFromFolio(){
        if (!folioSelect || !productSelect) return;
        const opt = folioSelect.selectedOptions?.[0];
        const pid = opt?.getAttribute('data-product-id') || '';

        if (pid) {
          productSelect.value = String(pid);
          onProductChange();
        }
      }

      async function onSolicitudChange() {
          const reqFolio = document.getElementById('folio_solicitud')?.value;
          if (!reqFolio) return;

          const reqs = await getCustomerRequests();
          const matched = reqs.find(r => r.folio === reqFolio);
          if (matched) {
                const destNombre = document.querySelector('#salida-muestras-form input[name="dest_nombre"]');
                if (destNombre) destNombre.value = matched.cliente_nombre || '';
                
                const destDir = document.querySelector('#salida-muestras-form input[name="dest_direccion"]');
                if (destDir) destDir.value = matched.cliente_direccion || '';
                
                const destCorreo = document.querySelector('#salida-muestras-form input[name="dest_correo"]');
                if (destCorreo) destCorreo.value = matched.cliente_correo || '';
                
                const destTel = document.querySelector('#salida-muestras-form input[name="dest_telefono"]');
                if (destTel) destTel.value = matched.cliente_telefono || '';
                
                const destRecibe = document.querySelector('#salida-muestras-form input[name="dest_recibe"]');
                if (destRecibe) destRecibe.value = matched.solicitante_nombre || '';

                const paqEmp = document.querySelector('#salida-muestras-form input[name="paq_empresa"]');
                if (paqEmp) paqEmp.value = matched.paq_nombre || '';
                
                const paqGuia = document.querySelector('#salida-muestras-form input[name="paq_guia"]');
                if (paqGuia) paqGuia.value = matched.paq_guia || '';

                const entPaq = document.querySelector('#salida-muestras-form input[name="entrega_paqueteria"]');
                if (entPaq) entPaq.checked = !!matched.entrega_paqueteria;
                
                const entRec = document.querySelector('#salida-muestras-form input[name="entrega_recoleccion_planta"]');
                if (entRec) entRec.checked = !!matched.entrega_recoleccion_planta;
                
                const entPers = document.querySelector('#salida-muestras-form input[name="entrega_personal_empresa"]');
                if (entPers) entPers.checked = !!matched.entrega_personal_empresa;
                
                const entOtro = document.querySelector('#salida-muestras-form input[name="entrega_otro"]');
                if (entOtro) entOtro.checked = !!matched.entrega_otro;
                
                const entOtroTxt = document.querySelector('#salida-muestras-form input[name="entrega_otro_txt"]');
                if (entOtroTxt) entOtroTxt.value = matched.entrega_otro_txt || '';
                
                const docsCc = document.querySelector('#salida-muestras-form input[name="docs_cc"]');
                if (docsCc) docsCc.checked = !!matched.docs_cc;
                
                const docsFt = document.querySelector('#salida-muestras-form input[name="docs_ft"]');
                if (docsFt) docsFt.checked = !!matched.docs_ft;
                
                const docsHs = document.querySelector('#salida-muestras-form input[name="docs_hs"]');
                if (docsHs) docsHs.checked = !!matched.docs_hs;
                
                const docsOtro = document.querySelector('#salida-muestras-form input[name="docs_otro"]');
                if (docsOtro) docsOtro.checked = !!matched.docs_otro;
                
                const docsOtroTxt = document.querySelector('#salida-muestras-form input[name="docs_otro_txt"]');
                if (docsOtroTxt) docsOtroTxt.value = matched.docs_otro_txt || '';

                if (typeof syncEntrega === 'function') syncEntrega();
                if (typeof syncDocs === 'function') syncDocs();
          }
      }

      productSelect?.addEventListener('change', onProductChange);
      folioSelect?.addEventListener('change', setProductFromFolio);
      document.getElementById('folio_solicitud')?.addEventListener('change', onSolicitudChange);

      onProductChange();
      setProductFromFolio();

    })();
  </script>
</x-modal>
