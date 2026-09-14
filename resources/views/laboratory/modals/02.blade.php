@php
  $sortedProducts = collect($products ?? [])->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
  $sortedCustomers = collect($customers ?? [])->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
@endphp

<template id="product-options-template-02">
  <option value="">— Selecciona un producto —</option>
  @foreach($sortedProducts as $p)
    <option value="{{ $p->product_id }}" data-sku="{{ $p->sku }}">{{ $p->name }}</option>
  @endforeach
</template>

<x-modal id="02">
  <form id="solicitud-muestras-form" method="POST" action="{{ route('laboratory.pdf2') }}" class="space-y-4">
    @csrf

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS GENERALES</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Fecha solicitud</label>
          <input type="date" name="fecha_solicitud" class="w-full border rounded px-2 py-1" value="{{ old('fecha_solicitud') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Fecha de recolección</label>
          <input type="date" name="fecha_recoleccion" class="w-full border rounded px-2 py-1" value="{{ old('fecha_recoleccion') }}">
        </div>
      </div>
    </div>

    {{-- ======= Datos de la muestra (Dynamic) ======= --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white flex justify-between items-center" style="background:#16a34a;">
        <span>DATOS DE LA MUESTRA</span>
        <button type="button" id="add-sample-btn" class="bg-white text-green-700 hover:bg-gray-100 px-2 py-1 rounded text-xs font-bold shadow">+ Añadir Muestra</button>
      </div>
      <div class="p-3" id="samples-container"></div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DEL CLIENTE</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="lg:col-span-4">
          <label for="customer_id" class="block text-sm font-semibold">Cliente</label>
          <select name="customer_id" id="customer_id" class="w-full border rounded px-2 py-1">
            <option value="">— Selecciona un cliente —</option>
            @foreach($sortedCustomers as $c)
              @php
                $addrParts = array_filter([
                  $c->address ?? null,
                  $c->district ?? null,
                  $c->city ?? null,
                  $c->state ?? null,
                  $c->postal_code ? ('C.P. '.$c->postal_code) : null,
                  $c->country ?? null,
                ]);
                $fullAddress = implode(', ', $addrParts);
              @endphp
              <option value="{{ $c->customer_id }}"
                      data-name="{{ $c->name }}"
                      data-email="{{ $c->email ?? '' }}"
                      data-phone="{{ $c->phone ?? '' }}"
                      data-address="{{ $fullAddress }}"
                {{ (int)old('customer_id', $model->customer_id ?? 0) === (int)$c->customer_id ? 'selected' : '' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Nombre del cliente</label>
          <input type="text" name="cliente_nombre" class="w-full border rounded px-2 py-1"
                 value="{{ old('cliente_nombre', $model->cliente_nombre ?? '') }}" required>
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Dirección</label>
          <input type="text" name="cliente_direccion" class="w-full border rounded px-2 py-1"
                 value="{{ old('cliente_direccion', $model->cliente_direccion ?? '') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Correo electrónico</label>
          <input type="email" name="cliente_correo" class="w-full border rounded px-2 py-1"
                 value="{{ old('cliente_correo', $model->cliente_correo ?? '') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Teléfono</label>
          <input type="text" name="cliente_telefono" class="w-full border rounded px-2 py-1"
                 value="{{ old('cliente_telefono', $model->cliente_telefono ?? '') }}">
        </div>

        <div class="lg:col-span-2">
          <label class="block text-sm font-semibold">Estatus del cliente</label>
          @php $estatus = old('cliente_estatus', $model->cliente_estatus ?? null); @endphp
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="radio" name="cliente_estatus" value="nuevo" {{ $estatus==='nuevo'?'checked':'' }}> Nuevo</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="cliente_estatus" value="frecuente" {{ $estatus==='frecuente'?'checked':'' }}> Frecuente</label>
          </div>
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Personal de seguimiento (si aplica)</label>
          <input type="text" name="personal_seguimiento" class="w-full border rounded px-2 py-1" value="{{ old('personal_seguimiento', $model->personal_seguimiento ?? '') }}">
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Tipo de entrega</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_paqueteria" value="1" {{ old('entrega_paqueteria', $model->entrega_paqueteria ?? false) ? 'checked' : '' }}> Paquetería</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_personal_empresa" value="1" {{ old('entrega_personal_empresa', $model->entrega_personal_empresa ?? false) ? 'checked' : '' }}> Personal de la empresa</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_recoleccion_planta" value="1" {{ old('entrega_recoleccion_planta', $model->entrega_recoleccion_planta ?? false) ? 'checked' : '' }}> Recolección en planta</label>
            <label class="inline-flex items-center gap-2"><input id="entrega_otro_chk" type="checkbox" name="entrega_otro" value="1" {{ old('entrega_otro', $model->entrega_otro ?? false) ? 'checked' : '' }}> Otro</label>
            <input id="entrega_otro_txt" type="text" name="entrega_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('entrega_otro_txt', $model->entrega_otro_txt ?? '') }}" style="min-width:220px;" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE PAQUETERÍA (si aplica)</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Nombre de la paquetería</label>
          <input type="text" name="paq_nombre" class="w-full border rounded px-2 py-1" value="{{ old('paq_nombre', $model->paq_nombre ?? '') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">N.º Guía / Rastreo</label>
          <input type="text" name="paq_guia" class="w-full border rounded px-2 py-1" value="{{ old('paq_guia', $model->paq_guia ?? '') }}">
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">OBSERVACIONES Y FIRMA</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Observaciones</label>
          <textarea name="observaciones" rows="3" class="w-full border rounded px-2 py-1">{{ old('observaciones', $model->observaciones ?? '') }}</textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Nombre del solicitante</label>
          <input type="text" name="solicitante_nombre" class="w-full border rounded px-2 py-1" value="{{ old('solicitante_nombre', $model->solicitante_nombre ?? '') }}">
        </div>
      </div>
    </div>
  </form>

  <div class="mt-4 flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="button" id="solicitud-muestras-submit" class="bg-green-600 hover:bg-green-700 text-white">Generar PDF</x-button>
  </div>

  {{-- ======= JS ======= --}}
  <script>
    (function(){
        const modalRoot = document.getElementById('02');
        const form = document.getElementById('solicitud-muestras-form');
        const btnSubmit = document.getElementById('solicitud-muestras-submit');
        const BATCHES_BY_PRODUCT = @json($batchesByProduct ?? []);
        let sampleIndex = 0;

        // Template for samples
        function getSampleHtml(idx) {
            const tpl = document.getElementById('product-options-template-02');
            const productsOptions = tpl ? tpl.innerHTML : '<option value="">— Selecciona un producto —</option>';

            return `
            <div class="sample-row border p-3 mb-3 bg-gray-50 rounded relative">
                <button type="button" class="remove-sample-btn absolute top-2 right-2 text-red-500 hover:text-red-700" title="Eliminar Muestra"><i class="fas fa-trash"></i></button>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1">Producto</label>
                        <select id="product_select_${idx}" name="items[${idx}][product_id]" required class="product-select w-full rounded-md border border-gray-300 px-3 py-2">
                            ${productsOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">SKU</label>
                        <input type="text" name="items[${idx}][sku]" class="sku-input w-full border rounded px-2 py-1 bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Cantidad</label>
                        <input type="text" name="items[${idx}][cantidad]" class="w-full border rounded px-2 py-1" placeholder="p. ej., 500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Unidad de medida (UM)</label>
                        <input type="text" name="items[${idx}][um]" class="w-full border rounded px-2 py-1" placeholder="p. ej., g, kg">
                    </div>
                    
                    @can('laboratory.update')
                    <div>
                        <label class="block text-sm font-semibold">Lote almacén</label>
                        <select name="items[${idx}][lote_almacen]" class="lote-almacen-select w-full border rounded px-2 py-1" disabled>
                            <option value="">— Selecciona un producto primero —</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Lote venta</label>
                        <input type="text" name="items[${idx}][lote_venta]" class="w-full border rounded px-2 py-1">
                    </div>
                    @endcan
                    
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-sm font-semibold">Presentación</label>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <label><input type="checkbox" name="items[${idx}][pres_ziploc]" value="1"> Ziploc</label>
                            <label><input type="checkbox" name="items[${idx}][pres_whirlpak]" value="1"> Whirlpak</label>
                            <label><input type="checkbox" name="items[${idx}][pres_metalizada]" value="1"> Metalizada</label>
                            <label><input type="checkbox" name="items[${idx}][pres_frasco]" value="1"> Frasco</label>
                            <label><input type="checkbox" name="items[${idx}][pres_bidon]" value="1"> Bidón</label>
                            <label><input type="checkbox" name="items[${idx}][pres_otro]" class="pres-otro-chk" value="1"> Otro</label>
                            <input type="text" name="items[${idx}][pres_otro_txt]" class="pres-otro-txt border rounded px-2 py-1" placeholder="Especifique" disabled>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-4">
                        <label class="block text-sm font-semibold">Documentación solicitada</label>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <label><input type="checkbox" name="items[${idx}][docs_cc]" value="1"> CC</label>
                            <label><input type="checkbox" name="items[${idx}][docs_ft]" value="1"> FT</label>
                            <label><input type="checkbox" name="items[${idx}][docs_hs]" value="1"> HS</label>
                            <label><input type="checkbox" name="items[${idx}][docs_otro]" class="docs-otro-chk" value="1"> Otro</label>
                            <input type="text" name="items[${idx}][docs_otro_txt]" class="docs-otro-txt border rounded px-2 py-1" placeholder="Especifique" disabled>
                        </div>
                    </div>
                </div>
            </div>`;
        }

        const samplesContainer = document.getElementById('samples-container');
        
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

        function initSelect2(idx) {
            ensureSelect2(function($) {
                const $sel = $('#product_select_' + idx);
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

        function initCustomerSelect2() {
            ensureSelect2(function($) {
                const $cust = $('#customer_id');
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
        }

        function addSample() {
            let currentIdx = sampleIndex++;
            samplesContainer.insertAdjacentHTML('beforeend', getSampleHtml(currentIdx));
            initSelect2(currentIdx);
        }

        window.initSampleForm02 = function() {
            if (samplesContainer.querySelectorAll('.sample-row').length === 0) {
                addSample();
            } else {
                samplesContainer.querySelectorAll('.product-select').forEach(function(el) {
                    ensureSelect2(function($) {
                        if (!$(el).hasClass('select2-hidden-accessible')) {
                            $(el).select2({
                                width: '100%',
                                placeholder: '— Selecciona un producto —',
                                allowClear: true
                            }).on('select2:select select2:clear change', function(e) {
                                this.dispatchEvent(new Event('change', { bubbles: true }));
                            });
                        }
                    });
                });
            }
            initCustomerSelect2();
        };

        document.getElementById('add-sample-btn')?.addEventListener('click', function() {
            addSample();
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

        btnSubmit?.addEventListener('click', function() {
            if (!form) return;
            
            if(samplesContainer.querySelectorAll('.sample-row').length === 0) {
                Swal.fire('Error', 'Debe agregar al menos una muestra', 'error');
                return;
            }

            if (!form.checkValidity()) { form.reportValidity(); return; }

            btnSubmit.disabled = true;
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            const formData = new FormData(form);
            fetch("{{ route('laboratory.store2') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(res => {
                if (res.ok) {
                    form.submit();
                    if (typeof Swal !== 'undefined') Swal.fire({icon: 'success', title: 'Éxito', text: 'Solicitud guardada', timer: 2000, showConfirmButton: false});
                    setTimeout(() => {
                        form.reset();
                        samplesContainer.innerHTML = '';
                        addSample();
                        if ($('#customer_id').length && typeof $('#customer_id').select2 === 'function') {
                            $('#customer_id').val('').trigger('change.select2');
                        }
                        $('#02').addClass('hidden').hide();
                        $('#customer-requests-table').DataTable().ajax.reload(null, false);
                    }, 2000);
                }
            })
            .catch(e => {
                console.error(e);
                Swal.fire('Error', e.message || 'Error al guardar.', 'error');
            })
            .finally(() => {
                setTimeout(() => { btnSubmit.disabled = false; btnSubmit.innerHTML = originalText; }, 1000);
            });
        });

        // Initialize sample and customer on load
        window.initSampleForm02();

        const entregaOtroChk = document.getElementById('entrega_otro_chk');
        const entregaOtroTxt = document.getElementById('entrega_otro_txt');
        entregaOtroChk?.addEventListener('change', function() {
            entregaOtroTxt.disabled = !this.checked;
            if(!this.checked) entregaOtroTxt.value = '';
        });

        const customerSelect = document.getElementById('customer_id');
        customerSelect?.addEventListener('change', function() {
            const opt = this.selectedOptions[0];
            if (!opt || !opt.value) return;
            document.querySelector('input[name="cliente_nombre"]').value = opt.getAttribute('data-name') || '';
            document.querySelector('input[name="cliente_direccion"]').value = opt.getAttribute('data-address') || '';
            document.querySelector('input[name="cliente_correo"]').value = opt.getAttribute('data-email') || '';
            document.querySelector('input[name="cliente_telefono"]').value = opt.getAttribute('data-phone') || '';
        });
    })();
  </script>
</x-modal>
