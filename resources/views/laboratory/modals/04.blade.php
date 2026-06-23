<x-modal id="04">
  <form id="lab-sample-form" method="POST" action="{{ route('laboratory_samples.inv') }}" class="space-y-6">
    @csrf

    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">Nueva muestra de laboratorio</h3>
      <button type="button" class="modal-close text-gray-500 hover:text-gray-700" data-modal="#04">✕</button>
    </div>

    <div id="lab-errors" class="hidden rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700"></div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Identificación</div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
          <div class="md:col-span-1">
              <label class="block text-sm font-semibold">Folio</label>
              <input type="text" 
                    name="folio_muestra" 
                    id="folio_muestra" 
                    class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-green-500" 
                    placeholder="Escriba el folio..."
                    value="{{ old('folio_muestra') }}">
            </div>
      <div class="md:col-span-1">
        <label class="block text-sm font-semibold">Tipo de muestra *</label>
        <select name="tipo_muestra" id="tipo_muestra" class="w-full border rounded px-2 py-1" required>
          <option value="">Seleccione…</option>
          @php
            $tipos = [
              'Muestra almacen',
              'Muestra maquiladora',
              'Muestra producción',
              'Muestra proveedores',
              'Muestra SUCCESS',
              'Muestras alérgenos',
              'Muestras comerciales',
              'Muestras en inspección',
              'Muestras levadura',
              'Muestras levadura colorante',
              'Muestras proveedores',
              'N/A',
              '(Vacías)',
            ];
          @endphp

          @foreach ($tipos as $t)
            <option value="{{ $t }}" @selected(old('tipo_muestra') === $t)>{{ $t }}</option>
          @endforeach
        </select>
      </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Proveedor</label>

          @isset($suppliers)
            <select id="proveedor_select" class="w-full border rounded px-2 py-1">
              <option value="">Seleccione proveedor</option>
              @foreach ($suppliers as $s)
                <option value="{{ $s->name }}">{{ $s->name }}</option>
              @endforeach
            </select>
            <input type="hidden" name="proveedor" id="proveedor_input">
          @else
            <input type="text" name="proveedor" class="w-full border rounded px-2 py-1">
          @endisset
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Producto</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Producto *</label>
          @isset($products)
            <select id="producto_select" class="w-full border rounded px-2 py-1" required>
              <option value="">Seleccione producto</option>
              @foreach ($products as $p)
                <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-sku="{{ $p->sku }}">
                  {{ $p->name }}
                </option>
              @endforeach
            </select>
            <input type="hidden" name="producto" id="producto_input">
          @else
            <input type="text" name="producto" class="w-full border rounded px-2 py-1" required>
          @endisset
        </div>

        <div>
          <label class="block text-sm font-semibold">SKU</label>
          @isset($products)
            <input type="text" name="sku" id="sku_input" class="w-full border rounded px-2 py-1" readonly>
          @else
            <input type="text" name="sku" class="w-full border rounded px-2 py-1">
          @endisset
        </div>

      {{-- ===== Presentación ===== --}}
        <div>
          <label class="block text-sm font-semibold">Presentación</label>
          <input type="text"
                name="presentacion"
                id="presentacion"
                class="w-full border rounded px-2 py-1"
                list="presentacion_opts"
                value="{{ old('presentacion') }}">

          <datalist id="presentacion_opts">
            @php
              $presentaciones = [
                'Bidón de plástico',
                'Bolsa de plástico',
                'Bolsa metalizada',
                'Bolsa resellable transparente',
                'Bolsa sellable',
                'Bolsa sellada',
                'Bolsa whirl pak',
                'Bolsa ziploc',
                'Botella plástico',
                'Caja de cartón',
                'Clamshell plástico',
                'Costal',
                'Frasco de plástico',
                'Frasco vidrio',
                'Garrafa de 5 L',
                'Saco kraft',
                'Tubo de centrífuga',
                '(Vacías)',
              ];
            @endphp
            @foreach ($presentaciones as $p)
              <option value="{{ $p }}"></option>
            @endforeach
          </datalist>
        </div>

        {{-- ===== Ubicación stock===== --}}
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Ubicación stock</label>
          <input type="text"
                name="ubicacion_stock"
                id="ubicacion_stock"
                class="w-full border rounded px-2 py-1"
                list="ubicacion_stock_opts"
                value="{{ old('ubicacion_stock') }}">

          <datalist id="ubicacion_stock_opts">
            @php
              $ubicaciones = [
                'A1 Aceites y extractos','Almacen','Caja muestras proveedores 5','Caja muestras proveedores1',
                'Caja muestras retención levadura','Caja muestras retención levadura :','Caja muestras SUCCESS',
                'D1 Deshidratados','D2 Deshidratados','E Aceites y extractos','E1 Aceites y extractos',
                'G1 Granos','H1 Harinas','H2 Harinas','H3 Harinas','L1 Levadura','L2 Levadura','L3 Levadura',
                'Laboratorio','M1 minerales y sales','Muestra proveedores','Muestra proveedores 1',
                'Muestra proveedores 2','Muestra retención levadura 2','Muestras exposición','Muestras proveedores 4',
                'Nave 1 K3','P1 Procesados y digestas','Refrigerador','S1 Suelos','Saco kraft','Salieron','Salio',
              ];
            @endphp
            @foreach ($ubicaciones as $u)
              <option value="{{ $u }}"></option>
            @endforeach
          </datalist>
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Movimientos y fechas</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
          <label class="block text-sm font-semibold">Stock inicial (g)</label>
          <input type="number" step="0.01" min="0" name="stock_inicial" id="stock_inicial" class="w-full border rounded px-2 py-1">
        </div>

        {{--<div>
          <label class="block text-sm font-semibold">Cant. salida (g)</label>
          <input type="number" step="0.01" min="0" name="cantidad_salida" id="cantidad_salida" class="w-full border rounded px-2 py-1">
        </div>--}}

        <div>
          <label class="block text-sm font-semibold">Stock final (g)</label>
          <input type="number" step="0.01" min="0" id="stock_final_view" class="w-full border rounded px-2 py-1" readonly>
        </div>

        <div>
          <label class="block text-sm font-semibold">F. entrada a lab</label>
          <input type="date" name="fecha_entrada" class="w-full border rounded px-2 py-1">
        </div>

        {{--
        <div>
          <label class="block text-sm font-semibold">F. salida de lab</label>
          <input type="date" name="fecha_salida" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold">Motivo de salida</label>
          <select name="motivo_salida" class="w-full border rounded px-2 py-1">
            <option value="">—</option>
            <option value="cliente">Cliente</option>
            <option value="analisis">Análisis</option>
            <option value="desarrollo">Desarrollo</option>
            <option value="caducado">Caducado</option>
            <option value="exposicion">Exposición</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        --}}
      </div>
    </div>

    <div class="border rounded">
      <div class="px-3 py-2 font-semibold text-white" style="background:#16a34a;">Responsables</div>
      <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3">
        {{--<div>
          <label class="block text-sm font-semibold">Solicitante</label>
          <input type="text" name="solicitante" class="w-full border rounded px-2 py-1">
        </div>--}}
        <div>
          <label class="block text-sm font-semibold">Recolector</label>
          <input type="text" name="recolector" class="w-full border rounded px-2 py-1">
        </div>
       {{-- <div>
          <label class="block text-sm font-semibold">Cliente</label>
          @isset($customers)
            <select name="cliente" id="cliente" class="w-full border rounded px-2 py-1">
              <option value="">Seleccione cliente</option>
              @foreach ($customers as $c)
                <option value="{{ $c->name }}" @selected(old('cliente') === $c->name)>{{ $c->name }}</option>
              @endforeach
            </select>
          @else
            <input type="text" name="cliente" class="w-full border rounded px-2 py-1" value="{{ old('cliente') }}">
          @endisset
        </div>--}}
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <x-button type="button" class="modal-close px-4 py-2 rounded border" data-modal="#04">Cancelar</x-button>
      <x-button type="submit" class="px-4 py-2 rounded text-white" style="background:#16a34a;">Guardar</x-button>
    </div>
  </form>

  {{-- JS --}}
  <script>
    (function () {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      $('#proveedor_select').on('change', function () {
        $('#proveedor_input').val($(this).val());
      });

      $('#producto_select').on('change', function () {
        const opt = this.selectedOptions[0];
        const nombre = opt ? opt.getAttribute('data-name') : '';
        const sku    = opt ? opt.getAttribute('data-sku')  : '';
        $('#producto_input').val(nombre);
        $('#sku_input').val(sku);
      });

      function calcStockFinal() {
        const ini = parseFloat($('#stock_inicial').val()) || 0;
        const sal = parseFloat($('#cantidad_salida').val()) || 0;
        let fin = ini - sal;
        if (fin < 0) fin = 0;
        fin = Math.round(fin * 100) / 100;
        $('#stock_final_view').val(fin.toFixed(2));
      }
      $('#stock_inicial, #cantidad_salida').on('input', calcStockFinal);

      // Submit del form
      $('#lab-sample-form').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const url   = $form.attr('action');
        const data  = $form.serialize();

        $('#lab-errors').addClass('hidden').empty();

        $.post(url, data)
          .done(function (res) {
            if (window.Swal) {
              Swal.fire({ icon: 'success', title: 'Guardado', text: (res?.message || 'Registro guardado correctamente.') });
            }
            $form[0].reset();
            $('#stock_final_view').val('');
            hideModal('#04');

      
          })
          .fail(function (xhr) {
            if (xhr.status === 422) {
              const errs = xhr.responseJSON?.errors || {};
              const $box = $('#lab-errors').removeClass('hidden').empty();
              $box.append('<ul class="list-disc pl-5"></ul>');
              const $ul = $box.find('ul');
              Object.keys(errs).forEach(function (k) {
                (errs[k] || []).forEach(function (msg) {
                  $ul.append('<li>' + msg + '</li>');
                });
              });
            } else {
              if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar. Intenta nuevamente.' });
              }
            }
          });
      });

      // open / close modal
      function hideModal(sel) {
        const $m = $(sel);
        $m.addClass('hidden').attr('aria-hidden', 'true');
        $('body').removeClass('overflow-hidden');
        $('#lab-errors').addClass('hidden').empty();
        $('#stock_final_view').val('');
      }
      function showModal(sel) {
        const $m = $(sel);
        $m.removeClass('hidden').attr('aria-hidden', 'false');
        $('body').addClass('overflow-hidden');
      }

      $(document).on('click', '.modal-close', function (e) {
        e.preventDefault();
        const target = $(this).data('modal') || '#04';
        hideModal(target);
      });

      $('#04').on('click', function (e) {
        if (e.target === this) hideModal('#04');
      });

      $(document).on('keydown', function (e) {
        if (e.key === 'Escape') hideModal('#04');
      });

      $(document).on('click', '[data-open="#04"]', function (e) {
        e.preventDefault();
        showModal('#04');
      });
    })();
  </script>
</x-modal>
