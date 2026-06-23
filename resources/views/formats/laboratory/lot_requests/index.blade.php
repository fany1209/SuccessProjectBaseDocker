@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">

    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Peticiones de lote
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <div class="w-full mt-4">
      <table id="lot-requests-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
        <thead class="bg-gray-50 text-gray-700 uppercase text-md">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Fecha/Hora</th>
            <th class="px-4 py-3">Departamento</th>
            <th class="px-4 py-3">Comentarios</th>
            <th class="px-4 py-3">Estatus</th>
            <th class="px-4 py-3">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-md"></tbody>
      </table>
    </div>

  </div>
</section>
@endsection

@push('js')
<script>
$(function(){

  // ==== DataTable ====
  const table = $('#lot-requests-table').DataTable({
    ajax: {
      url: "{{ route('lot.request.datatable') }}",
      dataSrc: 'lots'
    },
    columns: [
      { data: 'id' },
      { data: 'requested_at' },
      { data: 'department' },
      { data: 'comments', defaultContent: '—' },
      { data: 'status',
        render: function(status){
          const map = {
            pendiente: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Pendiente</span>',
            terminado: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Terminado</span>',
          };
          return map[status] ?? status;
        }
      },
      { data: null, orderable:false, searchable:false,
        render: function(row){
          return `
            <select class="lr-status border rounded px-2 py-1"
                    data-id="${row.id}">
              <option value="pendiente" ${row.status === 'pendiente' ? 'selected' : ''}>Pendiente</option>
              <option value="terminado" ${row.status === 'terminado' ? 'selected' : ''}>Terminado</option>
            </select>
          `;
        }
      }
    ],
    lengthChange: false,
    pageLength: 15,
    language: {
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      lengthMenu: "Mostrar _MENU_ registros",
      infoEmpty: "No hay registros disponibles",
      zeroRecords: "No se encontraron resultados",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior"
      },
    }
  });

  // ==== Cambiar estatus (select) ====
  $(document).on('change', '.lr-status', function(){
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
      success: function(res){
        if (res?.success) {
          if (window.Swal) {
            Swal.fire('Listo', `Status changed to "${status}".`, 'success');
          } else {
            alert(`Status changed to "${status}".`);
          }
          table.ajax.reload(null, false); 
        }
      },
      error: function(xhr){
        const msg = xhr?.responseJSON?.message || 'The status could not be updated.';
        if (window.Swal) Swal.fire('Error', msg, 'error'); else alert(msg);
        table.ajax.reload(null, false);
      }
    });
  });

});
</script>
@endpush
