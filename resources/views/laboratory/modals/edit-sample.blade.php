<x-modal id="edit-lab-sample">
  <form id="edit-lab-sample-form" class="space-y-4">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" id="edit-lab-id" name="id" value="">

    <x-wrapper-form-1>
      <x-tittle-form>Edit Laboratory Sample</x-tittle-form>
    </x-wrapper-form-1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

      <div>
        <label class="block text-sm font-semibold">Folio</label>
        <input type="text" id="edit-folio" name="folio" class="w-full border rounded px-2 py-1" readonly>
      </div>

      <div>
        <label class="block text-sm font-semibold">Tipo de muestra</label>
        <input type="text" id="edit-tipo_muestra" name="tipo_muestra" class="w-full border rounded px-2 py-1">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Producto</label>
        <input type="text" id="edit-producto" name="producto" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">SKU</label>
        <input type="text" id="edit-sku" name="sku" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Ubicación</label>
        <input type="text" id="edit-ubicacion_stock" name="ubicacion_stock" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Stock inicial (g)</label>
        <input type="number" step="0.01" min="0" id="edit-stock_inicial" name="stock_inicial" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Salida (g)</label>
        <input type="number" step="0.01" min="0" id="edit-cantidad_salida" name="cantidad_salida" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Stock final (g)</label>
        <input type="number" step="0.01" min="0" id="edit-stock_final" name="stock_final" class="w-full border rounded px-2 py-1" readonly>
        <p class="text-xs text-gray-500 mt-1">Se recalcula automáticamente (inicial - salida).</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">F. Entrada</label>
        <input type="date" id="edit-fecha_entrada" name="fecha_entrada" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">F. Salida</label>
        <input type="date" id="edit-fecha_salida" name="fecha_salida" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Estatus</label>
        <select id="edit-status" name="status" class="w-full border rounded px-2 py-1">
          <option value="En laboratorio">En laboratorio</option>
          <option value="Fuera de laboratorio">Fuera de laboratorio</option>
        </select>
      </div>

     {{-- <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Proveedor</label>
        <input type="text" id="edit-proveedor" name="proveedor" class="w-full border rounded px-2 py-1">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Presentación</label>
        <input type="text" id="edit-presentacion" name="presentacion" class="w-full border rounded px-2 py-1">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Motivo de salida</label>
        <input type="text" id="edit-motivo_salida" name="motivo_salida" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Solicitante</label>
        <input type="text" id="edit-solicitante" name="solicitante" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Recolector</label>
        <input type="text" id="edit-recolector" name="recolector" class="w-full border rounded px-2 py-1">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Cliente</label>
        <input type="text" id="edit-cliente" name="cliente" class="w-full border rounded px-2 py-1">
      </div>--}}
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
        Cancelar
      </x-button>

      <x-button type="submit" id="edit-lab-save" class="bg-green-600 hover:bg-green-700 text-white">
        Guardar cambios
      </x-button>
    </div>
  </form>

  <script>
    (function(){
      const modalRoot = document.getElementById('edit-lab-sample');
      const form = document.getElementById('edit-lab-sample-form');
      if(!modalRoot || !form) return;

      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

      const iniInp = document.getElementById('edit-stock_inicial');
      const salInp = document.getElementById('edit-cantidad_salida');
      const finInp = document.getElementById('edit-stock_final');

      function n(v){
        if (v === null || v === undefined || v === '') return 0;
        const x = parseFloat(String(v).replace(',', '.'));
        return isNaN(x) ? 0 : x;
      }

      function recalcFinal(){
        const fin = n(iniInp?.value) - n(salInp?.value);
        if(finInp) finInp.value = (fin >= 0 ? fin : 0);
      }

      iniInp?.addEventListener('input', recalcFinal);
      salInp?.addEventListener('input', recalcFinal);
      form.addEventListener('submit', async function(ev){
        ev.preventDefault();

        const id = document.getElementById('edit-lab-id')?.value;
        if(!id){
          Swal?.fire?.('Ups', 'No se encontró el ID', 'warning');
          return;
        }

        recalcFinal();

        const fd = new FormData(form);

        fd.delete('folio');
        fd.delete('stock_final');

        const url = form.dataset.updateUrl || (`/laboratory/lab-samples/${id}`);

        try {
          const resp = await fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json'
            },
            body: fd
          });

          if(!resp.ok){
            let msg = `Error ${resp.status}`;
            try {
              const j = await resp.json();
              msg = j.message || msg;
              if(j.errors){
                const first = Object.values(j.errors)[0];
                if(Array.isArray(first) && first[0]) msg = first[0];
              }
            } catch {
              const t = await resp.text();
              if(t) msg = t;
            }
            throw new Error(msg);
          }

          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';

          if (window.labTable?.ajax) {
            window.labTable.ajax.reload(null, false);
          } else if ($.fn.dataTable.isDataTable('#lab-samples-table')) {
            $('#lab-samples-table').DataTable().ajax.reload(null, false);
          }

          Swal?.fire?.({ title:'OK', text:'Registro actualizado.', icon:'success', timer:1200, showConfirmButton:false });

        } catch (e) {
          console.error('[Lab] save edit error:', e);
          Swal?.fire?.('Error', e.message || 'No se pudo guardar', 'error');
        }
      });

      document.addEventListener('click', function(e){
        if(e.target.closest('.close-modal')){
          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';
        }
      });

    })();
  </script>
</x-modal>
