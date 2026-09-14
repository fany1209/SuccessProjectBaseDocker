@php
  $sortedProducts = collect($products ?? [])->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
  $sortedCustomers = collect($customers ?? [])->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
@endphp

<x-modal id="edit-02">
  <form id="edit-solicitud-muestras-form" method="POST" class="space-y-4">
    @csrf
    <input type="hidden" name="id" id="edit_id">

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">EDITAR DATOS GENERALES</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Fecha solicitud</label>
          <input type="date" name="fecha_solicitud" id="edit_fecha_solicitud" class="w-full border rounded px-2 py-1">
        </div>
        <div>
          <label class="block text-sm font-semibold">Fecha de recolección</label>
          <input type="date" name="fecha_recoleccion" id="edit_fecha_recoleccion" class="w-full border rounded px-2 py-1">
        </div>
      </div>
    </div>

    {{-- ======= Datos de la muestra (Dynamic) ======= --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white flex justify-between items-center" style="background:#16a34a;">
        <span>EDITAR DATOS DE LA MUESTRA</span>
        <button type="button" id="edit-add-sample-btn" class="bg-white text-green-700 hover:bg-gray-100 px-2 py-1 rounded text-xs font-bold shadow">+ Añadir Muestra</button>
      </div>
      <div class="p-3" id="edit-samples-container">
        <!-- Samples will be added here via JS -->
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DEL CLIENTE</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="lg:col-span-4">
          <label for="edit_customer_id" class="block text-sm font-semibold">Cliente</label>
          <select name="customer_id" id="edit_customer_id" class="w-full border rounded px-2 py-1">
            <option value="">— Selecciona un cliente —</option>
            @foreach($sortedCustomers as $c)
              @php
                $addrParts = array_filter([$c->address, $c->city, $c->state]);
                $fullAddr = implode(', ', $addrParts);
              @endphp
              <option value="{{ $c->customer_id }}"
                      data-name="{{ $c->name }}"
                      data-email="{{ $c->email }}"
                      data-phone="{{ $c->phone }}"
                      data-address="{{ $fullAddr }}">
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Nombre del cliente</label>
          <input type="text" name="cliente_nombre" id="edit_cliente_nombre" class="w-full border rounded px-2 py-1" required>
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Dirección</label>
          <input type="text" name="cliente_direccion" id="edit_cliente_direccion" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold">Correo electrónico</label>
          <input type="email" name="cliente_correo" id="edit_cliente_correo" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold">Teléfono</label>
          <input type="text" name="cliente_telefono" id="edit_cliente_telefono" class="w-full border rounded px-2 py-1">
        </div>

        <div class="lg:col-span-2">
          <label class="block text-sm font-semibold">Estatus del cliente</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="radio" name="cliente_estatus" id="edit_estatus_nuevo" value="nuevo"> Nuevo</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="cliente_estatus" id="edit_estatus_frecuente" value="frecuente"> Frecuente</label>
          </div>
        </div>
        
        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Tipo de entrega</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_paqueteria" id="edit_entrega_paqueteria" value="1"> Paquetería</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_personal_empresa" id="edit_entrega_personal_empresa" value="1"> Personal empresa</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_recoleccion_planta" id="edit_entrega_recoleccion_planta" value="1"> Recolección planta</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_otro" id="edit_entrega_otro_chk" value="1"> Otro</label>
            <input type="text" name="entrega_otro_txt" id="edit_entrega_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique">
          </div>
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE PAQUETERÍA / OBSERVACIONES</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <input type="text" name="paq_nombre" id="edit_paq_nombre" placeholder="Nombre Paquetería" class="w-full border rounded px-2 py-1">
        <input type="text" name="paq_guia" id="edit_paq_guia" placeholder="Nº Guía" class="w-full border rounded px-2 py-1">
        <div class="md:col-span-2">
          <textarea name="observaciones" id="edit_observaciones" rows="2" class="w-full border rounded px-2 py-1" placeholder="Observaciones..."></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Solicitante</label>
          <input type="text" name="solicitante_nombre" id="edit_solicitante_nombre" class="w-full border rounded px-2 py-1">
        </div>
      </div>
    </div>
  </form>

  <div class="mt-4 flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="button" id="edit-solicitud-muestras-submit" class="bg-green-600 hover:bg-green-700 text-white">Actualizar</x-button>
  </div>

<script>
(function(){
    const form = document.getElementById('edit-solicitud-muestras-form');
    const btnSubmit = document.getElementById('edit-solicitud-muestras-submit');
    const samplesContainer = document.getElementById('edit-samples-container');
    const BATCHES_BY_PRODUCT = @json($batchesByProduct ?? []);
    let sampleIndex = 0;

    window.editToggleText = function(enable, elId) { 
        const el = document.getElementById(elId);
        if (!el) return; 
        el.disabled = !enable; 
        if (!enable) el.value = ''; 
    }

    const setups = [
        { chk: 'edit_entrega_otro_chk', txt: 'edit_entrega_otro_txt' }
    ];

    setups.forEach(s => {
        const checkbox = document.getElementById(s.chk);
        checkbox?.addEventListener('change', () => editToggleText(checkbox.checked, s.txt));
    });

    const custSel = document.getElementById('edit_customer_id');
    custSel?.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        if(!opt || !opt.value) return;
        document.getElementById('edit_cliente_nombre').value = opt.dataset.name || '';
        document.getElementById('edit_cliente_direccion').value = opt.dataset.address || '';
        document.getElementById('edit_cliente_correo').value = opt.dataset.email || '';
        document.getElementById('edit_cliente_telefono').value = opt.dataset.phone || '';
    });

    function getEditSampleHtml(idx, item = null) {
        let productsOptions = '<option value="">— Selecciona un producto —</option>';
        @foreach($sortedProducts as $p)
            productsOptions += `<option value="{{ $p->product_id }}" data-sku="{{ $p->sku }}" ${item && item.product_id == {{ $p->product_id }} ? 'selected' : ''}>{{ $p->name }}</option>`;
        @endforeach

        let lotesHtml = '<option value="">— Selecciona un producto primero —</option>';
        let loteDisabled = 'disabled';
        if(item && item.product_id && BATCHES_BY_PRODUCT[item.product_id]) {
            let lotes = BATCHES_BY_PRODUCT[item.product_id];
            if(lotes.length > 0) {
                lotesHtml = '<option value="">— Selecciona un lote —</option>';
                lotes.forEach(l => {
                    let sel = (String(l) === String(item.lote_almacen)) ? 'selected' : '';
                    lotesHtml += `<option value="${l}" ${sel}>${l}</option>`;
                });
                loteDisabled = '';
            } else {
                lotesHtml = '<option value="">— No hay lotes —</option>';
            }
        }

        return `
        <div class="sample-row border p-3 mb-3 bg-gray-50 rounded relative">
            <button type="button" class="remove-sample-btn absolute top-2 right-2 text-red-500 hover:text-red-700" title="Eliminar Muestra"><i class="fas fa-trash"></i></button>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium mb-1">Producto</label>
                    <select id="edit_product_select_${idx}" name="items[${idx}][product_id]" required class="product-select w-full rounded-md border border-gray-300 px-3 py-2">
                        ${productsOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold">SKU</label>
                    <input type="text" name="items[${idx}][sku]" class="sku-input w-full border rounded px-2 py-1 bg-gray-100" value="${item ? (item.sku||'') : ''}" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold">Cantidad</label>
                    <input type="text" name="items[${idx}][cantidad]" class="w-full border rounded px-2 py-1" value="${item ? (item.cantidad||'') : ''}">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Unidad de medida (UM)</label>
                    <input type="text" name="items[${idx}][um]" class="w-full border rounded px-2 py-1" value="${item ? (item.um||'') : ''}">
                </div>
                
                @can('laboratory.update')
                <div>
                    <label class="block text-sm font-semibold">Lote almacén</label>
                    <select name="items[${idx}][lote_almacen]" class="lote-almacen-select w-full border rounded px-2 py-1" ${loteDisabled}>
                        ${lotesHtml}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold">Lote venta</label>
                    <input type="text" name="items[${idx}][lote_venta]" class="w-full border rounded px-2 py-1" value="${item ? (item.lote_venta||'') : ''}">
                </div>
                @endcan
                
                <div class="md:col-span-2 lg:col-span-4">
                    <label class="block text-sm font-semibold">Presentación</label>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label><input type="checkbox" name="items[${idx}][pres_ziploc]" value="1" ${item && item.pres_ziploc ? 'checked' : ''}> Ziploc</label>
                        <label><input type="checkbox" name="items[${idx}][pres_whirlpak]" value="1" ${item && item.pres_whirlpak ? 'checked' : ''}> Whirlpak</label>
                        <label><input type="checkbox" name="items[${idx}][pres_metalizada]" value="1" ${item && item.pres_metalizada ? 'checked' : ''}> Metalizada</label>
                        <label><input type="checkbox" name="items[${idx}][pres_frasco]" value="1" ${item && item.pres_frasco ? 'checked' : ''}> Frasco</label>
                        <label><input type="checkbox" name="items[${idx}][pres_bidon]" value="1" ${item && item.pres_bidon ? 'checked' : ''}> Bidón</label>
                        <label><input type="checkbox" name="items[${idx}][pres_otro]" class="pres-otro-chk" value="1" ${item && item.pres_otro ? 'checked' : ''}> Otro</label>
                        <input type="text" name="items[${idx}][pres_otro_txt]" class="pres-otro-txt border rounded px-2 py-1" value="${item ? (item.pres_otro_txt||'') : ''}" ${item && item.pres_otro ? '' : 'disabled'}>
                    </div>
                </div>
                
                <div class="lg:col-span-4">
                    <label class="block text-sm font-semibold">Documentación solicitada</label>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label><input type="checkbox" name="items[${idx}][docs_cc]" value="1" ${item && item.docs_cc ? 'checked' : ''}> CC</label>
                        <label><input type="checkbox" name="items[${idx}][docs_ft]" value="1" ${item && item.docs_ft ? 'checked' : ''}> FT</label>
                        <label><input type="checkbox" name="items[${idx}][docs_hs]" value="1" ${item && item.docs_hs ? 'checked' : ''}> HS</label>
                        <label><input type="checkbox" name="items[${idx}][docs_otro]" class="docs-otro-chk" value="1" ${item && item.docs_otro ? 'checked' : ''}> Otro</label>
                        <input type="text" name="items[${idx}][docs_otro_txt]" class="docs-otro-txt border rounded px-2 py-1" value="${item ? (item.docs_otro_txt||'') : ''}" ${item && item.docs_otro ? '' : 'disabled'}>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function ensureSelect2(cb) {
        if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            cb(window.jQuery);
        } else {
            let s = document.getElementById('select2-cdn-script');
            if (!s) {
                s = document.createElement('script');
                s.id = 'select2-cdn-script';
                s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                document.head.appendChild(s);
            }
            const checkInterval = setInterval(function() {
                if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                    clearInterval(checkInterval);
                    cb(window.jQuery);
                }
            }, 50);
        }
    }

    function initEditSelect2(idx) {
        ensureSelect2(function($) {
            const $sel = $('#edit_product_select_' + idx);
            if (!$sel.length) return;
            $sel.select2({
                width: '100%',
                placeholder: '— Selecciona un producto —',
                allowClear: true
            }).on('select2:select select2:clear change', function(e) {
                this.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    window.renderEditSamples = function(items) {
        samplesContainer.innerHTML = '';
        sampleIndex = 0;
        if(items && items.length > 0) {
            items.forEach(item => {
                let currentIdx = sampleIndex++;
                samplesContainer.insertAdjacentHTML('beforeend', getEditSampleHtml(currentIdx, item));
                initEditSelect2(currentIdx);
            });
        } else {
            let currentIdx = sampleIndex++;
            samplesContainer.insertAdjacentHTML('beforeend', getEditSampleHtml(currentIdx));
            initEditSelect2(currentIdx);
        }
        
        // Also ensure customer select2 is initialized/refreshed
        ensureSelect2(function($) {
            const $cust = $('#edit_customer_id');
            if ($cust.length && !$cust.hasClass('select2-hidden-accessible')) {
                $cust.select2({
                    width: '100%',
                    placeholder: '— Selecciona un cliente —',
                    allowClear: true
                }).on('select2:select select2:clear change', function(e) {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        });
    };

    document.getElementById('edit-add-sample-btn')?.addEventListener('click', function() {
        let currentIdx = sampleIndex++;
        samplesContainer.insertAdjacentHTML('beforeend', getEditSampleHtml(currentIdx));
        initEditSelect2(currentIdx);
    });

    samplesContainer.addEventListener('change', function(e) {
        if(e.target.classList.contains('pres-otro-chk')) {
            const txt = e.target.closest('.sample-row').querySelector('.pres-otro-txt');
            txt.disabled = !e.target.checked;
            if(!e.target.checked) txt.value = '';
        }
        if(e.target.classList.contains('docs-otro-chk')) {
            const txt = e.target.closest('.sample-row').querySelector('.docs-otro-txt');
            txt.disabled = !e.target.checked;
            if(!e.target.checked) txt.value = '';
        }
        if(e.target.classList.contains('product-select')) {
            const row = e.target.closest('.sample-row');
            const opt = e.target.selectedOptions[0];
            const pid = e.target.value;
            const skuInp = row.querySelector('.sku-input');
            if(skuInp) skuInp.value = opt ? (opt.getAttribute('data-sku') || '') : '';

            const loteSel = row.querySelector('.lote-almacen-select');
            if(loteSel) {
                const lotes = BATCHES_BY_PRODUCT[pid] || [];
                let html = '';
                if (!pid) {
                    html = '<option value="">— Selecciona un producto primero —</option>';
                    loteSel.disabled = true;
                } else if (!lotes.length) {
                    html = '<option value="">— No hay lotes disponibles —</option>';
                    loteSel.disabled = true;
                } else {
                    html = '<option value="">— Selecciona un lote —</option>';
                    lotes.forEach(lote => { html += `<option value="${lote}">${lote}</option>`; });
                    loteSel.disabled = false;
                }
                loteSel.innerHTML = html;
            }
        }
    });

    samplesContainer.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-sample-btn');
        if(btn) {
            if(samplesContainer.querySelectorAll('.sample-row').length > 1) {
                btn.closest('.sample-row').remove();
            } else {
                Swal.fire('Atención', 'Debe haber al menos una muestra.', 'warning');
            }
        }
    });

    btnSubmit?.addEventListener('click', async function() {
        if (!form) return;
        
        if(samplesContainer.querySelectorAll('.sample-row').length === 0) {
            Swal.fire('Error', 'Debe agregar al menos una muestra', 'error');
            return;
        }

        if (!form.checkValidity()) { form.reportValidity(); return; }
        
        btnSubmit.disabled = true;
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

        const id = document.getElementById('edit_id').value;
        const formData = new FormData(form);

        try {
            const resp = await fetch(`/laboratory/customer-request/${id}`, {
                method: 'POST', 
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const res = await resp.json();
            if(res.ok) {
                Swal.fire({icon:'success', title:'Updated', timer:1500, showConfirmButton:false});
                $('#edit-02').addClass('hidden').hide();
                $('#customer-requests-table').DataTable().ajax.reload(null, false);
            } else {
                throw new Error(res.message);
            }
        } catch (err) {
            Swal.fire('Error', err.message || 'Could not save changes', 'error');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    });
    
})();
</script>
</x-modal>