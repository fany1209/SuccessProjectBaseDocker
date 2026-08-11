<x-modal id="create-equipment">
  <form id="create-equipment-form" class="space-y-4">
    @csrf

    <x-wrapper-form-1>
      <x-tittle-form>Registrar Nuevo Equipo</x-tittle-form>
    </x-wrapper-form-1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      
      <div>
        <label class="block text-sm font-semibold">Código Interno</label>
        <input type="text" id="create-eq-code" name="internal_code" class="w-full border rounded px-2 py-1" placeholder="ej. EQ-001">
      </div>

      <div>
        <label class="block text-sm font-semibold">Nombre del Equipo</label>
        <input type="text" id="create-eq-name" name="name" class="w-full border rounded px-2 py-1" required>
      </div>

      <div>
        <label class="block text-sm font-semibold">Marca</label>
        <input type="text" id="create-eq-brand" name="brand" class="w-full border rounded px-2 py-1">
      </div>

      <div>
        <label class="block text-sm font-semibold text-green-700">Cantidad</label>
        <input type="number" step="0.01" min="0" id="create-eq-quantity" name="quantity" class="w-full border border-green-300 rounded px-2 py-1" value="1" required>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-blue-700">Estado del Equipo</label>
        <select id="create-eq-status" name="status" class="w-full border border-blue-300 rounded px-2 py-1" required>
          <option value="funcional" selected>Funcional</option>
          <option value="en reparacion">En reparación</option>
          <option value="no funciona">No funciona</option>
        </select>
      </div>

    </div>

    <div class="mt-4 flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
        Cancelar
      </x-button>

      <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
        Guardar Equipo
      </x-button>
    </div>
  </form>

  <script>
    (function(){
      const form = document.getElementById('create-equipment-form');
      const modalRoot = document.getElementById('create-equipment');
      if(!form || !modalRoot) return;

      form.addEventListener('submit', async function(ev){
        ev.preventDefault();

        try {
          const resp = await fetch(`{{ route('equipments.store') }}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: new FormData(form)
          });

          if(!resp.ok) {
              const errData = await resp.json();
              throw new Error(errData.message || 'Error creating record');
          }

          modalRoot.classList.add('hidden');
          modalRoot.style.display = '';
          form.reset();

          $('#equipment-table').DataTable().ajax.reload(null, false);
          
          Swal.fire({ title: 'Success!', text: 'Equipment registered successfully.', icon: 'success', timer: 1500, showConfirmButton: false });

        } catch (e) {
          Swal.fire('Error', e.message || 'Could not save the equipment.', 'error');
        }
      });

      modalRoot.querySelector('.close-modal')?.addEventListener('click', function() {
          modalRoot.classList.add('hidden');
          modalRoot.style.display = '';
          form.reset(); 
      });
    })();
  </script>
</x-modal>
