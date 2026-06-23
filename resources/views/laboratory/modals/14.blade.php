<x-modal id="14">
  <form id="bitacora14-form" method="POST" action="{{ route('laboratory.pdf14') }}" target="_blank" class="space-y-6">
    @csrf

    {{-- =================== Registros de bitácora =================== --}}
    <div class="border rounded" x-data>
      <div class="px-3 py-2 font-semibold text-white flex items-center justify-between" style="background:#16a34a;">
        <span>Registros</span>
        <button type="button" id="reg-add" class="px-2 py-1 border rounded text-sm bg-white/10">+ Agregar registro</button>
      </div>

      <div id="reg-container" class="p-3 space-y-4">
        @php
          $oldRegs = old('registros', [[
            'titulo' => 'Registro 1',
            'fecha' => '',
            'folio' => '',
            'lote' => '',
            'peso' => '',
            'ph' => '',
            'humedad' => '',
            'proteina' => '',
            'sensorial' => ['color'=>'', 'olor'=>'', 'sabor'=>''],
            'granulometria' => ['65'=>'', '85'=>'', '100'=>'', 'x1'=>'', 'x2'=>'', 'x3'=>''],
            'observaciones' => '',
          ]]);
        @endphp

        @foreach($oldRegs as $i => $r)
        <div class="reg-card border rounded overflow-hidden">
          <div class="px-3 py-2 bg-gray-50 flex items-center justify-between">
            <div class="font-semibold">Tarjeta: <input
                class="inline-block border rounded px-2 py-1 ml-1"
                type="text"
                name="registros[{{ $i }}][titulo]"
                value="{{ $r['titulo'] ?? ('Registro '.($i+1)) }}"
                placeholder="Título del registro">
            </div>
            <button type="button" class="reg-del text-red-600 text-sm px-2 py-1">Eliminar</button>
          </div>

          <div class="p-3 space-y-4">
            {{-- Fila: Fecha / Folio / Lote --}}
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">Fecha</label>
                <input type="date" name="registros[{{ $i }}][fecha]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['fecha'] ?? '' }}">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">Folio</label>
                <input type="text" name="registros[{{ $i }}][folio]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['folio'] ?? '' }}">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">Lote</label>
                <input type="text" name="registros[{{ $i }}][lote]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['lote'] ?? '' }}">
              </div>
            </div>

            {{-- Fila: Peso / pH / Humedad --}}
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">Peso</label>
                <input type="text" name="registros[{{ $i }}][peso]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['peso'] ?? '' }}" placeholder="e.g. 500 g">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">pH</label>
                <input type="text" name="registros[{{ $i }}][ph]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['ph'] ?? '' }}">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">Humedad</label>
                <input type="text" name="registros[{{ $i }}][humedad]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['humedad'] ?? '' }}" placeholder="%">
              </div>
            </div>

            {{-- Fila: Proteína / Sensorial (Color, Olor, Sabor) --}}
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold">Proteína</label>
                <input type="text" name="registros[{{ $i }}][proteina]" class="w-full border rounded px-2 py-1"
                       value="{{ $r['proteina'] ?? '' }}" placeholder="%">
              </div>
              <div class="md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                  <label class="block text-sm font-semibold">Sensorial - Color</label>
                  <input type="text" name="registros[{{ $i }}][sensorial][color]" class="w-full border rounded px-2 py-1"
                         value="{{ $r['sensorial']['color'] ?? '' }}">
                </div>
                <div>
                  <label class="block text-sm font-semibold">Sensorial - Olor</label>
                  <input type="text" name="registros[{{ $i }}][sensorial][olor]" class="w-full border rounded px-2 py-1"
                         value="{{ $r['sensorial']['olor'] ?? '' }}">
                </div>
                <div>
                  <label class="block text-sm font-semibold">Sensorial - Sabor</label>
                  <input type="text" name="registros[{{ $i }}][sensorial][sabor]" class="w-full border rounded px-2 py-1"
                         value="{{ $r['sensorial']['sabor'] ?? '' }}">
                </div>
              </div>
            </div>

            {{-- Granulometría --}}
            <div>
            <div class="text-sm font-semibold mb-2">Granulometría</div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div>
                <label class="block text-xs font-semibold">Malla #10</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][10]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['10'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #24</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][24]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['24'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #50</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][50]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['50'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #65</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][65]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['65'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #85</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][85]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['85'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #100</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][100]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['100'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #120</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][120]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['120'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #150</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][150]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['150'] ?? '' }}" placeholder="%">
                </div>
                <div>
                <label class="block text-xs font-semibold">Malla #200</label>
                <input type="number" step="0.01" min="0" max="100" inputmode="decimal"
                        name="registros[{{ $i }}][granulometria][200]"
                        class="w-full border rounded px-2 py-1"
                        value="{{ $r['granulometria']['200'] ?? '' }}" placeholder="%">
                </div>
            </div>

            {{-- Tips / validación opcional --}}
            <p class="text-xs text-gray-600 mt-2">
                Solo se imprimirán en el PDF las mallas con valor capturado. (Opcional) Mantén el total ≈ 100%.
            </p>
            </div>

            {{-- Observaciones --}}
            <div>
              <label class="block text-sm font-semibold">Observaciones generales</label>
              <textarea name="registros[{{ $i }}][observaciones]" rows="3" class="w-full border rounded px-2 py-1"
                        placeholder="Notas, hallazgos, etc.">{{ $r['observaciones'] ?? '' }}</textarea>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" form="bitacora14-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
    </div>
  </form>

  {{-- ========== JS: agregar========== --}}
  <script>
  (function(){
    const container = document.getElementById('reg-container');
    const btnAdd = document.getElementById('reg-add');

    function template(idx){
      return `
      <div class="reg-card border rounded overflow-hidden">
        <div class="px-3 py-2 bg-gray-50 flex items-center justify-between">
          <div class="font-semibold">Tarjeta:
            <input class="inline-block border rounded px-2 py-1 ml-1"
                   type="text" name="registros[${idx}][titulo]" value="Registro ${idx+1}"
                   placeholder="Título del registro">
          </div>
          <button type="button" class="reg-del text-red-600 text-sm px-2 py-1">Eliminar</button>
        </div>

        <div class="p-3 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">Fecha</label>
              <input type="date" name="registros[${idx}][fecha]" class="w-full border rounded px-2 py-1">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">Folio</label>
              <input type="text" name="registros[${idx}][folio]" class="w-full border rounded px-2 py-1">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">Lote</label>
              <input type="text" name="registros[${idx}][lote]" class="w-full border rounded px-2 py-1">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">Peso</label>
              <input type="text" name="registros[${idx}][peso]" class="w-full border rounded px-2 py-1" placeholder="e.g. 500 g">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">pH</label>
              <input type="text" name="registros[${idx}][ph]" class="w-full border rounded px-2 py-1">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">Humedad</label>
              <input type="text" name="registros[${idx}][humedad]" class="w-full border rounded px-2 py-1" placeholder="%">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold">Proteína</label>
              <input type="text" name="registros[${idx}][proteina]" class="w-full border rounded px-2 py-1" placeholder="%">
            </div>
            <div class="md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="block text-sm font-semibold">Sensorial - Color</label>
                <input type="text" name="registros[${idx}][sensorial][color]" class="w-full border rounded px-2 py-1">
              </div>
              <div>
                <label class="block text-sm font-semibold">Sensorial - Olor</label>
                <input type="text" name="registros[${idx}][sensorial][olor]" class="w-full border rounded px-2 py-1">
              </div>
              <div>
                <label class="block text-sm font-semibold">Sensorial - Sabor</label>
                <input type="text" name="registros[${idx}][sensorial][sabor]" class="w-full border rounded px-2 py-1">
              </div>
            </div>
          </div>

          <div>
            <div class="text-sm font-semibold mb-2">Granulometría</div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
              <div>
                <label class="block text-xs font-semibold">Malla #65</label>
                <input type="text" name="registros[${idx}][granulometria][65]" class="w-full border rounded px-2 py-1" placeholder="%">
              </div>
              <div>
                <label class="block text-xs font-semibold">Malla #85</label>
                <input type="text" name="registros[${idx}][granulometria][85]" class="w-full border rounded px-2 py-1" placeholder="%">
              </div>
              <div>
                <label class="block text-xs font-semibold">Malla #100</label>
                <input type="text" name="registros[${idx}][granulometria][100]" class="w-full border rounded px-2 py-1" placeholder="%">
              </div>
              <div>
                <label class="block text-xs font-semibold">Malla # (extra 1)</label>
                <input type="text" name="registros[${idx}][granulometria][x1]" class="w-full border rounded px-2 py-1">
              </div>
              <div>
                <label class="block text-xs font-semibold">Malla # (extra 2)</label>
                <input type="text" name="registros[${idx}][granulometria][x2]" class="w-full border rounded px-2 py-1">
              </div>
              <div>
                <label class="block text-xs font-semibold">Malla # (extra 3)</label>
                <input type="text" name="registros[${idx}][granulometria][x3]" class="w-full border rounded px-2 py-1">
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold">Observaciones generales</label>
            <textarea name="registros[${idx}][observaciones]" rows="3" class="w-full border rounded px-2 py-1" placeholder="Notas, hallazgos, etc."></textarea>
          </div>
        </div>
      </div>
      `;
    }

    btnAdd?.addEventListener('click', ()=>{
      const idx = container.querySelectorAll('.reg-card').length;
      const wrapper = document.createElement('div');
      wrapper.innerHTML = template(idx);
      container.appendChild(wrapper.firstElementChild);
    });

    container?.addEventListener('click', (e)=>{
      if(e.target.classList.contains('reg-del')){
        const card = e.target.closest('.reg-card');
        card?.remove();
      }
    });
  })();
  </script>
</x-modal>
