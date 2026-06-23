<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Material Inventory
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="material-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4 text-left">NOMBRE</th>
          <th class="px-6 py-4 text-left">ENTRADAS</th>
          <th class="px-6 py-4 text-left">SALIDAS</th>
          <th class="px-6 py-4 text-left font-bold text-blue-600">STOCK (CANTIDAD)</th>
          <th class="px-6 py-4 text-left">UM</th>
          <th class="px-6 py-4 text-left">MARCA</th>
          <th class="px-6 py-4 text-right">ACCIONES</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>
  </div>

  <!-- === edit=== -->
  <x-modal id="edit-material-modal">
    <form id="edit-material-form" class="space-y-4">
      @csrf
      <input type="hidden" name="_method" value="PUT">
      <input type="hidden" id="edit-mat-id" name="id" value="">

      <x-wrapper-form-1>
        <x-tittle-form>Editar Material</x-tittle-form>
      </x-wrapper-form-1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold">Nombre</label>
          <input type="text" id="edit-mat-name" name="name" class="w-full border rounded px-2 py-1" required>
        </div>

        <div>
          <label class="block text-sm font-semibold">UM</label>
          <input type="text" id="edit-mat-um" name="um" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold">Marca</label>
          <input type="text" id="edit-mat-brand" name="brand" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold text-green-700">Entradas</label>
          <input type="number" step="0.01" min="0" id="edit-mat-entries" name="entries" class="w-full border border-green-300 rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold text-red-700">Salidas</label>
          <input type="number" step="0.01" min="0" id="edit-mat-exits" name="exits" class="w-full border border-red-300 rounded px-2 py-1">
        </div>

        <div class="md:col-span-2 bg-blue-50 p-2 rounded">
          <label class="block text-sm font-bold text-blue-800">Stock Actual</label>
          <input type="number" step="0.01" min="0" id="edit-mat-stock" name="stock" class="w-full border border-blue-300 bg-gray-100 rounded px-2 py-1 font-bold" readonly>
          <p class="text-xs text-gray-500 mt-1">Se recalcula automáticamente.</p>
        </div>
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <x-button type="button" class="close-mat-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
          Cancelar
        </x-button>
        <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
          Guardar cambios
        </x-button>
      </div>
    </form>
  </x-modal>

  <script>
    function renderMaterialActions(data, type, row) {
      const id = row.id ?? '';
      return `
        <button data-id="${id}" class="edit-material-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Editar">
          <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
        </button>
        <button data-id="${id}" class="delete-material-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Eliminar">
          <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
        </button>
      `;
    }

    document.addEventListener('DOMContentLoaded', function () {
      const matTable = $('#material-table').DataTable({
        ajax: {
          url: "{{ route('materials.index') }}", 
          dataSrc: function(json) { return json.data || json; },
          headers: { 'Accept': 'application/json' }
        },
        columns: [
          { data: 'name' },
          { data: 'entries', render: (d) => `<span class="text-green-600">${d ?? 0}</span>` },
          { data: 'exits', render: (d) => `<span class="text-red-600">${d ?? 0}</span>` },
          { data: 'stock', render: (d) => `<span class="font-bold text-blue-600">${d ?? 0}</span>` },
          { data: 'um', render: (d) => d || '—' },
          { data: 'brand', render: (d) => d || '—' },
          { data: null, render: renderMaterialActions, orderable: false, searchable: false, className: 'text-right' }
        ],
        lengthChange: false,
        pageLength: 10,
        language: {
          info: "Showing _START_ to _END_ of _TOTAL_ materials",
          infoEmpty: "No materials available",
          zeroRecords: "No matching records found",
          paginate: { next: "Next", previous: "Previous" }
        }
      });

      const modalRoot = document.getElementById('edit-material-modal');
      const form = document.getElementById('edit-material-form');
      const entInp = document.getElementById('edit-mat-entries');
      const exiInp = document.getElementById('edit-mat-exits');
      const stkInp = document.getElementById('edit-mat-stock');

      function recalcStock() {
        const stock = (parseFloat(entInp.value) || 0) - (parseFloat(exiInp.value) || 0);
        if(stkInp) stkInp.value = stock >= 0 ? stock.toFixed(2) : 0;
      }

      entInp?.addEventListener('input', recalcStock);
      exiInp?.addEventListener('input', recalcStock);

      // editar
      $(document).on('click', '.edit-material-btn', async function() {
        const id = this.dataset.id;
        try {
          const resp = await fetch(`/laboratory/materials/${id}/edit`);
          if (!resp.ok) throw new Error(`Error ${resp.status}`);
          const data = await resp.json();

          document.getElementById('edit-mat-id').value = data.id;
          document.getElementById('edit-mat-name').value = data.name;
          document.getElementById('edit-mat-um').value = data.um;
          document.getElementById('edit-mat-brand').value = data.brand;
          entInp.value = data.entries;
          exiInp.value = data.exits;
          stkInp.value = data.stock;

          modalRoot.style.display = 'flex';
          modalRoot.classList.add('open');
        } catch (err) {
          Swal.fire('Error', 'Could not load the data.', 'error');
        }
      });

      form.addEventListener('submit', async function(ev){
        ev.preventDefault();
        const id = document.getElementById('edit-mat-id').value;
        recalcStock();
        
        try {
          const resp = await fetch(`/laboratory/materials/${id}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: new FormData(form)
          });

          if(!resp.ok) throw new Error('Error saving record');

          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';
          matTable.ajax.reload(null, false);
          Swal.fire({ title: 'Success!', text: 'Record updated successfully.', icon: 'success', timer: 1200, showConfirmButton: false });

        } catch (e) {
          Swal.fire('Error', 'Could not save the changes.', 'error');
        }
      });

      document.addEventListener('click', function(e){
        if(e.target.closest('#edit-material-modal .close-mat-modal')){
          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';
        }
      });

      // Eliminar
      $(document).on('click', '.delete-material-btn', async function() {
        const id = this.dataset.id;
        const res = await Swal.fire({
          title: 'Delete material?',
          text: "This action cannot be undone.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it',
          cancelButtonText: 'Cancel',
          confirmButtonColor: '#ef4444'
        });

        if (!res.isConfirmed) return;

        try {
          const resp = await fetch(`/laboratory/materials/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          });
          if (!resp.ok) throw new Error();
          matTable.ajax.reload(null, false);
          Swal.fire('Deleted!', 'Record deleted successfully.', 'success');
        } catch (err) {
          Swal.fire('Error', 'Could not delete the material.', 'error');
        }
      });
    });
  </script>
</section>