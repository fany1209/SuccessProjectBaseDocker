<x-modal id="15">
  <form id="pro01-form" method="POST" action="{{ route('laboratory.pdf01pr') }}" target="_blank" class="space-y-6">
    @csrf

    {{-- =============== CABECERA DE LA ORDEN =============== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Cabecera de la orden</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Fecha</label>
          <input type="date" name="fecha" class="w-full border rounded px-2 py-1" value="{{ old('fecha') }}">
        </div>

        <div class="md:col-span-2">
          <label for="product_id" class="block text-sm font-semibold">Producto (BD)</label>
          <select name="product_id" id="product_id" class="w-full border rounded px-2 py-1" required>
            <option value="">— Selecciona —</option>
            @foreach(($products ?? []) as $p)
              <option value="{{ $p->product_id ?? $p->id }}"
                      data-name="{{ $p->name }}"
                      data-sku="{{ $p->sku ?? '' }}"
                      @selected( (int)old('product_id') === (int)($p->product_id ?? $p->id) )>
                {{ $p->name }}
              </option>
            @endforeach
          </select>
          <p class="text-xs text-gray-600 mt-1">Al seleccionar, se llena el SKU.</p>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Código de producto (SKU)</label>
          <input type="text" id="sku" name="sku" class="w-full border rounded px-2 py-1" value="{{ old('sku') }}" readonly>
        </div>
      </div>
    </div>

    {{-- =============== CANTIDADES =============== --}}
    <div class="border rounded" x-data>
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Cantidades</div>

      <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div class="md:col-span-1">
          <label class="block text-sm font-semibold">Tipo</label>
          <input type="text" name="pedido" class="w-full border rounded px-2 py-1" value="{{ old('pedido') }}">
        </div>

        <div class="md:col-span-1">
          <label class="block text-sm font-semibold">Cantidad</label>
          <input type="number" step="0.001" min="0" id="vol_fabricar" name="volumen_fabricar_l"
                 class="w-full border rounded px-2 py-1" value="{{ old('volumen_fabricar_l', 0) }}">
        </div>

        <div class="md:col-span-1">
          <label class="block text-sm font-semibold">Producir cantidad</label>
          <input type="number" step="0.001" min="0" id="vol_producir" name="volumen_producir_l"
                 class="w-full border rounded px-2 py-1" value="{{ old('volumen_producir_l') }}">
          <p class="text-xs text-gray-600 mt-1">Si lo dejas vacío, se usará la “Cantidad”.</p>
        </div>

        <div class="md:col-span-1">
          <label class="block text-sm font-semibold">Unidad</label>
          <input type="text" name="unidad_cantidades" class="w-full border rounded px-2 py-1"
                 list="unidades" value="{{ old('unidad_cantidades', 'L') }}" placeholder="g, kg, mL, L, u…">
        </div>
      </div>

      <datalist id="unidades">
        <option value="g"></option>
        <option value="kg"></option>
        <option value="mg"></option>
        <option value="mL"></option>
        <option value="L"></option>
        <option value="u"></option>
      </datalist>
    </div>

    {{-- =============== MATERIALES =============== --}}
    <div class="border rounded" x-data>
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Materiales</div>
      <div class="p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold">Lista de materiales</span>
          <button type="button" id="mat-add" class="px-2 py-1 border rounded text-sm">+ Material</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="mat-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:40%;">Material</th>
                <th class="border px-2 py-1" style="width:15%;">Cant. para 1 L</th>
                <th class="border px-2 py-1" style="width:10%;">Unidad</th>
                <th class="border px-2 py-1" style="width:15%;">Total a utilizar</th>
                <th class="border px-2 py-1" style="width:20%;">Lote</th>
                <th class="border px-2 py-1">—</th>
              </tr>
            </thead>
            <tbody id="mat-rows">
              @php $matOld = old('materiales', []); @endphp
              @foreach($matOld as $i => $r)
                <tr>
                  <td class="border">
                    <input name="materiales[{{ $i }}][nombre]" class="w-full px-2 py-1" value="{{ $r['nombre'] ?? '' }}" placeholder="Nombre del material">
                  </td>
                  <td class="border">
                    <input name="materiales[{{ $i }}][cant_por_1l]" class="w-full px-2 py-1 mat-1l" type="number" step="0.001" min="0" value="{{ $r['cant_por_1l'] ?? '' }}" placeholder="por 1 L">
                  </td>
                  <td class="border">
                    <input name="materiales[{{ $i }}][unidad]" class="w-full px-2 py-1" value="{{ $r['unidad'] ?? '' }}" placeholder="Unidad">
                  </td>
                  <td class="border">
                    <input name="materiales[{{ $i }}][total_utilizar]" class="w-full px-2 py-1 mat-total" type="number" step="0.001" min="0" value="{{ $r['total_utilizar'] ?? '' }}" placeholder="total">
                  </td>
                  <td class="border">
                    <input name="materiales[{{ $i }}][lote]" class="w-full px-2 py-1" value="{{ $r['lote'] ?? '' }}" placeholder="Lote">
                  </td>
                  <td class="border text-center">
                    <button type="button" class="mat-del px-2">×</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- =============== RECEPCIÓN DE MATERIA PRIMA =============== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Recepción de materia prima</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Nombre</label>
          <input type="text" name="recepcion_mp_nombre" class="w-full border rounded px-2 py-1" value="{{ old('recepcion_mp_nombre') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Fecha</label>
          <input type="date" name="recepcion_mp_fecha" class="w-full border rounded px-2 py-1" value="{{ old('recepcion_mp_fecha') }}">
        </div>
      </div>
    </div>

    {{-- =============== PROCEDIMIENTO DE FABRICACIÓN =============== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Procedimiento de fabricación</div>
      <div class="p-3">
        <label class="block text-sm font-semibold">Instrucciones adicionales</label>
        <textarea name="procedimiento_extra" rows="4" class="w-full border rounded px-2 py-1" placeholder="Puntos adicionales al procedimiento base">{{ old('procedimiento_extra') }}</textarea>
      </div>
    </div>

    {{-- =============== CONDICIONES DE CONTROL =============== --}}
    <div class="border rounded" x-data>
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Condiciones de control</div>
      <div class="p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold">Parámetros</span>
          <button type="button" id="ctrl-add" class="px-2 py-1 border rounded text-sm">+ Parámetro</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="ctrl-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:30%;">Parámetro</th>
                <th class="border px-2 py-1" style="width:15%;">Unidad</th>
                <th class="border px-2 py-1" style="width:15%;">Valor</th>
                <th class="border px-2 py-1" style="width:40%;">Observaciones</th>
                <th class="border px-2 py-1">—</th>
              </tr>
            </thead>
            <tbody id="ctrl-rows">
              @php
                $ctrlOld = old('controles', [
                  ['param'=>'Temperatura','unidad'=>'°C','valor'=>'','obs'=>''],
                  ['param'=>'Agitación','unidad'=>'rpm','valor'=>'','obs'=>''],
                ]);
              @endphp
              @foreach($ctrlOld as $i => $c)
                <tr>
                  <td class="border"><input name="controles[{{ $i }}][param]"  class="w-full px-2 py-1" value="{{ $c['param'] ?? '' }}"></td>
                  <td class="border"><input name="controles[{{ $i }}][unidad]" class="w-full px-2 py-1" value="{{ $c['unidad'] ?? '' }}"></td>
                  <td class="border"><input name="controles[{{ $i }}][valor]"  class="w-full px-2 py-1" value="{{ $c['valor'] ?? '' }}"></td>
                  <td class="border"><input name="controles[{{ $i }}][obs]"    class="w-full px-2 py-1" value="{{ $c['obs'] ?? '' }}"></td>
                  <td class="border text-center"><button type="button" class="ctrl-del px-2">×</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- =============== SALIDA Y VALIDACIONES =============== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Salida de producto y validaciones</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">N° de lote (salida)</label>
          <input type="text" name="lote_salida" class="w-full border rounded px-2 py-1" value="{{ old('lote_salida') }}">
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold">Validación área de Calidad (nombre)</label>
          <input type="text" name="val_calidad_nombre" class="w-full border rounded px-2 py-1" value="{{ old('val_calidad_nombre') }}">
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold">Recepción área de Almacén (nombre)</label>
          <input type="text" name="rec_almacen_nombre" class="w-full border rounded px-2 py-1" value="{{ old('rec_almacen_nombre') }}">
        </div>
      </div>
    </div>

    {{-- BOTONES --}}
    <div class="flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" form="pro01-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
    </div>
  </form>

  {{-- =============== JS DINÁMICO =============== --}}
  <script>
    (function(){
      const modal = document.getElementById('15');
      if (!modal) return;

      const sel  = modal.querySelector('#product_id');
      const skuI = modal.querySelector('#sku');

      function fillSku(){
        if (!sel || !skuI) return;
        const opt = sel.selectedOptions && sel.selectedOptions[0] ? sel.selectedOptions[0] : null;
        const sku = opt ? (opt.dataset.sku || '').trim() : '';
        skuI.value = sku;
      }

      sel?.addEventListener('change', fillSku);
      if (sel && sel.value) fillSku();
      setTimeout(()=>{ if (sel && sel.value) fillSku(); }, 200);
    })();

    (function(){
      const volFabEl  = document.getElementById('vol_fabricar');
      const volProdEl = document.getElementById('vol_producir');

      const matTbody  = document.getElementById('mat-rows');
      const matAddBtn = document.getElementById('mat-add');

      const ctrlTbody  = document.getElementById('ctrl-rows');
      const ctrlAddBtn = document.getElementById('ctrl-add');

      function parse(v){ const n = parseFloat(v); return isNaN(n) ? 0 : n; }

      function getVolumenProducir(){
        const vProd = parse(volProdEl?.value || '');
        if(vProd > 0) return vProd;
        return parse(volFabEl?.value || '');
      }

      function recalcMateriales(){
        const vol = getVolumenProducir();
        matTbody?.querySelectorAll('tr').forEach(tr=>{
          const per1L = tr.querySelector('.mat-1l');
          const total = tr.querySelector('.mat-total');
          if(per1L && total && per1L.value !== ''){
            const t = parse(per1L.value) * vol;
            total.value = (Math.round(t * 1000) / 1000).toString();
          }
        });
      }

      volFabEl?.addEventListener('input', recalcMateriales);
      volProdEl?.addEventListener('input', recalcMateriales);

      matTbody?.addEventListener('input', (e)=>{
        if(e.target.classList.contains('mat-1l')) recalcMateriales();
      });

      matTbody?.addEventListener('click', (e)=>{
        if(e.target.classList.contains('mat-del')){
          e.target.closest('tr')?.remove();
          recalcMateriales();
        }
      });

      matAddBtn?.addEventListener('click', ()=>{
        const idx = matTbody.children.length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="border"><input name="materiales[${idx}][nombre]" class="w-full px-2 py-1" placeholder="Nombre del material"></td>
          <td class="border"><input name="materiales[${idx}][cant_por_1l]" class="w-full px-2 py-1 mat-1l" type="number" step="0.001" min="0" placeholder="por 1 L"></td>
          <td class="border"><input name="materiales[${idx}][unidad]" class="w-full px-2 py-1" placeholder="Unidad"></td>
          <td class="border"><input name="materiales[${idx}][total_utilizar]" class="w-full px-2 py-1 mat-total" type="number" step="0.001" min="0" placeholder="total"></td>
          <td class="border"><input name="materiales[${idx}][lote]" class="w-full px-2 py-1" placeholder="Lote"></td>
          <td class="border text-center"><button type="button" class="mat-del px-2">×</button></td>
        `;
        matTbody.appendChild(tr);
        recalcMateriales();
      });

      ctrlTbody?.addEventListener('click', (e)=>{
        if(e.target.classList.contains('ctrl-del')){
          e.target.closest('tr')?.remove();
        }
      });

      ctrlAddBtn?.addEventListener('click', ()=>{
        const idx = ctrlTbody.children.length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="border"><input name="controles[${idx}][param]"  class="w-full px-2 py-1" placeholder="Parámetro"></td>
          <td class="border"><input name="controles[${idx}][unidad]" class="w-full px-2 py-1" placeholder="Unidad"></td>
          <td class="border"><input name="controles[${idx}][valor]"  class="w-full px-2 py-1" placeholder="Valor"></td>
          <td class="border"><input name="controles[${idx}][obs]"    class="w-full px-2 py-1" placeholder="Observaciones"></td>
          <td class="border text-center"><button type="button" class="ctrl-del px-2">×</button></td>
        `;
        ctrlTbody.appendChild(tr);
      });

      recalcMateriales();
    })();
  </script>
</x-modal>
