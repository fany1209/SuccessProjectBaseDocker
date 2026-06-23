<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Quality Certificates (Outputs)
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="certificados-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-4 py-3">Folio</th>
          <th class="px-4 py-3">Fecha</th>
          <th class="px-4 py-3">Cliente</th>
          <th class="px-4 py-3">Producto</th>
          <th class="px-4 py-3">Lote</th>
          <th class="px-4 py-3">Cantidad</th>
          <th class="px-4 py-3"># Tarimas</th>
          <th class="px-4 py-3">Salida CEDIS</th>
          <th class="px-4 py-3">Certificado</th>
          <th class="px-4 py-3">Tipo</th>
          <th class="px-4 py-3">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>

    <div class="w-full mt-4 flex justify-start">
      <a href="{{ route('certificados.export') }}"
        class="inline-flex items-center gap-2 bg-[#198754] hover:bg-[#157347] text-white px-4 py-2 rounded-md text-sm font-medium">
        <img width="16" src="{{ asset('images/excel.png') }}" alt="Excel">
        Download Excel
      </a>
    </div>

  </div>
</section>

@push('js')
<script>
$(function () {
  const ORIGIN = window.location.origin;

  function ensureStorageUrl(pathOrUrl){
    if (!pathOrUrl) return null;
    if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;
    return `${ORIGIN}/storage/${String(pathOrUrl).replace(/^\/+/, '')}`;
  }

  function fmtDateYmdToDmy(ymd){
    if(!ymd) return '';
    const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(ymd);
    if(!m) return ymd;
    return `${m[3]}/${m[2]}/${m[1]}`;
  }

  const table = $('#certificados-table').DataTable({
    ajax: {
      url: "{{ route('certificados.json') }}",
      dataSrc: function(json){ return json.data ?? []; }
    },
    columns: [
      { data: 'folio', defaultContent: '' },
      { data: 'fecha', render: (d)=> fmtDateYmdToDmy(d), defaultContent: '' },
      { data: 'cliente', defaultContent: '' },
      { data: 'producto', defaultContent: '' },
      { data: 'lote', defaultContent: '' },
      { data: 'cantidad', defaultContent: '' },
      { data: 'no_tarimas', defaultContent: '' },
      { data: 'fecha_salida_cedis', render: (d)=> fmtDateYmdToDmy(d), defaultContent: '' },
      {
        data: 'certificado_tarima',
        render: $.fn.dataTable.render.text(),
        defaultContent: '—'
      },
      { data: 'muestra_o_pf', defaultContent: '' },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function(row){
          let buttons = '';
          @can('quality.buttons.show')
            buttons += `
              <button data-id="${row.id}" class="btn-del bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-sm" title="Eliminar">
                <img width="16" src="{{ asset('images/borrar.png') }}" alt="Eliminar"/>
              </button>
            `;
          @endcan
          return `<div class="flex items-center gap-2">${buttons}</div>`;
        }
      }
    ],
    responsive: true,
    autoWidth: false,
    pageLength: 10,
    lengthChange: false,
    searching: false,   
    dom: 'lrtip',       
    language: {
      processing:     "Processing...",
      search:         "Search:",
      lengthMenu:     "Show _MENU_ entries",
      info:           "Showing _START_ to _END_ of _TOTAL_ entries",
      infoEmpty:      "Showing 0 to 0 of 0 entries",
      infoFiltered:   "(filtered from _MAX_ total entries)",
      infoPostFix:    "",
      loadingRecords: "Loading...",
      zeroRecords:    "No matching records found",
      emptyTable:     "No data available in the table",
      paginate: {
        first:    "First",
        previous: "Previous",
        next:     "Next",
        last:     "Last"
      }
    },
    columnDefs: [
      { targets: [0,1,2,3,4,5,6,7,8,9], className: 'px-4 py-2' },
      { targets: [10], className: 'px-2 py-2' }
    ]
  });

  // Delete
  $('#certificados-table tbody').on('click', '.btn-del', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: 'Are you sure you want to delete?',
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete',
      cancelButtonText: 'Cancel'
    }).then((res) => {
      if (res.isConfirmed) {
        $.ajax({
          url: `/certificados/${id}`,
          type: 'DELETE',
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function () {
            Swal.fire('Deleted', 'The certificate was deleted successfully.', 'success');
            table.ajax.reload(null, false);
          },
          error: function (xhr) {
            console.error(xhr?.responseText || xhr);
            Swal.fire('Error', 'The record could not be deleted.', 'error');
          }
        });
      }
    });
  });

});
</script>
@endpush
