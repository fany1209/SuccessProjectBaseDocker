<x-modal id="05">
  <form id="analisis-interno-form" method="POST" action="{{ route('laboratory.pdf5') }}" target="_blank" enctype="multipart/form-data" class="space-y-5">
    @csrf

  <div class="border rounded p-3 bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
              <label class="block text-sm font-semibold">Folio muestra *</label>
              <input 
                  type="text" 
                  name="folio_muestra" 
                  id="folio_muestra" 
                  class="w-full border rounded px-2 py-1" 
                  placeholder="Escriba el folio manualmente"
                  value="{{ old('folio_muestra', $model->folio_muestra ?? '') }}" 
                  required
              >
          </div>
      </div>
  </div>

    {{-- =================== FECHA DE MUESTREO =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">FECHA DE MUESTREO</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="block text-sm font-semibold">Fecha de muestreo</label>
          <input type="date" name="fecha_muestreo" class="w-full border rounded px-2 py-1" value="{{ old('fecha_muestreo', $model->fecha_muestreo ?? '') }}">
        </div>
      </div>
    </div>

    {{-- =================== DATOS GENERALES =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DATOS GENERALES</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label for="proveedor" class="block text-sm font-semibold">Proveedor</label>
          <select name="proveedor" id="proveedor" class="w-full border rounded px-2 py-1" required>
            <option value="">— Selecciona un proveedor —</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->name }}"
                      data-id="{{ $s->supplier_id }}"
                      {{ old('proveedor', $model->proveedor ?? '') === $s->name ? 'selected' : '' }}>
                {{ $s->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold">Lote</label>
          <input type="text" name="lote" class="w-full border rounded px-2 py-1" value="{{ old('lote', $model->lote ?? '') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Vida de anaquel</label>
          <input type="text" name="vida_anaquel" class="w-full border rounded px-2 py-1" value="{{ old('vida_anaquel', $model->vida_anaquel ?? '') }}">
        </div>
        <div>
          <label class="block text-sm font-semibold">Tipo de inspección</label>
          <input type="text" name="tipo_inspeccion" class="w-full border rounded px-2 py-1" value="{{ old('tipo_inspeccion', $model->tipo_inspeccion ?? '') }}">
        </div>
      </div>
    </div>

    {{-- =================== TIPO DE MUESTRA / Observaciones =================== --}}
   <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">TIPO DE MUESTRA</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="md:col-span-1">
          <label class="block text-sm font-semibold">Tipo de muestra</label>
          <input type="text" name="tipo_muestra" class="w-full border rounded px-2 py-1" value="{{ old('tipo_muestra', $model->tipo_muestra ?? '') }}">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Observaciones específicas</label>
          <input type="text" name="obs_tipo_muestra" class="w-full border rounded px-2 py-1" value="{{ old('obs_tipo_muestra', $model->obs_tipo_muestra ?? '') }}">
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold">Observaciones generales de la muestra</label>
          <textarea name="observaciones_generales_muestra" rows="2" class="w-full border rounded px-2 py-1" placeholder="Detalles específicos del estado de la muestra...">{{ old('observaciones_generales_muestra', $model->observaciones_generales_muestra ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== PRESENCIA DE MATERIA EXTRAÑA =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">PRESENCIA DE MATERIA EXTRAÑA</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="block text-sm font-semibold">Elija un elemento</label>
          @php $me = old('materia_extrana', $model->materia_extrana ?? 'ausente'); @endphp
          <div class="flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2"><input type="radio" name="materia_extrana" value="presente" {{ $me==='presente'?'checked':'' }}> Presente</label>
            <label class="inline-flex items-center gap-2"><input type="radio" name="materia_extrana" value="ausente"  {{ $me==='ausente'?'checked':'' }}> Ausente</label>
            <label class="inline-flex items-center gap-2"><input id="me_otro_radio" type="radio" name="materia_extrana" value="otro" {{ $me==='otro'?'checked':'' }}> Otro</label>
          </div>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Especifique (si “Otro”)</label>
          <input id="me_otro_txt" type="text" name="materia_extrana_otro" class="w-full border rounded px-2 py-1" value="{{ old('materia_extrana_otro', $model->materia_extrana_otro ?? '') }}">
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-semibold">Observaciones</label>
          <textarea name="obs_materia_extrana" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_materia_extrana', $model->obs_materia_extrana ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== % HUMEDAD =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">% HUMEDAD</div>
      <div class="p-3 grid grid-cols-1 gap-3">
        <div>
          <label class="block text-sm font-semibold">Método</label>
          <textarea name="metodo_humedad" rows="2" class="w-full border rounded px-2 py-1">{{ old('metodo_humedad', $model->metodo_humedad ?? 'Por triplicado, colocando el producto en termobalanza MB23 OHAUS a peso constante.') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold">Observaciones</label>
          <textarea name="obs_humedad" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_humedad', $model->obs_humedad ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== % PROTEÍNA =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">% PROTEÍNA</div>
      <div class="p-3 grid grid-cols-1 gap-3">
        <div>
          <label class="block text-sm font-semibold">Método</label>
          <textarea name="metodo_proteina" rows="2" class="w-full border rounded px-2 py-1">{{ old('metodo_proteina', $model->metodo_proteina ?? 'Se realiza determinación con metodología Kjeldahl en muestreo por duplicado de una muestra representativa de la carga (ver apartado anexos).') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold">Observaciones</label>
          <textarea name="obs_proteina" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_proteina', $model->obs_proteina ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== GRANULOMETRÍA =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">GRANULOMETRÍA</div>
      <div class="p-3 grid grid-cols-1 gap-3">
        @php
          $gOld = (array) old('granulometria', $model->granulometria ?? []);
          $is = fn($v)=> in_array($v, $gOld) ? 'checked' : '';
        @endphp
        <div class="flex flex-wrap gap-4 text-sm">
          @foreach(['#10','#24','#50','#65','#85','#100','#120','#150','#200'] as $m)
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="granulometria[]" value="{{ $m }}" {{ $is($m) }}> {{ $m }}
            </label>
          @endforeach
          <label class="inline-flex items-center gap-2">
            <input id="gran_otro_chk" type="checkbox" name="gran_otro_check" value="1" {{ old('gran_otro', $model->gran_otro ?? '') ? 'checked' : '' }}>
            Otro
          </label>
          <input id="gran_otro_txt" type="text" name="gran_otro" class="border rounded px-2 py-1" placeholder="Especifique" value="{{ old('gran_otro', $model->gran_otro ?? '') }}" style="min-width:220px;">
        </div>
        <div>
          <label class="block text-sm font-semibold">Observaciones</label>
          <textarea name="obs_granulometria" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_granulometria', $model->obs_granulometria ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== DETERMINACIÓN DE PATÓGENOS =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">DETERMINACIÓN DE PATÓGENOS</div>
      <div class="p-3 grid grid-cols-1 gap-3">
        <div>
          <label class="block text-sm font-semibold">Determinación de patógenos</label>
          <textarea name="determinacion_patogenos" rows="3" class="w-full border rounded px-2 py-1">{{ old('determinacion_patogenos', $model->determinacion_patogenos ?? '') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold">Observaciones</label>
          <textarea name="obs_determinacion_patogenos" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_determinacion_patogenos', $model->obs_determinacion_patogenos ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- =================== SENSORIAL =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">SENSORIAL</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Color — Cumple</label>
          @php $cc = old('cumple_color', $model->cumple_color ?? 'si'); @endphp
          <div class="flex gap-6 text-sm">
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="cumple_color" value="si" {{ $cc==='si'?'checked':'' }}> Sí
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="cumple_color" value="no" {{ $cc==='no'?'checked':'' }}> No
            </label>
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold">Observaciones de color</label>
          <textarea name="obs_color" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_color', $model->obs_color ?? '') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold">Olor (texto corto)</label>
          <input type="text" name="olor_texto" class="w-full border rounded px-2 py-1" value="{{ old('olor_texto', $model->olor_texto ?? '') }}">
          <label class="block text-sm font-semibold mt-2">Observaciones de olor</label>
          <textarea name="obs_olor" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_olor', $model->obs_olor ?? '') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold">Sabor (texto corto)</label>
          <input type="text" name="sabor_texto" class="w-full border rounded px-2 py-1" value="{{ old('sabor_texto', $model->sabor_texto ?? '') }}">
          <label class="block text-sm font-semibold mt-2">Observaciones de sabor</label>
          <textarea name="obs_sabor" rows="2" class="w-full border rounded px-2 py-1">{{ old('obs_sabor', $model->obs_sabor ?? '') }}</textarea>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Otro</label>
          <div class="flex gap-3 items-center">
            <input id="sensorial_otro_chk" type="checkbox" name="sensorial_otro_check" value="1" {{ old('sensorial_otro', $model->sensorial_otro ?? '') ? 'checked' : '' }}>
            <span>Marcar si aplica</span>
            <input id="sensorial_otro_txt" type="text" name="sensorial_otro" class="border rounded px-2 py-1 ml-4 flex-1" placeholder="Especifique" value="{{ old('sensorial_otro', $model->sensorial_otro ?? '') }}">
          </div>
        </div>
      </div>
    </div>

    {{-- =================== OBSERVACIONES GENERALES + TABLA RESULTADOS =================== --}}
    @php
      $MESH_MAP = [
          '#10'=>'ret10', '#24'=>'ret24', '#50'=>'ret50', '#65'=>'ret65', '#85'=>'ret85',
          '#100'=>'ret100', '#120'=>'ret120', '#150'=>'ret150', '#200'=>'ret200',
      ];
      $granSel = collect(old('granulometria', $model->granulometria ?? []))->map(fn($v)=>strtolower(trim($v)))->all();
      $cols = ['folio'];
      foreach ($granSel as $m) { if (isset($MESH_MAP[$m])) $cols[] = $MESH_MAP[$m]; }
      array_push($cols, 'humedad', 'proteina', 'ph', 'microbiologicos', 'sensorial');

      $labels = [
        'folio'           => 'Folio o mezcla',
        'humedad'         => '% Humedad',
        'proteina'        => '% Proteína',
        'ph'              => 'pH',
        'microbiologicos' => 'Microbiológicos',
        'sensorial'       => 'Sensorial',
      ];
      foreach ($MESH_MAP as $malla => $key) { $labels[$key] = '% Ret ' . $malla; }

      $rows = is_array(old('tabla_resultados', $model->tabla_resultados ?? null))
            ? old('tabla_resultados', $model->tabla_resultados ?? [])
            : [];
    @endphp

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">OBSERVACIONES GENERALES Y RESULTADOS</div>
      <div class="p-3 space-y-4">
        <div>
          <label class="block text-sm font-semibold">Observaciones generales</label>
          <textarea name="observaciones_generales_intro" rows="2" class="w-full border rounded px-2 py-1">{{ old('observaciones_generales_intro', $model->observaciones_generales_intro ?? '') }}</textarea>
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold">Tabla de resultados</label>
            <button type="button" id="add-row" class="px-2 py-1 border rounded text-sm">+ Fila</button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-sm border" id="tabla-resultados" data-cols='@json($cols)'>
              <thead>
                <tr class="bg-gray-100">
                  @foreach($cols as $c)
                    <th class="border px-2 py-1">{{ $labels[$c] ?? $c }}</th>
                  @endforeach
                  <th class="border px-2 py-1 w-10">—</th>
                </tr>
              </thead>
              <tbody id="result-rows">
                @foreach($rows as $i => $r)
                  <tr>
                    @foreach($cols as $c)
                      <td class="border">
                        <input name="tabla_resultados[{{ $i }}][{{ $c }}]" class="w-full px-2 py-1" value="{{ $r[$c] ?? '' }}">
                      </td>
                    @endforeach
                    <td class="border text-center">
                      <button type="button" class="del-row px-2">×</button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- =================== ANEXO A — HALLAZGOS (ahora igual que B) =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">
        ANEXO A — HALLAZGOS
      </div>

      <div class="p-3 space-y-3">
        <div class="flex items-center justify-between">
          <label class="block text-sm font-semibold">Tabla de hallazgos físicos</label>
          <button type="button" id="anexoA-add-row" class="px-3 py-1 border rounded text-sm">+ Fila</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="anexoA-items-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:220px;">Folio o mezclas</th>
                <th class="border px-2 py-1">Observaciones generales</th>
                <th class="border px-2 py-1" style="width:260px;">Evidencias (imágenes)</th>
                <th class="border px-2 py-1 w-10">—</th>
              </tr>
            </thead>
            <tbody id="anexoA-items-body">
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Template ANEXO A --}}
    <template id="tpl-anexoA-item-row">
      <tr>
        <td class="border p-1 align-top">
          <input type="text" class="w-full px-2 py-1 anexoA-muestra" autocomplete="off">
        </td>
        <td class="border p-1 align-top">
          <textarea class="w-full px-2 py-1 anexoA-observaciones" rows="3"></textarea>
        </td>
        <td class="border p-1 align-top">
          <input type="file" accept="image/*" multiple class="block w-full rounded-md border border-gray-300 px-2 py-1 anexoA-files-input">
          <div class="mt-2 flex flex-wrap gap-2 anexoA-files-preview"></div>
        </td>
        <td class="border p-1 text-center align-top">
          <button type="button" class="px-2 text-red-600 anexoA-del-row" title="Eliminar fila">×</button>
        </td>
      </tr>
    </template>

    {{-- =================== ANEXO B — HALLAZGOS MICROBIOLÓGICOS  =================== --}}
    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">
        ANEXO B — HALLAZGOS MICROBIOLÓGICOS
      </div>

      <div class="p-3 space-y-3">
        <div class="flex items-center justify-between">
          <label class="block text-sm font-semibold">Tabla de hallazgos microbiológicos</label>
          <button type="button" id="anexoB-add-row" class="px-3 py-1 border rounded text-sm">+ Fila</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border" id="anexoB-items-table">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1" style="width:220px;">Muestra</th>
                <th class="border px-2 py-1">Observaciones generales</th>
                <th class="border px-2 py-1" style="width:260px;">Evidencias (imágenes)</th>
                <th class="border px-2 py-1 w-10">—</th>
              </tr>
            </thead>
            <tbody id="anexoB-items-body">
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Template ANEXO B --}}
    <template id="tpl-anexoB-item-row">
      <tr>
        <td class="border p-1 align-top">
          <input type="text" class="w-full px-2 py-1 anexoB-muestra" autocomplete="off">
        </td>
        <td class="border p-1 align-top">
          <textarea class="w-full px-2 py-1 anexoB-observaciones" rows="3"></textarea>
        </td>
        <td class="border p-1 align-top">
          <input type="file" accept="image/*" multiple class="block w-full rounded-md border border-gray-300 px-2 py-1 anexoB-files-input">
          <div class="mt-2 flex flex-wrap gap-2 anexoB-files-preview"></div>
        </td>
        <td class="border p-1 text-center align-top">
          <button type="button" class="px-2 text-red-600 anexoB-del-row" title="Eliminar fila">×</button>
        </td>
      </tr>
    </template>

    {{-- =================== CONCLUSIONES Y FIRMAS =================== --}}
      <div class="border rounded">
        <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">CONCLUSIONES Y FIRMAS</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold">Conclusiones</label>
            <textarea name="conclusiones" rows="3" class="w-full border rounded px-2 py-1">{{ old('conclusiones', $model->conclusiones ?? '') }}</textarea>
          </div>
          
          <div>
            <label class="block text-sm font-semibold">Realizó Nombre</label>
            <input type="text" name="realizo_nombre" class="w-full border rounded px-2 py-1" value="{{ old('realizo_nombre', $model->realizo_nombre ?? '') }}">
          </div>
          <div>
            <label class="block text-sm font-semibold">Puesto (Realizó)</label>
            <input type="text" name="realizo_puesto" class="w-full border rounded px-2 py-1" value="{{ old('realizo_puesto', $model->realizo_puesto ?? '') }}">
          </div>

          <div>
            <label class="block text-sm font-semibold">Revisó Nombre</label>
            <input type="text" name="reviso_nombre" class="w-full border rounded px-2 py-1" value="{{ old('reviso_nombre', $model->reviso_nombre ?? '') }}">
          </div>
          <div>
            <label class="block text-sm font-semibold">Puesto (Revisó)</label>
            <input type="text" name="reviso_puesto" class="w-full border rounded px-2 py-1" value="{{ old('reviso_puesto', $model->reviso_puesto ?? '') }}">
          </div>
        </div>
      </div>

    <div class="flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="button" id="analisis-interno-submit" class="bg-green-600 hover:bg-green-700 text-white">Generar PDF</x-button>
    </div>
  </form>

  <script>
  (function(){
    'use strict';
    function $(sel, root=document){ return root.querySelector(sel); }
    function $all(sel, root=document){ return Array.from(root.querySelectorAll(sel)); }
    function toggle(el, on){ if(!el) return; el.disabled = !on; if(!on) el.value = ''; }

    // ===== Submit
    const submitBtn = document.getElementById('analisis-interno-submit');
    submitBtn?.addEventListener('click', function(){
      const modal = this.closest('[role="dialog"], .modal, [data-modal]') || document;
      const form  = modal.querySelector('#analisis-interno-form');
      if (form) form.requestSubmit();
    });

    // ===== Materia extraña: "otro"
    function syncME(){
      const v = (document.querySelector('input[name="materia_extrana"]:checked')||{}).value;
      toggle(document.getElementById('me_otro_txt'), v === 'otro');
    }
    $all('input[name="materia_extrana"]').forEach(r => r.addEventListener('change', syncME));
    syncME();

    // ===== Granulometría: "otro"
    const gChk = document.getElementById('gran_otro_chk');
    const gTxt = document.getElementById('gran_otro_txt');
    function syncGran(){ toggle(gTxt, !!gChk?.checked); }
    gChk?.addEventListener('change', syncGran);
    syncGran();

    // ===== Sensorial: "otro"
    const sChk = document.getElementById('sensorial_otro_chk');
    const sTxt = document.getElementById('sensorial_otro_txt');
    function syncSens(){ toggle(sTxt, !!sChk?.checked); }
    sChk?.addEventListener('change', syncSens);
    syncSens();

    // ===== Tabla resultados dinámicos
    const table      = document.getElementById('tabla-resultados');
    const tbody      = document.getElementById('result-rows');
    const addBtn     = document.getElementById('add-row');
    const granChecks = $all('input[name="granulometria[]"]');

    const LABELS_BASE = {
      folio:'Folio o mezcla',
      humedad:'% Humedad',
      proteina:'% Proteína',
      ph:'pH',
      microbiologicos:'Microbiológicos',
      sensorial:'Sensorial'
    };
    const MESH_MAP = { '#10':'ret10', '#24':'ret24', '#50':'ret50', '#65':'ret65', '#85':'ret85', '#100':'ret100', '#120':'ret120', '#150':'ret150', '#200':'ret200' };

    function labelFor(col){
      if (LABELS_BASE[col]) return LABELS_BASE[col];
      const m = col.match(/^ret(\d+)$/);
      if (m) return `% Ret #${m[1]}`;
      return col;
    }

    function currentColsFromUI() {
      const selected = $all('input[name="granulometria[]"]:checked').map(ch => ch.value);
      const cols = ['folio'];
      selected.forEach(m => { if (MESH_MAP[m]) cols.push(MESH_MAP[m]); });
      cols.push('humedad','proteina','ph','microbiologicos','sensorial');
      return cols;
    }

    function rebuildHead(cols) {
      if (!table) return;
      const thead = table.tHead || table.createTHead();
      thead.innerHTML = '';
      const trHead = document.createElement('tr');
      trHead.className = 'bg-gray-100';
      cols.forEach(c => {
        const th = document.createElement('th');
        th.className = 'border px-2 py-1';
        th.textContent = labelFor(c);
        trHead.appendChild(th);
      });
      const thDel = document.createElement('th');
      thDel.className = 'border px-2 py-1 w-10';
      thDel.textContent = '—';
      trHead.appendChild(thDel);
      thead.appendChild(trHead);
    }

    function buildInput(nameAttr, value='') {
      const inp = document.createElement('input');
      inp.name = nameAttr;
      inp.className = 'w-full px-2 py-1';
      inp.value = value != null ? String(value) : '';
      return inp;
    }

    function buildRow(index, cols, values = {}){
      const tr = document.createElement('tr');
      cols.forEach(c => {
        const td = document.createElement('td');
        td.className = 'border';
        const ctrl = buildInput(`tabla_resultados[${index}][${c}]`, values[c] ?? '');
        td.appendChild(ctrl);
        tr.appendChild(td);
      });
      const tdDel = document.createElement('td');
      tdDel.className = 'border text-center';
      tdDel.innerHTML = `<button type="button" class="del-row px-2" aria-label="Eliminar fila">×</button>`;
      tr.appendChild(tdDel);
      return tr;
    }

    function collectRowValues(tr) {
      const row = { values: {} };
      const fields = $all('input[name^="tabla_resultados["]', tr);
      fields.forEach(field => {
        const m = field.name.match(/\]\[(\w+)\]$/);
        if (m) row.values[m[1]] = field.value;
      });
      return row;
    }

    function rebuildBody(cols) {
      if (!tbody) return;
      const oldRows = Array.from(tbody.querySelectorAll('tr')).map(collectRowValues);
      tbody.innerHTML = '';
      oldRows.forEach((r, idx) => tbody.appendChild(buildRow(idx, cols, r.values)));
    }

    function nextIndex(){ return tbody ? tbody.querySelectorAll('tr').length : 0; }

    function syncTableToGranulometria() {
      const cols = currentColsFromUI();
      if (table) table.dataset.cols = JSON.stringify(cols);
      rebuildHead(cols);
      rebuildBody(cols);
    }

    addBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      if (!tbody) return;
      let cols = [];
      try { cols = JSON.parse(table?.dataset?.cols || '[]'); } catch(_e) {}
      if (!Array.isArray(cols) || !cols.length) cols = ['folio','humedad','proteina','ph','microbiologicos','sensorial'];
      tbody.appendChild(buildRow(nextIndex(), cols));
    });

    tbody?.addEventListener('click', (e) => {
      if (e.target.closest('.del-row')) {
        e.preventDefault();
        e.target.closest('tr')?.remove();
        $all('tr', tbody).forEach((tr, i) => {
          $all('input[name^="tabla_resultados["]', tr).forEach(field => {
            field.name = field.name.replace(/tabla_resultados\[\d+\]/, `tabla_resultados[${i}]`);
          });
        });
      }
    });

    granChecks.forEach(ch => ch.addEventListener('change', syncTableToGranulometria));
    if (table && !table.dataset.cols) table.dataset.cols = JSON.stringify(currentColsFromUI());
    syncTableToGranulometria();

    // ======== ANEXO A ========
    const anexoABody = document.getElementById('anexoA-items-body');
    const anexoAAdd  = document.getElementById('anexoA-add-row');
    const anexoATpl  = document.getElementById('tpl-anexoA-item-row');

    function anexoANextIndex(){
      return anexoABody ? anexoABody.querySelectorAll('tr').length : 0;
    }

    function anexoASetNamesForRow(tr, i){
      const muestra = tr.querySelector('.anexoA-muestra');
      const obs     = tr.querySelector('.anexoA-observaciones');
      const file    = tr.querySelector('.anexoA-files-input');

      if (muestra) muestra.name = `anexo_a_items[${i}][muestra]`;
      if (obs)     obs.name     = `anexo_a_items[${i}][observaciones]`;
      if (file)    file.name    = `anexo_a_items[${i}][evidencias][]`;
    }

    function anexoAReindex(){
      Array.from(anexoABody.querySelectorAll('tr')).forEach((tr, i) => anexoASetNamesForRow(tr, i));
    }

    function anexoARenderPreview(inputEl, previewWrap){
      if (!inputEl || !previewWrap) return;
      previewWrap.innerHTML = '';
      const files = Array.from(inputEl.files || []);
      files.forEach((file, idx) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
          const box = document.createElement('div');
          box.className = 'relative inline-block';

          const img = document.createElement('img');
          img.src = ev.target.result;
          img.style.maxWidth = '120px';
          img.style.maxHeight = '100px';
          img.style.objectFit = 'contain';
          img.className = 'border rounded shadow';

          const rm = document.createElement('button');
          rm.type = 'button';
          rm.textContent = '✖';
          rm.title = 'Quitar archivo';
          rm.className = 'absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center';

          rm.addEventListener('click', () => {
            const dt = new DataTransfer();
            files.forEach((f, j) => { if (j !== idx) dt.items.add(f); });
            inputEl.files = dt.files;
            anexoARenderPreview(inputEl, previewWrap);
          });

          box.appendChild(img);
          box.appendChild(rm);
          previewWrap.appendChild(box);
        };
        reader.readAsDataURL(file);
      });
    }

    function addAnexoARow(prefill = {muestra:'', observaciones:''}){
      if (!anexoATpl || !anexoABody) return;
      const tr = anexoATpl.content.firstElementChild.cloneNode(true);

      const muestra = tr.querySelector('.anexoA-muestra');
      const obs     = tr.querySelector('.anexoA-observaciones');
      if (muestra) muestra.value = prefill.muestra || '';
      if (obs)     obs.value     = prefill.observaciones || '';

      const fileInput = tr.querySelector('.anexoA-files-input');
      const preview   = tr.querySelector('.anexoA-files-preview');
      fileInput?.addEventListener('change', (e) => anexoARenderPreview(e.target, preview));

      tr.querySelector('.anexoA-del-row')?.addEventListener('click', (e) => {
        e.preventDefault();
        tr.remove();
        anexoAReindex();
      });

      const i = anexoANextIndex();
      anexoASetNamesForRow(tr, i);
      anexoABody.appendChild(tr);
    }

    anexoAAdd?.addEventListener('click', (e) => {
      e.preventDefault();
      addAnexoARow();
    });

    if (anexoANextIndex() === 0) addAnexoARow();

    // ======== ANEXO B ========
    const anexoBBody = document.getElementById('anexoB-items-body');
    const anexoBAdd  = document.getElementById('anexoB-add-row');
    const anexoBTpl  = document.getElementById('tpl-anexoB-item-row');

    function anexoBNextIndex(){
      return anexoBBody ? anexoBBody.querySelectorAll('tr').length : 0;
    }

    function anexoBSetNamesForRow(tr, i){
      const muestra = tr.querySelector('.anexoB-muestra');
      const obs     = tr.querySelector('.anexoB-observaciones');
      const file    = tr.querySelector('.anexoB-files-input');

      if (muestra) muestra.name = `anexo_b_items[${i}][muestra]`;
      if (obs)     obs.name     = `anexo_b_items[${i}][observaciones]`;
      if (file)    file.name    = `anexo_b_items[${i}][evidencias][]`;
    }

    function anexoBReindex(){
      Array.from(anexoBBody.querySelectorAll('tr')).forEach((tr, i) => anexoBSetNamesForRow(tr, i));
    }

    function anexoBRenderPreview(inputEl, previewWrap){
      if (!inputEl || !previewWrap) return;
      previewWrap.innerHTML = '';
      const files = Array.from(inputEl.files || []);
      files.forEach((file, idx) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
          const box = document.createElement('div');
          box.className = 'relative inline-block';

          const img = document.createElement('img');
          img.src = ev.target.result;
          img.style.maxWidth = '120px';
          img.style.maxHeight = '100px';
          img.style.objectFit = 'contain';
          img.className = 'border rounded shadow';

          const rm = document.createElement('button');
          rm.type = 'button';
          rm.textContent = '✖';
          rm.title = 'Quitar archivo';
          rm.className = 'absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center';

          rm.addEventListener('click', () => {
            const dt = new DataTransfer();
            files.forEach((f, j) => { if (j !== idx) dt.items.add(f); });
            inputEl.files = dt.files;
            anexoBRenderPreview(inputEl, previewWrap);
          });

          box.appendChild(img);
          box.appendChild(rm);
          previewWrap.appendChild(box);
        };
        reader.readAsDataURL(file);
      });
    }

    function addAnexoBRow(prefill = {muestra:'', observaciones:''}){
      if (!anexoBTpl || !anexoBBody) return;
      const tr = anexoBTpl.content.firstElementChild.cloneNode(true);

      const muestra = tr.querySelector('.anexoB-muestra');
      const obs     = tr.querySelector('.anexoB-observaciones');
      if (muestra) muestra.value = prefill.muestra || '';
      if (obs)     obs.value     = prefill.observaciones || '';

      const fileInput = tr.querySelector('.anexoB-files-input');
      const preview   = tr.querySelector('.anexoB-files-preview');
      fileInput?.addEventListener('change', (e) => anexoBRenderPreview(e.target, preview));

      tr.querySelector('.anexoB-del-row')?.addEventListener('click', (e) => {
        e.preventDefault();
        tr.remove();
        anexoBReindex();
      });

      const i = anexoBNextIndex();
      anexoBSetNamesForRow(tr, i);
      anexoBBody.appendChild(tr);
    }

    anexoBAdd?.addEventListener('click', (e) => {
      e.preventDefault();
      addAnexoBRow();
    });

    if (anexoBNextIndex() === 0) addAnexoBRow();

  })();
  </script>
</x-modal>
