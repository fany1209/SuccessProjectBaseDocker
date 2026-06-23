<x-modal id="edit-reception">
  <form id="edit-reception-form" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" id="recepcion-id" name="id">

    <div class="space-y-6">
      <h3 class="text-base font-semibold">Editar: Recepción de muestra</h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="block text-sm font-semibold">Folio muestra</label>
          <input type="text" name="folio_muestra" id="folio_muestra"
                 class="w-full rounded-md border px-3 py-2 bg-gray-100"
                 readonly >
        </div>
      </div>

      <div class="mt-2 border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">INFORMACIÓN DE LA MUESTRA</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          <div>
            <label for="product_id" class="block text-sm font-medium mb-1">Producto</label>
            <select name="product_id" id="product_id"
                    class="w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600">
              <option value="">— Selecciona un producto —</option>
              @foreach($products as $p)
                <option value="{{ $p->product_id }}" data-sku="{{ $p->sku }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-semibold">Nombre comercial</label>
            <input type="text" name="nombre_comercial" id="nombre_comercial"
                   class="w-full rounded-md border px-3 py-2">
          </div>

          <div class="mt-3">
            <label class="block text-sm font-semibold">SKU</label>
            <input type="text" name="sku" id="sku"
                   class="w-full rounded-md border px-3 py-2 bg-gray-100" readonly>
          </div>

          <div class="mt-3">
            <label for="batch" class="block text-sm font-semibold">Lote</label>
            <select name="batch" id="batch" class="w-full rounded-md border px-3 py-2">
              <option value="">— Selecciona un lote —</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold">Fecha de entrada</label>
            <input type="date" name="fecha_entrada" id="fecha_entrada"
                   class="w-full rounded-md border px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-semibold">Fecha de caducidad</label>
            <input type="date" name="fecha_caducidad" id="fecha_caducidad"
                   class="w-full rounded-md border px-3 py-2">
          </div>

          <div class="md:col-span-3">
            <label class="block text-sm font-semibold">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="2"
                      class="w-full rounded-md border px-3 py-2"></textarea>
          </div>

          <div class="md:col-span-3">
            <label class="block text-sm font-semibold mb-1">Origen de la muestra</label>
            <div class="flex flex-wrap gap-4 text-sm">
              <label class="inline-flex items-center gap-2"><input type="radio" name="origen_muestra" value="proveedor"> Proveedor</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="origen_muestra" value="produccion"> Producción</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="origen_muestra" value="almacen"> Almacén</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="origen_muestra" value="otro" id="origen_otro_radio"> Otro</label>
              <input id="origen_otro_txt" type="text" name="origen_otro" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:220px;">
            </div>
          </div>
        </div>
      </div>

      <div class="mt-3 border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS DE RECEPCIÓN</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          
          {{-- SECCIÓN CORREGIDA: OBJETIVO (CHECKBOXES) --}}
          <div class="md:col-span-3">
            <label class="block text-sm font-semibold mb-1">Objetivo de la muestra</label>
            <div class="flex flex-wrap gap-4 text-sm">
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="objetivo_muestra[]" value="inspeccion"> Inspección</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="objetivo_muestra[]" value="retencion"> Retención</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="objetivo_muestra[]" value="analisis"> Análisis</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="objetivo_muestra[]" value="desarrollo"> Desarrollo</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="objetivo_muestra[]" value="exposicion"> Exposición</label>
              <label class="inline-flex items-center gap-2"><input type="checkbox" name="objetivo_muestra[]" value="otro" id="obj_otro_radio"> Otro</label>
              <input id="obj_otro_txt" type="text" name="objetivo_otro" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:220px;">
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold">Cantidad</label>
            <input type="number" step="any" name="cantidad" id="cantidad"
                   class="w-full rounded-md border px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-semibold">Unidad de medida</label>
            <div class="flex flex-wrap gap-3 text-sm">
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="g"> g</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="kg"> kg</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="l"> l</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="ml"> ml</label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="um" value="otro" id="um_otro_radio"> Otro</label>
              <input id="um_otro_txt" type="text" name="um_otro" class="border rounded px-2 py-1" placeholder="Especifique" style="min-width:180px;">
            </div>
          </div>

          <div class="mt-3">
            <label for="supplier_id" class="block text-sm font-semibold">Proveedor</label>
            <select name="supplier_id" id="supplier_id" class="w-full rounded-md border px-3 py-2">
              <option value="">— Selecciona un proveedor —</option>
              @foreach($suppliers as $s)
                <option value="{{ $s->supplier_id }}">{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="mt-3 border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">OBSERVACIONES LABORATORIO</div>
        <div class="p-3">
          <textarea name="observaciones_laboratorio" id="observaciones_laboratorio" rows="4"
                    class="w-full rounded-md border px-3 py-2"></textarea>
        </div>
      </div>

      <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Nombre de quien entrega en laboratorio</label>
          <input type="text" name="firma_entrega_nombre" id="firma_entrega_nombre"
                 class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-semibold">Nombre de recepción en laboratorio</label>
          <input type="text" name="firma_recepcion_nombre" id="firma_recepcion_nombre"
                 class="w-full rounded-md border px-3 py-2">
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-2 mt-6">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">Guardar cambios</x-button>
    </div>
  </form>

@push('js')
<script>
/* ===================== HELPERS ===================== */
function toggleInput(enable, $el){
  if(!$el?.length) return;
  $el.prop('disabled', !enable);
  if(!enable && !$el.data('keep')) $el.val('');
}

function setRadios(name, val){
  if(!val) return;
  $(`input[name="${name}"][value="${val}"]`).prop('checked', true).trigger('change');
}

function ensureSelectValue($select, value, fallbackLabel, dataAttrs = {}){
  const v = String(value ?? '');
  if(!v){ $select.val(''); return; }
  if($select.find(`option[value="${v.replace(/"/g,'\\"')}"]`).length === 0){
    const data = Object.entries(dataAttrs).map(([k,val]) => ` data-${k}="${String(val ?? '').replace(/"/g,'&quot;')}"`).join('');
    $select.append(`<option value="${v}"${data}>${fallbackLabel || v}</option>`);
  }
  $select.val(v);
}

/* === Listeners para habilitar/deshabilitar "Otro" en tiempo real === */
$(document).on('change', 'input[name="origen_muestra"]', function() {
    toggleInput($(this).val() === 'otro', $('#origen_otro_txt'));
});

$(document).on('change', 'input[name="objetivo_muestra[]"]', function() {
    toggleInput($('#obj_otro_radio').is(':checked'), $('#obj_otro_txt'));
});

$(document).on('change', 'input[name="um"]', function() {
    toggleInput($(this).val() === 'otro', $('#um_otro_txt'));
});

/* ===================== PRODUCT CHANGE ===================== */
$(document).on('change', '#edit-reception #product_id', function () {
  const $m = $('#edit-reception'), $bat = $m.find('#batch'), $sku = $m.find('#sku');
  const pid = $(this).val();
  const sk = $(this).find('option:selected').data('sku') || '';
  if(sk) $sku.val(sk);

  $bat.html('<option value="">— Cargando lotes… —</option>').prop('disabled', true);
  if(!pid){
    $bat.html('<option value="">— Selecciona un lote —</option>').prop('disabled', true);
    return;
  }

  $.ajax({
    url: "{{ route('getReceptionBatches') }}",
    dataType: 'json',
    data: { product_id: pid, _: Date.now() },
    success: function(res){
      const list = (res?.batches || []).map(b => typeof b === 'object' ? b.batch : b).filter(Boolean);
      if(list.length){
        $bat.empty().append('<option value="">— Selecciona un lote —</option>');
        list.forEach(v => $bat.append(`<option value="${v}">${v}</option>`));
        $bat.prop('disabled', false);
      } else {
        $bat.html('<option value="">— No hay lotes —</option>').prop('disabled', true);
      }
    }
  });
});

/* ===================== MODAL EDIT (CARGAR DATOS) ===================== */
$(document).on('click', '.edit-btn', function () {
  const id = $(this).data('id');
  if(!id) return;

  const $m = $('#edit-reception');
  $m.find('input[name="objetivo_muestra[]"]').prop('checked', false); // Reset checkboxes

  $.ajax({
    url: "{{ route('reception.show', ':id') }}".replace(':id', id),
    dataType: 'json',
    success: function(res){
      const rec = res?.recepcion;
      if(!rec) return;

      $m.find('#recepcion-id').val(rec.id);
      $m.find('#folio_muestra').val(rec.folio_muestra || '');
      $m.find('#nombre_comercial').val(rec.nombre_comercial || '');
      $m.find('#fecha_entrada').val(rec.fecha_entrada || '');
      $m.find('#fecha_caducidad').val(rec.fecha_caducidad || '');
      $m.find('#descripcion').val(rec.descripcion || '');
      $m.find('#cantidad').val(rec.cantidad || '');
      $m.find('#observaciones_laboratorio').val(rec.observaciones_laboratorio || '');
      $m.find('#firma_entrega_nombre').val(rec.firma_entrega_nombre || '');
      $m.find('#firma_recepcion_nombre').val(rec.firma_recepcion_nombre || '');

      ensureSelectValue($m.find('#product_id'), rec.product_id, rec.product_name || '', { sku: rec.sku || '' });
      $m.find('#product_id').trigger('change');
      if(rec.sku) $m.find('#sku').val(rec.sku);

      setTimeout(() => { $m.find('#batch').val(String(rec.batch || '')); }, 350);

      ensureSelectValue($m.find('#supplier_id'), rec.supplier_id, '');
      
      // Origen
      setRadios('origen_muestra', rec.origen_muestra);
      $m.find('#origen_otro_txt').val(rec.origen_otro || '');
      toggleInput(rec.origen_muestra === 'otro', $m.find('#origen_otro_txt'));

      // OBJETIVO (Múltiple)
      if (rec.objetivo_muestra) {
          const objetivosArray = String(rec.objetivo_muestra).split(',').map(s => s.trim());
          objetivosArray.forEach(val => {
              $m.find(`input[name="objetivo_muestra[]"][value="${val}"]`).prop('checked', true);
          });
          $m.find('#obj_otro_txt').val(rec.objetivo_otro || '');
          toggleInput(objetivosArray.includes('otro'), $m.find('#obj_otro_txt'));
      }

      // UM
      setRadios('um', rec.um);
      $m.find('#um_otro_txt').val(rec.um_otro || '');
      toggleInput(rec.um === 'otro', $m.find('#um_otro_txt'));

      // Docs
      $m.find('#docs_ccf').prop('checked', !!rec.docs_ccf);
      $m.find('#docs_ft').prop('checked',  !!rec.docs_ft);
      $m.find('#docs_hs').prop('checked',  !!rec.docs_hs);
      $m.find('#doc_otro_chk').prop('checked', !!rec.docs_otro);
      $m.find('#doc_otro_txt').val(rec.docs_otro_txt || '');
      toggleInput(!!rec.docs_otro, $m.find('#doc_otro_txt'));

      const updateUrl = "{{ route('pdf1.update', ':id') }}".replace(':id', rec.id);
      $m.find('#edit-reception-form').attr('action', updateUrl);

      $m.removeClass('hidden').addClass('flex');
    }
  });
});

/* ===================== SUBMIT (AJAX) ===================== */
$('#edit-reception-form').on('submit', function(e){
  e.preventDefault();
  const $btn = $(this).find('button[type="submit"]');
  $btn.prop('disabled', true).text('Guardando...');

  $.ajax({
    type: 'POST',
    url: $(this).attr('action'),
    data: $(this).serialize(),
    success: function(res){
      Swal.fire({ icon:'success', title:'Guardado', text: res?.message || 'Cambios guardados' });
      $('#edit-reception').removeClass('flex').addClass('hidden');
      if($.fn.DataTable.isDataTable('#reception-table')){
        $('#reception-table').DataTable().ajax.reload(null, false);
      }
    },
    error: function(xhr){
      Swal.fire({ icon:'error', title:'Error', text: xhr?.responseJSON?.message || 'No se pudo guardar' });
    },
    complete: function(){
      $btn.prop('disabled', false).text('Guardar cambios');
    }
  });
});
</script>
@endpush
</x-modal>