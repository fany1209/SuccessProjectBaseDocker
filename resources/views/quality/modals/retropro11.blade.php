<x-modal id="rp">
  <form id="retro-form" method="POST" action="{{ route('quality.pdf11') }}" class="w-full space-y-6"  enctype="multipart/form-data">
    @csrf

    <div class="flex justify-end">
      <div class="w-full md:w-1/3">
        <x-label value="Fecha del registro"/>
        <input type="date" name="fecha" value="{{ old('fecha') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2" required>
      </div>
    </div>

    {{-- ================= INFORMACIÓN DEL PROVEEDOR ================= --}}
    <div class="border rounded-lg p-4">
        <h2 class="font-semibold mb-3">Información del proveedor</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
            <x-label value="Nombre de la empresa"/>
            <select id="supplier" name="supplier_id"
                    class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                <option value="">-- Selecciona un proveedor --</option>
                @foreach($suppliers as $s)
                <option value="{{ $s->supplier_id }}"
                        data-code="{{ $s->supplier_code }}"
                        {{ old('supplier_id')==$s->supplier_id ? 'selected' : '' }}>
                    {{ $s->name }}
                </option>
                @endforeach
            </select>
            <input type="hidden" name="supplier_code" id="supplier_code" value="{{ old('supplier_code') }}">
            <input type="hidden" name="empresa" id="supplier_name" value="{{ old('empresa') }}">
            </div>

            <div>
            <x-label value="Nombre del contacto"/>
            <input type="text" name="contacto" value="{{ old('contacto') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">
            </div>
            <div>
            <x-label value="Teléfono"/>
            <input type="text" name="telefono" value="{{ old('telefono') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">
            </div>
            <div>
            <x-label value="Correo"/>
            <input type="email" name="correo" value="{{ old('correo') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">
            </div>
            <div class="md:col-span-2">
            <x-label value="Dirección"/>
            <input type="text" name="direccion" value="{{ old('direccion') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">
            </div>
        </div>
    </div>

    {{-- ================= DETALLES DE LA NO CONFORMIDAD ================= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Detalles de la no conformidad</h2>

      <div class="space-y-3">
        <div>
          <x-label value="Motivo de la no conformidad"/>
          <input type="text" name="motivo" value="{{ old('motivo') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <x-label value="Fecha"/>
            <input type="date" name="fecha_incidencia" value="{{ old('fecha_incidencia') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2" required>
          </div>
          <div>
            <x-label value="Hora"/>
            <input type="time" name="hora_incidencia" value="{{ old('hora_incidencia') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2">
          </div>
        </div>

        <div>
          <x-label value="Producto o servicio afectado"/>
          <input type="text" name="producto_servicio" value="{{ old('producto_servicio') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
        </div>

        <div>
          <x-label value="Referencia al contrato"/>
          <input type="text" name="referencia_contrato" value="{{ old('referencia_contrato') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
        </div>

       <div class="mt-3">
        <x-label value="Especificaciones incumplidas (texto)"/>
        <textarea name="especificaciones" rows="4"
                  class="w-full rounded-md border border-gray-300 px-3 py-2"
                  placeholder="Describe las especificaciones que no se cumplieron…">{{ old('especificaciones') }}</textarea>
      </div>

    <div class="mt-3">
        <x-label value="Evidencia (imágenes)"/>
            <input type="file" id="especificaciones_imgs" name="especificaciones_imgs[]" 
                    accept="image/*" multiple
                    class="block w-full rounded-md border border-gray-300 px-3 py-2">
        <p class="text-xs text-gray-500 mt-1">
                Puedes subir varias imágenes (JPG/PNG/WebP), tamaño máx. 3 MB c/u.
        </p>
        </div>
            <div id="preview-container" class="mt-3 flex flex-wrap gap-3"></div>
        </div>
    </div>

    {{-- ================= IMPACTO ================= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Impacto</h2>

      <div class="space-y-3">
        <div>
          <x-label value="Consecuencias de la no conformidad en la Producción"/>
          <textarea name="impacto_consecuencias" rows="4"
                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                    placeholder="Describe el impacto en producción…">{{ old('impacto_consecuencias') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <x-label value="¿Implicó un costo adicional?"/>
            <div class="flex items-center gap-6">
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="costo_adicional" value="si" class="accent-green-600" {{ old('costo_adicional')==='si'?'checked':'' }}>
                <span>Sí</span>
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="costo_adicional" value="no" class="accent-green-600" {{ old('costo_adicional','no')==='no'?'checked':'' }}>
                <span>No</span>
              </label>
            </div>
          </div>
          <div>
            <x-label value="¿Implicó retraso de producción?"/>
            <div class="flex items-center gap-6">
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="retraso_produccion" value="si" class="accent-green-600" {{ old('retraso_produccion')==='si'?'checked':'' }}>
                <span>Sí</span>
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="retraso_produccion" value="no" class="accent-green-600" {{ old('retraso_produccion','no')==='no'?'checked':'' }}>
                <span>No</span>
              </label>
            </div>
          </div>
        </div>

        <div>
          <x-label value="Importancia para Success"/>
          <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="importancia" value="baja"  class="accent-green-600" {{ old('importancia')==='baja'?'checked':'' }}>
              <span>Baja</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="importancia" value="media" class="accent-green-600" {{ old('importancia')==='media'?'checked':'' }}>
              <span>Media</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="importancia" value="alta"  class="accent-green-600" {{ old('importancia','alta')==='alta'?'checked':'' }}>
              <span>Alta</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    {{-- ================= ACCIÓN CORRECTIVA ================= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Acción correctiva</h2>

      <div class="space-y-3">
        <div>
          <x-label value="Medidas iniciales tomadas para mitigar el problema (Descripción)"/>
          <textarea name="medidas_iniciales" rows="3"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">{{ old('medidas_iniciales') }}</textarea>
        </div>

        <div>
          <x-label value="Propuesta de acción correctiva por parte del proveedor"/>
          <textarea name="propuesta_accion" rows="4"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">{{ old('propuesta_accion') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <x-label value="Fecha límite para la implementación"/>
            <input type="date" name="fecha_limite" value="{{ old('fecha_limite') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2">
          </div>
          <div>
            <x-label value="Acción final"/>
            <input type="text" name="accion_final" value="{{ old('accion_final') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2">
          </div>
        </div>
      </div>
    </div>

    {{-- ================= SEGUIMIENTO Y VERIFICACIÓN ================= --}}
    <div class="border rounded-lg p-4">
      <h2 class="font-semibold mb-3">Seguimiento y verificación</h2>

      <div class="space-y-3">
        <div>
          <x-label value="Responsable de verificar la implementación"/>
          <input type="text" name="resp_verificacion" value="{{ old('resp_verificacion') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
        </div>

        <div>
          <x-label value="Fecha de la verificación"/>
          <input type="date" name="fecha_verificacion" value="{{ old('fecha_verificacion') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2">
        </div>

        <div>
          <x-label value="Resultados de la verificación"/>
          <textarea name="resultados_verificacion" rows="4"
                    class="w-full rounded-md border border-gray-300 px-3 py-2">{{ old('resultados_verificacion') }}</textarea>
        </div>
      </div>
    </div>

    <div class="border rounded-lg p-4 space-y-4">
        <h2 class="font-semibold">Firmas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
            <x-label value="Nombre y firma del responsable de Success"/>
            <input type="text" name="firma_responsable_success"
                    class="w-full border rounded px-3 py-2"
                    value="{{ old('firma_responsable_success') }}">
            </div>
            <div>
            <x-label value="Nombre y firma del representante del proveedor"/>
            <input type="text" name="firma_representante_proveedor"
                    class="w-full border rounded px-3 py-2"
                    value="{{ old('firma_representante_proveedor') }}">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
        <x-button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Generar PDF</x-button>
    </div>

  </form>
</x-modal>

@push('js')
<script>
    document.getElementById('supplier').addEventListener('change', function () {
    const selected = this.selectedOptions[0];
    const code = selected?.getAttribute('data-code') || '';
    const name = selected?.textContent || '';
    document.getElementById('supplier_code').value = code;
    document.getElementById('supplier_name').value = name;
    });
    let selectedFiles = []; 

    const input = document.getElementById('especificaciones_imgs');
    const preview = document.getElementById('preview-container');

    input.addEventListener('change', function (e) {
    selectedFiles = Array.from(e.target.files); 
    renderPreviews();
    });

    function renderPreviews() {
    preview.innerHTML = '';

    selectedFiles.forEach((file, idx) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
        const wrapper = document.createElement('div');
        wrapper.classList.add('relative', 'inline-block');

        const img = document.createElement('img');
        img.src = ev.target.result;
        img.classList.add('border', 'rounded', 'shadow');
        img.style.maxWidth = '120px';
        img.style.maxHeight = '100px';
        img.style.objectFit = 'contain';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = '✖';
        btn.classList.add('absolute', 'top-0', 'right-0', 'bg-red-600', 'text-white', 'rounded-full', 'w-5', 'h-5', 'text-xs');
        btn.onclick = () => {
            selectedFiles.splice(idx, 1);
            renderPreviews();
        };

        wrapper.appendChild(img);
        wrapper.appendChild(btn);
        preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
    }

    document.getElementById('retro-form').addEventListener('submit', function(e) {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
    });
</script>
@endpush