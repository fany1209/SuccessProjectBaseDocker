<x-modal id="prueba2">
  <form id="recepcion-form" method="POST" action="{{ route('laboratory.reception.store') }}" class="space-y-4">
    @csrf

{{-- ======================= FOLIO ======================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-semibold">Folio muestra *</label>

        <select name="folio_muestra" id="folio_muestra" class="w-full border rounded px-2 py-1" required>
          <option value="">Seleccione folio…</option>
          
          @foreach ($samples->sortByDesc('folio')->unique('folio') as $s)
            <option
              value="{{ $s->folio }}"
              data-product-id="{{ $s->product_id }}"
              data-producto="{{ $s->producto }}"
              data-sku="{{ $s->sku }}"
              data-supplier-id="{{ $s->supplier_id ?? '' }}"
              data-proveedor="{{ $s->proveedor ?? '' }}"
              data-stock-inicial="{{ $s->stock_inicial ?? '' }}"
              data-fecha-entrada="{{ $s->fecha_entrada ?? '' }}"
              data-recolector="{{ $s->recolector ?? '' }}"
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
          <label for="producto_input" class="block text-sm font-medium mb-1">Producto</label>
          <input type="text" name="producto" id="producto_input" list="product_list" required
                 class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                 placeholder="Selecciona o escribe un producto" value="{{ old('producto') }}">
          <datalist id="product_list">
            @foreach($products as $p)
              <option value="{{ $p->name }}" data-id="{{ $p->product_id }}" data-sku="{{ $p->sku }}"></option>
            @endforeach
          </datalist>
          <input type="hidden" name="product_id" id="product_id_hidden" value="{{ old('product_id') }}">
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
          <label for="proveedor_input" class="block text-sm font-semibold">Proveedor</label>
          <input type="text" name="proveedor" id="proveedor_input" list="supplier_list" class="w-full border rounded px-2 py-1" required placeholder="Selecciona o escribe un proveedor" value="{{ old('proveedor') }}">
          <datalist id="supplier_list">
            @foreach($suppliers as $s)
              <option value="{{ $s->name }}" data-id="{{ $s->supplier_id }}"></option>
            @endforeach
          </datalist>
          <input type="hidden" name="supplier_id" id="supplier_id_hidden" value="{{ old('supplier_id') }}">
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

  window.productInput = document.getElementById('producto_input');
  window.productIdHidden = document.getElementById('product_id_hidden');
  window.skuInput      = document.getElementById('sku')
  window.batchSelect   = document.getElementById('batch');
  window.supplierInput = document.getElementById('proveedor_input');
  window.supplierIdHidden = document.getElementById('supplier_id_hidden');

  window.SUPPLIER_BY_PRODUCT = @json($supplierByProduct ?? []);
  window.PREV_SUPPLIER       = @json(old('supplier_id', $model->supplier_id ?? ''));
  window.BATCHES             = @json($batchesByProduct ?? []);
  window.PREV_BATCH          = @json(old('batch', $model->batch ?? ''));

  let userTouchedSupplier = false;
  supplierInput?.addEventListener('change', () => userTouchedSupplier = true);
  supplierInput?.addEventListener('input', function() {
    const val = this.value;
    const list = document.getElementById('supplier_list');
    let foundId = '';
    if (list) {
      for (const opt of list.options) {
        if (opt.value === val) {
          foundId = opt.getAttribute('data-id');
          break;
        }
      }
    }
    supplierIdHidden.value = foundId;
  });

  window.setSkuFromProduct = function(){
    if(!productInput || !skuInput) return;
    const val = productInput.value;
    const list = document.getElementById('product_list');
    let foundId = '';
    let foundSku = '';
    if (list) {
      for (const opt of list.options) {
        if (opt.value === val) {
          foundId = opt.getAttribute('data-id');
          foundSku = opt.getAttribute('data-sku') || '';
          break;
        }
      }
    }
    productIdHidden.value = foundId;
    if (foundId) {
      skuInput.value = foundSku;
      skuInput.readOnly = true;
    } else {
      // allow user to type a new sku if the product doesn't exist
      skuInput.value = '';
      skuInput.readOnly = false;
    }
    return foundId;
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
    if (userTouchedSupplier || !supplierInput) return;
    if (PREV_SUPPLIER) return;
    const sid = SUPPLIER_BY_PRODUCT[pid] || '';
    if (sid) {
      const list = document.getElementById('supplier_list');
      if (list) {
        for (const opt of list.options) {
          if (opt.getAttribute('data-id') === String(sid)) {
            supplierInput.value = opt.value;
            supplierIdHidden.value = sid;
            break;
          }
        }
      }
    }
  }

  productInput?.addEventListener('input', () => {
    const pid = setSkuFromProduct();
    if(productInput.value) {
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
    if (pid) {
      fillBatches(pid);
      maybePreselectSupplier(pid);
    } else {
      if(document.getElementById('batch-list')) document.getElementById('batch-list').innerHTML = '';
    }
  });

  const pid = setSkuFromProduct();
  if(productInput?.value){
    if (pid) {
      fillBatches(pid);
      maybePreselectSupplier(pid);
    }
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
    const productoText = opt.data('producto');
    const proveedorText = opt.data('proveedor');
    const fechaEntrada = opt.data('fecha-entrada');
    const recolector = opt.data('recolector');

    if(!productoText && !pid){
      $('#producto_input').val('');
      $('#product_id_hidden').val('');
      $('#sku').val('').prop('readonly', false);
      $('#batch-list').html('');
      $('#batch').val('').attr('placeholder', '— Selecciona un producto primero —').prop('disabled', true);
      return;
    }

    $('#producto_input').val(productoText || '');
    $('#product_id_hidden').val(pid || '');
    $('#sku').val(sku).prop('readonly', true);
    $('#batch').prop('disabled', false).attr('placeholder', 'Seleccione o escriba un lote');
    
    if (pid) {
      fillBatches(pid);
    }

    if (proveedorText) {
      $('#proveedor_input').val(proveedorText);
      $('#supplier_id_hidden').val(supplierId || '');
    }
    
    if (stockInicial !== undefined && stockInicial !== '') {
      $('input[name="cantidad"]').val(stockInicial);
    }

    if (fechaEntrada) {
      $('input[name="fecha_entrada"]').val(fechaEntrada);
    }
    
    if (recolector) {
      $('input[name="firma_recepcion_nombre"]').val(recolector);
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
