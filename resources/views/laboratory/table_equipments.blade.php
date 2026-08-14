<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Inventario de Equipos
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="equipment-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4 text-left">CÓDIGO INTERNO</th>
          <th class="px-6 py-4 text-left">NOMBRE</th>
          <th class="px-6 py-4 text-left">MARCA</th>
          <th class="px-6 py-4 text-left">CANTIDAD</th>
          <th class="px-6 py-4 text-left">ESTADO</th>
          <th class="px-6 py-4 text-right">ACCIONES</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>
  </div>

  <!-- === edit modal === -->
  <x-modal id="edit-equipment-modal">
    <form id="edit-equipment-form" class="space-y-4">
      @csrf
      <input type="hidden" name="_method" value="PUT">
      <input type="hidden" id="edit-eq-id" name="id" value="">

      <x-wrapper-form-1>
        <x-tittle-form>Editar Equipo</x-tittle-form>
      </x-wrapper-form-1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-semibold">Código Interno</label>
          <input type="text" id="edit-eq-code" name="internal_code" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold">Nombre</label>
          <input type="text" id="edit-eq-name" name="name" class="w-full border rounded px-2 py-1" required>
        </div>

        <div>
          <label class="block text-sm font-semibold">Marca</label>
          <input type="text" id="edit-eq-brand" name="brand" class="w-full border rounded px-2 py-1">
        </div>

        <div>
          <label class="block text-sm font-semibold text-green-700">Cantidad</label>
          <input type="number" step="0.01" min="0" id="edit-eq-quantity" name="quantity" class="w-full border border-green-300 rounded px-2 py-1">
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-blue-700">Estado del Equipo</label>
          <select id="edit-eq-status" name="status" class="w-full border border-blue-300 rounded px-2 py-1" required>
            <option value="funcional">Funcional</option>
            <option value="en reparacion">En reparación</option>
            <option value="no funciona">No funciona</option>
          </select>
        </div>
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <x-button type="button" class="close-eq-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
          Cancelar
        </x-button>
        <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
          Guardar cambios
        </x-button>
      </div>
    </form>
  </x-modal>

  <script>
    function renderEquipmentActions(data, type, row) {
      const id = row.id ?? '';
      return `
        <button data-id="${id}" class="edit-equipment-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Editar">
          <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
        </button>
        <button data-id="${id}" class="delete-equipment-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Eliminar">
          <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
        </button>
      `;
    }

    document.addEventListener('DOMContentLoaded', function () {
      const eqTable = $('#equipment-table').DataTable({
        ajax: {
          url: "{{ route('equipments.index') }}", 
          dataSrc: function(json) { return json.data || json; },
          headers: { 'Accept': 'application/json' }
        },
        columns: [
          { data: 'internal_code', render: (d) => d || '—' },
          { data: 'name' },
          { data: 'brand', render: (d) => d || '—' },
          { data: 'quantity', render: (d) => `<span class="font-bold text-blue-600">${d ?? 0}</span>` },
          { 
              data: 'status', 
              render: (d) => {
                  let colorClass = 'text-green-600';
                  if (d === 'en reparacion') colorClass = 'text-yellow-600';
                  if (d === 'no funciona') colorClass = 'text-red-600';
                  return `<span class="font-bold ${colorClass} uppercase text-xs">${d || '—'}</span>`;
              } 
          },
          { data: null, render: renderEquipmentActions, orderable: false, searchable: false, className: 'text-right' }
        ],
        lengthChange: false,
        pageLength: 10,
        language: {
          info: "Showing _START_ to _END_ of _TOTAL_ equipments",
          infoEmpty: "No equipments available",
          zeroRecords: "No matching records found",
          paginate: { next: "Next", previous: "Previous" }
        }
      });

      const eqModalRoot = document.getElementById('edit-equipment-modal');
      const eqForm = document.getElementById('edit-equipment-form');

      // editar
      $(document).on('click', '.edit-equipment-btn', async function() {
        const id = this.dataset.id;
        try {
          const resp = await fetch(`/laboratory/equipments/${id}/edit`);
          if (!resp.ok) throw new Error(`Error ${resp.status}`);
          const data = await resp.json();

          document.getElementById('edit-eq-id').value = data.id;
          document.getElementById('edit-eq-code').value = data.internal_code || '';
          document.getElementById('edit-eq-name').value = data.name;
          document.getElementById('edit-eq-brand').value = data.brand || '';
          document.getElementById('edit-eq-quantity').value = data.quantity;
          document.getElementById('edit-eq-status').value = data.status || 'funcional';

          eqModalRoot.style.display = '';
          eqModalRoot.classList.remove('hidden');
        } catch (err) {
          Swal.fire('Error', 'Could not load the data.', 'error');
        }
      });

      eqForm.addEventListener('submit', async function(ev){
        ev.preventDefault();
        const id = document.getElementById('edit-eq-id').value;
        
        try {
          const resp = await fetch(`/laboratory/equipments/${id}`, {
            method: 'POST', // Usamos POST porque en Laravel Method Spoofing se hace con _method=PUT
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: new FormData(eqForm)
          });

          if(!resp.ok) throw new Error('Error saving record');

          eqModalRoot.classList.add('hidden');
          eqModalRoot.style.display = '';
          eqTable.ajax.reload(null, false);
          Swal.fire({ title: 'Success!', text: 'Record updated successfully.', icon: 'success', timer: 1200, showConfirmButton: false });

        } catch (e) {
          Swal.fire('Error', 'Could not save the changes.', 'error');
        }
      });

      document.addEventListener('click', function(e){
        if(e.target.closest('#edit-equipment-modal .close-eq-modal')){
          eqModalRoot.classList.add('hidden');
          eqModalRoot.style.display = '';
        }
      });

      // Eliminar
      $(document).on('click', '.delete-equipment-btn', async function() {
        const id = this.dataset.id;
        const res = await Swal.fire({
          title: 'Delete equipment?',
          text: "This action cannot be undone.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it',
          cancelButtonText: 'Cancel',
          confirmButtonColor: '#ef4444'
        });

        if (!res.isConfirmed) return;

        try {
          const resp = await fetch(`/laboratory/equipments/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          });
          if (!resp.ok) throw new Error();
          eqTable.ajax.reload(null, false);
          Swal.fire('Deleted!', 'Record deleted successfully.', 'success');
        } catch (err) {
          Swal.fire('Error', 'Could not delete the equipment.', 'error');
        }
      });
    });
  </script>
</section>
