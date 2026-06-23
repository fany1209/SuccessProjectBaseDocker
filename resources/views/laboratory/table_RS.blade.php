<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Reception of Sample
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="reception-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4 text-left">PRODUCT</th>
          <th class="px-6 py-4 text-left">BATCH</th>
          <th class="px-6 py-4 text-left">ENTRY DATE</th>
          <th class="px-6 py-4 text-left">STATUS</th>
          <th class="px-6 py-4">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>
  </div>

  @include('laboratory.modals.reception.reception')
  @include('laboratory.modals.reception.receptionq')

  <script>
// === Helpers ===
function debugDataSrc(json){
  if (Array.isArray(json)) return json;
  if (Array.isArray(json?.data)) return json.data;
  if (Array.isArray(json?.rows)) return json.rows;
  console.warn('Respuesta JSON inesperada para DataTables:', json);
  return [];
}

function renderActions(data, type, row){
  const id = row.id ?? '';
  return `
    @can('laboratory.update')
    <button data-id="${id}" data-target="edit-reception"
            class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2"
            title="Edit ">
      <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
    </button>
    @endcan

    @can('laboratory.quality')
    <button data-id="${id}" data-target="edit-receptionq"
            class="open-modal edit-yellow-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2"
            title="Edit (yellow)">
      <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit Yellow"/>
    </button>
    @endcan

    @can('laboratory.delete')
    <button data-id="${id}"
            class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
            title="Delete">
      <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
    </button>

    <button data-id="${id}"
            class="generate-pdf-btn bg-green-600 hover:bg-green-700 text-white p-2 rounded-sm"
            title="Generar PDF">
      <img width="18" src="{{ asset('images/pdf.png') }}" alt="PDF"/>
    </button>
    @endcan

  `;
}

document.addEventListener('DOMContentLoaded', function () {
  const table = $('#reception-table').DataTable({
    ajax: {
      url: "{{ route('reception') }}", 
      dataSrc: debugDataSrc,
      headers: { 'Accept': 'application/json' },
      error: function (xhr, status, err) {
        console.error('DataTables AJAX error:', status, err);
        console.error('HTTP', xhr.status, '->', xhr.responseText);
      }
    },
    columns: [
      { data: 'product' },
      { data: 'batch' },
      { data: 'entry_at' },
      {
        data: 'estatus',
        className: 'text-right',
        render: function (v, type, row) {

          if (v === undefined || v === null) {
            const txt = String(row.status || '').toUpperCase();
            v = (txt === 'TERMINADO' || txt === 'APPROVED') ? 1
              : (txt === 'PENDING' || txt === 'PENDIENTE') ? 0
              : null;
          }

          const map = {
            1: { bg: '#4ade80', txt: 'Terminado' }, 
            0: { bg: '#fbbf24', txt: 'Pendiente' }  
          };
          const m = map[v] ?? { bg: '#e5e7eb', txt: '—' };

          return `<span style="background:${m.bg}; color:#fff; padding:2px 6px; border-radius:4px; font-weight:600;">
            ${m.txt}
          </span>`;
        }
      },
      { data: null, render: renderActions, orderable:false, searchable:false, className:'text-right' }
    ],
    lengthChange: false,
    searching: false,
    pageLength: 5,
    serverSide: false,
    language: {
      info: "Show _START_ to _END_ of _TOTAL_ records",
      lengthMenu: "Show _MENU_ records",
      infoEmpty: "No records available",
      zeroRecords: "No results found",
      infoFiltered: "(filtered from _MAX_ total)",
      paginate: { first:"First", last:"Last", next:"Next", previous:"Previous" }
    }
  });

 // === Eliminar  ===
    $(document).on('click', '.delete-btn', async function(){
      const id = this.dataset.id;
      const rowData = table.row($(this).closest('tr')).data();

      if (!id || !rowData) {
        Swal.fire('Ups', 'The record to be deleted was not found.', 'warning');
        return;
      }

      const res = await Swal.fire({
        title: '¿Delete record?',
        html: `
          <p style="margin-top:8px;">This action cannot be undone.</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true
      });

      if (!res.isConfirmed) return;

        const url = (rowData && rowData.delete_url)
        ? rowData.delete_url
        : `/laboratory/reception/${id}`;

      try {
        const resp = await fetch(url, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        });

        if (!resp.ok) {
          let msg = `Error ${resp.status}`;
          try {
            const j = await resp.json();
            msg = j.message || msg;
          } catch {
            const t = await resp.text();
            if (t) msg = t;
          }
          throw new Error(msg);
        }

        await resp.json().catch(()=> ({}));

        table.ajax.reload(null, false);
        Swal.fire({title:'Delete', text:'Record deleted successfully.', icon:'success', timer:1400, showConfirmButton:false});

      } catch (err) {
        console.error('Error deleting:', err);
        Swal.fire('Could not be deleted', err?.message || 'An error occurred.', 'error');
      }
    });

    //pdf
    $(document).on('click', '.generate-pdf-btn', function () {
    const id = this.dataset.id;
    const table = $('#reception-table').DataTable();
    const rowData = table.row($(this).closest('tr')).data();

    if (!id && !rowData?.id) {
        Swal?.fire?.('Ups', 'No record found to generate PDF.', 'warning');
        return;
    }

    const url = rowData?.pdf_url ?? `/laboratory/reception/${id || rowData.id}/pdf`;

    window.open(url, '_blank');
    });

    //alert
     $(document).ready(function () {
    $.ajax({
      url: "{{ route('quality.checkPendingQuality') }}",
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        const total = res?.count ?? (res?.pending?.length || 0);
        if (total > 0) {
          Swal.fire({
            icon: 'info',
            title: 'Pending sample receptions',
            html: `You have <b>${total}</b> pending sample reception(s) in Laboratory.`,
            confirmButtonText: 'Accept'
          });
        }
      },
      error: function(err) {
        console.error('Error checking Quality pending', err);
      }
    });
  });
  });
</script>
