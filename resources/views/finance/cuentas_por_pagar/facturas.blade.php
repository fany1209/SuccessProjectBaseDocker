@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="w-full px-4 sm:px-6 lg:px-8 pb-12">
  <section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <div class="mt-6 text-center w-full relative">
      <a href="{{ route('cuentas-por-pagar.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold">
        <i class="ri-arrow-left-s-line text-xl"></i> Volver
      </a>

      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        <i class="ri-building-line mr-1"></i> Facturas por Pagar
      </h1>
      <p class="text-sm text-gray-600 mt-1">Gestión de Cuentas por Pagar</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    @include('finance.cuentas_por_pagar.modals.payments')
    <button id="btn-open-edit-cxp" type="button" class="open-modal hidden" data-target="edit-cxp"></button>
    <button id="btn-open-payments" type="button" class="open-modal hidden" data-target="payments-cxp"></button>

    <!-- Filtros -->
    <div class="mb-2 mt-6 flex justify-end w-full">
      <div class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl p-3 shadow-sm flex items-center gap-3">
        <div class="flex items-center gap-2">
          <label for="filter-month" class="text-sm font-medium text-gray-600">Mes de Factura:</label>
          <select id="filter-month" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
            <option value="">Todos</option>
            @for ($i = 1; $i <= 12; $i++)
              <option value="{{ $i }}">
                {{ ucfirst(\Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}
              </option>
            @endfor
          </select>
        </div>
        <div class="flex items-center gap-2">
          <label for="filter-year" class="text-sm font-medium text-gray-600">Año:</label>
          <select id="filter-year" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
            <option value="">Todos</option>
            @for ($i = now()->year; $i >= 2020; $i--)
              <option value="{{ $i }}">{{ $i }}</option>
            @endfor
          </select>
        </div>
        <button type="button" id="btn-filter" class="bg-[#198754] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#157347] transition flex items-center gap-1">
          <i class="ri-filter-3-line"></i> Filtrar
        </button>
        <button type="button" id="btn-export" class="bg-[#198754] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#157347] transition flex items-center gap-1 ml-2 shadow-sm">
          <i class="ri-file-excel-2-line text-white"></i> Exportar a Excel
        </button>
      </div>
    </div>

    <!-- Tabla de facturas -->
    <div class="w-full mt-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full p-4">
          <table id="cxp-table" class="display w-full divide-y divide-gray-200 text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs tracking-wider">
              <tr>
                <th class="px-2 py-2">Empresa</th>
                <th class="px-2 py-2">Factura</th>
                <th class="px-2 py-2">Motivo</th>
                <th class="px-2 py-2">Banco / Método</th>
                <th class="px-2 py-2">Fechas (Fac/Pago)</th>
                <th class="px-2 py-2">Semana/Año</th>
                <th class="px-2 py-2">Estatus</th>
                <th class="px-2 py-2 text-right">Cantidad</th>
                <th class="px-2 py-2 text-right">Saldo Restante</th>
                <th class="px-2 py-2 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-800">
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </section>
</div>
@endsection

@push('css')
<style>
  .row-canceled td {
    background-color: #fee2e2 !important; /* light red */
    color: #991b1b !important;
  }
</style>
@endpush

@push('js')
<script>
$(function(){
  const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

  // Format Helpers
  const formatCurrency = (val) => '$' + parseFloat(val || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    return isNaN(d.getTime()) ? dateStr : d.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
  };
  const toInputDate = (dateStr) => dateStr ? dateStr.slice(0, 10) : '';

  // Datatable Init
  const table = $('#cxp-table').DataTable({
    ajax: {
      url: "{{ route('cuentas-por-pagar.datatable') }}",
      dataSrc: 'data',
      data: function(d) {
        d.month = $('#filter-month').val();
        d.year = $('#filter-year').val();
      }
    },
    createdRow: function(row, data, dataIndex) {
      if (data.is_canceled) {
        $(row).addClass('row-canceled');
      }
    },
    columns: [
      { data: 'empresa', render: data => `<span class="font-bold text-gray-700">${data}</span>` },
      { data: 'folio_factura', render: data => `<span class="font-semibold text-blue-800">${data || '—'}</span>` },
      { data: 'motivo', render: data => `<span class="text-xs truncate max-w-[150px] block" title="${data}">${data || '—'}</span>` },
      { data: null, render: function(row) {
          return `<span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium">${row.banco || '—'}</span><br>
                  <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500">${row.metodo_pago || '—'}</span>`;
      }},
      { data: null, render: function(row) {
          return `<span class="text-xs text-gray-500">F: ${formatDate(row.fecha_factura)}</span><br>
                  <span class="text-xs text-gray-800 font-medium">P: ${formatDate(row.fecha_pago)}</span>`;
      }},
      { data: null, render: function(row) {
          return `Sem ${row.semana || '—'} / ${row.anio || '—'}`;
      }},
      { data: 'estatus', render: function(data, type, row){
          if (row.is_canceled) return '<span class="font-bold text-red-600">CANCELADO</span>';
          let color = 'bg-gray-100 text-gray-800';
          if(data === 'PAGADO') color = 'bg-green-100 text-green-800';
          if(data === 'PENDIENTE') color = 'bg-blue-100 text-blue-800';
          if(data === 'PARCIAL') color = 'bg-yellow-100 text-yellow-800';
          return `<span class="px-2 py-1 rounded text-xs font-semibold uppercase ${color}">${data}</span>`;
      }},
      { data: 'total', className: 'text-right font-medium', render: formatCurrency },
      { data: 'saldo', className: 'text-right font-bold', render: function(data, type, row){
          if(row.is_canceled) return `<span class="text-red-600">${formatCurrency(0)}</span>`;
          return `<span class="${data <= 0 ? 'text-green-600' : 'text-red-600'}">${formatCurrency(data)}</span>`;
      }},
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(row){
          const disabledAttr = row.is_canceled ? 'disabled opacity-50 cursor-not-allowed' : '';
          
          return `
            <div class="flex items-center justify-center gap-2">
              <button type="button" class="btn-payments p-1.5 rounded bg-purple-100 text-purple-600 hover:bg-purple-200 transition" title="Abonos" data-row='${escapeHtml(JSON.stringify(row))}' ${disabledAttr}>
                <i class="ri-money-dollar-circle-line text-lg"></i>
              </button>
              <button type="button" class="btn-edit-cxp p-1.5 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition" title="Editar Detalles" data-row='${escapeHtml(JSON.stringify(row))}' ${disabledAttr}>
                <i class="ri-edit-2-line text-lg"></i>
              </button>
              <button type="button" class="btn-cancel-cxp p-1.5 rounded bg-red-100 text-red-600 hover:bg-red-200 transition" title="Cancelar Cuenta" data-id="${row.cxp_id}" ${disabledAttr}>
                <i class="ri-close-circle-line text-lg"></i>
              </button>
            </div>
          `;
        }
      }
    ],
    pageLength: 10,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
    }
  });

  function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
  }

  $('#btn-filter').on('click', function() {
    table.ajax.reload();
  });

  $('#btn-export').on('click', function() {
    const m = $('#filter-month').val();
    const y = $('#filter-year').val();
    let url = "{{ route('cuentas-por-pagar.export-excel') }}?";
    if (m) url += `month=${m}&`;
    if (y) url += `year=${y}`;
    window.location.href = url;
  });

  // Cancelar
  $(document).on('click', '.btn-cancel-cxp', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: '¿Estás seguro?',
      text: "Se cancelará esta cuenta por pagar.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6e7d88',
      confirmButtonText: 'Sí, cancelar',
      cancelButtonText: 'No, mantener'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/cuentas-por-pagar/${id}/cancel`,
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
          success: function(res) {
            if(res.success) {
              Swal.fire('Cancelada', res.message, 'success');
              table.ajax.reload(null, false);
            }
          },
          error: function() {
            Swal.fire('Error', 'No se pudo cancelar la cuenta.', 'error');
          }
        });
      }
    });
  });

  // Abrir Modal de Edición
  $(document).on('click', '.btn-edit-cxp', function() {
    const row = $(this).data('row');
    $('#edit-cxp-id').val(row.cxp_id);
    $('#edit-fecha-pago').val(toInputDate(row.fecha_pago));
    $('#edit-semana').val(row.semana);
    $('#edit-anio').val(row.anio);
    $('#edit-banco').val(row.banco);
    $('#edit-metodo-pago').val(row.metodo_pago);
    
    $('#btn-open-edit-cxp').trigger('click');
  });

  // Guardar Edición
  $('#edit-cxp-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#edit-cxp-id').val();
    const payload = {
      fecha_pago: $('#edit-fecha-pago').val(),
      semana: $('#edit-semana').val(),
      anio: $('#edit-anio').val(),
      banco: $('#edit-banco').val(),
      metodo_pago: $('#edit-metodo-pago').val(),
    };

    $('#btn-save-cxp').prop('disabled', true).text('Guardando...');

    $.ajax({
      url: `/cuentas-por-pagar/${id}/update`,
      method: 'POST',
      data: payload,
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire('Guardado', res.message, 'success');
          table.ajax.reload(null, false);
          $('#edit-cxp').find('.close-modal').trigger('click');
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar', 'error');
      },
      complete: function() {
        $('#btn-save-cxp').prop('disabled', false).text('Guardar Cambios');
      }
    });
  });

  // Abrir Modal de Pagos (Abonos)
  $(document).on('click', '.btn-payments', function() {
    const row = $(this).data('row');
    $('#pay-cxp-id').val(row.cxp_id);
    $('#pay-folio').text(row.folio_factura || row.empresa);
    $('#pay-total').text(formatCurrency(row.total));
    $('#pay-saldo').text(formatCurrency(row.saldo));
    
    // Clear form
    $('#pay-amount').val('');
    $('#pay-date').val(new Date().toISOString().split('T')[0]);
    $('#pay-comprobante').val('');
    $('#pay-notas').val('');

    loadPayments(row.cxp_id);
    $('#btn-open-payments').trigger('click');
  });

  function loadPayments(id) {
    $('#payments-list').html('<tr><td colspan="4" class="text-center py-4">Cargando...</td></tr>');
    $('#no-payments-msg').addClass('hidden');

    $.get(`/cuentas-por-pagar/${id}/payments`, function(res) {
      if(res.success) {
        const tbody = $('#payments-list');
        tbody.empty();
        if(res.payments.length === 0) {
          $('#no-payments-msg').removeClass('hidden');
        } else {
          res.payments.forEach(p => {
            tbody.append(`
              <tr>
                <td class="px-4 py-2 text-gray-500">#${p.id}</td>
                <td class="px-4 py-2">${formatDate(p.date)}</td>
                <td class="px-4 py-2 truncate max-w-[150px]" title="${escapeHtml(p.notas)}">${escapeHtml(p.notas) || '—'}</td>
                <td class="px-4 py-2 text-right font-semibold text-green-700">${formatCurrency(p.amount)}</td>
              </tr>
            `);
          });
        }
      }
    });
  }

  // Guardar Abono
  $('#add-payment-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#pay-cxp-id').val();
    const payload = {
      amount: $('#pay-amount').val(),
      date: $('#pay-date').val(),
      comprobante: $('#pay-comprobante').val(),
      notas: $('#pay-notas').val(),
    };

    $('#btn-save-payment').prop('disabled', true);

    $.ajax({
      url: `/cuentas-por-pagar/${id}/payments`,
      method: 'POST',
      data: payload,
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
            icon: 'success', title: res.message
          });
          loadPayments(id);
          table.ajax.reload(null, false);
          
          const currentSaldo = parseFloat($('#pay-saldo').text().replace(/[^0-9.-]+/g,""));
          const newSaldo = currentSaldo - parseFloat(payload.amount);
          $('#pay-saldo').text(formatCurrency(newSaldo));
          $('#pay-amount').val('');
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Error al añadir abono', 'error');
      },
      complete: function() {
        $('#btn-save-payment').prop('disabled', false);
      }
    });
  });

});
</script>
@endpush
