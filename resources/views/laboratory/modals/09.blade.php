<x-modal id="09">
<form id="solicitud-formulacion-form" method="POST" action="{{ route('laboratory.pdf9') }}" target="_blank" class="space-y-6">
  @csrf

  {{-- =================== Fecha de solicitud & paginación =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Encabezado</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Fecha de solicitud</label>
        <input type="date" name="fecha_solicitud" class="w-full border rounded px-2 py-1" value="{{ old('fecha_solicitud') }}">
      </div>
    </div>
  </div>

  {{-- =================== Propuesta & Campo de aplicación =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Propuesta / Campo de aplicación</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-1 gap-3">
      <div>
        <label class="block text-sm font-semibold">Propuesta del nombre del producto</label>
        <input type="text" name="propuesta_nombre" class="w-full border rounded px-2 py-1" value="{{ old('propuesta_nombre') }}">
      </div>

      <div>
        <label class="block text-sm font-semibold">Campo de aplicación (elige los que apliquen)</label>
        <div class="flex flex-wrap gap-4 mt-1">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="agricola" {{ in_array('agricola', old('aplicacion', [])) ? 'checked' : '' }}> Agrícola
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="pecuario" {{ in_array('pecuario', old('aplicacion', [])) ? 'checked' : '' }}> Pecuario
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="petfood" {{ in_array('petfood', old('aplicacion', [])) ? 'checked' : '' }}> Petfood
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="aplicacion[]" value="otro" {{ in_array('otro', old('aplicacion', [])) ? 'checked' : '' }}> Otro
          </label>
        </div>
        <div class="mt-2">
          <input type="text" name="ap_otro" class="w-full border rounded px-2 py-1" placeholder="Especifique 'Otro' (opcional)" value="{{ old('ap_otro') }}">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label for="uso_especifico" class="block text-sm font-semibold">Uso Específico</label>
          <select name="uso_especifico" id="uso_especifico"
                  class="w-full border rounded px-2 py-1">
            <option value="" disabled {{ old('uso_especifico')===null ? 'selected' : '' }}>Elija un elemento.</option>
            <option value="Avícola"           {{ old('uso_especifico')==='Avícola' ? 'selected' : '' }}>Avícola</option>
            <option value="Porcícola"         {{ old('uso_especifico')==='Porcícola' ? 'selected' : '' }}>Porcícola</option>
            <option value="Acuícola"          {{ old('uso_especifico')==='Acuícola' ? 'selected' : '' }}>Acuícola</option>
            <option value="Carne"             {{ old('uso_especifico')==='Carne' ? 'selected' : '' }}>Carne</option>
            <option value="Leche"             {{ old('uso_especifico')==='Leche' ? 'selected' : '' }}>Leche</option>
            <option value="Bioestimulante"    {{ old('uso_especifico')==='Bioestimulante' ? 'selected' : '' }}>Bioestimulante</option>
            <option value="Nutrición vegetal" {{ old('uso_especifico')==='Nutrición vegetal' ? 'selected' : '' }}>Nutrición vegetal</option>
            <option value="Alimento"          {{ old('uso_especifico')==='Alimento' ? 'selected' : '' }}>Alimento</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold">Otro (uso específico)</label>
          <input type="text" name="uso_otro" class="w-full border rounded px-2 py-1" value="{{ old('uso_otro') }}">
        </div>
      </div>
    </div>
  </div>

  {{-- =================== Información del desarrollo / formulación =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Información del desarrollo / formulación</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Objetivo</label>
        <textarea name="objetivo" rows="3" class="w-full border rounded px-2 py-1">{{ old('objetivo') }}</textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Información Relevante</label>
        <textarea name="info_relevante" rows="3" class="w-full border rounded px-2 py-1">{{ old('info_relevante') }}</textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Composición</label>
        <textarea name="composicion" rows="4" class="w-full border rounded px-2 py-1">{{ old('composicion') }}</textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Resultados</label>
        <textarea name="resultados" rows="3" class="w-full border rounded px-2 py-1">{{ old('resultados') }}</textarea>
      </div>
    </div>
  </div>

  {{-- =================== Información de autorización =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Información de autorización</div>
    <div class="p-3">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-start">
        <div>
          <label class="block text-sm font-semibold">Solvencia técnico-científica</label>
          <label class="inline-flex items-center gap-2 mt-1">
            <input type="checkbox" name="autorizacion[solvencia]" value="1" {{ old('autorizacion.solvencia') ? 'checked' : '' }}>
            Cumple
          </label>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Observaciones</label>
          <input type="text" name="autorizacion[solvencia_obs]" class="w-full border rounded px-2 py-1" value="{{ old('autorizacion.solvencia_obs') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Insumos e infraestructura</label>
          <label class="inline-flex items-center gap-2 mt-1">
            <input type="checkbox" name="autorizacion[insumos]" value="1" {{ old('autorizacion.insumos') ? 'checked' : '' }}>
            Cumple
          </label>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Observaciones</label>
          <input type="text" name="autorizacion[insumos_obs]" class="w-full border rounded px-2 py-1" value="{{ old('autorizacion.insumos_obs') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Modo y tiempo de conservación/uso</label>
          <label class="inline-flex items-center gap-2 mt-1">
            <input type="checkbox" name="autorizacion[modo_tiempo]" value="1" {{ old('autorizacion.modo_tiempo') ? 'checked' : '' }}>
            Cumple
          </label>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Observaciones</label>
          <input type="text" name="autorizacion[modo_tiempo_obs]" class="w-full border rounded px-2 py-1" value="{{ old('autorizacion.modo_tiempo_obs') }}">
        </div>

        <div>
          <label class="block text-sm font-semibold">Viabilidad y control de costos</label>
          <label class="inline-flex items-center gap-2 mt-1">
            <input type="checkbox" name="autorizacion[viabilidad]" value="1" {{ old('autorizacion.viabilidad') ? 'checked' : '' }}>
            Cumple
          </label>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Observaciones</label>
          <input type="text" name="autorizacion[viabilidad_obs]" class="w-full border rounded px-2 py-1" value="{{ old('autorizacion.viabilidad_obs') }}">
        </div>
      </div>
    </div>
  </div>

  {{-- =================== Anexos =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Anexos</div>
    <div class="p-3">
      <textarea name="anexos" rows="4" class="w-full border rounded px-2 py-1" placeholder="Referencias, documentos adjuntos, etc.">{{ old('anexos') }}</textarea>
    </div>
  </div>

  {{-- =================== Firmas =================== --}}
  <div class="border rounded">
    <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Firmas</div>
    <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-semibold">(18) Formuló</label>
        <input type="text" name="firmo_nombre" class="w-full border rounded px-2 py-1" value="{{ old('firmo_nombre') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">(19) Revisó</label>
        <input type="text" name="reviso_nombre" class="w-full border rounded px-2 py-1" value="{{ old('reviso_nombre') }}">
      </div>
      <div>
        <label class="block text-sm font-semibold">(20) Autorizó</label>
        <input type="text" name="autorizo_nombre" class="w-full border rounded px-2 py-1" value="{{ old('autorizo_nombre') }}">
      </div>
    </div>
  </div>

  <div class="flex justify-end gap-2">
    <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
    <x-button type="submit" form="solicitud-formulacion-form" formtarget="_blank" class="px-3 py-2 rounded text-white" style="background:#16a34a;">Generar PDF</x-button>
  </div>
</form>
</x-modal>