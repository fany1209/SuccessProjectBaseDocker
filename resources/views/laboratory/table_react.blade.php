<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Reagent Inventory
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="reagent-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4 text-left">CODE</th>
          <th class="px-6 py-4 text-left">NAME</th>
          <th class="px-6 py-4 text-left">ENTRIES</th>
          <th class="px-6 py-4 text-left">EXITS</th>
          <th class="px-6 py-4 text-left font-bold text-blue-600">STOCK</th>
          <th class="px-6 py-4 text-left">BRAND</th>
          <th class="px-6 py-4 text-left">COLOR</th>
          <th class="px-6 py-4 text-right">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>
  </div>

  @include('laboratory.modals.reagents.edit')
  
  
<script>
function debugReagentDataSrc(json){
  if (Array.isArray(json)) return json;
  if (Array.isArray(json?.data)) return json.data;
  return [];
}

function renderReagentActions(data, type, row){
  const id = row.id ?? '';
  return `
   <button data-id="${id}" data-target="edit-reagent"
            class="edit-reagent-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2"
            title="Edit Reagent">
      <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
    </button>

    <button data-id="${id}"
            class="delete-reagent-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
            title="Delete">
      <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
    </button>
  `;
}

document.addEventListener('DOMContentLoaded', function () {
  const table = $('#reagent-table').DataTable({
    ajax: {
      url: "{{ route('reagents.index') }}", 
      dataSrc: debugReagentDataSrc,
      headers: { 'Accept': 'application/json' }
    },
    columns: [
      { data: 'code' },
      { data: 'name' },
      { 
        data: 'entries', 
        render: function(data, type, row) {
          return `<span class="text-green-600">${data ?? 0} ${row.um || ''}</span>`; 
        }
      },
      { 
        data: 'exits', 
        render: function(data, type, row) {
          return `<span class="text-red-600">${data ?? 0} ${row.um || ''}</span>`; 
        }
      },
      { 
        data: 'stock', 
        render: function(data, type, row) {
          return `<span class="font-bold text-blue-600">${data ?? 0} ${row.um || ''}</span>`; 
        }
      },
      { data: 'brand', render: (d) => d || '—' },
      { 
        data: 'color',
        render: function(v) {
          return v ? `<span class="capitalize">${v}</span>` : '—';
        }
      },
      { data: null, render: renderReagentActions, orderable:false, searchable:false, className:'text-right' }
    ],
    lengthChange: false,
    pageLength: 10,
    language: {
      info: "Showing _START_ to _END_ of _TOTAL_ reagents",
      infoEmpty: "No reagents available",
      zeroRecords: "No results found",
      paginate: { next:"Next", previous:"Previous" }
    }
  });

  // === Eliminar ===
  $(document).on('click', '.delete-reagent-btn', async function(){
    const id = this.dataset.id;
    const res = await Swal.fire({
      title: 'Delete reagent?',
      text: "This action cannot be undone.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#ef4444'
    });

    if (!res.isConfirmed) return;

    try {
      const resp = await fetch(`/laboratory/reagents/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });

      if (!resp.ok) throw new Error('Error on delete');

      table.ajax.reload(null, false);
      Swal.fire('Deleted', 'Record removed.', 'success');
    } catch (err) {
      Swal.fire('Error', 'Could not delete the reagent.', 'error');
    }
  });

 // === Editar ===
  $(document).on('click', '.edit-reagent-btn', async function() {
    const id = this.dataset.id;
    try {
      const resp = await fetch(`/laboratory/reagents/${id}/edit`);
      if (!resp.ok) throw new Error(`Error HTTP ${resp.status}`);

      const data = await resp.json();

      document.getElementById('edit-reagent-id').value = data.id;
      document.getElementById('edit-code').value = data.code;
      document.getElementById('edit-name').value = data.name;
      document.getElementById('edit-um').value = data.um;
      document.getElementById('edit-brand').value = data.brand;
      document.getElementById('edit-color').value = data.color;
      
      document.getElementById('edit-entries').value = data.entries;
      document.getElementById('edit-exits').value = data.exits;
      document.getElementById('edit-stock').value = data.stock;

      const modal = document.getElementById('edit-reagent-modal');
      modal.style.display = 'flex'; 
      modal.classList.add('open');

    } catch (err) {
      console.error("Error al cargar:", err);
      Swal.fire('Error', 'No se pudieron cargar los datos.', 'error');
    }
  });
});
  </script>
</section>