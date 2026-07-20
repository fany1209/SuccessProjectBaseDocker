<x-modal id="prueba2">
  <form id="recepcion-form" method="POST" action="{{ route('laboratory.reception.store') }}" class="space-y-4">
    @csrf

{{-- ======================= FOLIO ======================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-semibold">Folio muestra *</label>

        <select name="folio_muestra" id="folio_muestra" class="w-full border rounded px-2 py-1" required>
          <option value="">Seleccione folio…</option>
          
          @foreach ($samples->sortByDesc('folio') as $s)
            <option
              value="{{ $s->folio }}"
              data-product-id="{{ $s->product_id }}"
              data-sku="{{ $s->sku }}"
              data-supplier-id="{{ $s->supplier_id ?? '' }}"
              data-stock-inicial="{{ $s->stock_inicial ?? '' }}"
            >
              {{ $s->folio }} — {{ $s->producto ?? 'Sin producto' }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- ======================= INFORMACIÓN DE LA MUESTRA ======================= --}}
    <div class="mt-2 border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">INFORMACIÓN DE LA MUESTRA</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

        <div>
          <label for="product_id" class="block text-sm font-medium mb-1">Producto</label>
          <select name="product_id" id="product_id" required
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">— Selecciona un producto —</option>
            @foreach($products as $p)
              <option value="{{ $p->product_id }}"
                      data-sku="{{ $p->sku }}"
                {{ (int) old('product_id', $model->product_id ?? 0) === (int) $p->product_id ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Nombre comercial</label>
          <input type="text" name="nombre_comercial" class="w-full border rounded px-2 py-1" value="{{ old('nombre_comercial') }}">
        </div>

        <div class="mt-3">
          <label class="block text-sm font-semibold">SKU</label>
          <input type="text" name="sku" id="sku"
                 class="w-full border rounded px-2 py-1"
                 value="{{ old('sku', $model->sku ?? '') }}"
                 readonly>
        </div>

        <div class="mt-3">
          <label for="batch" class="block text-sm font-semibold">Lote</label>
          <input type="text" name="batch" id="batch" list="batch-list" class="w-full border rounded px-2 py-1" required autocomplete="off" placeholder="Seleccione o escriba un lote" value="{{ old('batch', $model->batch ?? '') }}">
          <datalist id="batch-list"></datalist>
        </div>

        <div>
          <label class="block text-sm font-semibold">Fecha de entrada</label>
          <input type="date" name="fecha_entrada" class="w-full border rounded px-2 py-1" value="{{ old('fecha_entrada') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Fecha de caducidad</label>
          <input type="date" name="fecha_caducidad" class="w-full border rounded px-2 py-1" value="{{ old('fecha_caducidad') }}">
        </div>

        <div class="md:col-span-3">
          <label class="block text-sm font-semibold">Descripción</label>
          <textarea name="descripcion" rows="2" class="w-full border rounded px-2 py-1">{{ old('descripcion') }}</textarea>
        </div>

        <div class="md:col-span-3">
          <label class="block text-sm font-semibold mb-1">Origen de la muestra</label>
          <div class="flex flex-wrap gap-4 text-sm">
            @php $orig = old('origen_muestra'); @endphp
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="origen_muestra" value="proveedor" {{ $orig==='proveedor'?'checked':'' }}> Proveedor
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="origen_muestra" value="produccion" {{ $orig==='produccion'?'checked':'' }}> Producción
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="origen_muestra" value="almacen" {{ $orig==='almacen'?'checked':'' }}> Almacén
            </label>
            <label class="inline-flex items-center gap-2">
              <input id="origen_otro_radio" type="radio" name="origen_muestra" value="otro" {{ $orig==='otro'?'checked':'' }}> Otro
            </label>
            <input id="origen_otro_txt" type="text" name="origen_otro" class="border rounded px-2 py-1"
                   placeholder="Especifique" value="{{ old('origen_otro') }}" style="min-width:220px;">
          </div>
        </div>
      </div>
    </div>

    {{-- ======================= DATOS DE RECEPCIÓN ======================= --}}
    <div class="mt-3 border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE RECEPCIÓN</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

       <div class="md:col-span-3">
          <label class="block text-sm font-semibold mb-1">Objetivo de la muestra</label>
          @php 
            $obj = old('objetivo_muestra', []); 
            if(!is_array($obj)) $obj = [$obj];
          @endphp
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="objetivo_muestra[]" value="inspeccion" {{ in_array('inspeccion', $obj)?'checked':'' }}> Inspección
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="objetivo_muestra[]" value="retencion" {{ in_array('retencion', $obj)?'checked':'' }}> Retención
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="objetivo_muestra[]" value="analisis" {{ in_array('analisis', $obj)?'checked':'' }}> Análisis
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="objetivo_muestra[]" value="desarrollo" {{ in_array('desarrollo', $obj)?'checked':'' }}> Desarrollo
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="objetivo_muestra[]" value="exposicion" {{ in_array('exposicion', $obj)?'checked':'' }}> Exposición
            </label>
            <label class="inline-flex items-center gap-2">
              <input id="obj_otro_radio" type="checkbox" name="objetivo_muestra[]" value="otro" {{ in_array('otro', $obj)?'checked':'' }}> Otro
            </label>
            <input id="obj_otro_txt" type="text" name="objetivo_otro" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('objetivo_otro') }}" style="min-width:220px;">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold">Cantidad</label>
          <input type="number" step="any" name="cantidad" class="w-full border rounded px-2 py-1" value="{{ old('cantidad') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Unidad de medida</label>
          @php $um = old('um'); @endphp
          <div class="flex flex-wrap gap-3 text-sm">
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="g"  {{ $um==='g'?'checked':'' }}> g</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="kg" {{ $um==='kg'?'checked':'' }}> kg</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="l"  {{ $um==='l'?'checked':'' }}> l</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="ml" {{ $um==='ml'?'checked':'' }}> ml</label>
            <label class="inline-flex items-center gap-2"><input id="um_otro_radio" type="radio" name="um" value="otro" {{ $um==='otro'?'checked':'' }}> Otro</label>
            <input id="um_otro_txt" type="text" name="um_otro" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('um_otro') }}" style="min-width:180px;">
          </div>
        </div>

        <div class="mt-3">
          <label for="supplier_id" class="block text-sm font-semibold">Proveedor</label>
          <select name="supplier_id" id="supplier_id" class="w-full border rounded px-2 py-1" required>
            <option value="">— Selecciona un proveedor —</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->supplier_id }}"
                {{ (int)old('supplier_id', $model->supplier_id ?? 0) === (int)$s->supplier_id ? 'selected' : '' }}>
                {{ $s->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-3">
          {{--<label class="block text-sm font-semibold mb-1">Documentación anexa</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_ccf" value="1" {{ old('docs_ccf') ? 'checked' : '' }}> CCF</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_ft"  value="1" {{ old('docs_ft')  ? 'checked' : '' }}> FT</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_hs"  value="1" {{ old('docs_hs')  ? 'checked' : '' }}> HS</label>
            <label class="inline-flex items-center gap-2"><input id="doc_otro_chk" type="checkbox" name="docs_otro" value="1" {{ old('docs_otro') ? 'checked' : '' }}> Otro</label>
            <input id="doc_otro_txt" type="text" name="docs_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('docs_otro_txt') }}" style="min-width:220px;">
          </div>--}}
        </div>

      </div>
    </div>

    <div class="mt-3 border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">OBSERVACIONES LABORATORIO</div>
      <div class="p-3">
        <textarea name="observaciones_laboratorio" rows="4" class="w-full border rounded px-2 py-1">{{ old('observaciones_laboratorio') }}</textarea>
      </div>
    </div>

    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-semibold">Nombre de quien entrega en laboratorio</label>
        <input type="text" name="firma_entrega_nombre" class="w-full border rounded px-2 py-1" value="{{ old('firma_entrega_nombre') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">Nombre de recepción en laboratorio</label>
        <input type="text" name="firma_recepcion_nombre" class="w-full border rounded px-2 py-1" value="{{ old('firma_recepcion_nombre') }}">
      </div>
    </div>
  </form>

  <div class="mt-4 flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="button" id="submit-recepcion" class="bg-green-600 hover:bg-green-700 text-white">Guardar</x-button>
  </div>

<script>
(function(){
  function toggleText(enable, inputEl){
    if (!inputEl) return;
    inputEl.disabled = !enable;
    if (!enable) inputEl.value = '';
  }

  const origenOtroTxt = document.getElementById('origen_otro_txt');
  function syncOrigen(){
    const val = (document.querySelector('input[name="origen_muestra"]:checked')||{}).value;
    toggleText(val === 'otro', origenOtroTxt);
  }
  document.querySelectorAll('input[name="origen_muestra"]').forEach(r=>r.addEventListener('change', syncOrigen));
  syncOrigen();

  const objOtroTxt = document.getElementById('obj_otro_txt');
  const objOtroChk = document.getElementById('obj_otro_radio'); 

  function syncObj(){
    toggleText(!!objOtroChk?.checked, objOtroTxt);
  }
  document.querySelectorAll('input[name="objetivo_muestra[]"]').forEach(r=>r.addEventListener('change', syncObj));
  syncObj();

  const umOtroTxt = document.getElementById('um_otro_txt');
  function syncUm(){
    const val = (document.querySelector('input[name="um"]:checked')||{}).value;
    toggleText(val === 'otro', umOtroTxt);
  }
  document.querySelectorAll('input[name="um"]').forEach(r=>r.addEventListener('change', syncUm));
  syncUm();

  const docOtroChk = document.getElementById('doc_otro_chk');
  const docOtroTxt = document.getElementById('doc_otro_txt');
  function syncDoc(){
    toggleText(!!docOtroChk?.checked, docOtroTxt);
  }
  docOtroChk?.addEventListener('change', syncDoc);
  syncDoc();
})();


document.addEventListener('DOMContentLoaded', function(){

  window.productSelect = document.getElementById('product_id');
  window.skuInput      = document.getElementById('sku');
  window.batchSelect   = document.getElementById('batch');
  window.supplierSel   = document.getElementById('supplier_id');

  window.SUPPLIER_BY_PRODUCT = @json($supplierByProduct ?? []);
  window.PREV_SUPPLIER       = @json(old('supplier_id', $model->supplier_id ?? ''));
  window.BATCHES             = @json($batchesByProduct ?? []);
  window.PREV_BATCH          = @json(old('batch', $model->batch ?? ''));

  let userTouchedSupplier = false;
  supplierSel?.addEventListener('change', () => userTouchedSupplier = true);

  window.setSkuFromProduct = function(){
    if(!productSelect || !skuInput) return;
    const opt = productSelect.selectedOptions[0];
    skuInput.value = opt ? (opt.getAttribute('data-sku') || '') : '';
  };

  window.fillBatches = function(pid){
    const batches = BATCHES[pid] || [];
    const batchList = document.getElementById('batch-list');
    let html = '';

    for(const b of batches){
      html += `<option value="${String(b)}"></option>`;
    }

    if(batchList) batchList.innerHTML = html;
  };

  function maybePreselectSupplier(pid){
    if (userTouchedSupplier || !supplierSel) return;
    if (PREV_SUPPLIER) return;
    const sid = SUPPLIER_BY_PRODUCT[pid] || '';
    if (sid) supplierSel.value = String(sid);
  }

  productSelect?.addEventListener('change', () => {
    const pid = productSelect.value;
    setSkuFromProduct();
    if(pid) {
      if(batchSelect) {
        batchSelect.disabled = false;
        batchSelect.placeholder = "Seleccione o escriba un lote";
      }
    } else {
      if(batchSelect) {
        batchSelect.disabled = true;
        batchSelect.placeholder = "— Selecciona un producto primero —";
        batchSelect.value = '';
      }
    }
    fillBatches(pid);
    maybePreselectSupplier(pid);
  });

  setSkuFromProduct();
  if(productSelect?.value){
    fillBatches(productSelect.value);
    maybePreselectSupplier(productSelect.value);
    if(batchSelect) {
      batchSelect.disabled = false;
      batchSelect.placeholder = "Seleccione o escriba un lote";
    }
  } else {
    if(document.getElementById('batch-list')) document.getElementById('batch-list').innerHTML = '';
    if(batchSelect) {
      batchSelect.disabled = true;
      batchSelect.placeholder = "— Selecciona un producto primero —";
      if (!PREV_BATCH) batchSelect.value = '';
    }
  }
});

$(function(){
  $('#folio_muestra').on('change', function(){

    const opt = $(this).find(':selected');
    const pid = opt.data('product-id');
    const sku = opt.data('sku');
    const supplierId = opt.data('supplier-id');
    const stockInicial = opt.data('stock-inicial');

    if(!pid){
      $('#product_id').val('');
      $('#sku').val('');
      $('#batch-list').html('');
      $('#batch').val('').attr('placeholder', '— Selecciona un producto primero —').prop('disabled', true);
      return;
    }

    $('#product_id').val(pid);
    setSkuFromProduct();
    $('#batch').prop('disabled', false).attr('placeholder', 'Seleccione o escriba un lote');
    fillBatches(pid);

    if (supplierId) {
      $('#supplier_id').val(supplierId);
    }
    
    if (stockInicial !== undefined && stockInicial !== '') {
      $('input[name="cantidad"]').val(stockInicial);
    }
  });
});

document.getElementById('submit-recepcion')?.addEventListener('click', function(){

  const modal = this.closest('[role="dialog"], .modal, [data-modal]') || document;
  const form  = modal.querySelector('#recepcion-form');
  if (!form) return;

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const fd = new FormData(form);

  
    ['docs_ccf','docs_ft','docs_hs','docs_otro'].forEach(name=>{
    if(!fd.has(name)) fd.append(name, 0);
  });

  const url = form.getAttribute('action');

  const btn = this;
  const prev = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Guardando…';

  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
    },
    body: fd
  })
  .then(async resp=>{
    if(!resp.ok){
      let msg = `Error ${resp.status}`;
      try {
        const j = await resp.json();
        msg = j.message || msg;
      } catch {
        msg = await resp.text();
      }
      throw new Error(msg);
    }
    return resp.json().catch(()=>({}));
  })
  .then(json=>{
    Swal.fire({
      icon:'success',
      title:'Guardado',
      text: json?.message || 'Registro guardado correctamente'
    });

    document.getElementById('prueba2')?.classList.remove('open');

    if ($.fn.DataTable && $('#reception-table').length) {
      $('#reception-table').DataTable().ajax.reload(null, false);
    }
  })
  .catch(err=>{
    console.error(err);
    Swal.fire({ icon:'error', title:'Error', text: err.message || 'No se pudo guardar' });
  })
  .finally(()=>{
    btn.disabled = false;
    btn.textContent = prev;
  });
});
</script>
</x-modal>
