<x-modal id="02">
  <form id="solicitud-muestras-form" method="POST" action="{{ route('laboratory.pdf2') }}" class="space-y-4">
    @csrf

    {{-- ======= Datos de la muestra ======= --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE LA MUESTRA</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
          <label class="block text-sm font-semibold">Fecha solicitud</label>
          <input type="date" name="fecha_solicitud" class="w-full border rounded px-2 py-1" value="{{ old('fecha_solicitud') }}">
        </div>

        <div class="mt-3">
          <label class="block text-sm font-semibold">SKU</label>
          <input type="text" name="sku" id="sku"
                 class="w-full border rounded px-2 py-1"
                 value="{{ old('sku', $model->sku ?? '') }}"
                 readonly>
        </div>

        {{-- === Producto === --}}
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

        <div>
          <label class="block text-sm font-semibold">Unidad de medida (UM)</label>
          <input type="text" name="um" class="w-full border rounded px-2 py-1" placeholder="p. ej., g, kg, ml" value="{{ old('um') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Cantidad</label>
          <input type="text" name="cantidad" class="w-full border rounded px-2 py-1" placeholder="p. ej., 500 g" value="{{ old('cantidad') }}">
        </div>

        <div class="md:col-span-2 lg:col-span-2">
          <label class="block text-sm font-semibold">Presentación</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_ziploc" value="1" {{ old('pres_ziploc')?'checked':'' }}> Bolsa ziploc</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_whirlpak" value="1" {{ old('pres_whirlpak')?'checked':'' }}> Bolsa whirlpak</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_metalizada" value="1" {{ old('pres_metalizada')?'checked':'' }}> Bolsa metalizada</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_frasco" value="1" {{ old('pres_frasco')?'checked':'' }}> Frasco</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_bidon" value="1" {{ old('pres_bidon')?'checked':'' }}> Bidón</label>
            <label class="inline-flex items-center gap-2"><input id="pres_otro_chk" type="checkbox" name="pres_otro" value="1" {{ old('pres_otro')?'checked':'' }}> Otro</label>
            <input id="pres_otro_txt" type="text" name="pres_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('pres_otro_txt') }}" style="min-width:220px;">
          </div>
        </div>

        @can('laboratory.update')
        <div>
          <label class="block text-sm font-semibold">Lote almacén</label>
          <select name="lote_almacen" id="lote_almacen" class="w-full border rounded px-2 py-1" >
            <option value="">— Selecciona un producto primero —</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold">Lote venta</label>
          <input type="text" name="lote_venta" class="w-full border rounded px-2 py-1" value="{{ old('lote_venta') }}">
        </div>
        @endcan

        <div>
          <label class="block text-sm font-semibold">Fecha de recolección</label>
          <input type="date" name="fecha_recoleccion" class="w-full border rounded px-2 py-1" value="{{ old('fecha_recoleccion') }}">
        </div>

        <div class="lg:col-span-3">
          <label class="block text-sm font-semibold">Documentación solicitada</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_cc" value="1" {{ old('docs_cc')?'checked':'' }}> CC</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_ft" value="1" {{ old('docs_ft')?'checked':'' }}> FT</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_hs" value="1" {{ old('docs_hs')?'checked':'' }}> HS</label>
            <label class="inline-flex items-center gap-2"><input id="docs_otro_chk" type="checkbox" name="docs_otro" value="1" {{ old('docs_otro')?'checked':'' }}> Otro</label>
            <input id="docs_otro_txt" type="text" name="docs_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('docs_otro_txt') }}" style="min-width:220px;">
          </div>
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DEL CLIENTE</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="lg:col-span-4">
          <label for="customer_id" class="block text-sm font-semibold">Cliente</label>
          <select name="customer_id" id="customer_id" class="w-full border rounded px-2 py-1">
            <option value="">— Selecciona un cliente —</option>
            @foreach($customers as $c)
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
          <label class="block text sm font-semibold">Personal de seguimiento (si aplica)</label>
          <input type="text" name="personal_seguimiento" class="w-full border rounded px-2 py-1" value="{{ old('personal_seguimiento', $model->personal_seguimiento ?? '') }}">
        </div>

        <div class="lg:col-span-4">
          <label class="block text-sm font-semibold">Tipo de entrega</label>
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_paqueteria" value="1" {{ old('entrega_paqueteria', $model->entrega_paqueteria ?? false) ? 'checked' : '' }}> Paquetería</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_personal_empresa" value="1" {{ old('entrega_personal_empresa', $model->entrega_personal_empresa ?? false) ? 'checked' : '' }}> Personal de la empresa</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="entrega_recoleccion_planta" value="1" {{ old('entrega_recoleccion_planta', $model->entrega_recoleccion_planta ?? false) ? 'checked' : '' }}> Recolección en planta</label>
            <label class="inline-flex items-center gap-2"><input id="entrega_otro_chk" type="checkbox" name="entrega_otro" value="1" {{ old('entrega_otro', $model->entrega_otro ?? false) ? 'checked' : '' }}> Otro</label>
            <input id="entrega_otro_txt" type="text" name="entrega_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('entrega_otro_txt', $model->entrega_otro_txt ?? '') }}" style="min-width:220px;">
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

        btnSubmit?.addEventListener('click', function() {
            if (!form) return;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            btnSubmit.disabled = true;
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            const formData = new FormData(form);

            fetch("{{ route('laboratory.store2') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) return response.json().then(err => { throw err; });
                return response.json();
            })
            .then(res => {
                if (res.ok) {
                    form.submit();
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'The record was saved and the PDF is being generated.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error saving record',
                        text: error.message || 'There was a problem connecting to the server.'
                    });
                } else {
                    alert('Error saving record: ' + (error.message || 'Unknown error'));
                }
            })
            .finally(() => {
                setTimeout(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                }, 1000);
            });
        });

        /* ===================== LÓGICA DE CAMPOS "OTRO" ===================== */
        function toggleText(enable, el) { 
            if (!el) return; 
            el.disabled = !enable; 
            if (!enable) el.value = ''; 
        }

        const setups = [
            { chk: 'pres_otro_chk', txt: 'pres_otro_txt' },
            { chk: 'docs_otro_chk', txt: 'docs_otro_txt' },
            { chk: 'entrega_otro_chk', txt: 'entrega_otro_txt' }
        ];

        setups.forEach(s => {
            const checkbox = document.getElementById(s.chk);
            const input = document.getElementById(s.txt);
            if (checkbox) {
                checkbox.addEventListener('change', () => toggleText(checkbox.checked, input));
                toggleText(checkbox.checked, input); 
            }
        });

        /* ===================== PRODUCTOS Y LOTES ===================== */
        const productSelect = modalRoot?.querySelector('select[name="product_id"]');
        const skuInput      = modalRoot?.querySelector('input[name="sku"]');
        const loteSelect    = modalRoot?.querySelector('select[name="lote_almacen"]');

        const BATCHES_BY_PRODUCT = @json($batchesByProduct ?? []);
        const PREV_LOTE = @json(old('lote_almacen', $model->lote_almacen ?? ''));

        function onProductChange() {
            const opt = productSelect?.selectedOptions?.[0];
            const pid = productSelect?.value || '';
            
            if (skuInput) skuInput.value = opt ? (opt.getAttribute('data-sku') || '') : '';

            if (!loteSelect) return;
            const lotes = BATCHES_BY_PRODUCT[pid] || [];
            let html = '';

            if (!pid) {
                html = '<option value="">— Selecciona un producto primero —</option>';
                loteSelect.disabled = true;
            } else if (!lotes.length) {
                html = '<option value="">— No hay lotes disponibles —</option>';
                loteSelect.disabled = true;
            } else {
                html = '<option value="">— Selecciona un lote —</option>';
                lotes.forEach(lote => {
                    const sel = (String(lote) === String(PREV_LOTE)) ? ' selected' : '';
                    html += `<option value="${lote}"${sel}>${lote}</option>`;
                });
                loteSelect.disabled = false;
            }
            loteSelect.innerHTML = html;
        }

        if (productSelect) {
            productSelect.addEventListener('change', onProductChange);
            onProductChange(); 
        }

        /* ===================== AUTOLLENADO DE CLIENTE ===================== */
        const customerSelect = modalRoot?.querySelector('select[name="customer_id"]');
        const customerFields = {
            name: modalRoot?.querySelector('input[name="cliente_nombre"]'),
            address: modalRoot?.querySelector('input[name="cliente_direccion"]'),
            email: modalRoot?.querySelector('input[name="cliente_correo"]'),
            phone: modalRoot?.querySelector('input[name="cliente_telefono"]')
        };

        function setCustomerFields() {
            const opt = customerSelect?.selectedOptions?.[0];
            if (!opt || !opt.value) return;

            if (customerFields.name)    customerFields.name.value    = opt.getAttribute('data-name') || '';
            if (customerFields.address) customerFields.address.value = opt.getAttribute('data-address') || '';
            if (customerFields.email)   customerFields.email.value   = opt.getAttribute('data-email') || '';
            if (customerFields.phone)   customerFields.phone.value   = opt.getAttribute('data-phone') || '';
        }

        if (customerSelect) {
            customerSelect.addEventListener('change', setCustomerFields);
            if (!customerFields.name?.value) setCustomerFields();
        }
    })();
</script>
</x-modal>
