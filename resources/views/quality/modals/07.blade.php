<x-modal id="CC">
  <form id="certificado-form" method="POST" action="{{ route('quality.pdf7') }}" class="space-y-6">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Consecutivo</label>
        <input type="text" name="consecutivo"
                class="w-full rounded-md border border-gray-300 px-3 py-2"
                placeholder="Ej. CC25001"
                value="{{ old('consecutivo') }}">
    </div>


    {{-- =================== DATOS GENERALES =================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Ciudad</label>
        <input type="text" name="ciudad" class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('ciudad') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fecha</label>
        <input type="date" name="fecha" class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha') }}">
      </div>

    {{-- customers --}}
      <div>
        <label class="block text-sm font-medium mb-1">Cliente</label>

        <select id="cliente_select"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">— Selecciona un cliente —</option>

          @php
            $oldCliente = old('cliente', '');

            $clientesOrdenados = collect($customers ?? [])
              ->map(function ($c) {
                $name = is_array($c) ? ($c['name'] ?? $c['customer_name'] ?? null)
                                    : ($c->name ?? $c->customer_name ?? null);
                return trim((string)$name);
              })
              ->filter(fn ($x) => $x !== '')
              ->unique(fn ($x) => mb_strtolower($x))
              ->sortBy(fn ($x) => $x, SORT_NATURAL | SORT_FLAG_CASE)
              ->values();

            $oldEnListaCliente = $clientesOrdenados->contains($oldCliente);
          @endphp

          @forelse($clientesOrdenados as $cname)
            <option value="{{ $cname }}" {{ $oldCliente === $cname ? 'selected' : '' }}>
              {{ $cname }}
            </option>
          @empty
            <option value="">— Sin datos de clientes —</option>
          @endforelse

          <option value="__OTRO__" {{ (!$oldEnListaCliente && $oldCliente !== '') ? 'selected' : '' }}>
            Otro...
          </option>
        </select>

        <input type="text"
              id="cliente_otro"
              class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 {{ (!$oldEnListaCliente && $oldCliente !== '') ? '' : 'hidden' }}"
              placeholder="Escribe el cliente"
              value="{{ (!$oldEnListaCliente ? $oldCliente : '') }}">

        <input type="hidden" name="cliente" id="cliente_hidden" value="{{ old('cliente') }}">
      </div>
      
     {{-- products --}}
      <div>
        <label class="block text-sm font-medium mb-1">Producto</label>

        <select id="producto_select"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">— Selecciona un producto —</option>

          @php
            $oldProducto = old('producto', '');
            $listaProductos = collect($products ?? [])
              ->map(function ($p) {
                $name = is_array($p) ? ($p['name'] ?? null) : ($p->name ?? null);
                return trim((string)$name);
              })
              ->filter(fn($x) => $x !== '')
              ->unique()
              ->values();

            $oldEnLista = $listaProductos->contains($oldProducto);
          @endphp

          @foreach($listaProductos as $pname)
            <option value="{{ $pname }}" {{ $oldProducto === $pname ? 'selected' : '' }}>
              {{ $pname }}
            </option>
          @endforeach

          <option value="__OTRO__" {{ (!$oldEnLista && $oldProducto !== '') ? 'selected' : '' }}>
            Otro...
          </option>
        </select>

        <input type="text"
              id="producto_otro"
              class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 {{ (!$oldEnLista && $oldProducto !== '') ? '' : 'hidden' }}"
              placeholder="Escribe el producto"
              value="{{ (!$oldEnLista ? $oldProducto : '') }}">

        <input type="hidden" name="producto" id="producto_hidden" value="{{ old('producto') }}">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Lote</label>
        <input type="text" name="lote" class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('lote') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Cantidad</label>
        <input type="text" name="cantidad" class="w-full rounded-md border border-gray-300 px-3 py-2"
               placeholder="Ej. 500 kg" value="{{ old('cantidad') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fecha de fabricación</label>
        <input type="date" name="fecha_fabricacion" class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha_fabricacion') }}">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fecha de caducidad</label>
        <input type="date" name="fecha_caducidad" class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha_caducidad') }}">
      </div>
    </div>

    {{-- =========== ANÁLISIS BROMATOLÓGICO (dinámico) =========== --}}
  @php
    $bromaOptions = ['Proteína','Humedad','Cenizas','Fibra','Grasa','pH','Lactosa','Concentración','Sólidos','Otros'];
    $bromaOld = old('bromato', [['prueba'=>'','especificacion'=>'','resultado'=>'']]);
  @endphp

  <div class="border rounded-lg">
    <div class="flex items-center justify-between px-3 py-2 bg-green-100 rounded-t-lg">
      <h3 class="font-semibold text-sm">Análisis Bromatológico</h3>
      <button type="button" id="add-broma" class="text-sm border px-2 py-1 rounded-md hover:bg-white">
        + Agregar fila
      </button>
    </div>

    <div class="p-3 overflow-x-auto">
      <table class="min-w-full text-sm" id="broma-table">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-2 py-2 text-left w-[40%]">Prueba</th>
            <th class="px-2 py-2 text-left w-[30%]">Especificación</th>
            <th class="px-2 py-2 text-left w-[30%]">Resultado</th>
            <th class="px-2 py-2 text-center w-[60px]">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bromaOld as $i => $row)
            @php
              $valorPrueba = trim((string)($row['prueba'] ?? ''));
              $enLista = in_array($valorPrueba, $bromaOptions, true);
              $selectValue = $enLista ? $valorPrueba : ($valorPrueba !== '' ? 'Otros' : '');
              $otroValue = $enLista ? '' : $valorPrueba;
            @endphp
            <tr class="broma-row" data-index="{{ $i }}">
              <td class="px-2 py-2">
                <input type="hidden" name="bromato[{{ $i }}][prueba]" class="broma-hidden-prueba" value="{{ $valorPrueba }}">

                <div class="flex flex-col gap-2">
                  <select class="broma-select w-full rounded-md border border-gray-300 px-2 py-1">
                    <option value="" {{ $selectValue==='' ? 'selected' : '' }}>Selecciona…</option>
                    @foreach($bromaOptions as $opt)
                      <option value="{{ $opt }}" {{ $selectValue===$opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                  </select>

                  <input type="text"
                        class="broma-otros w-full rounded-md border border-gray-300 px-2 py-1 {{ $selectValue==='Otros' ? '' : 'hidden' }}"
                        placeholder="Especifica la prueba"
                        value="{{ $otroValue }}">
                </div>
              </td>

              <td class="px-2 py-2">
                <input type="text" name="bromato[{{ $i }}][especificacion]"
                      class="w-full rounded-md border border-gray-300 px-2 py-1"
                      value="{{ $row['especificacion'] ?? '' }}">
              </td>

              <td class="px-2 py-2">
                <input type="text" name="bromato[{{ $i }}][resultado]"
                      class="w-full rounded-md border border-gray-300 px-2 py-1"
                      value="{{ $row['resultado'] ?? '' }}">
              </td>

              <td class="px-2 py-2 text-center">
                <button type="button" class="rm-broma text-red-600 hover:underline">Quitar</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

 {{-- ========== ANÁLISIS MICROBIOLÓGICOS (dinámico) ========== --}}
  @php
    $microOpts = [
      'Cuenta Total Bacteria','Coliformes','Coliformes Totales',
      'Hongos','Levaduras','E.coli','Salmonella spp.','S.aureus','Otros'
    ];
    $microOld = old('micro', [['prueba'=>'','resultado'=>'','unidades'=>'']]);
  @endphp

  <div class="border rounded-lg">
    <div class="flex items-center justify-between px-3 py-2 bg-green-100 rounded-t-lg">
      <h3 class="font-semibold text-sm">Análisis Microbiológicos</h3>
      <button type="button" id="add-micro" class="text-sm border px-2 py-1 rounded-md hover:bg-white">
        + Agregar fila
      </button>
    </div>

    <div class="p-3 overflow-x-auto">
      <table class="min-w-full text-sm" id="micro-table">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-2 py-2 text-left w-[40%]">Prueba</th>
            <th class="px-2 py-2 text-left w-[30%]">Resultado</th>
            <th class="px-2 py-2 text-left w-[30%]">Unidades</th>
            <th class="px-2 py-2 text-center w-[60px]">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($microOld as $i => $row)
            @php
              $v = trim((string)($row['prueba'] ?? ''));
              $enLista = in_array($v, $microOpts, true);
              $selVal  = $enLista ? $v : ($v!=='' ? 'Otros' : '');
              $otroVal = $enLista ? '' : $v;
            @endphp
            <tr class="micro-row" data-index="{{ $i }}">
              <td class="px-2 py-2">
                <input type="hidden" name="micro[{{ $i }}][prueba]" class="micro-hidden-prueba" value="{{ $v }}">
                <div class="flex flex-col gap-2">
                  <select class="micro-select w-full rounded-md border border-gray-300 px-2 py-1">
                    <option value="" {{ $selVal==='' ? 'selected' : '' }}>Selecciona…</option>
                    @foreach($microOpts as $opt)
                      <option value="{{ $opt }}" {{ $selVal===$opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                  </select>
                  <input type="text"
                        class="micro-otros w-full rounded-md border border-gray-300 px-2 py-1 {{ $selVal==='Otros' ? '' : 'hidden' }}"
                        placeholder="Especifica la prueba"
                        value="{{ $otroVal }}">
                </div>
              </td>
              <td class="px-2 py-2">
                <input type="text" name="micro[{{ $i }}][resultado]" class="w-full rounded-md border border-gray-300 px-2 py-1"
                      value="{{ $row['resultado'] ?? '' }}">
              </td>
              <td class="px-2 py-2">
                <input type="text" name="micro[{{ $i }}][unidades]" class="w-full rounded-md border border-gray-300 px-2 py-1"
                      value="{{ $row['unidades'] ?? '' }}">
              </td>
              <td class="px-2 py-2 text-center">
                <button type="button" class="rm-micro text-red-600 hover:underline">Quitar</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
      <div>
        <label class="block text-sm font-medium mb-1">No. de tarimas</label>
        <input type="number" min="0" step="1" name="no_tarimas"
               class="w-full rounded-md border border-gray-300 px-3 py-2"
               placeholder="Ej. 10" value="{{ old('no_tarimas') }}">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Fecha de salida CEDIS</label>
        <input type="date" name="fecha_salida_cedis"
               class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('fecha_salida_cedis') }}">
      </div>

      <div class="md:col-span-1">
        <label class="block text-sm font-medium mb-1">Certificado de tarima</label>
        <input type="text" name="certificado_tarima"
              class="w-full rounded-md border border-gray-300 px-3 py-2"
              placeholder="Ej. CT-001 / Observación..."
              value="{{ old('certificado_tarima') }}">
        <p class="text-xs text-gray-500 mt-1">Escribe el folio o nota del certificado de tarima.</p>
      </div>  

      <div class="md:col-span-1">
        <label class="block text-sm font-medium mb-1">Tipo: Muestra o PT</label>
        <select name="muestra_o_pf" class="w-full rounded-md border border-gray-300 px-3 py-2">
          <option value="">— Selecciona —</option>
          <option value="Muestra" {{ old('muestra_o_pf')==='Muestra' ? 'selected' : '' }}>Muestra</option>
          <option value="PT"      {{ old('muestra_o_pf')==='PT' ? 'selected' : '' }}>PT</option>
        </select>
      </div>
    </div>

  <div class="md:col-span-1">
  <label class="block text-sm font-medium mb-1">Jefe de calidad</label>

  <select class="js-firmante-select w-full rounded-md border border-gray-300 px-3 py-2">
    <option value="">— Selecciona —</option>

    <option value="MBP. Claudio Rugarcia Anaya"
            data-cedula="11078201"
            data-puesto="Jefe de calidad">
      MBP. Claudio Rugarcia Anaya — 11078201
    </option>

    <option value="Dra. Alejandra Sarahí Ramírez Segovia"
            data-cedula="11511768"
            data-puesto="Jefe de calidad">
      Dra. Alejandra Sarahí Ramírez Segovia — 11511768
    </option>

    <option value="I.BQ. Diana Elizabeth Escobedo Álvarez"
            data-cedula="09932389"
            data-puesto="Jefe de calidad">
      I.BQ. Diana Elizabeth Escobedo Álvarez — 09932389
    </option>
  </select>
</div>

<input type="hidden" name="firmante_nombre" class="js-firmante-nombre" value="{{ old('firmante_nombre') }}">
<input type="hidden" name="firmante_cedula" class="js-firmante-cedula" value="{{ old('firmante_cedula') }}">
<input type="hidden" name="firmante_puesto" class="js-firmante-puesto" value="{{ old('firmante_puesto', 'Jefe de calidad') }}">

    {{-- =================== PIE / RESPONSABLE =================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div class="md:col-span-1">
        <label class="block text-sm font-medium mb-1">No. Sello</label>
        <input type="text" name="no_sello" class="w-full rounded-md border border-gray-300 px-3 py-2"
               value="{{ old('no_sello') }}">
      </div>
    </div>

    {{-- =================== ACCIONES =================== --}}
    <div class="flex items-center justify-end gap-3">
        <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
        <x-button type="submit" class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">
        Generar certificado PDF
      </x-button>
    </div>
  </form>

  {{-- ======= JS para filas dinámicas (sin dependencias) ======= --}}
 <script>
  (function () {
    const OPTIONS = ["Proteína","Humedad","Cenizas","Fibra","Grasa","pH","Lactosa","Concentración","Sólidos","Otros"];
    const $table = document.getElementById('broma-table');
    const $tbody = $table.querySelector('tbody');
    const $addBtn = document.getElementById('add-broma');

    function escapeHtml(str) {
      return (str ?? '').toString().replace(/[&<>"']/g, s => (
        { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[s]
      ));
    }

    function createSelect(selected) {
      const sel = document.createElement('select');
      sel.className = 'broma-select w-full rounded-md border border-gray-300 px-2 py-1';
      sel.appendChild(new Option('Selecciona…', ''));
      OPTIONS.forEach(o => sel.appendChild(new Option(o, o)));
      if (selected) {
        if (OPTIONS.includes(selected)) sel.value = selected;
        else sel.value = 'Otros';
      }
      return sel;
    }

    function nextIndex() {
      const rows = [...$tbody.querySelectorAll('tr.broma-row')];
      const idxs = rows.map(r => parseInt(r.dataset.index, 10)).filter(n => !Number.isNaN(n));
      return idxs.length ? Math.max(...idxs) + 1 : 0;
    }

    function syncHidden(tr) {
      const hidden = tr.querySelector('.broma-hidden-prueba');
      const sel    = tr.querySelector('.broma-select');
      const otros  = tr.querySelector('.broma-otros');

      if (!hidden || !sel) return;

      const setHidden = () => {
        if (sel.value === 'Otros') {
          hidden.value = (otros?.value || '').trim();
        } else {
          hidden.value = sel.value;
        }
      };

      sel.onchange = () => {
        if (sel.value === 'Otros') {
          if (otros) otros.classList.remove('hidden');
        } else {
          if (otros) { otros.classList.add('hidden'); otros.value = ''; }
        }
        setHidden();
      };
      if (otros) {
        otros.oninput = setHidden;
      }

      if (sel.value === 'Otros') {
        if (otros) otros.classList.remove('hidden');
      } else {
        if (otros) otros.classList.add('hidden');
      }
      setHidden();
    }

    function attachRow(tr) {
      let hidden = tr.querySelector('.broma-hidden-prueba');
      if (!hidden) {
        const td = tr.querySelector('td');
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.className = 'broma-hidden-prueba';
        hidden.name = `bromato[${tr.dataset.index || 0}][prueba]`;
        td.prepend(hidden);
      }

      let sel = tr.querySelector('select.broma-select');
      let wrap = tr.querySelector('.broma-select-wrap');
      if (!wrap) {
        const firstTd = tr.querySelector('td');
        let inner = firstTd?.querySelector('.flex.flex-col.gap-2');
        if (!inner) {
          inner = document.createElement('div');
          inner.className = 'flex flex-col gap-2';
          firstTd.prepend(inner);
        }
        wrap = document.createElement('div');
        wrap.className = 'broma-select-wrap';
        if (sel) {
          wrap.appendChild(sel);
        }
        inner.prepend(wrap);
      }
      if (!sel) {
        sel = createSelect((hidden.value || '').trim());
        wrap.appendChild(sel);
      }

      let otros = tr.querySelector('.broma-otros');
      if (!otros) {
        otros = document.createElement('input');
        otros.type = 'text';
        otros.className = 'broma-otros w-full rounded-md border border-gray-300 px-2 py-1 hidden';
        otros.placeholder = 'Especifica la prueba';
        wrap.after(otros);
      }
      const hv = (hidden.value || '').trim();
      if (hv && !OPTIONS.includes(hv)) {
        sel.value = 'Otros';
        otros.value = hv;
        otros.classList.remove('hidden');
      }

      syncHidden(tr);
    }

    function buildRow(idx, data = {}) {
      const prueba = (data.prueba ?? '').toString().trim();
      const enLista = OPTIONS.includes(prueba);
      const otroVal = enLista ? '' : (prueba || '');
      const tr = document.createElement('tr');
      tr.className = 'broma-row';
      tr.dataset.index = idx;

      tr.innerHTML = `
        <td class="px-2 py-2">
          <input type="hidden" name="bromato[${idx}][prueba]" class="broma-hidden-prueba" value="${escapeHtml(prueba)}">
          <div class="flex flex-col gap-2">
            <div class="broma-select-wrap"></div>
            <input type="text" class="broma-otros w-full rounded-md border border-gray-300 px-2 py-1 ${enLista ? 'hidden':''}"
                  placeholder="Especifica la prueba" value="${escapeHtml(otroVal)}">
          </div>
        </td>
        <td class="px-2 py-2">
          <input type="text" name="bromato[${idx}][especificacion]" class="w-full rounded-md border border-gray-300 px-2 py-1"
                value="${escapeHtml((data.especificacion ?? ''))}">
        </td>
        <td class="px-2 py-2">
          <input type="text" name="bromato[${idx}][resultado]" class="w-full rounded-md border border-gray-300 px-2 py-1"
                value="${escapeHtml((data.resultado ?? ''))}">
        </td>
        <td class="px-2 py-2 text-center">
          <button type="button" class="rm-broma text-red-600 hover:underline">Quitar</button>
        </td>
      `;

      const wrap = tr.querySelector('.broma-select-wrap');
      const sel = createSelect(prueba);
      wrap.appendChild(sel);
      attachRow(tr);

      return tr;
    }

    $addBtn.addEventListener('click', () => {
      const idx = nextIndex();
      const tr = buildRow(idx, { prueba:'', especificacion:'', resultado:'' });
      $tbody.appendChild(tr);
    });

    $tbody.addEventListener('click', (e) => {
      const btn = e.target.closest('.rm-broma');
      if (!btn) return;
      const tr = btn.closest('tr.broma-row');
      if (tr) tr.remove();
    });

    $tbody.querySelectorAll('tr.broma-row').forEach(attachRow);

  })();

  (function () {
    const OPTIONS = [
      "Cuenta Total Bacteria","Coliformes","Coliformes Totales",
      "Hongos","Levaduras","E.coli","Salmonella spp.","S.aureus","Otros"
    ];

    const $table = document.getElementById('micro-table');
    const $tbody = $table.querySelector('tbody');
    const $addBtn = document.getElementById('add-micro');

    function escapeHtml(str){return (str??'').toString().replace(/[&<>"']/g,s=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]))}
    function createSelect(selected){
      const sel=document.createElement('select');
      sel.className='micro-select w-full rounded-md border border-gray-300 px-2 py-1';
      sel.appendChild(new Option('Selecciona…',''));
      OPTIONS.forEach(o=>sel.appendChild(new Option(o,o)));
      if(selected){ sel.value = OPTIONS.includes(selected) ? selected : 'Otros'; }
      return sel;
    }
    function nextIndex(){
      const rows=[...$tbody.querySelectorAll('tr.micro-row')];
      const ids=rows.map(r=>parseInt(r.dataset.index,10)).filter(n=>!Number.isNaN(n));
      return ids.length ? Math.max(...ids)+1 : 0;
    }

    function syncHidden(tr){
      const hidden=tr.querySelector('.micro-hidden-prueba');
      const sel=tr.querySelector('.micro-select');
      const otros=tr.querySelector('.micro-otros');
      if(!hidden||!sel) return;

      const setHidden=()=>{ hidden.value = (sel.value==='Otros') ? (otros?.value||'').trim() : sel.value; };

      sel.onchange=()=>{
        if(sel.value==='Otros'){ otros?.classList.remove('hidden'); }
        else { if(otros){ otros.classList.add('hidden'); otros.value=''; } }
        setHidden();
      };
      if(otros){ otros.oninput=setHidden; }

      if(sel.value==='Otros'){ otros?.classList.remove('hidden'); } else { otros?.classList.add('hidden'); }
      setHidden();
    }

    function attachRow(tr){
      let hidden = tr.querySelector('.micro-hidden-prueba');
      if(!hidden){
        const td=tr.querySelector('td');
        hidden=document.createElement('input');
        hidden.type='hidden';
        hidden.className='micro-hidden-prueba';
        hidden.name=`micro[${tr.dataset.index||0}][prueba]`;
        td.prepend(hidden);
      }
      let sel = tr.querySelector('select.micro-select');
      let wrap = tr.querySelector('.micro-select-wrap');
      if(!wrap){
        const firstTd=tr.querySelector('td');
        let inner=firstTd?.querySelector('.flex.flex-col.gap-2');
        if(!inner){ inner=document.createElement('div'); inner.className='flex flex-col gap-2'; firstTd.prepend(inner); }
        wrap=document.createElement('div'); wrap.className='micro-select-wrap';
        if(sel){ wrap.appendChild(sel); }
        inner.prepend(wrap);
      }
      if(!sel){
        sel=createSelect((hidden.value||'').trim());
        wrap.appendChild(sel);
      }
      let otros=tr.querySelector('.micro-otros');
      if(!otros){
        otros=document.createElement('input');
        otros.type='text';
        otros.className='micro-otros w-full rounded-md border border-gray-300 px-2 py-1 hidden';
        otros.placeholder='Especifica la prueba';
        wrap.after(otros);
      }
      const hv=(hidden.value||'').trim();
      if(hv && !OPTIONS.includes(hv)){ sel.value='Otros'; otros.value=hv; otros.classList.remove('hidden'); }
      syncHidden(tr);
    }

    function buildRow(idx, data={}){
      const prueba=(data.prueba??'').toString().trim();
      const enLista=OPTIONS.includes(prueba);
      const otroVal=enLista?'':(prueba||'');
      const tr=document.createElement('tr');
      tr.className='micro-row';
      tr.dataset.index=idx;
      tr.innerHTML=`
        <td class="px-2 py-2">
          <input type="hidden" name="micro[${idx}][prueba]" class="micro-hidden-prueba" value="${escapeHtml(prueba)}">
          <div class="flex flex-col gap-2">
            <div class="micro-select-wrap"></div>
            <input type="text" class="micro-otros w-full rounded-md border border-gray-300 px-2 py-1 ${enLista?'hidden':''}"
                  placeholder="Especifica la prueba" value="${escapeHtml(otroVal)}">
          </div>
        </td>
        <td class="px-2 py-2">
          <input type="text" name="micro[${idx}][resultado]" class="w-full rounded-md border border-gray-300 px-2 py-1"
                value="${escapeHtml((data.resultado??''))}">
        </td>
        <td class="px-2 py-2">
          <input type="text" name="micro[${idx}][unidades]" class="w-full rounded-md border border-gray-300 px-2 py-1"
                value="${escapeHtml((data.unidades??''))}">
        </td>
        <td class="px-2 py-2 text-center">
          <button type="button" class="rm-micro text-red-600 hover:underline">Quitar</button>
        </td>
      `;
      const wrap=tr.querySelector('.micro-select-wrap');
      const sel=createSelect(prueba);
      wrap.appendChild(sel);
      attachRow(tr);
      return tr;
    }

    $addBtn.addEventListener('click', ()=>{
      const idx=nextIndex();
      const tr=buildRow(idx,{prueba:'',resultado:'',unidades:''});
      $tbody.appendChild(tr);
    });

    $tbody.addEventListener('click', (e)=>{
      const btn=e.target.closest('.rm-micro');
      if(!btn) return;
      const tr=btn.closest('tr.micro-row');
      if(tr) tr.remove();
    });

    $tbody.querySelectorAll('tr.micro-row').forEach(attachRow);
  })();

(function () {
  function syncFirmante(selectEl) {
    const form = selectEl.closest("form");
    if (!form) return;

    const nombre = form.querySelector(".js-firmante-nombre");
    const cedula = form.querySelector(".js-firmante-cedula");
    const puesto = form.querySelector(".js-firmante-puesto");

    if (!nombre || !cedula || !puesto) return;

    const opt = selectEl.options[selectEl.selectedIndex];

    if (!opt || !opt.value) {
      nombre.value = "";
      cedula.value = "";
      puesto.value = "Jefe de calidad";
      return;
    }

    nombre.value = (opt.value || "").trim();
    cedula.value = (opt.dataset.cedula || "").trim();
    puesto.value = (opt.dataset.puesto || "Jefe de calidad").trim();
  }

  document.addEventListener("change", function (e) {
    const selectEl = e.target.closest(".js-firmante-select");
    if (!selectEl) return;
    syncFirmante(selectEl);
  });

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".js-firmante-select").forEach(syncFirmante);
  });
})();

document.addEventListener("DOMContentLoaded", () => {
  const sel = document.getElementById("producto_select");
  const otro = document.getElementById("producto_otro");
  const hid = document.getElementById("producto_hidden");

  if (!sel || !otro || !hid) return;

  function sync() {
    if (sel.value === "__OTRO__") {
      otro.classList.remove("hidden");
      hid.value = (otro.value || "").trim();
    } else {
      otro.classList.add("hidden");
      otro.value = "";
      hid.value = sel.value;
    }
  }

  sel.addEventListener("change", sync);
  otro.addEventListener("input", sync);

  sync();
});

 function syncCliente() {
    const sel = document.getElementById('cliente_select');
    const otro = document.getElementById('cliente_otro');
    const hidden = document.getElementById('cliente_hidden');

    if (!sel || !otro || !hidden) return;

    if (sel.value === '__OTRO__') {
      otro.classList.remove('hidden');
      hidden.value = (otro.value || '').trim();
    } else {
      otro.classList.add('hidden');
      hidden.value = sel.value;
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('cliente_select');
    const otro = document.getElementById('cliente_otro');

    if (sel) sel.addEventListener('change', syncCliente);
    if (otro) otro.addEventListener('input', syncCliente);

    // Inicial
    syncCliente();
  });

</script>

</x-modal>
