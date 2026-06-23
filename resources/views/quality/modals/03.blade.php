<x-modal id="HS">
<form action="{{ route('quality.pdf3') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
  @csrf

  {{-- ===== Encabezado simple / Producto ===== --}}
  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Producto (catálogo)</label>
      <select name="product_id" class="w-full rounded-md border border-gray-300 px-3 py-2">
        <option value="">— Selecciona —</option>
        @isset($products)
          @foreach($products as $p)
            <option value="{{ $p->product_id }}">{{ $p->name }}</option>
          @endforeach
        @endisset
      </select>
    </div>
  </div>

  {{-- ===== SECCIÓN 1: Identificación ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">1) Identificación del producto y de la empresa</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Uso</label>
        <textarea name="uso" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Sinónimos</label>
        <textarea name="sinonimo" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
      </div>
    </div>
  </div>

  
 {{-- Pictogramas GHS --}}
    <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-semibold">Pictogramas GHS</h4>
            <span id="pic-count" class="text-xs text-gray-500">0 / 6</span>
        </div>

        @php
            $pictos = [
            ['slug'=>'explosion',                   'file'=>'explosion.jpg',                   'label'=>'Explosivo (GHS01)'],
            ['slug'=>'inflamable',                  'file'=>'inflamable.jpg',                  'label'=>'Inflamable (GHS02)'],
            ['slug'=>'comburente',                  'file'=>'comburente.jpg',                  'label'=>'Comburente (GHS03)'],
            ['slug'=>'gas-a-presion',               'file'=>'gas-a-presion.jpg',               'label'=>'Gas a presión (GHS04)'],
            ['slug'=>'corrosivo',                   'file'=>'corrosivo.jpg',                   'label'=>'Corrosivo (GHS05)'],
            ['slug'=>'toxicidad-aguda',             'file'=>'toxicidad-aguda.jpg',             'label'=>'Toxicidad aguda (GHS06)'],
            ['slug'=>'peligro-salud',               'file'=>'peligro-salud.jpg',               'label'=>'Irritante (GHS07)'],
            ['slug'=>'peligro-grave-para-la-salud', 'file'=>'peligro-grave-para-la-salud.jpg', 'label'=>'Peligro grave para la salud (GHS08)'],
            ];
            $prev = old('pictos', []);
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($pictos as $p)
            @php $id = 'pic-'.$p['slug']; @endphp

            <div class="relative">
                <input id="{{ $id }}" type="checkbox"
                    name="pictos[]" value="{{ $p['slug'] }}"
                    class="peer hidden"
                    {{ in_array($p['slug'], (array)$prev) ? 'checked' : '' }}>
                <label for="{{ $id }}"
                    class="flex flex-col items-center justify-center h-40 rounded-lg border p-3 hover:shadow
                            peer-checked:border-green-600 peer-checked:ring-2 peer-checked:ring-green-600">
                <img src="{{ asset('images/ghs/'.$p['file']) }}"
                    alt="{{ $p['label'] }}"
                    class="h-16 object-contain mb-2">
                <div class="text-xs text-center leading-tight">{{ $p['label'] }}</div>
                </label>
            </div>
            @endforeach
        </div>

        <p class="text-xs text-gray-500 mt-2">Elige hasta 6 pictogramas.</p>
    
    {{-- ===== INDICADORES DE PELIGRO (H-codes) ===== --}}
        <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-semibold">INDICADORES DE PELIGRO (H-codes)</h4>
            <button type="button" class="btn-add-row-hp border px-3 py-1 rounded" data-target="h-codes">+ Agregar</button>
        </div>
        <div class="overflow-x-auto border rounded">
            <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                <th class="px-3 py-2 w-[24%] text-left">Código (H###)</th>
                <th class="px-3 py-2 text-left">Indicador</th>
                </tr>
            </thead>
            <tbody id="h-codes-rows" data-name="h_codes">
                <tr class="dyn-row">
                <td class="px-3 py-2">
                    <input type="text" name="h_codes[0][code]" class="w-full rounded-md border px-3 py-2" placeholder="H315">
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="h_codes[0][text]" class="w-full rounded-md border px-3 py-2" placeholder="Causa irritación cutánea">
                </td>
                </tr>
            </tbody>
            </table>
        </div>
        </div>

        {{-- ===== CONSEJOS DE PRECAUCIÓN (P-codes) ===== --}}
        <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-semibold">CONSEJOS DE PRECAUCIÓN (P-codes)</h4>
            <button type="button" class="btn-add-row-hp border px-3 py-1 rounded" data-target="p-codes">+ Agregar</button>
        </div>
        <div class="overflow-x-auto border rounded">
            <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                <th class="px-3 py-2 w-[24%] text-left">Código (P###)</th>
                <th class="px-3 py-2 text-left">Consejo</th>
                </tr>
            </thead>
            <tbody id="p-codes-rows" data-name="p_codes">
                <tr class="dyn-row">
                <td class="px-3 py-2">
                    <input type="text" name="p_codes[0][code]" class="w-full rounded-md border px-3 py-2" placeholder="P280">
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="p_codes[0][text]" class="w-full rounded-md border px-3 py-2" placeholder="Llevar guantes de protección">
                </td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
  </div>

  {{-- ===== SECCIÓN 3: Composición (componentes y aditivos) ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">3) Composición</h3>

    <div class="mb-3">
      <div class="flex items-center justify-between mb-2">
        <h4 class="text-sm font-semibold">Componentes (sustancias principales)</h4>
        <button type="button" class="btn-add-row border border-gray-300 px-3 py-1 rounded" data-target="componentes">+ Agregar</button>
      </div>
      <div class="overflow-x-auto border border-gray-200 rounded">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 w-[55%] text-left">Identidad química</th>
              <th class="px-3 py-2 w-[20%] text-center">% en volumen</th>
              <th class="px-3 py-2 w-[20%] text-center">No. CAS</th>
              <th class="px-3 py-2 w-[5%]  text-center">Acción</th>
            </tr>
          </thead>
          <tbody id="componentes-rows" data-name="componentes">
            <tr class="dyn-row">
              <td class="px-3 py-2"><input type="text" name="componentes[0][nombre]" class="w-full rounded-md border border-gray-300 px-3 py-2"></td>
              <td class="px-3 py-2"><input type="text" name="componentes[0][porcentaje]" class="w-full rounded-md border border-gray-300 px-3 py-2 text-center"></td>
              <td class="px-3 py-2"><input type="text" name="componentes[0][cas]" class="w-full rounded-md border border-gray-300 px-3 py-2 text-center"></td>
              <td class="px-3 py-2 text-center"><button type="button" class="btn-remove-row text-red-600 hover:underline">Quitar</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between mb-2">
        <h4 class="text-sm font-semibold">Aditivos / Coadyuvantes</h4>
        <button type="button" class="btn-add-row border border-gray-300 px-3 py-1 rounded" data-target="aditivos">+ Add</button>
      </div>
      <div class="overflow-x-auto border border-gray-200 rounded">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 w-[55%] text-left">Identidad química</th>
              <th class="px-3 py-2 w-[20%] text-center">% en volumen</th>
              <th class="px-3 py-2 w-[20%] text-center">No. CAS</th>
              <th class="px-3 py-2 w-[5%]  text-center">Acción</th>
            </tr>
          </thead>
          <tbody id="aditivos-rows" data-name="aditivos">
            <tr class="dyn-row">
              <td class="px-3 py-2"><input type="text" name="aditivos[0][nombre]" class="w-full rounded-md border border-gray-300 px-3 py-2"></td>
              <td class="px-3 py-2"><input type="text" name="aditivos[0][porcentaje]" class="w-full rounded-md border border-gray-300 px-3 py-2 text-center"></td>
              <td class="px-3 py-2"><input type="text" name="aditivos[0][cas]" class="w-full rounded-md border border-gray-300 px-3 py-2 text-center"></td>
              <td class="px-3 py-2 text-center"><button type="button" class="btn-remove-row text-red-600 hover:underline">Quitar</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ===== SECCIÓN 4: Primeros auxilios ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">4) Primeros auxilios</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Contacto con los ojos</label><textarea name="aux_ojos" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Contacto con la piel</label><textarea name="aux_piel" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Ingestión</label><textarea name="aux_ingestion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Inhalación</label><textarea name="aux_inhalacion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Síntomas y efectos más</label><textarea name="aux_sintomas" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Agudos o crónicos</label><textarea name="aux_agudos" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Tratamiento especial</label><textarea name="aux_tratamiento" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 5: Medidas contra incendios ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">5) Medidas contra incendios</h3>
    <div class="space-y-3">
      <div><label class="block text-sm font-medium mb-1">Medios de extinción apropiados</label><textarea name="extincion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Peligros específicos de la sustancia</label><textarea name="peligros_incendio" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Química peligrosa o mezcla</label><textarea name="quimica_peligrosa" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Medidas especiales (grupos de combate)</label><textarea name="medidas_especiales" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 6: Fuga o derrame ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">6) Medidas en caso de fuga o derrame accidental</h3>
    <div class="space-y-3">
      <div><label class="block text-sm font-medium mb-1">Equipo de protección personal</label><textarea name="fuga_equipo" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Precauciones medio ambiente</label><textarea name="fuga_precauciones" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Métodos y materiales de contención y limpieza</label><textarea name="fuga_metodos" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 7: Manejo y almacenamiento ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">7) Manejo y almacenamiento</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Manejo seguro</label><textarea name="manejo_seguro" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Almacenamiento seguro</label><textarea name="almacenamiento_seguro" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 8: Controles de exposición / PPE ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">8) Controles de exposición / Protección personal</h3>
    <div class="space-y-3">
      <div><label class="block text-sm font-medium mb-1">Parámetros de control</label><textarea name="control_parametros" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Controles técnicos apropiados</label><textarea name="controles_tecnicos" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Protección personal</label><textarea name="proteccion_personal" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 9: Propiedades físicas y químicas ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">9) Propiedades físicas y químicas</h3>
    <div class="grid md:grid-cols-2 gap-4">
      @php
        $props = [
          'apariencia'=>'Apariencia','color'=>'Color','olor'=>'Olor','umbral_olfativo'=>'Umbral olfativo','ph'=>'pH',
          'fusion'=>'Punto de fusión','ebullicion'=>'Punto inicial e intervalo de ebullición','inflamacion'=>'Punto de inflamación',
          'evaporacion'=>'Velocidad de evaporación','inflamabilidad'=>'Inflamabilidad (sólido o gas)',
          'limite_inflamabilidad'=>'Límite sup/inf de inflamabilidad o explosión','presion_vapor'=>'Presión de vapor',
          'densidad_vapor'=>'Densidad de vapor','densidad_relativa'=>'Densidad relativa','solubilidad'=>'Solubilidad(es)',
          'coef_particion'=>'Coeficiente de partición n-octanol/agua','ignicion'=>'Temperatura de ignición espontánea',
          'descomposicion'=>'Temperatura de descomposición','viscosidad'=>'Viscosidad (cinemática)','peso_molecular'=>'Peso molecular',
        ];
      @endphp

      @foreach($props as $name => $label)
        <div>
          <label class="block text-sm font-medium mb-1">{{ $label }}</label>
          <input type="text" name="{{ $name }}" class="w-full rounded-md border border-gray-300 px-3 py-2">
        </div>
      @endforeach
    </div>
  </div>

  {{-- ===== SECCIÓN 10: Estabilidad y reactividad ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">10) Estabilidad y reactividad</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Reactividad</label><textarea name="reactividad" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Estabilidad química</label><textarea name="estabilidad_quimica" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Posibilidad de reacciones peligrosas</label><textarea name="reacciones" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Condiciones que deben evitarse</label><textarea name="condiciones" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Materiales incompatibles</label><textarea name="incompatibles" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Productos de descomposición peligrosos</label><textarea name="productos" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 11: Información toxicológica ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">11) Información toxicológica</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Toxicidad aguda</label><textarea name="toxicidad_aguda" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Corrosión/Irritación cutánea</label><textarea name="irritacion_cutanea" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Lesión ocular grave / Irritación ocular</label><textarea name="irritacion_ocular" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Sensibilización respiratoria o cutánea</label><textarea name="sensibilizacion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Mutagenicidad en células germinales</label><textarea name="mutagenicidad" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Carcinogenicidad</label><textarea name="carcinogenicidad" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Toxicidad para la reproducción</label><textarea name="reproduccion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Toxicidad sistémica (exposición única)</label><textarea name="sistemica_unica" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Toxicidad sistémica (exposiciones repetidas)</label><textarea name="sistemica_repetida" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Peligro por aspiración</label><textarea name="aspiracion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 12: Información ecotoxicológica ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">12) Información ecotoxicológica</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Toxicidad</label><textarea name="eco_toxicidad" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Persistencia y degradabilidad</label><textarea name="eco_persistencia" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Potencial de bioacumulación</label><textarea name="eco_bioacumulacion" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Movilidad en el suelo</label><textarea name="eco_movilidad" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Otros efectos adversos</label><textarea name="eco_otros" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 13: Eliminación ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">13) Eliminación del producto</h3>
    <label class="block text-sm font-medium mb-1">Métodos de eliminación</label>
    <textarea name="eliminacion_metodos" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
  </div>

  {{-- ===== SECCIÓN 14: Transporte ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">14) Transporte</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Número ONU</label><input type="text" name="trans_onu" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
      <div><label class="block text-sm font-medium mb-1">Designación oficial</label><input type="text" name="trans_designacion" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
      <div><label class="block text-sm font-medium mb-1">Clases relativas al transporte</label><input type="text" name="trans_clase" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
      <div><label class="block text-sm font-medium mb-1">Grupo de embalaje</label><input type="text" name="trans_embalaje" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
      <div><label class="block text-sm font-medium mb-1">Riesgos ambientales</label><input type="text" name="trans_riesgos" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
      <div><label class="block text-sm font-medium mb-1">Precauciones especiales</label><input type="text" name="trans_precauciones" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
      <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Transporte a granel (MARPOL/IBC)</label><input type="text" name="trans_granel" class="w-full rounded-md border border-gray-300 px-3 py-2"></div>
    </div>
  </div>

  {{-- ===== SECCIÓN 15: Información reglamentaria ===== 
  <div>
    <h3 class="text-base font-semibold mb-2">15) Información reglamentaria</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Reglamentaria 1</label><textarea name="reglamentaria1" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
      <div><label class="block text-sm font-medium mb-1">Reglamentaria 2</label><textarea name="reglamentaria2" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea></div>
    </div>
  </div>--}}

  {{-- ===== SECCIÓN 16: Otra información ===== --}}
  <div>
    <h3 class="text-base font-semibold mb-2">16) Otra información</h3>
    <div class="grid md:grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1">Fecha de elaboración</label><input type="text" name="fecha_elaboracion" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="30/Enero/2023"></div>
      <div><label class="block text-sm font-medium mb-1">Número de revisión</label><input type="text" name="revision" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="1"></div>
    </div>
  </div>

  <div class="flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</x-button>
    <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">Guardar</x-button>
  </div>
</form>

{{-- ===== JS mínimo para pictogramas y filas dinámicas ===== --}}
<script>
(function(){
  const max = 6;
  const boxes = document.querySelectorAll('input[name="pictos[]"]');
  const counter = document.getElementById('pic-count');

  function update(){
    const checked = Array.from(boxes).filter(b => b.checked);
    if (counter) counter.textContent = `${checked.length} / ${max}`;
    boxes.forEach(b => {
      b.disabled = !b.checked && checked.length >= max;
    });
  }

  boxes.forEach(b => b.addEventListener('change', update));
  update(); 
})();
  document.querySelectorAll('.btn-add-row').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const tbody = document.getElementById(btn.dataset.target+'-rows');
      const name  = tbody.dataset.name; 
      const idx   = tbody.querySelectorAll('tr.dyn-row').length;
      const tr    = document.createElement('tr');
      tr.className = 'dyn-row';
      tr.innerHTML = `
        <td class="px-3 py-2"><input type="text" name="${name}[${idx}][nombre]" class="w-full rounded-md border border-gray-300 px-3 py-2"></td>
        <td class="px-3 py-2"><input type="text" name="${name}[${idx}][porcentaje]" class="w-full rounded-md border border-gray-300 px-3 py-2 text-center"></td>
        <td class="px-3 py-2"><input type="text" name="${name}[${idx}][cas]" class="w-full rounded-md border border-gray-300 px-3 py-2 text-center"></td>
        <td class="px-3 py-2 text-center"><button type="button" class="btn-remove-row text-red-600 hover:underline">Quitar</button></td>`;
      tbody.appendChild(tr);
    });
  });
  document.addEventListener('click', (e)=>{
    if(e.target.matches('.btn-remove-row')){
      e.target.closest('tr')?.remove();
    }
  });

  document.querySelectorAll('.btn-add-row-hp').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const tbody = document.getElementById(btn.dataset.target + '-rows'); 
      const name  = tbody.dataset.name; 
      const idx   = tbody.querySelectorAll('tr.dyn-row').length;
      const isH   = name === 'h_codes';
      const phC   = isH ? 'H315' : 'P280';
      const phT   = isH ? 'Causa irritación cutánea' : 'Llevar guantes de protección';

      const tr = document.createElement('tr');
      tr.className = 'dyn-row';
      tr.innerHTML = `
        <td class="px-3 py-2"><input type="text" name="${name}[${idx}][code]" class="w-full rounded-md border px-3 py-2" placeholder="${phC}"></td>
        <td class="px-3 py-2"><input type="text" name="${name}[${idx}][text]" class="w-full rounded-md border px-3 py-2" placeholder="${phT}"></td>
      `;
      tbody.appendChild(tr);
    });
  });
</script>
</x-modal>
