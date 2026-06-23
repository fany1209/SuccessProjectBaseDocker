<x-modal id="ins">
  <form id="rcv08-form" method="POST" action="{{ route('quality.pdf8') }}" class="w-full space-y-6" id="formatEight">
    @csrf

    {{-- =============== DATOS GENERALES =============== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Datos Generales</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <x-label value="Proveedor"/>
          <select id="supplier" name="supplier_id" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
            <option value="">-- Selecciona un proveedor --</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->supplier_id ?? '' }}" data-code="{{ $s->supplier_code ?? '' }}">
                {{ $s->name }}
              </option>
            @endforeach
          </select>
          <input type="hidden" name="supplier_code" id="supplier_code">
        </div>

        <div>
          <x-label value="Código de proveedor" />
          <input type="text" id="supplier_code_view" class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-50" readonly>
        </div>

        <div>
          <x-label value="Fecha de entrada"/>
          <input type="date" name="fecha_inspeccion" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
        </div>
      </div>
    </div>

    {{-- =============== DATOS DE LA CARGA =============== --}}
    <div class="border rounded-lg p-4 space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Datos de la Carga</h2>
        <button type="button" id="add-row" class="px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
          + Agregar producto
        </button>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm text-center">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-2 py-2 w-[6%]">N°</th>
              <th class="px-2 py-2 w-[32%]">Producto</th>
              <th class="px-2 py-2 w-[16%]">N° de lote</th>
              <th class="px-2 py-2 w-[16%]">Presentación</th>
              <th class="px-2 py-2 w-[12%]">Cantidad</th>
              <th class="px-2 py-2 w-[16%]">Empaque</th>
              <th class="px-2 py-2 w-[6%]">Acción</th>
            </tr>
          </thead>
          <tbody id="items-body">
            <tr class="item-row">
              <td class="px-2 py-2"><span class="row-n">1</span></td>
              <td class="px-2 py-2">
                <select name="items[0][product_id]" class="prod-sl w-full rounded border border-gray-300 px-2 py-1" required>
                  <option value="">Seleccione…</option>
                  @foreach($products as $p)
                    @php
                      $batches = [];
                      if (isset($inventory) && is_iterable($inventory)) {
                        $batches = collect($inventory)
                          ->where('product_id', $p->product_id)
                          ->pluck('batch')->filter()->unique()->values()->toArray();
                      }
                    @endphp
                    <option value="{{ $p->product_id }}"
                            data-batches='@json($batches)'>
                      {{ $p->name }}
                    </option>
                  @endforeach
                </select>
              </td>
              <td class="px-2 py-2">
                <input type="text" name="items[0][lote]" class="lote-in w-full rounded border border-gray-300 px-2 py-1" placeholder="Lote">
              </td>
              <td class="px-2 py-2">
                <input type="text" name="items[0][presentacion]" class="w-full rounded border border-gray-300 px-2 py-1" placeholder="Presentación">
              </td>
              <td class="px-2 py-2">
                <input type="number" step="any" min="0" name="items[0][cantidad]" class="w-full rounded border border-gray-300 px-2 py-1 text-right" placeholder="0">
              </td>
              <td class="px-2 py-2">
                <input type="text" name="items[0][empaque]" class="w-full rounded border border-gray-300 px-2 py-1" placeholder="Ej. Saco 25 kg">
              </td>
              <td class="px-2 py-2">
                <button type="button" class="rm-row text-red-600 hover:underline">Quitar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- =============== LIBERACIÓN DE CARGA =============== --}}
    <div class="border rounded-lg p-4 space-y-3">
      <h2 class="font-semibold">Liberación de carga</h2>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 w-[70%] text-left">El producto viene en tarima</th>
              <th class="px-3 py-2 w-[15%]" >Sí</th>
              <th class="px-3 py-2 w-[15%]">No</th>
            </tr>
          </thead>
          <tbody>
            <tr class="text-center">
              <td class="px-3 py-2 text-left">Seleccione una opción</td>
              <td class="px-3 py-2"><input type="radio" class="platform-radio" name="tarima" value="si"></td>
              <td class="px-3 py-2"><input type="radio" class="platform-radio" name="tarima" value="no"></td>
            </tr>
          </tbody>
        </table>
      </div>

      @php
        $lib_rows = [
          'envase_sellado'        => 'Envase sellado (sin derrames)',
          'embalaje_limpio'       => 'Embalaje (emplaye limpio, sin rasgaduras y/o roto)',
          'identificacion'        => 'Identificación del producto (nombre, lote, cantidad)',
          'otro'                  => 'Otro (especifique)',
        ];
      @endphp

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 w-[42%] text-left">Concepto</th>
              <th class="px-3 py-2 w-[16%]">Cumple</th>
              <th class="px-3 py-2 w-[16%]">No Cumple</th>
              <th class="px-3 py-2 w-[26%] text-left">Observaciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($lib_rows as $k => $label)
              <tr>
                <td class="px-3 py-2 text-left">{{ $label }}</td>
                <td class="px-3 py-2 text-center">
                  <input type="radio" name="lib_{{ $k }}" value="aplica">
                </td>
                <td class="px-3 py-2 text-center">
                  <input type="radio" name="lib_{{ $k }}" value="no_aplica">
                </td>
                <td class="px-3 py-2">
                  <input type="text" name="lib_{{ $k }}_obs" class="w-full rounded border border-gray-300 px-2 py-1" placeholder="Observaciones">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  <div class="border rounded-lg p-4 space-y-3 hidden" id="cargo-reception">
    <h2 class="font-semibold">Cargo Reception</h2>
    <div class="flex items-center justify-between mb-2">
      <h4 class="text-sm font-semibold">Tarimas recibidas</h4>
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-500"><span id="row-platform-count" class="text-xs text-gray-500">1</span> / 26</span>
        <button type="button" id="add-row" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50">+ Agregar fila</button>
      </div>
    </div>
    <table class="min-w-full text-xs md:text-sm">
      <thead class="bg-gray-50">
        <tr class="text-left">
          <th class="px-2 py-2 w-[4%] text-center">No.Tarima</th>
          <th class="px-2 py-2 w-[8%]">Lote</th>
          <th class="px-2 py-2 w-[15%]">Identificación (nombre, lote, cantidad)</th>
          <th class="px-2 py-2 w-[12%] text-center">Tarima en buen estado</th>
          <th class="px-2 py-2 w-[12%] text-center">Emplaye sin rasgaduras</th>
          <th class="px-2 py-2 w-[10%] text-center">No rota / No astillada</th>
          <th class="px-2 py-2 w-[8%]  text-center">Libre de fauna</th>
          <th class="px-2 py-2 w-[9%]  text-center">Libre de materia extraña</th>
          <th class="px-2 py-2 w-[10%] text-center">Saco sellado</th>
          <th class="px-2 py-2 w-[14%]">Observaciones</th>
          <th class="px-2 py-2 w-[6%]  text-center">Acción</th>
        </tr>
      </thead>
      <tbody id="platforms"></tbody>
    </table>
  </div>

    {{-- =============== VERIFICACIÓN DEL TRANSPORTE =============== --}}
    <div class="border rounded-lg p-4 space-y-3">
      <h2 class="font-semibold">Verificación del transporte</h2>

      @php
        $tr_rows = [
          'sello_seguridad' => 'Sello de seguridad (sin violar, colocado correctamente)',
          'fumigacion'      => 'Certificado de fumigación vigente',
          'limpieza'        => 'Limpieza (libre de basura, material o equipo extraño, sin vómito, sin heces fecales)',
          'aromas'          => 'Libre de aromas atípicos',
          'puertas'         => 'Puertas herméticas',
          'piso'            => 'Piso (sin orificios, sin desprendimiento de maderas, sellados)',
          'techo'           => 'Techo (sellado, sin desprendimiento de pintura)',
          'paredes'         => 'Paredes sin astillas o filos cortantes',
          'otro'            => 'Otro (especifique):',
        ];
      @endphp

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 w-[42%] text-left">Concepto</th>
              <th class="px-3 py-2 w-[16%]">Aplica</th>
              <th class="px-3 py-2 w-[16%]">No aplica</th>
              <th class="px-3 py-2 w-[26%] text-left">Observaciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tr_rows as $k => $label)
              <tr>
                <td class="px-3 py-2 text-left">{{ $label }}</td>
                <td class="px-3 py-2 text-center">
                  <input type="radio" name="tr_{{ $k }}" value="aplica">
                </td>
                <td class="px-3 py-2 text-center">
                  <input type="radio" name="tr_{{ $k }}" value="no_aplica">
                </td>
                <td class="px-3 py-2">
                  <input type="text" name="tr_{{ $k }}_obs" class="w-full rounded border border-gray-300 px-2 py-1" placeholder="Observaciones">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

 {{-- ================== INCIDENCIAS ================== --}}
        <div class="w-full mt-6" id="incidencias8">
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-green-200">
                        <tr>
                            <th class="px-3 py-2 w-[55%]">Incidencias</th>
                            <th class="px-3 py-2 w-[10%] text-center">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="incident[has]" id="inc_yes" value="1" class="accent-green-600">
                                    <span>Sí</span>
                                </label>
                            </th>
                            <th class="px-3 py-2 w-[10%] text-center">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="incident[has]" id="inc_no" value="0" class="accent-green-600" checked>
                                    <span>No</span>
                                </label>
                            </th>
                            <th class="px-3 py-2 w-[25%] text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="font-semibold">Folio:</span>
                                    <input type="text" name="incident[folio]" id="inc_folio_8"
                                        class="w-36 rounded-md border border-gray-300 px-2 py-1 text-right"
                                        placeholder="SRI0001"
                                        list="inc_folios_8">
                                    <datalist id="inc_folios_8"></datalist>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="inc-extra8">
                            <td colspan="4" class="px-3 py-2 align-top">
                                <div class="font-semibold mb-1">Descripción:</div>
                                <textarea name="incident[description]" id="inc_desc_8" rows="4"
                                    class="w-full rounded-md border border-gray-300 px-2 py-2"
                                    placeholder="• Se detectaron sacos rotos y sucios.
                                                • Se observan sacos con terrones de levadura.
                                                • ..."></textarea>
                            </td>
                        </tr>

                        <tr class="inc-extra8">
                            <td colspan="4" class="px-3 py-2 align-top">
                                <div class="font-semibold mb-1">Acciones que se implementaron:</div>
                                <textarea name="incident[actions]" id="inc_actions_8" rows="3"
                                    class="w-full rounded-md border border-gray-300 px-2 py-2"
                                    placeholder="• Se reensacaron los sacos dañados.
                                                • Se clasificaron para devolución al proveedor.
                                                • ..."></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    {{-- =============== FIRMAS =============== --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Nombre</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <x-label value="Nombre de quien realizó la inspección"/>
          <input type="text" name="inspector_nombre" class="w-full rounded border border-gray-300 px-3 py-2" placeholder="Nombre del responsable">
        </div>
      </div>
    </div>

    <div class="flex justify-end">
        <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
        <x-button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Generar PDF</x-button>
    </div>
  </form>

  <table class="d-none">
    <tbody>
        <tr id="row-platform-template" class="border-t">
          <td class="px-2 py-2 text-center row-index"></td>
          <td class="px-2 py-2"><input type="text" name="batch[][]" class="w-full rounded-md border border-gray-300 px-2 py-1" placeholder="Ej. L-2405"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="ident_ok" name="option[0][ident_ok]" value="1"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="buen_estado" name="option[0][buen_estado]" value="1"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="emplaye_ok" name="option[0][emplaye_ok]" value="1"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="no_rota" name="option[0][no_rota]" value="1"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="fauna_libre" name="option[0][fauna_libre]" value="1"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="materia_libre" name="option[0][materia_libre]" value="1"></td>
          <td class="px-2 py-2 text-center"><input type="checkbox" data-key="saco_sellado" name="option[0][saco_sellado]" value="1"></td>
          <td class="px-2 py-2"><input type="text" name="observation[][]" class="w-full rounded-md border border-gray-300 px-2 py-1" placeholder="Observation."></td>
          <td class="px-2 py-2 text-center"><button type="button" class="remove-row text-red-600 hover:underline">Quitar</button></td>
        </tr>
    </tbody>
  </table>

  {{-- =================== TEMPLATE FILA =================== --}}
  <template id="row-tpl">
    <tr class="item-row">
      <td class="px-2 py-2"><span class="row-n">#</span></td>
      <td class="px-2 py-2">
        <select name="items[0][product_id]" class="prod-sl w-full rounded border border-gray-300 px-2 py-1" required>
          <option value="">Seleccione…</option>
          @foreach($products as $p)
            @php
              $batches = [];
              if (isset($inventory) && is_iterable($inventory)) {
                $batches = collect($inventory)
                  ->where('product_id', $p->product_id)
                  ->pluck('batch')->filter()->unique()->values()->toArray();
              }
            @endphp
            <option value="{{ $p->product_id }}" data-batches='@json($batches)'>{{ $p->name }}</option>
          @endforeach
        </select>
      </td>
      <td class="px-2 py-2">
        <input type="text" name="items[0][lote]" class="lote-in w-full rounded border border-gray-300 px-2 py-1" placeholder="Lote">
      </td>
      <td class="px-2 py-2">
        <input type="text" name="items[0][presentacion]" class="w-full rounded border border-gray-300 px-2 py-1" placeholder="Presentación">
      </td>
      <td class="px-2 py-2">
        <input type="number" step="any" min="0" name="items[0][cantidad]" class="w-full rounded border border-gray-300 px-2 py-1 text-right" placeholder="0">
      </td>
      <td class="px-2 py-2">
        <input type="text" name="items[0][empaque]" class="w-full rounded border border-gray-300 px-2 py-1" placeholder="Ej. Saco 25 kg">
      </td>
      <td class="px-2 py-2">
        <button type="button" class="rm-row text-red-600 hover:underline">Quitar</button>
      </td>
    </tr>
  </template>
</x-modal>
@push('js')
<script>
$(document).ready(function () {
  let inc8Loaded = false;
  const inc8Map  = {};

 function normalizeBullets(txt){
  if (!txt) return '';
  let out = String(txt);
  try {
    const arr = JSON.parse(out);
    if (Array.isArray(arr)) {
      out = arr.map(x => String(x).trim()).filter(Boolean).join('\n');
    }
  } catch(e){}
  return out.split(/\r?\n/).map(s => s.trim()).filter(Boolean).join('\n');
}
  function loadIncidencias8(){
    if (inc8Loaded) return;
    const $list = $('#inc_folios_8');
    if (!$list.length) return;

    $list.empty();
    $.getJSON("{{ route('quality.incidencias') }}", function(rows){
      (rows || []).forEach(r => {
        const folio = (r.folio || '').toString();
        if (!folio) return;
        const desc = normalizeBullets(r.descripcion || '');
        const acts = normalizeBullets(r.acciones    || '');
        inc8Map[folio] = { desc, acts };
        const preview = (desc.split('\n')[0] || '').slice(0, 100);
        $list.append($('<option>', { value: folio, label: preview }));
      });
      inc8Loaded = true;
    });
  }

  function reactiveFormart(){
    const hasInc = $('input[name="incident[has]"]:checked').val() === '1';

    const $folio = $('#inc_folio_8');
    const $desc  = $('#inc_desc_8');
    const $acts  = $('#inc_actions_8');
    const $insp  = $('#inc_inspector_8'); 
    const $extras= $('.inc-extra8'); 
    if (hasInc) {
      $extras.removeClass('hidden').each(function(){ this.style.display = ''; });
      $desc.prop('disabled', false);
      $acts.prop('disabled', false);
      $insp.prop('disabled', false);
      $folio.prop('readonly', false).prop('disabled', false);
      if (($folio.val() || '').trim().toUpperCase() === 'NA') $folio.val('');

      if ($('#inc_folios_8').children().length === 0) loadIncidencias8();

    } else {
      $extras.addClass('hidden').each(function(){ this.style.display = 'none'; });
      $desc.val('').prop('disabled', true);
      $acts.val('').prop('disabled', true);
      $insp.val('').prop('disabled', true);
      $folio.val('NA').prop('readonly', true).prop('disabled', false);
    }
  }
  $(document).on('change', 'input[name="incident[has]"]', reactiveFormart);
  $(document).on('change', '#inc_folio_8', function(){
    const f = ($(this).val() || '').trim();
    if (inc8Map[f]) {
      $('#inc_desc_8').val(inc8Map[f].desc || '');
      $('#inc_actions_8').val(inc8Map[f].acts || '');
    }
  });

  $(document).on('click', '.format-8', function(){
    $('#inc_folio_8').val('NA').prop('readonly', true).prop('disabled', false);
    $('.inc-extra8').addClass('hidden');
    $('#inc_desc_8, #inc_actions_8, #inc_inspector_8').val('').prop('disabled', true);
    $('input[name="incident[has]"][value="0"]').prop('checked', true);
  });

  $(document).on('submit', '#inspection-form', function(){
    const hasInc = $('input[name="incident[has]"]:checked').val() === '1';
    const $folio = $('#inc_folio_8');
    $folio.prop('disabled', false);
    if (!hasInc) $folio.val('NA').prop('readonly', true);
  });

  reactiveFormart();
  if ($('input[name="incident[has]"]:checked').val() === '1') loadIncidencias8();

});
</script>
<script>
(function(){
  const $tbody = document.getElementById('items-body');   
  const $add   = document.getElementById('add-row');
  let row_index = 1;
  let rowIndex = 1;
  function reactiveFormatEight(){    
   $(document).on('click', '#add-row', function () {
      if (row_index >= 26) return;

      row_index += 1;
      rowIndex   = row_index;
      const $tpl = $('#row-platform-template');
      const $row = $tpl.clone(false, false)
                      .removeAttr('id')    
                      .removeClass('hidden');

      $row.find('input[type="text"], input[type="number"], input[type="hidden"], textarea').val('');
      $row.find('input[type="checkbox"], input[type="radio"]')
          .prop('checked', false)
          .removeAttr('checked'); 
      $row.find('select').prop('selectedIndex', 0);
      $row.find('.row-index').text(row_index);
      $row.find('[data-key]').each(function () {
        const key = $(this).data('key');
        this.name = `option[${rowIndex}][${key}]`;
      });

      $('#platforms').append($row);
      $('#row-platform-count').text(row_index);
      updateRowIndex();
    });

    $('.platform-radio').on('change',function(){
      const rowPlatform = $('#row-platform-template').clone()
      var option = $(this).val();
      if(option === 'si'){
        $('#cargo-reception').removeClass('hidden');
        rowPlatform.find('.row-index').text(row_index);
        $('#platforms').append(rowPlatform);
      }else{
        $('#cargo-reception').addClass('hidden');
        $('#platforms').empty();
        row_index = 1;
        $('#row-platform-count').text(row_index);
      }
    });

    $(document).on('click', '.remove-row', function () {
      row_index -= 1;
      $('#row-platform-count').text(row_index);
      $(this).closest('tr').remove();
      updateRowIndex()
    });

    function updateRowIndex() {
      $('#platforms tr').each(function (index) {
        $(this).find('.row-index').text(index + 1);
      });
    }
  }
  reactiveFormatEight();

  function reindexItems(){
    if (!$tbody) return;
    [...$tbody.querySelectorAll('tr.item-row')].forEach((tr, i) => {
      const n = tr.querySelector('.row-n');
      if (n) n.textContent = i + 1;
      tr.querySelectorAll('input,select,textarea').forEach(el => {
        const name = el.getAttribute('name');
        if (!name) return;
        el.setAttribute('name', name.replace(/items\[\d+\]/, `items[${i}]`));
      });
    });
  }

  function ensureLotWidget(selectEl){
    if (!selectEl) return;

    // Lotes desde data-batches del <option selected>
    const opt = selectEl.selectedOptions ? selectEl.selectedOptions[0] : null;
    let batches = [];
    if (opt) {
      try { batches = JSON.parse(opt.getAttribute('data-batches') || '[]') || []; }
      catch(e){ batches = []; }
    }

    const tr = selectEl.closest('tr');
    if (!tr) return;
    const td = tr.querySelector('td:nth-child(3)');
    if (!td) return;

    let current  = td.querySelector('.lote-in, .lote-sl');
    const name   = current ? current.getAttribute('name') : 'items[0][lote]';

    if (batches.length > 0) {
      if (!current || !current.classList.contains('lote-sl')) {
        td.innerHTML = `<select name="${name}" class="lote-sl w-full rounded border border-gray-300 px-2 py-1"></select>`;
        current = td.querySelector('.lote-sl');
      }
      current.innerHTML = `<option value="">Seleccione…</option>` +
        batches.map(b => `<option value="${b}">${b}</option>`).join('');
    } else {
      if (!current || !current.classList.contains('lote-in')) {
        td.innerHTML = `<input type="text" name="${name}" class="lote-in w-full rounded border border-gray-300 px-2 py-1" placeholder="Lote">`;
      }
    }
  }

  function clearRow(tr){
    const prod = tr.querySelector('select.prod-sl');
    if (prod) prod.selectedIndex = 0;
    const loteTd = tr.querySelector('td:nth-child(3)');
    if (loteTd) {
      loteTd.innerHTML = `<input type="text" name="items[0][lote]" class="lote-in w-full rounded border border-gray-300 px-2 py-1" placeholder="Lote">`;
    }
    const pres = tr.querySelector('input[name*="[presentacion]"]');
    if (pres) pres.value = '';
    const cant = tr.querySelector('input[name*="[cantidad]"]');
    if (cant) cant.value = '';
    const emp = tr.querySelector('input[name*="[empaque]"]');
    if (emp) emp.value = '';
  }

  $add && $add.addEventListener('click', () => {
    if (!$tbody) return;
    const first = $tbody.querySelector('tr.item-row');
    if (!first) return;

    const clone = first.cloneNode(true);
    clearRow(clone);
    $tbody.appendChild(clone);
    reindexItems();
  });

  document.addEventListener('click', (e) => {
    if (!e.target.classList.contains('rm-row')) return;
    if (!$tbody) return;

    const rows = $tbody.querySelectorAll('tr.item-row').length;
    const tr = e.target.closest('tr');

    if (rows <= 1) {
      clearRow(tr);
      reindexItems();
    } else {
      tr.remove();
      reindexItems();
    }
  });

  document.addEventListener('change', (e) => {
    if (e.target.classList.contains('prod-sl')) {
      ensureLotWidget(e.target);
    }
  });

  const firstProd = document.querySelector('.prod-sl');
  if (firstProd) ensureLotWidget(firstProd);
})();
</script>

@endpush