{{--
Finance
Fecha de creación: 18-12-2025
Creado por: Stefany
Actualizado por: Stefany
Fecha de actualización: 30-03-2026
--}}
@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4">

  <section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

      <div class="mt-6 text-center w-full">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
          Programación de pagos
        </h1>
        <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
      </div>

      <x-section-1>
        <div class="flex justify-end items-center w-full my-1 gap-2">

          <x-button data-target="add-finance"
            class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Add Finance
          </x-button>

          @include('finance.modals.create')
          @include('finance.modals.edit')

          <button id="btn-open-edit-finance" type="button" class="open-modal hidden" data-target="edit-finance"></button>

        </div>
      </x-section-1>

      <div class="w-full mt-4">
        <table id="finance-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
          <thead class="bg-gray-50 text-gray-700 uppercase text-md">
            <tr>
              <th class="px-4 py-3">Empresa</th>
              <th class="px-4 py-3">Cantidad</th>
              <th class="px-4 py-3">Motivo</th>
              <th class="px-4 py-3">Banco</th>
              <th class="px-4 py-3">Factura</th>
              <th class="px-4 py-3">Fecha factura</th>
              <th class="px-4 py-3">Fecha pago</th>
              <th class="px-4 py-3">Semana</th>
              <th class="px-4 py-3">Tipo pago</th>
              <th class="px-4 py-3">Estatus</th>
              <th class="px-4 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-md"></tbody>
        </table>
      </div>

    </div>
  </section>

</div>
@endsection

@push('js')
<script>
$(function(){

  function formatDateYMDToDMY(data){
    if (!data) return '—';
    if (typeof data !== 'string') return data;

    const ymd = data.slice(0, 10);
    const parts = ymd.split('-');
    if (parts.length !== 3) return data;

    const [y, m, d] = parts;
    return `${d}/${m}/${y}`;
  }

  function toInputDateYMD(data){
    if (!data) return '';
    if (typeof data !== 'string') return '';

    return data.slice(0, 10);
  }

  const table = $('#finance-table').DataTable({
    ajax: {
      url: "{{ route('finance.datatable') }}",
      dataSrc: 'data'
    },
    columns: [
      { data: 'empresa' },
      {
        data: 'cantidad',
        render: data => `$${parseFloat(data || 0).toFixed(2)}`
      },
      { data: 'motivo' },
      { data: 'banco', defaultContent: '—' },
      { data: 'factura', defaultContent: '—' },
      { data: 'fecha_factura', render: data => formatDateYMDToDMY(data) },
      { data: 'fecha_pago',    render: data => formatDateYMDToDMY(data) },
      { data: null, render: row => `Semana ${row.semana} / ${row.anio}` },
      { 
        data: null, 
        render: function(row) {
          let html = `<span>${row.comentarios || '—'}</span>`;
          if (row.comentarios === 'Tarjeta' && row.terminacion) {
            html += `<br><span class="text-blue-600 font-bold text-xs">Term: ${row.terminacion}</span>`;
          }
          if (row.efectivo === 'Si') {
            html += `<br><span class="text-green-600 font-bold text-xs">[Efectivo]</span>`;
          }
          return html;
        }
      },

      {
        data: null,
        render: function(row){
          const map = {
            PENDIENTE: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Pendiente</span>',
            PAGADO: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Pagado</span>',
            CANCELADO: '<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Cancelado</span>',
          };

          return `
            <div class="flex flex-col gap-2">
              ${map[row.estatus] ?? row.estatus}

              <select
                class="finance-status border rounded px-2 py-1"
                data-id="${row.id}"
                data-prev="${row.estatus}">
                <option value="PENDIENTE" ${row.estatus === 'PENDIENTE' ? 'selected' : ''}>Pendiente</option>
                <option value="PAGADO" ${row.estatus === 'PAGADO' ? 'selected' : ''}>Pagado</option>
                <option value="CANCELADO" ${row.estatus === 'CANCELADO' ? 'selected' : ''}>Cancelado</option>
              </select>
            </div>
          `;
        }
      },

      {
        data: null,
        orderable: false,
        searchable: false,
        render: function(row){
          return `
            <div class="flex gap-2 justify-center">

              <button type="button"
                      title="Editar"
                      class="btn-edit-finance p-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition"
                      data-id="${row.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 3.487a2.1 2.1 0 013.07 3.07L7.125 19.364
                           3 21l1.636-4.125L16.862 3.487z"/>
                </svg>
              </button>

              <button type="button"
                      title="Eliminar"
                      class="btn-delete-finance p-2 rounded bg-red-600 text-white hover:bg-red-700 transition"
                      data-id="${row.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                           a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0
                           H7m3-3h4a1 1 0 011 1v1H9V5a1 1 0 011-1z"/>
                </svg>
              </button>

            </div>
          `;
        }
      }
    ],
    pageLength: 15,
    lengthChange: false,
    language: {
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "No hay registros",
      zeroRecords: "No se encontraron resultados",
      paginate: { first:"Primero", last:"Último", next:"Siguiente", previous:"Anterior" },
    }
  });

  $(document).on('change', '.finance-status', function(){
    const $select = $(this);
    const id = $select.data('id');

    const prev = ($select.data('prev') || '').toString();
    const estatus = ($select.val() || '').toString();

    const requiereConfirmacion =
      (prev === 'PAGADO' && estatus !== 'PAGADO') ||
      (prev !== 'PAGADO' && estatus === 'PAGADO');

    const continuar = () => {
      $.ajax({
        url: "{{ route('finance.update-status', ':id') }}".replace(':id', id),
        method: 'PATCH',
        data: { estatus },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(res){
          if(res?.success){
            $select.data('prev', estatus);
            if(window.Swal) Swal.fire('Listo', 'Estatus actualizado', 'success');
            else alert('Estatus actualizado');
            table.ajax.reload(null, false);
          } else {
            $select.val(prev);
            const msg = res?.message || 'No se pudo actualizar el estatus';
            if(window.Swal) Swal.fire('Error', msg, 'error'); else alert(msg);
            table.ajax.reload(null, false);
          }
        },
        error: function(xhr){
          $select.val(prev);
          const msg = xhr?.responseJSON?.message || 'No se pudo actualizar el estatus';
          if(window.Swal) Swal.fire('Error', msg, 'error');
          else alert(msg);
          table.ajax.reload(null, false);
        }
      });
    };

    if (!requiereConfirmacion) return continuar();

    if (window.Swal) {
      Swal.fire({
        title: 'Confirmar cambio de estatus',
        text: `Vas a cambiar de "${prev}" a "${estatus}". ¿Deseas continuar?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (!result.isConfirmed) {
          $select.val(prev);
          return;
        }
        continuar();
      });
    } else {
      const ok = confirm(`Vas a cambiar de "${prev}" a "${estatus}". ¿Deseas continuar?`);
      if (!ok) {
        $select.val(prev);
        return;
      }
      continuar();
    }
  });

  // ELIMINAR
  $(document).on('click', '.btn-delete-finance', function(){
    const id = $(this).data('id');

    Swal.fire({
      title: '¿Eliminar?',
      text: 'Este registro se eliminará definitivamente.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: "{{ route('finance.destroy', ':id') }}".replace(':id', id),
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(res){
          if(res?.success){
            Swal.fire('Listo', 'Registro eliminado', 'success');
            table.ajax.reload(null, false);
          } else {
            Swal.fire('Error', res?.message || 'No se pudo eliminar', 'error');
          }
        },
        error: function(xhr){
          const msg = xhr?.responseJSON?.message || 'No se pudo eliminar';
          Swal.fire('Error', msg, 'error');
        }
      });
    });
  });

  // EDIT
  $(document).on('click', '.btn-edit-finance', function(){
    const id = $(this).data('id');

    $('#btn-open-edit-finance').trigger('click');
    $('#edit-finance-id').val(id);
    $('#edit-banco').val('');
    $('#edit-fecha-pago').val('');
    $('#edit-comentarios').val('');

    $.ajax({
      url: "{{ route('finance.show', ':id') }}".replace(':id', id),
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res){
        if(res?.success && res.data){
          $('#edit-banco').val(res.data.banco || '');
          $('#edit-comentarios').val(res.data.comentarios || '');
          $('#edit-fecha-pago').val(toInputDateYMD(res.data.fecha_pago));
        }
      },
      error: function(xhr){
        const msg = xhr?.responseJSON?.message || 'No se pudo cargar el registro';
        if(window.Swal) Swal.fire('Error', msg, 'error'); else alert(msg);
      }
    });
  });

  // UPDATE
  $(document).on('submit', '#edit-finance-form', function(e){
    e.preventDefault();

    const id = $('#edit-finance-id').val();

    const payload = {
      empresa: $('#edit-empresa').val(),
      cantidad: $('#edit-cantidad').val(),
      banco: $('#edit-banco').val(),
      factura: $('#edit-factura').val(),
      motivo: $('#edit-motivo').val(),
      fecha_factura: $('#edit-fecha-factura').val(),
      fecha_pago: $('#edit-fecha-pago').val(),
      semana: $('#edit-semana').val(),
      anio: $('#edit-anio').val(),
      estatus: $('#edit-estatus').val(),
      comentarios: $('#edit-comentarios').val(),
    };

    $('#update-finance').prop('disabled', true);

    $.ajax({
      url: "{{ route('finance.update', ':id') }}".replace(':id', id),
      method: 'PATCH',
      data: payload,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(res){
        if(res?.success){
          if(window.Swal) Swal.fire('Listo', 'Actualizado correctamente', 'success');
          if ($.fn.DataTable.isDataTable('#finance-table')) {
            $('#finance-table').DataTable().ajax.reload(null, false);
          } else {
            location.reload();
          }
          $('#edit-finance').find('.close-modal').trigger('click');
        }
      },
      error: function(xhr){
        let msg = xhr?.responseJSON?.message || 'No se pudo actualizar';
        if (xhr.status === 422 && xhr.responseJSON?.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
        }
        if(window.Swal) Swal.fire('Error', msg, 'error'); else alert(msg);
      },
      complete: function(){
        $('#update-finance').prop('disabled', false);
      }
    });
  });

});
</script>
@endpush