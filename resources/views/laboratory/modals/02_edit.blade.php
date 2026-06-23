<x-modal id="edit-02">
    <form id="edit-solicitud-muestras-form" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="id" id="edit_id">

        <div class="border rounded">
            <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">EDITAR DATOS DE LA MUESTRA</div>
            <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-semibold">Fecha solicitud</label>
                    <input type="date" name="fecha_solicitud" id="edit_fecha_solicitud" class="w-full border rounded px-2 py-1">
                </div>

                <div>
                    <label class="block text-sm font-semibold">SKU</label>
                    <input type="text" name="sku" id="edit_sku" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
                </div>

                <div>
                    <label for="edit_product_id" class="block text-sm font-medium mb-1">Producto</label>
                    <select name="product_id" id="edit_product_id" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="">— Selecciona un producto —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->product_id }}" data-sku="{{ $p->sku }}">
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold">Unidad de medida (UM)</label>
                    <input type="text" name="um" id="edit_um" class="w-full border rounded px-2 py-1">
                </div>

                <div>
                    <label class="block text-sm font-semibold">Cantidad</label>
                    <input type="text" name="cantidad" id="edit_cantidad" class="w-full border rounded px-2 py-1">
                </div>

                <div class="md:col-span-2 lg:col-span-2">
                    <label class="block text-sm font-semibold">Presentación</label>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_ziploc" id="edit_pres_ziploc" value="1"> Bolsa ziploc</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_whirlpak" id="edit_pres_whirlpak" value="1"> Bolsa whirlpak</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_metalizada" id="edit_pres_metalizada" value="1"> Bolsa metalizada</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_frasco" id="edit_pres_frasco" value="1"> Frasco</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_bidon" id="edit_pres_bidon" value="1"> Bidón</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="pres_otro" id="edit_pres_otro_chk" value="1"> Otro</label>
                        <input type="text" name="pres_otro_txt" id="edit_pres_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:200px;">
                    </div>
                </div>

                @can('laboratory.update')
                <div>
                    <label class="block text-sm font-semibold">Lote almacén</label>
                    <select name="lote_almacen" id="edit_lote_almacen" class="w-full border rounded px-2 py-1">
                        <option value="">— Selecciona un producto primero —</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold">Lote venta</label>
                    <input type="text" name="lote_venta" id="edit_lote_venta" class="w-full border rounded px-2 py-1">
                </div>
                @endcan

                <div>
                    <label class="block text-sm font-semibold">Fecha de recolección</label>
                    <input type="date" name="fecha_recoleccion" id="edit_fecha_recoleccion" class="w-full border rounded px-2 py-1">
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold">Documentación solicitada</label>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_cc" id="edit_docs_cc" value="1"> CC</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_ft" id="edit_docs_ft" value="1"> FT</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_hs" id="edit_docs_hs" value="1"> HS</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="docs_otro" id="edit_docs_otro_chk" value="1"> Otro</label>
                        <input type="text" name="docs_otro_txt" id="edit_docs_otro_txt" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:200px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="border rounded">
            <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DEL CLIENTE</div>
            <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-4">
                    <label for="edit_customer_id" class="block text-sm font-semibold">Cliente</label>
                    <select name="customer_id" id="edit_customer_id" class="w-full border rounded px-2 py-1">
                        <option value="">— Selecciona un cliente —</option>
                        @foreach($customers as $c)
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
        <x-button type="button" id="edit-solicitud-muestras-submit" class="bg-green-600 hover:bg-green-700 text-white">Actualizar y Generar PDF</x-button>
    </div>

<script>
(function(){
    const modal = document.getElementById('edit-02');
    const form = document.getElementById('edit-solicitud-muestras-form');
    const btnSubmit = document.getElementById('edit-solicitud-muestras-submit');
    const BATCHES_BY_PRODUCT = @json($batchesByProduct ?? []);

    window.editToggleText = function(enable, elId) { 
        const el = document.getElementById(elId);
        if (!el) return; 
        el.disabled = !enable; 
        if (!enable) el.value = ''; 
    }

    const setups = [
        { chk: 'edit_pres_otro_chk', txt: 'edit_pres_otro_txt' },
        { chk: 'edit_docs_otro_chk', txt: 'edit_docs_otro_txt' },
        { chk: 'edit_entrega_otro_chk', txt: 'edit_entrega_otro_txt' }
    ];

    setups.forEach(s => {
        const checkbox = document.getElementById(s.chk);
        checkbox?.addEventListener('change', () => editToggleText(checkbox.checked, s.txt));
    });

    window.fillEditLotes = function(productId, selectedLote = null) {
        const loteSel = document.getElementById('edit_lote_almacen');
        if (!loteSel) return;

        let html = '<option value="">— Selecciona un lote —</option>';
        const lotes = BATCHES_BY_PRODUCT[productId] || [];

        if (lotes.length > 0) {
            lotes.forEach(l => {
                const isSelected = (String(l) === String(selectedLote)) ? 'selected' : '';
                html += `<option value="${l}" ${isSelected}>${l}</option>`;
            });
            loteSel.disabled = false;
        } else {
            html = productId ? '<option value="">— No hay lotes —</option>' : '<option value="">— Selecciona un producto primero —</option>';
            loteSel.disabled = true;
        }
        loteSel.innerHTML = html;
    };

    const prodSel = document.getElementById('edit_product_id');
    const skuInp = document.getElementById('edit_sku');

    prodSel?.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        skuInp.value = opt ? opt.dataset.sku : '';
        fillEditLotes(this.value); 
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

    btnSubmit?.addEventListener('click', async function() {
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
                window.open(`/laboratory/customer-request/${id}/pdf`, '_blank');
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