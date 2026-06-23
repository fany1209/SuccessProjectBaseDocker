<x-modal id="qj">
  <form id="qys12-form" method="POST" action="{{ route('quality.pdf12') }}" class="w-full space-y-6">
    @csrf

    <div class="flex justify-end">
      <div class="w-full md:w-1/3">
        <x-label value="Fecha de llenado"/>
        <input type="date" name="fecha" value="{{ old('fecha') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        @error('fecha') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- ======= TIPO DE SOLICITUD ======= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Tipo de solicitud</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="tipo" value="queja" {{ old('tipo')=='queja' ? 'checked' : '' }} required>
          <span>Queja</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="tipo" value="reclamo" {{ old('tipo')=='reclamo' ? 'checked' : '' }} required>
          <span>Reclamo</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="tipo" value="sugerencia" {{ old('tipo')=='sugerencia' ? 'checked' : '' }} required>
          <span>Sugerencia</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="tipo" value="felicitacion" {{ old('tipo')=='felicitacion' ? 'checked' : '' }} required>
          <span>Felicitación</span>
        </label>
      </div>
      @error('tipo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ======= DATOS DE IDENTIFICACIÓN ======= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Datos de identificación</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <x-label value="Nombre y apellidos"/>
          <input type="text" name="nombre" value="{{ old('nombre') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2" required>
          @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
          <x-label value="Tipo de persona"/>
          <div class="flex flex-wrap gap-x-6 gap-y-2">
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="persona" value="cliente" {{ old('persona')=='cliente' ? 'checked' : '' }} required> <span>Cliente</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="persona" value="proveedor" {{ old('persona')=='proveedor' ? 'checked' : '' }} required> <span>Proveedor</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="persona" value="trabajador" {{ old('persona')=='trabajador' ? 'checked' : '' }} required> <span>Trabajador</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="persona" value="visita" {{ old('persona')=='visita' ? 'checked' : '' }} required> <span>Visita</span>
            </label>
          </div>
          @error('persona') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
          <x-label value="Empresa"/>
          <input type="text" name="empresa" value="{{ old('empresa') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
          @error('empresa') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <x-label value="Área"/>
          <input type="text" name="area" value="{{ old('area') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
          @error('area') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <x-label value="Puesto"/>
          <input type="text" name="puesto" value="{{ old('puesto') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
          @error('puesto') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <x-label value="Correo"/>
          <input type="email" name="correo" value="{{ old('correo') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
          @error('correo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <x-label value="Teléfono"/>
          <input type="text" name="telefono" value="{{ old('telefono') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
          @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- ======= MOTIVO ======= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Motivo</h2>

      <div class="space-y-2">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="motivos[]" value="calidad_producto" {{ in_array('calidad_producto', old('motivos', [])) ? 'checked' : '' }}>
          <span>Calidad del producto</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="motivos[]" value="plazo_entrega" {{ in_array('plazo_entrega', old('motivos', [])) ? 'checked' : '' }}>
          <span>Plazo de entrega</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="motivos[]" value="soporte_tecnico" {{ in_array('soporte_tecnico', old('motivos', [])) ? 'checked' : '' }}>
          <span>Soporte técnico</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="motivos[]" value="atencion_personal" {{ in_array('atencion_personal', old('motivos', [])) ? 'checked' : '' }}>
          <span>Atención del personal</span>
        </label>

        <div class="flex items-center gap-2">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" id="motivo_otro_chk" name="motivos[]" value="otro" {{ in_array('otro', old('motivos', [])) ? 'checked' : '' }}>
            <span>Otro</span>
          </label>
          <input type="text" id="motivo_otro_input" name="motivo_otro" placeholder="Especifique"
                 value="{{ old('motivo_otro') }}"
                 class="flex-1 rounded-md border border-gray-300 px-3 py-2"
                 {{ in_array('otro', old('motivos', [])) ? '' : 'disabled' }}>
        </div>
      </div>
      @error('motivos') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      @error('motivo_otro') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ======= DESCRIPCIÓN ======= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Descripción de la queja / sugerencia</h2>
      <p class="text-sm text-gray-600 mb-2">* Describa la incidencia colocando la fecha en que sucedió *</p>
      <textarea name="descripcion" rows="6" class="w-full rounded-md border border-gray-300 px-3 py-2" required>{{ old('descripcion') }}</textarea>
      @error('descripcion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- ======= RESPUESTA POR EMAIL ======= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">¿Desea recibir respuesta por email?</h2>
      <div class="flex gap-6">
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="respuesta_email" value="si" {{ old('respuesta_email')=='si' ? 'checked' : '' }} required>
          <span>Sí</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="respuesta_email" value="no" {{ old('respuesta_email')=='no' ? 'checked' : '' }} required>
          <span>No</span>
        </label>
      </div>
      @error('respuesta_email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      <p class="text-xs text-gray-500 mt-2">
        * Para poder responder por email, es necesario capturar el campo “Correo”.
      </p>
    </div>

    <div class="flex justify-end">
        <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
        <x-button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Generar PDF</x-button>
    </div>
  </form>
</x-modal>

@push('js')
<script>
  const chkOtro  = document.getElementById('motivo_otro_chk');
  const inpOtro  = document.getElementById('motivo_otro_input');
  if (chkOtro) {
    chkOtro.addEventListener('change', function(){
      inpOtro.disabled = !this.checked;
      if (!this.checked) inpOtro.value = '';
    });
  }
</script>
@endpush
