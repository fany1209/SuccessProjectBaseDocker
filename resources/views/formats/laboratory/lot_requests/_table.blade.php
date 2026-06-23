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
            <th class="px-4 py-3">Comments</th>
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

  const table = $('#lot-requests-table').DataTable({
    ajax: {
      url: "{{ route('lot.request.datatable') }}",
      dataSrc: 'lots'
    },
    columns: [
      { data: 'id' },
      { data: 'requested_at' },
      { data: 'department' },
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

    if (window.Swal) {
      Swal.fire({
        title: 'Comentario completo',
        html: `<div class="text-left whitespace-pre-wrap text-gray-700">${comment}</div>`,
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

    $.ajax({
      url: "{{ route('lot.request.update', ':id') }}".replace(':id', id),
      method: 'PATCH',
      data: { status },
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
