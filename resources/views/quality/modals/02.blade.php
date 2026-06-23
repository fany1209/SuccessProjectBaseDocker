<x-modal id="FT">
  <form id="inspection-form" method="POST" action="{{ route('quality.pdf2') }}" enctype="multipart/form-data" autocomplete="off">
    @csrf

    {{-- ================== FICHA TÉCNICA CARACTERÍSTICAS ================== --}}
    <div class="w-full mt-8" id="pdf2-caracteristicas">
      <h3 class="text-center font-semibold mb-3">Ficha técnica</h3>

      <div class="mt-4">
        <label for="product_id" class="block text-sm font-medium mb-1">Producto</label>
        <select name="product_id" id="product_id" required
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">— Selecciona un producto —</option>
          @foreach($products as $p)
            <option value="{{ $p->product_id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <label class="block text-sm font-medium mb-1">Descripción breve</label>
          <textarea name="product_desc" rows="3"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Resumen corto del producto..."></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Imagen del producto (opcional)</label>
          <input type="file" name="product_image" accept="image/*"
                 class="block w-full text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
          <p class="text-xs text-gray-500 mt-1">Se incrusta en el PDF; formatos: JPG/PNG.</p>
        </div>
      </div>

     @php
  $groups = [
    ['id'=>'car-org',  'label'=>'CARACTERÍSTICAS ORGANOLEPTICAS', 'name'=>'car_org'],
    ['id'=>'car-fis',  'label'=>'CARACTERÍSTICAS FISICOQUÍMICAS', 'name'=>'car_fis'],
    ['id'=>'macro',    'label'=>'MACROELEMENTOS (mg/100 g)',      'name'=>'macro'],
    ['id'=>'micro',    'label'=>'MICROELEMENTOS (mg/100 g)',      'name'=>'micro'],
    ['id'=>'microbio', 'label'=>'MICROBIOLOGÍA',                  'name'=>'microbio'],
  ];
@endphp

@foreach ($groups as $g)
  <div class="mt-6">
    <div class="flex items-center justify-between mb-2">
      <h4 class="text-sm font-semibold">{{ $g['label'] }}</h4>
      <button type="button"
              class="btn-add-row-01 inline-flex items-center rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50"
              data-target="{{ $g['id'] }}">
        + Agregar fila
      </button>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="min-w-full text-sm table-fixed">
        <colgroup>
          <col style="width:40%">
          <col style="width:52%">
          <col style="width:8%">
        </colgroup>
        <thead class="bg-gray-50">
          <tr class="text-left">
            <th class="px-3 py-2">Característica</th>
            <th class="px-3 py-2">Valor</th>
            <th class="px-3 py-2 text-center">Acción</th>
          </tr>
        </thead>

        <tbody id="{{ $g['id'] }}-rows" data-name="{{ $g['name'] }}">
          <tr class="dyn-row">
            <td class="px-3 py-2">
              <input type="text" name="{{ $g['name'] }}[0][k]" placeholder="Ej. Humedad / Textura / Calcio"
                     class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </td>
            <td class="px-3 py-2">
              <input type="text" name="{{ $g['name'] }}[0][v]" placeholder="Ej. ≤ 8 % / Fina homogénea / 35"
                     class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </td>
            <td class="px-3 py-2 text-center whitespace-nowrap">
              <button type="button" class="btn-remove-row-01 inline-block text-red-600 hover:underline">Quitar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

      @if($g['id'] === 'microbio')
        <div class="mt-4">
          <h4 class="text-sm font-semibold mb-2">Propiedades del producto</h4>
          <div class="overflow-x-auto rounded border border-gray-200">
            <table class="min-w-full text-sm border-collapse table-fixed">
              <colgroup>
                <col style="width:38%">
                <col style="width:62%">
              </colgroup>
              <tbody>
                <tr>
                  <td class="px-3 py-2 font-semibold border border-gray-300" style="background-color:#8ED14F;">Tipo</td>
                  <td class="px-3 py-2 border border-gray-300">
                    <input type="text" name="prop[tipo]" placeholder="Hidroalcohólico"
                          class="w-full rounded-md border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                  </td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold border border-gray-300" style="background-color:#8ED14F;">Parte extraída</td>
                  <td class="px-3 py-2 border border-gray-300">
                    <input type="text" name="prop[parte_extraida]" placeholder="Botón floral"
                          class="w-full rounded-md border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                  </td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold border border-gray-300" style="background-color:#8ED14F;">Activo</td>
                  <td class="px-3 py-2 border border-gray-300">
                    <input type="text" name="prop[activo]" placeholder="Eugenol"
                          class="w-full rounded-md border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      @endif
    @endforeach
  </div>

    {{-- ================== CAMPOS ADICIONALES ================== --}}
    <div class="w-full mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Instrucciones técnicas</label>
        <textarea name="inst_tecnicas" rows="4"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Especifica procedimientos, condiciones, consideraciones..."></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Almacenamiento</label>
        <textarea name="almacenamiento" rows="4"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Temperatura, humedad, envase, condiciones..."></textarea>
      </div>

      {{-- ========== PRESENTACIÓN ========== --}}
      <div>
        <label class="block text-sm font-medium mb-1">Presentación</label>
        <div class="flex items-center gap-2 presentacion-picker" data-idx="ft">
          <div id="presentacion-thumbs-ft" class="flex gap-1 flex-wrap"></div>
          <input type="text"
                 name="presentacion"
                 id="presentacion-input-ft"
                 class="w-full rounded-md border border-gray-300 px-3 py-2 bg-gray-50 cursor-default"
                 placeholder="Selecciona una o varias presentaciones"
                 readonly>
          <input type="hidden" name="presentacion_img" id="presentacion-img-ft">
          <button type="button"
                  class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700"
                  data-open-gallery="ft">Elegir</button>
          <button type="button"
                  class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300"
                  data-clear="ft">Quitar</button>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Vida de anaquel</label>
        <textarea name="vida_anaquel" rows="3"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Ej. 12 meses a 20–25°C, condiciones..."></textarea>
      </div>
    </div>

    <div class="w-full mt-4">
      <label class="block text-sm font-medium mb-1">Instrucciones de uso y aplicaciones</label>
      <textarea name="uso_aplicaciones" rows="5"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Dosificación, modo de uso, aplicaciones sugeridas..."></textarea>
    </div>

    {{-- ====== INFORMACIÓN NUTRICIONAL ====== --}}
    <div class="mt-4">
      <label class="block text-sm font-medium mb-1">Información nutricional (imagen)</label>
      <input type="file"
            name="nutricional_img"
            id="nutricional_img"
            accept="image/*"
            class="block w-full text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
      <p class="text-xs text-gray-500 mt-1">Se incrustará en el PDF. Formatos: JPG/PNG.</p>

      <img id="nutricional_preview"
          alt="Vista previa información nutricional"
          class="mt-2 rounded border border-gray-200 hidden"
          style="max-width: 320px; height: auto;">
    </div>

    <div class="mt-6 flex items-center justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" id="submit-ficha" class="bg-green-600 hover:bg-green-700 text-white">Guardar PDF</x-button>
    </div>
  </form>
</x-modal>

{{-- ====================== MODAL presentacion ====================== --}}
  <div id="gallery-modal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50" data-close-gallery></div>
    <div class="relative mx-auto mt-10 w-[min(1000px,92vw)] bg-white rounded-2xl p-4 shadow-xl">
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-semibold">Selecciona una o varias presentaciones</h3>
        <button type="button" data-close-gallery class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">Cerrar</button>
      </div>
      <div id="gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[60vh] overflow-auto"></div>
      <div class="text-right mt-4">
        <button type="button" id="gallery-accept"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Aceptar selección</button>
      </div>
    </div>
  </div>

@push('js')
<script>
  const PRESENTACIONES = [
    { label: '2 L', file: '2litros.png' },
    { label: '5 L', file: '5litros.png' },
    { label: '20 L', file: '20litros.png' },
    { label: '30 L', file: '30litros.png' },
    { label: '40 L', file: '40litros.png' },
    { label: '250 L', file: '250litros.png' },
    { label: '1 L', file: 'litro.png' },
    { label: 'Granel', file: 'granel.png' },
    { label: '20 Kg', file: '20kilos.png' },
    { label: '25 Kg', file: '25kilos.png' },
    { label: '1000 Kg', file: 'milkilos.png' },
    { label: '1000 L', file: 'millitros.png' },
  ];
  const BASE = "{{ asset('images/quality/presentacion') }}";

  (function(){
    const modal  = document.getElementById('gallery-modal');
    const grid   = document.getElementById('gallery-grid');
    const accept = document.getElementById('gallery-accept');
    let currentIdx = null;
    let selected = new Set();

    function renderGrid(){
      if (grid.children.length) return;
      PRESENTACIONES.forEach((it, i)=>{
        const btn = document.createElement('button');
        btn.type='button';
        btn.className='border rounded-xl p-2 hover:shadow focus:outline-none';
        btn.setAttribute('data-pick', i);
        btn.innerHTML = `
          <img src="${BASE}/${it.file}" alt="${it.label}"
               class="w-full h-28 object-contain bg-white rounded-lg border border-gray-200"/>
          <div class="mt-2 text-sm text-center">${it.label}</div>`;
        grid.appendChild(btn);
      });
    }

    function openModal(idx){
      currentIdx = idx;
      renderGrid();
      selected.clear();
      modal.classList.remove('hidden');
      grid.querySelectorAll('[data-pick]').forEach(b => b.classList.remove('ring-4','ring-green-500'));
    }
    function closeModal(){
      modal.classList.add('hidden');
      currentIdx = null;
    }

    grid.addEventListener('click', e=>{
      const pick = e.target.closest('[data-pick]');
      if(!pick) return;
      const i = +pick.getAttribute('data-pick');
      if(selected.has(i)){
        selected.delete(i);
        pick.classList.remove('ring-4','ring-green-500');
      } else {
        selected.add(i);
        pick.classList.add('ring-4','ring-green-500');
      }
    });

    accept.addEventListener('click', ()=>{
      if(currentIdx===null) return;
      const selArr = Array.from(selected).map(i=>PRESENTACIONES[i]);
      const labels = selArr.map(s=>s.label).join(', ');
      const files  = selArr.map(s=>s.file);

      const input = document.getElementById(`presentacion-input-${currentIdx}`);
      const hidden= document.getElementById(`presentacion-img-${currentIdx}`);
      const thumbs= document.getElementById(`presentacion-thumbs-${currentIdx}`);
      if(input)  input.value = labels;
      if(hidden) hidden.value = JSON.stringify(files);

      if(thumbs){
        thumbs.innerHTML = '';
        selArr.forEach(s=>{
          const img=document.createElement('img');
          img.src=`${BASE}/${s.file}`;
          img.alt=s.label;
          img.className='w-10 h-10 object-contain border rounded';
          thumbs.appendChild(img);
        });
      }
      closeModal();
    });

    document.addEventListener('click', e=>{
      const opener = e.target.closest('[data-open-gallery]');
      if(opener){ openModal(opener.getAttribute('data-open-gallery')); return; }
      if(e.target.matches('[data-close-gallery]')){ closeModal(); return; }
      const clearer = e.target.closest('[data-clear]');
      if(clearer){
        const idx = clearer.getAttribute('data-clear');
        document.getElementById(`presentacion-input-${idx}`).value='';
        document.getElementById(`presentacion-img-${idx}`).value='';
        document.getElementById(`presentacion-thumbs-${idx}`).innerHTML='';
      }
    });
  })();
  (function () {
    const input = document.getElementById('nutricional_img');
    const prev  = document.getElementById('nutricional_preview');

    if (input) {
      input.addEventListener('change', () => {
        const f = input.files && input.files[0];
        if (!f) { if(prev){ prev.src=''; prev.classList.add('hidden'); } return; }
        const reader = new FileReader();
        reader.onload = e => {
          if (prev) { prev.src = e.target.result; prev.classList.remove('hidden'); }
        };
        reader.readAsDataURL(f);
      });
    }
  })();

(function () {
  function addRowTo(tbody) {
    const groupName = tbody.getAttribute('data-name'); 
    if (!groupName) return;

    const currentRows = tbody.querySelectorAll('tr.dyn-row');
    const nextIndex = currentRows.length; 

    const tr = document.createElement('tr');
    tr.className = 'dyn-row';
    tr.innerHTML = `
      <td class="px-3 py-2">
        <input type="text"
               name="${groupName}[${nextIndex}][k]"
               placeholder="Ej. Humedad / Textura / Calcio"
               class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
      </td>
      <td class="px-3 py-2">
        <input type="text"
               name="${groupName}[${nextIndex}][v]"
               placeholder="Ej. ≤ 8 % / Fina homogénea / 35"
               class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
      </td>
      <td class="px-3 py-2 text-center whitespace-nowrap">
        <button type="button" class="btn-remove-row-01 inline-block text-red-600 hover:underline">Quitar</button>
      </td>
    `;
    tbody.appendChild(tr);
  }

  function reindex(tbody) {
    const groupName = tbody.getAttribute('data-name');
    const rows = tbody.querySelectorAll('tr.dyn-row');

    rows.forEach((tr, idx) => {
      const k = tr.querySelector('input[name*="[k]"]');
      const v = tr.querySelector('input[name*="[v]"]');
      if (k) k.name = `${groupName}[${idx}][k]`;
      if (v) v.name = `${groupName}[${idx}][v]`;
    });
  }

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-add-row-01');
    if (!btn) return;

    const target = btn.getAttribute('data-target'); 
    const tbody = document.getElementById(`${target}-rows`);
    if (!tbody) return;

    addRowTo(tbody);
  });

  document.addEventListener('click', function (e) {
    const rm = e.target.closest('.btn-remove-row-01');
    if (!rm) return;

    const tr = rm.closest('tr.dyn-row');
    const tbody = rm.closest('tbody');
    if (!tr || !tbody) return;

    const rows = tbody.querySelectorAll('tr.dyn-row');
    if (rows.length <= 1) {
      const inputs = tr.querySelectorAll('input, textarea');
      inputs.forEach(i => i.value = '');
      return;
    }

    tr.remove();
    reindex(tbody);
  });
})();
</script>

@endpush
