<x-modal id="edit-reagent-modal">
  <form id="edit-reagent-form" class="space-y-4">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" id="edit-reagent-id" name="id" value="">

    <x-wrapper-form-1>
      <x-tittle-form>Edit Reagent</x-tittle-form>
    </x-wrapper-form-1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-semibold">Code</label>
        <input type="text" id="edit-code" name="code" class="w-full border rounded px-2 py-1" required> 
      </div>

      <div>
        <label class="block text-sm font-semibold">UM (Unit)</label>
        <input type="text" id="edit-um" name="um" class="w-full border rounded px-2 py-1">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Name</label>
        <input type="text" id="edit-name" name="name" class="w-full border rounded px-2 py-1" required>
      </div>

      <div>
        <label class="block text-sm font-semibold">Brand</label>
        <input type="text" id="edit-brand" name="brand" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold">Color</label>
        <input type="text" id="edit-color" name="color" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold text-green-700">Entradas</label>
        <input type="number" step="0.01" min="0" id="edit-entries" name="entries" class="w-full border border-green-300 rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold text-red-700">Salidas</label>
        <input type="number" step="0.01" min="0" id="edit-exits" name="exits" class="w-full border border-red-300 rounded px-2 py-1">
      </div>

      <div class="md:col-span-2 bg-blue-50 p-2 rounded">
        <label class="block text-sm font-bold text-blue-800">Stock Final</label>
        <input type="number" step="0.01" min="0" id="edit-stock" name="stock" class="w-full border border-blue-300 bg-gray-100 rounded px-2 py-1 font-bold" readonly>
        <p class="text-xs text-gray-500 mt-1">Se recalcula automáticamente (Entradas - Salidas).</p>
      </div>
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
        Cancelar
      </x-button>

      <x-button type="submit" id="edit-reagent-save" class="bg-green-600 hover:bg-green-700 text-white">
        Guardar cambios
      </x-button>
    </div>
  </form>

  <script>
    (function(){
      const modalRoot = document.getElementById('edit-reagent-modal');
      const form = document.getElementById('edit-reagent-form');
      if(!modalRoot || !form) return;

      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

      const entInp = document.getElementById('edit-entries');
      const exiInp = document.getElementById('edit-exits');
      const stkInp = document.getElementById('edit-stock');

      function n(v){
        if (v === null || v === undefined || v === '') return 0;
        const x = parseFloat(String(v).replace(',', '.'));
        return isNaN(x) ? 0 : x;
      }

      function recalcStock(){
        const stock = n(entInp?.value) - n(exiInp?.value);
        if(stkInp) stkInp.value = (stock >= 0 ? stock.toFixed(2) : 0);
      }

      entInp?.addEventListener('input', recalcStock);
      exiInp?.addEventListener('input', recalcStock);

      form.addEventListener('submit', async function(ev){
        ev.preventDefault();

        const id = document.getElementById('edit-reagent-id')?.value;
        if(!id){
          Swal?.fire?.('Oops', 'ID not found', 'warning');
          return;
        }

        recalcStock();

        const fd = new FormData(form);

        const url = `/laboratory/reagents/${id}`;

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
            } catch {
              const t = await resp.text();
              if(t) msg = t;
            }
            throw new Error(msg);
          }

          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';

          $('#reagent-table').DataTable().ajax.reload(null, false);

          Swal?.fire?.({ title:'Success!', text:'Record updated successfully.', icon:'success', timer:1200, showConfirmButton:false });

        } catch (e) {
          console.error('[Reagent] save edit error:', e);
          Swal?.fire?.('Error', e.message || 'Could not save the changes.', 'error');
        }
      });

      document.addEventListener('click', function(e){
        if(e.target.closest('#edit-reagent-modal .close-modal')){
          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';
        }
      });
    })();
  </script>
</x-modal>