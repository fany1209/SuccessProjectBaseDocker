<x-modal id="create-material">
  <form id="create-material-form" class="space-y-4">
    @csrf

    <x-wrapper-form-1>
      <x-tittle-form>Registrar Nuevo Material</x-tittle-form>
    </x-wrapper-form-1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold">Nombre del material</label>
        <input type="text" id="create-mat-name" name="name" class="w-full border rounded px-2 py-1" required>
      </div>

      <div>
        <label class="block text-sm font-semibold">UM (Unidad de Medida)</label>
        <input type="text" id="create-mat-um" name="um" class="w-full border rounded px-2 py-1" placeholder="ej. piezas, cajas, mL">
      </div>

      <div>
        <label class="block text-sm font-semibold">Marca</label>
        <input type="text" id="create-mat-brand" name="brand" class="w-full border rounded px-2 py-1">
      </div>

      <div class="md:col-span-2 bg-green-50 p-3 rounded border border-green-200 mt-2">
        <label class="block text-sm font-bold text-green-800">Cantidad Inicial (Entradas)</label>
        <input type="number" step="0.01" min="0" id="create-mat-entries" name="entries" class="w-full border border-green-300 rounded px-2 py-1" value="0" required>
        <p class="text-xs text-green-600 mt-1">Esta cantidad será tu stock inicial. Las salidas iniciarán en 0.</p>
      </div>

    </div>

    <div class="mt-4 flex justify-end gap-2">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
        Cancelar
      </x-button>

      <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
        Guardar Material
      </x-button>
    </div>
  </form>

  <script>
    (function(){
      const form = document.getElementById('create-material-form');
      const modalRoot = document.getElementById('create-material');
      if(!form || !modalRoot) return;

      form.addEventListener('submit', async function(ev){
        ev.preventDefault();

        try {
          const resp = await fetch(`{{ route('materials.store') }}`, {
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

          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';
          form.reset();

          $('#material-table').DataTable().ajax.reload(null, false);
          
          Swal.fire({ title: 'Success!', text: 'Material registered successfully.', icon: 'success', timer: 1500, showConfirmButton: false });

        } catch (e) {
          Swal.fire('Error', e.message || 'Could not save the material.', 'error');
        }
      });

      modalRoot.querySelector('.close-modal')?.addEventListener('click', function() {
          modalRoot.classList.remove('open');
          modalRoot.style.display = 'none';
          form.reset(); 
      });
    })();
  </script>
</x-modal>