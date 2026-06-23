<x-modal id="rc">
  <form id="client-feedback-form"
        method="POST"
        action="{{ route('quality.pdf9') }}"
        enctype="multipart/form-data"
        class="w-full space-y-6">
    @csrf

    {{-- ================== FECHA ================== --}}
    <div class="border rounded-lg p-4 space-y-2">
      <h2 class="font-semibold">Fecha</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <x-label value="Fecha (del documento)"/>
          <input type="date" name="fecha" class="w-full border rounded px-3 py-2"
                 value="{{ old('fecha') }}" required>
        </div>
      </div>
    </div>

    {{-- ================== CLIENTE (solo empresa desde BD) ================== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Información del cliente</h2>

      <div>
        <x-label value="Empresa"/>
        <select id="customer_id" name="customer_id"
                class="w-full border rounded px-3 py-2" required>
          <option value="">-- Selecciona un cliente --</option>
          @foreach($customers as $c)
            <option value="{{ $c->customer_id }}"
                    data-name="{{ $c->name }}"
                    {{ old('customer_id') == $c->customer_id ? 'selected' : '' }}>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
        @error('customer_id')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Los demás campos se capturan normal (NO vienen de BD) --}}
      <div class="md:col-span-2">
        <x-label value="Nombre del contacto"/>
        <input type="text" id="contacto" name="contacto"
               class="w-full border rounded px-3 py-2"
               value="{{ old('contacto') }}">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <x-label value="Teléfono"/>
          <input type="text" id="telefono" name="telefono"
                 class="w-full border rounded px-3 py-2"
                 value="{{ old('telefono') }}">
        </div>
        <div>
          <x-label value="Correo"/>
          <input type="email" id="correo" name="correo"
                 class="w-full border rounded px-3 py-2"
                 value="{{ old('correo') }}">
        </div>
      </div>

      <div class="md:col-span-2">
        <x-label value="Dirección"/>
        <input type="text" id="direccion" name="direccion"
               class="w-full border rounded px-3 py-2"
               value="{{ old('direccion') }}">
      </div>
    </div>

    {{-- ================== DETALLES DE LA NO CONFORMIDAD ================== --}}
<div class="border rounded-lg p-4 space-y-4">
  <h2 class="font-semibold">Detalles de la no conformidad</h2>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <x-label value="Fecha de la incidencia"/>
      <input type="date" name="fecha_incidencia" class="w-full border rounded px-3 py-2"
             value="{{ old('fecha_incidencia') }}" required>
    </div>
    <div>
      <x-label value="Hora de la incidencia"/>
      <input type="time" name="hora_incidencia" class="w-full border rounded px-3 py-2"
             value="{{ old('hora_incidencia') }}">
    </div>
    <div>
      <x-label value="Producto o servicio"/>
      <input type="text" name="producto_servicio" class="w-full border rounded px-3 py-2"
             value="{{ old('producto_servicio') }}" required>
    </div>
  </div>

  <div>
    <x-label value="Descripción detallada"/>
    <textarea name="descripcion" rows="4" class="w-full border rounded px-3 py-2" required>{{ old('descripcion') }}</textarea>
  </div>

  {{-- ====== EVIDENCIAS (IMÁGENES) ====== --}}
  <div>
    <x-label value="Evidencias (imágenes)"/>
    <input type="file"
           name="evidencias[]"
           id="evidencias"
           accept="image/*"
           multiple
           class="w-full border rounded px-3 py-2">
    <p class="text-xs text-gray-600 mt-2">
      Puedes adjuntar hasta 6 imágenes (JPG, PNG, WEBP; máx 3 MB cada una).
    </p>
    <div id="ev-preview" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
  </div>
</div>

  {{-- ================== IMPACTO ================== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Impacto</h2>
      <div>
        <x-label value="¿Cómo afectó al cliente?"/>
        <textarea name="impacto" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('impacto') }}</textarea>
      </div>
      <div>
        <x-label value="Importancia para el cliente"/>
        <div class="flex items-center gap-6 mt-1">
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="importancia" value="Baja" class="accent-green-600"
                   {{ old('importancia')==='Baja' ? 'checked' : '' }} required>
            <span>Baja</span>
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="importancia" value="Media" class="accent-green-600"
                   {{ old('importancia')==='Media' ? 'checked' : '' }}>
            <span>Media</span>
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="importancia" value="Alta" class="accent-green-600"
                   {{ old('importancia')==='Alta' ? 'checked' : '' }}>
            <span>Alta</span>
          </label>
        </div>
      </div>
    </div>

    {{-- ================== RESOLUCIÓN SOLICITADA ================== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Resolución solicitada</h2>
      <textarea name="resolucion" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('resolucion') }}</textarea>
    </div>

    {{-- ================== ACCIÓN TOMADA ================== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Acción tomada</h2>
      <div>
        <x-label value="Medidas iniciales tomadas"/>
        <textarea name="medidas" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('medidas') }}</textarea>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <x-label value="Persona responsable"/>
          <input type="text" name="responsable_accion" class="w-full border rounded px-3 py-2"
                 value="{{ old('responsable_accion') }}" required>
        </div>
        <div>
          <x-label value="Fecha de resolución"/>
          <input type="date" name="fecha_resolucion" class="w-full border rounded px-3 py-2"
                 value="{{ old('fecha_resolucion') }}">
        </div>
      </div>
      <div>
        <x-label value="Acción final"/>
        <textarea name="accion_final" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('accion_final') }}</textarea>
      </div>
    </div>

    {{-- ================== COMENTARIOS ADICIONALES ================== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Comentarios adicionales</h2>
      <textarea name="comentarios_adicionales" rows="3" class="w-full border rounded px-3 py-2">{{ old('comentarios_adicionales') }}</textarea>
    </div>

    {{-- ================== FIRMAS ================== --}}
    <div class="border rounded-lg p-4 space-y-4">
      <h2 class="font-semibold">Firmas</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <x-label value="Nombre del Cliente"/>
          <input type="text" name="cliente_firma" class="w-full border rounded px-3 py-2" value="{{ old('cliente_firma') }}">
        </div>
        <div>
          <x-label value="Nombre del Receptor"/>
          <input type="text" name="receptor_firma" class="w-full border rounded px-3 py-2" value="{{ old('receptor_firma') }}">
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Generar PDF</x-button>
    </div>
  </form>
</x-modal>

<script>
  (function () {
    const sel = document.getElementById('customer_id');
    const empresa = document.getElementById('empresa');

    function sortSelectAlphabetically() {
      if (!sel) return;

      const current = sel.value;
      const opts = Array.from(sel.options);
      const head = (opts[0] && (opts[0].value === '' || opts[0].disabled)) ? opts.shift() : null;

      opts.sort((a, b) => {
        const ak = (a.dataset.name || a.textContent || '').trim();
        const bk = (b.dataset.name || b.textContent || '').trim();
        return ak.localeCompare(bk, 'es', { sensitivity: 'base', numeric: true });
      });

      sel.innerHTML = '';
      if (head) sel.appendChild(head);
      opts.forEach(o => sel.appendChild(o));

      if (current !== null && current !== undefined) sel.value = current;
    }

    function apply() {
      const opt = sel.options[sel.selectedIndex];
      empresa.value = opt ? (opt.getAttribute('data-name') || '') : '';
    }

    sel?.addEventListener('change', apply);

    document.addEventListener('DOMContentLoaded', function () {
      sortSelectAlphabetically();
      if (sel && sel.value) apply();
    });
  })();

  (function () {
    const input = document.getElementById('evidencias');
    const preview = document.getElementById('ev-preview');

    function clearPreview() {
      preview.innerHTML = '';
    }

    function createThumb(src, name) {
      const wrap = document.createElement('div');
      wrap.className = 'border rounded p-2';
      const img = document.createElement('img');
      img.src = src;
      img.alt = name || '';
      img.className = 'w-full h-28 object-cover';
      const cap = document.createElement('p');
      cap.className = 'text-xs mt-1 truncate';
      cap.textContent = name || '';
      wrap.appendChild(img);
      wrap.appendChild(cap);
      return wrap;
    }

    input?.addEventListener('change', () => {
      clearPreview();
      const files = Array.from(input.files || []);
      files.slice(0, 6).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
          preview.appendChild(createThumb(e.target.result, file.name));
        };
        reader.readAsDataURL(file);
      });
    });
  })();
</script>

