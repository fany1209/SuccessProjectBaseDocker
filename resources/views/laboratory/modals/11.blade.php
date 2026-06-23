<x-modal id="11">
  <form id="plan11-form" method="POST" action="{{ route('laboratory.pdf11') }}" target="_blank" class="space-y-6">
    @csrf

    {{-- =================== Encabezado (Semana / Revisión) =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Semana y revisión</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Semana (rango)</label>
          <input type="text" name="semana_rango" class="w-full border rounded px-2 py-1" placeholder="p. ej. 13–17 Ene 2025" value="{{ old('semana_rango') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Fecha de revisión</label>
          <input type="date" name="fecha_revision" class="w-full border rounded px-2 py-1" value="{{ old('fecha_revision') }}">
        </div>
      </div>
    </div>

    {{-- =================== Proyecto / Responsable =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Proyecto</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Nombre del proyecto</label>
          <input type="text" name="proyecto" class="w-full border rounded px-2 py-1" value="{{ old('proyecto') }}">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Responsable</label>
          <input type="text" name="responsable" class="w-full border rounded px-2 py-1" value="{{ old('responsable') }}">
        </div>
      </div>
    </div>

    {{-- =================== 1) Objetivos de la semana =================== --}}
    <div class="border rounded" id="obj-section">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">1) Objetivos de la semana</div>

      <div class="p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold">Lista de objetivos</span>
          <div class="flex items-center gap-3">
            <div class="text-sm">
              Total horas: <b><span id="obj-total-horas">0</span></b>
            </div>
            <button type="button" id="obj-add" class="px-2 py-1 border rounded text-sm">+ Objetivo</button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="obj-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:8%;">#</th>
                <th class="border px-2 py-1" style="width:22%;">Título</th>
                <th class="border px-2 py-1" style="width:50%;">Descripción</th>
                <th class="border px-2 py-1" style="width:12%;">Horas</th>
                <th class="border px-2 py-1" style="width:8%;">—</th>
              </tr>
            </thead>
            <tbody id="obj-rows">
              @php
                $objs = old('objetivos', [
                  ['n' => 1, 'titulo' => 'Objetivo 1', 'descripcion' => '', 'horas' => ''],
                ]);
              @endphp
              @foreach($objs as $i => $o)
              <tr>
                <td class="border px-2 py-1">
                  <input name="objetivos[{{ $i }}][n]" class="w-full px-2 py-1 text-center obj-n" type="number" min="1" value="{{ $o['n'] ?? $i+1 }}">
                </td>
                <td class="border px-2 py-1">
                  <input name="objetivos[{{ $i }}][titulo]" class="w-full px-2 py-1" value="{{ $o['titulo'] ?? '' }}" placeholder="Título breve">
                </td>
                <td class="border px-2 py-1">
                  <input name="objetivos[{{ $i }}][descripcion]" class="w-full px-2 py-1" value="{{ $o['descripcion'] ?? '' }}" placeholder="Descripción del objetivo">
                </td>
                <td class="border px-2 py-1">
                  <input name="objetivos[{{ $i }}][horas]" class="w-full px-2 py-1 obj-horas" type="number" min="0" step="1" value="{{ $o['horas'] ?? '' }}">
                </td>
                <td class="border px-2 py-1 text-center">
                  <button type="button" class="obj-del px-2">×</button>
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="font-semibold">
                <td class="border px-2 py-1 text-right" colspan="3">Total</td>
                <td class="border px-2 py-1 text-center"><span id="obj-total-horas-foot">0</span></td>
                <td class="border px-2 py-1"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    {{-- =================== 2) Resultados alcanzados =================== --}}
    <div class="border rounded" id="res-section">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">2) Resultados alcanzados</div>

      <div class="p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-700">
            Se generan filas según los objetivos (1:1). Puedes editar texto y check de cumplimiento.
          </span>
          <button type="button" id="res-sync" class="px-2 py-1 border rounded text-sm">↻ Sincronizar con objetivos</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="res-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:10%;"># Obj</th>
                <th class="border px-2 py-1" style="width:70%;">Resultados</th>
                <th class="border px-2 py-1" style="width:20%;">Cumple</th>
              </tr>
            </thead>
            <tbody id="res-rows">
              @php $res = old('resultados', []); @endphp
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- =================== 3) Análisis de hallazgos =================== --}}
    <div class="border rounded" id="hall-section">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">3) Análisis de hallazgos</div>

      <div class="p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold">Hallazgos</span>
          <button type="button" id="hall-add" class="px-2 py-1 border rounded text-sm">+ Fila</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="hall-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:40%;">Hallazgo</th>
                <th class="border px-2 py-1" style="width:30%;">Causa</th>
                <th class="border px-2 py-1" style="width:30%;">Propuesta</th>
              </tr>
            </thead>
            <tbody id="hall-rows">
              @php
                $hall = old('hallazgos', [
                  ['hallazgo'=>'', 'causa'=>'', 'propuesta'=>''],
                ]);
              @endphp
              @foreach($hall as $i => $h)
              <tr>
                <td class="border px-2 py-1">
                  <input name="hallazgos[{{ $i }}][hallazgo]" class="w-full px-2 py-1" value="{{ $h['hallazgo'] ?? '' }}">
                </td>
                <td class="border px-2 py-1">
                  <input name="hallazgos[{{ $i }}][causa]" class="w-full px-2 py-1" value="{{ $h['causa'] ?? '' }}">
                </td>
                <td class="border px-2 py-1">
                  <input name="hallazgos[{{ $i }}][propuesta]" class="w-full px-2 py-1" value="{{ $h['propuesta'] ?? '' }}">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- =================== 4) Plan de acción próxima semana =================== --}}
    <div class="border rounded" id="plan-section">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">4) Plan de acción para la próxima semana</div>

      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Rango de la próxima semana</label>
          <input type="text" name="proxima_semana_rango" class="w-full border rounded px-2 py-1" placeholder="p. ej. 20–24 Ene 2025" value="{{ old('proxima_semana_rango') }}">
        </div>
      </div>

      <div class="p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold">Plan / Acciones</span>
          <button type="button" id="plan-add" class="px-2 py-1 border rounded text-sm">+ Fila</button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="plan-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:50%;">Plan</th>
                <th class="border px-2 py-1" style="width:50%;">Acciones</th>
              </tr>
            </thead>
            <tbody id="plan-rows">
              @php
                $plan = old('plan_proxima', [
                  ['plan'=>'', 'acciones'=>''],
                ]);
              @endphp
              @foreach($plan as $i => $p)
              <tr>
                <td class="border px-2 py-1">
                  <input name="plan_proxima[{{ $i }}][plan]" class="w-full px-2 py-1" value="{{ $p['plan'] ?? '' }}">
                </td>
                <td class="border px-2 py-1">
                  <input name="plan_proxima[{{ $i }}][acciones]" class="w-full px-2 py-1" value="{{ $p['acciones'] ?? '' }}">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" form="plan11-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
    </div>
  </form>

  {{-- =================== JS =================== --}}
  <script>
  (function(){
    // ===== Objetivos
    const objTbody   = document.getElementById('obj-rows');
    const objAddBtn  = document.getElementById('obj-add');
    const objTotalEl = document.getElementById('obj-total-horas');
    const objTotalFt = document.getElementById('obj-total-horas-foot');

    function parseNum(v){ const n = parseFloat(v); return isNaN(n) ? 0 : n; }

    function recalcHoras(){
      let sum = 0;
      objTbody.querySelectorAll('.obj-horas').forEach(i => sum += parseNum(i.value));
      objTotalEl.textContent = sum;
      objTotalFt.textContent = sum;
    }

    function addObjRow(data = {}){
      const idx = objTbody.children.length;
      const n   = data.n ?? (idx + 1);
      const titulo = data.titulo ?? '';
      const desc   = data.descripcion ?? '';
      const horas  = data.horas ?? '';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="border px-2 py-1">
          <input name="objetivos[${idx}][n]" class="w-full px-2 py-1 text-center obj-n" type="number" min="1" value="${n}">
        </td>
        <td class="border px-2 py-1">
          <input name="objetivos[${idx}][titulo]" class="w-full px-2 py-1" value="${titulo}">
        </td>
        <td class="border px-2 py-1">
          <input name="objetivos[${idx}][descripcion]" class="w-full px-2 py-1" value="${desc}">
        </td>
        <td class="border px-2 py-1">
          <input name="objetivos[${idx}][horas]" class="w-full px-2 py-1 obj-horas" type="number" min="0" step="1" value="${horas}">
        </td>
        <td class="border px-2 py-1 text-center">
          <button type="button" class="obj-del px-2">×</button>
        </td>
      `;
      objTbody.appendChild(tr);
      recalcHoras();
      syncResultados(false); 
    }

    objAddBtn?.addEventListener('click', ()=> addObjRow());

    objTbody?.addEventListener('input', (e)=>{
      if(e.target.classList.contains('obj-horas')) recalcHoras();
    });

    objTbody?.addEventListener('click', (e)=>{
      if(e.target.classList.contains('obj-del')){
        e.target.closest('tr')?.remove();
        recalcHoras();
        syncResultados(false);
      }
    });

    recalcHoras();

    const resTbody = document.getElementById('res-rows');
    const resSync  = document.getElementById('res-sync');

    function currentResultadosMap(){
      const map = {};
      resTbody.querySelectorAll('tr').forEach(tr=>{
        const n  = tr.querySelector('.res-n')?.value || '';
        const tx = tr.querySelector('.res-texto')?.value || '';
        const ck = tr.querySelector('.res-cumple')?.checked || false;
        if(n) map[n] = { texto: tx, cumple: ck };
      });
      return map;
    }

    function syncResultados(overwriteText){
      const prev = currentResultadosMap();
      resTbody.innerHTML = '';
      const rows = Array.from(objTbody.querySelectorAll('tr'));
      rows.forEach((tr, i)=>{
        const n = tr.querySelector('.obj-n')?.value || (i+1);
        const prevRow = prev[n] || {};
        const texto = overwriteText ? '' : (prevRow.texto || '');
        const cumple = overwriteText ? false : !!prevRow.cumple;

        const rtr = document.createElement('tr');
        rtr.innerHTML = `
          <td class="border px-2 py-1">
            <input name="resultados[${i}][n]" class="w-full px-2 py-1 text-center res-n" value="${n}">
          </td>
          <td class="border px-2 py-1">
            <textarea name="resultados[${i}][texto]" class="w-full px-2 py-1 res-texto" rows="2" placeholder="Resultados del objetivo ${n}">${texto}</textarea>
          </td>
          <td class="border px-2 py-1 text-center">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="resultados[${i}][cumple]" class="res-cumple" ${cumple ? 'checked' : ''}>
              <span>Cumple</span>
            </label>
          </td>
        `;
        resTbody.appendChild(rtr);
      });
    }

    resSync?.addEventListener('click', ()=> syncResultados(false));
    if(resTbody.children.length === 0){ syncResultados(false); }

    const hallTbody = document.getElementById('hall-rows');
    const hallAdd   = document.getElementById('hall-add');

    hallAdd?.addEventListener('click', ()=>{
      const idx = hallTbody.children.length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="border px-2 py-1"><input name="hallazgos[${idx}][hallazgo]" class="w-full px-2 py-1" placeholder="Hallazgo"></td>
        <td class="border px-2 py-1"><input name="hallazgos[${idx}][causa]" class="w-full px-2 py-1" placeholder="Causa"></td>
        <td class="border px-2 py-1"><input name="hallazgos[${idx}][propuesta]" class="w-full px-2 py-1" placeholder="Propuesta"></td>
      `;
      hallTbody.appendChild(tr);
    });

    const planTbody = document.getElementById('plan-rows');
    const planAdd   = document.getElementById('plan-add');

    planAdd?.addEventListener('click', ()=>{
      const idx = planTbody.children.length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="border px-2 py-1"><input name="plan_proxima[${idx}][plan]" class="w-full px-2 py-1" placeholder="Plan"></td>
        <td class="border px-2 py-1"><input name="plan_proxima[${idx}][acciones]" class="w-full px-2 py-1" placeholder="Acciones"></td>
      `;
      planTbody.appendChild(tr);
    });
  })();
  </script>
</x-modal>
