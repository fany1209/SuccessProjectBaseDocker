<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="col-span-12 w-full px-1">
  <div class="w-full">

    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Requests by batch and/or product
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <div class="w-full mt-4 overflow-x-auto">
      <table
        id="lot-requests-table"
        class="display min-w-full table-auto divide-y divide-gray-200 text-md text-left"
      >
        <thead class="bg-gray-50 text-gray-700 uppercase text-md">
          <tr>
            <th class="px-4 py-3 whitespace-nowrap">ID</th>
            <th class="px-4 py-3 whitespace-nowrap">Date/Hour</th>
            <th class="px-4 py-3 whitespace-nowrap">Department</th>
            <th class="px-4 py-3 whitespace-nowrap">Product</th>
            <th class="px-4 py-3 whitespace-nowrap">Qty</th>
            <th class="px-4 py-3 whitespace-nowrap">Provider</th>
            <th class="px-4 py-3 whitespace-nowrap">Collector</th>
            <th class="px-4 py-3 whitespace-nowrap">Sector</th>
            <th class="px-4 py-3">Comments</th>
            <th class="px-4 py-3 whitespace-nowrap">SKU</th>
            <th class="px-4 py-3 whitespace-nowrap">Batch</th>
            <th class="px-4 py-3 whitespace-nowrap">Status</th>
            <th class="px-4 py-3 whitespace-nowrap">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-md"></tbody>
      </table>
    </div>

  </div>
</section>

@push('js')
<script>
$(function () {
  const canAssignBatch = @json(auth()->user() && auth()->user()->roles()->whereIn('name', ['Quality', 'quality'])->exists());

  const table = $('#lot-requests-table').DataTable({
    ajax: {
      url: "{{ route('lot.request.datatable') }}",
      dataSrc: function (json) {
        window.productSkus = json.productSkus || {};
        window.productBatches = json.productBatches || {};
        return json.lots;
      }
    },
    columns: [
      { data: 'id' },
      { data: 'requested_at' },
      { data: 'department' },
      { data: 'product', render: data => data || '—' },
      { data: 'quantity', render: data => data || '—' },
      { data: 'provider', render: data => data || '—' },
      { data: 'collector', render: data => data || '—' },
      { data: 'sector', render: data => data || '—' },
      {
        data: 'comments',
        render: function (data) {
          if (!data) return '—';

          const short = data.length > 80
            ? data.substring(0, 80) + '...'
            : data;

          return `
            <div class="text-sm text-gray-700">
              ${short}
              ${data.length > 80
                ? `<button
                      class="ml-2 text-green-700 font-semibold underline view-comment"
                      data-comment="${encodeURIComponent(data)}">
                      Ver más
                   </button>`
                : ''}
            </div>
          `;
        }
      },

      {
        data: 'sku',
        render: function (data, type, row) {
          if (row.status === 'terminado' || !canAssignBatch) return data || '—';
          
          let listHtml = '';
          const listId = `sku-list-${row.id}`;
          if (row.product && window.productSkus[row.product] && window.productSkus[row.product].length > 0) {
              listHtml = `<datalist id="${listId}">`;
              window.productSkus[row.product].forEach(sku => {
                  listHtml += `<option value="${sku}">`;
              });
              listHtml += `</datalist>`;
          }
          const listAttr = listHtml ? `list="${listId}"` : '';

          return `<input type="text" ${listAttr} class="lr-sku border rounded px-2 py-1 w-full text-sm" data-id="${row.id}" value="${data || ''}" placeholder="SKU">${listHtml}`;
        }
      },
      {
        data: 'batch',
        render: function (data, type, row) {
          if (row.status === 'terminado' || !canAssignBatch) return data || '—';

          let listHtml = '';
          const listId = `batch-list-${row.id}`;
          if (row.product && window.productBatches[row.product] && window.productBatches[row.product].length > 0) {
              listHtml = `<datalist id="${listId}">`;
              window.productBatches[row.product].forEach(batch => {
                  listHtml += `<option value="${batch}">`;
              });
              listHtml += `</datalist>`;
          }
          const listAttr = listHtml ? `list="${listId}"` : '';

          return `<input type="text" ${listAttr} class="lr-batch border rounded px-2 py-1 w-full text-sm" data-id="${row.id}" value="${data || ''}" placeholder="Batch">${listHtml}`;
        }
      },

      // Status badge
      {
        data: 'status',
        render: function (status) {
          const map = {
            pendiente: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Pendiente</span>',
            terminado: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Terminado</span>',
          };
          return map[status] ?? status;
        }
      },

      // Acciones
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function (row) {
          if (!canAssignBatch) {
              return row.status === 'terminado' 
                 ? '<span class="text-green-600 font-semibold">Resuelto</span>' 
                 : '<span class="text-gray-500 italic">En espera</span>';
          }
          
          const disabled = row.status === 'terminado' ? 'disabled' : '';

          return `
            <select
              class="lr-status border rounded px-2 py-1 ${disabled ? 'bg-gray-200 cursor-not-allowed' : ''}"
              data-id="${row.id}"
              ${disabled}>
              <option value="pendiente" ${row.status === 'pendiente' ? 'selected' : ''}>Pendiente</option>
              <option value="terminado" ${row.status === 'terminado' ? 'selected' : ''}>Terminado</option>
            </select>
          `;
        }
      }
    ],

    lengthChange: false,
    pageLength: 5,

    language: {
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "No records available",
      zeroRecords: "No results found",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      paginate: {
        first: "First",
        last: "Last",
        next: "Next",
        previous: "Previous"
      }
    }
  });

  // ver comentario completo
  $(document).on('click', '.view-comment', function () {
    const comment = decodeURIComponent($(this).data('comment'));
    const safeEscape = window.escapeHtml || function(str){ return (str ?? '').toString().replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s])); };

    if (window.Swal) {
      Swal.fire({
        title: 'Comentario completo',
        html: `<div class="text-left whitespace-pre-wrap text-gray-700">${safeEscape(comment)}</div>`,
        width: '600px',
        confirmButtonText: 'Cerrar'
      });
    } else {
      alert(comment);
    }
  });

  //  Cambiar estatus
  $(document).on('change', '.lr-status', function () {
    const id = $(this).data('id');
    const status = $(this).val();
    
    // Retrieve the inputted SKU and Batch from the corresponding row
    const sku = $(`.lr-sku[data-id="${id}"]`).val();
    const batch = $(`.lr-batch[data-id="${id}"]`).val();

    $.ajax({
      url: "{{ route('lot.request.update', ':id') }}".replace(':id', id),
      method: 'PATCH',
      data: { status, sku, batch },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
        success: function (res) {
        if (res?.success) {

            const badge = $('#pending-batch-badge');

            if (typeof res.pending !== 'undefined') {
                if (res.pending > 0) {
                    badge.text(res.pending).removeClass('hidden');
                } else {
                    badge.addClass('hidden');
                }
            }

            if (window.Swal) {
                Swal.fire('Listo', `Estatus cambiado a "${status}".`, 'success');
            } else {
                alert(`Estatus cambiado a "${status}".`);
            }

            table.ajax.reload(null, false);
        }
    },
      error: function (xhr) {
        const msg = xhr?.responseJSON?.message || 'The status could not be updated.';
        if (window.Swal) Swal.fire('Error', msg, 'error');
        else alert(msg);
        table.ajax.reload(null, false);
      }
    });
  });

});
</script>
@endpush
