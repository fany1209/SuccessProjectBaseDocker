@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="w-full px-4 sm:px-6 lg:px-8 pb-12">
  <section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <div class="mt-6 text-center w-full relative">
      <a href="{{ route('cuentas-por-cobrar.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold">
        <i class="ri-arrow-left-s-line text-xl"></i> Volver
      </a>

      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        <i class="ri-team-line mr-1"></i> Clientes
      </h1>
      <p class="text-sm text-gray-600 mt-1">Gestión de Cuentas por Cobrar</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    @include('finance.cuentas_por_cobrar.modals.modals')
    <button id="btn-open-edit-cxc" type="button" class="open-modal hidden" data-target="edit-cxc"></button>
    <button id="btn-open-payments" type="button" class="open-modal hidden" data-target="payments-cxc"></button>
    <button id="btn-open-edit-payment" type="button" class="open-modal hidden" data-target="edit-cxc-payment-modal"></button>

    <!-- Filtros -->
    <div class="mb-2 mt-6 flex justify-end w-full">
      <div class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl p-3 shadow-sm flex items-center gap-3">
        <div class="flex items-center gap-2">
          <label for="filter-month" class="text-sm font-medium text-gray-600">Mes:</label>
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
        <button type="button" id="btn-export-excel" class="bg-[#217346] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#1e6b40] transition flex items-center gap-1">
          <i class="ri-file-excel-2-line"></i> Exportar a Excel
        </button>
      </div>
    </div>

    <!-- Tabla de clientes -->
    <div class="w-full mt-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full p-4">
          <table id="cxc-table" class="display w-full divide-y divide-gray-200 text-sm text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs tracking-wider">
              <tr>
                <th class="px-2 py-2">Folio</th>
                <th class="px-2 py-2">Fecha Emisión</th>
                <th class="px-2 py-2">Cliente</th>
                <th class="px-2 py-2">Asesor</th>
                <th class="px-2 py-2">Documento</th>
                <th class="px-2 py-2">Método Pago</th>
                <th class="px-2 py-2">Estatus</th>
                <th class="px-2 py-2">Fecha Conclusión</th>
                <th class="px-2 py-2 text-right">Total</th>
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
    background-color: #fee2e2 !important; 
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
  const table = $('#cxc-table').DataTable({
    ajax: {
      url: "{{ route('cuentas-por-cobrar.datatable') }}",
      dataSrc: 'data',
      data: function(d) {
        d.month = $('#filter-month').val();
        d.year = $('#filter-year').val();
      }
    },
    createdRow: function(row, data, dataIndex) {
      if (data.is_canceled == 1 || data.is_canceled === true || data.is_canceled === '1') {
        $(row).addClass('row-canceled');
      }
    },
    columns: [
      { data: 'folio', render: data => `<span class="font-bold text-gray-700">#${escapeHtml(data)}</span>` },
      { data: 'fecha_emision', render: formatDate },
      { data: 'cliente_name', render: data => `<span class="font-semibold text-blue-800">${escapeHtml(data)}</span>` },
      { data: 'asesor', render: data => escapeHtml(data || '—') },
      { data: 'documento', render: data => `<span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium">${escapeHtml(data)}</span>` },
      { data: 'metodo_pago', render: data => {
          let color = data === 'PPD' ? 'bg-orange-100 text-orange-800' : (data === 'PUE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800');
          return `<span class="px-2 py-1 rounded text-xs font-semibold ${color}">${data}</span>`;
      }},
      { data: 'estatus', render: function(data, type, row){
          if (row.is_canceled == 1 || row.is_canceled === true || row.is_canceled === '1') return '<span class="font-bold text-red-600">CANCELADA</span>';
          let color = 'bg-gray-100 text-gray-800';
          if(data === 'Pagado') color = 'bg-green-100 text-green-800';
          if(data === 'Pendiente') color = 'bg-blue-100 text-blue-800';
          if(data === 'Parcial') color = 'bg-yellow-100 text-yellow-800';
          return `<span class="px-2 py-1 rounded text-xs font-semibold uppercase ${color}">${data}</span>`;
      }},
      { data: 'fecha_conclusion', render: formatDate },
      { data: 'total_venta', className: 'text-right font-medium', render: formatCurrency },
      { data: 'saldo', className: 'text-right font-bold', render: function(data, type, row){
          if (row.is_canceled == 1 || row.is_canceled === true || row.is_canceled === '1') return `<span class="text-red-600">${formatCurrency(0)}</span>`;
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
              
              <button type="button" class="btn-payments p-1.5 rounded bg-purple-100 text-purple-600 hover:bg-purple-200 transition" title="Complementos de Pago" data-row='${escapeHtml(JSON.stringify(row))}' ${disabledAttr}>
                <i class="ri-money-dollar-circle-line text-lg"></i>
              </button>
              <button type="button" class="btn-edit-cxc p-1.5 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition" title="Editar Detalles" data-row='${escapeHtml(JSON.stringify(row))}' ${disabledAttr}>
                <i class="ri-edit-2-line text-lg"></i>
              </button>
              <button type="button" class="btn-cancel-cxc p-1.5 rounded bg-red-100 text-red-600 hover:bg-red-200 transition" title="Cancelar Cuenta" data-id="${row.cxc_id}" ${disabledAttr}>
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

  // Util for escaping JSON strings for data attributes
  function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
  }

  // Filtro
  $('#btn-filter').on('click', function() {
    table.ajax.reload();
  });

  // Exportar Excel
  $('#btn-export-excel').on('click', function() {
    const month = $('#filter-month').val();
    const year = $('#filter-year').val();
    let url = "{{ route('cuentas-por-cobrar.export-excel') }}?";
    if (month) url += "month=" + month + "&";
    if (year) url += "year=" + year;
    window.location.href = url;
  });

  // --- ACTIONS ---
  // Cancelar
  $(document).on('click', '.btn-cancel-cxc', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: '¿Estás seguro?',
      text: "Se cancelará esta cuenta y no podrás editarla ni agregar pagos. La fila se marcará como CANCELADA.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6e7d88',
      confirmButtonText: 'Sí, cancelar cuenta',
      cancelButtonText: 'No, mantener'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/cuentas-por-cobrar/${id}/cancel`,
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
  $(document).on('click', '.btn-edit-cxc', function() {
    const row = $(this).data('row');
    $('#edit-cxc-id').val(row.cxc_id);
    $('#edit-documento').val(row.documento || 'Factura');
    $('#edit-metodo-pago').val(row.metodo_pago);
    $('#edit-fecha-conclusion').val(toInputDate(row.fecha_conclusion));
    $('#edit-descripcion').val(row.descripcion);
    
    $('#btn-open-edit-cxc').trigger('click');
  });

  // Guardar Edición
  $('#edit-cxc-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#edit-cxc-id').val();
    const payload = {
      documento: $('#edit-documento').val(),
      metodo_pago: $('#edit-metodo-pago').val(),
      fecha_conclusion: $('#edit-fecha-conclusion').val(),
      descripcion: $('#edit-descripcion').val(),
    };

    $('#btn-save-cxc').prop('disabled', true).text('Guardando...');

    $.ajax({
      url: `/cuentas-por-cobrar/${id}/update`,
      method: 'POST',
      data: payload,
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire('Guardado', res.message, 'success');
          table.ajax.reload(null, false);
          $('#edit-cxc').find('.close-modal').trigger('click');
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar', 'error');
      },
      complete: function() {
        $('#btn-save-cxc').prop('disabled', false).text('Guardar Cambios');
      }
    });
  });

  // Abrir Modal de Pagos
  $(document).on('click', '.btn-payments', function() {
    const row = $(this).data('row');
    $('#pay-cxc-id').val(row.cxc_id);
    $('#pay-folio').text('#' + row.folio);
    $('#pay-total').text(formatCurrency(row.total_venta));
    $('#pay-saldo').text(formatCurrency(row.saldo));
    
    // Clear form
    $('#pay-amount').val('');
    $('#pay-date').val(new Date().toISOString().split('T')[0]);

    if (row.estatus === 'Pagado' || row.saldo <= 0) {
      $('#add-payment-form').hide();
    } else {
      $('#add-payment-form').show();
    }

    loadPayments(row.cxc_id);
    $('#btn-open-payments').trigger('click');
  });

  function refreshCxcHeaderSaldo(cxcId) {
    $.get("{{ route('cuentas-por-cobrar.datatable') }}", function(dataResponse) {
      if (dataResponse && dataResponse.data) {
        const cxcRow = dataResponse.data.find(r => r.cxc_id == cxcId);
        if (cxcRow) {
          $('#pay-saldo').text(formatCurrency(cxcRow.saldo));
          if (parseFloat(cxcRow.saldo) <= 0 || cxcRow.estatus === 'Pagado') {
            $('#add-payment-form').hide();
          } else {
            $('#add-payment-form').show();
          }
        }
      }
    });
  }

  // Cargar lista de pagos
  function loadPayments(id) {
    $('#payments-list').html('<tr><td colspan="5" class="text-center py-4">Cargando...</td></tr>');
    $('#no-payments-msg').addClass('hidden');

    $.get(`/cuentas-por-cobrar/${id}/payments`, function(res) {
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
                <td class="px-4 py-2">
                  ${p.comprobante ? `<a href="/storage/comprobantes/${p.comprobante}" target="_blank" class="text-blue-600 hover:underline"><i class="ri-file-text-line"></i> Ver</a>` : '<span class="text-gray-400">N/A</span>'}
                </td>
                <td class="px-4 py-2 text-right font-semibold text-green-700">${formatCurrency(p.amount)}</td>
                <td class="px-4 py-2 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button type="button" class="btn-edit-payment p-1 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition" title="Editar" data-payment='${escapeHtml(JSON.stringify(p))}'>
                      <i class="ri-edit-2-line"></i>
                    </button>
                    <button type="button" class="btn-delete-payment p-1 rounded bg-red-100 text-red-600 hover:bg-red-200 transition" title="Eliminar" data-id="${p.id}">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
            `);
          });
        }
      }
    });
  }

  // Guardar Pago
  $('#add-payment-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#pay-cxc-id').val();
    const formData = new FormData(this);
    formData.append('amount', $('#pay-amount').val());
    formData.append('date', $('#pay-date').val());
    
    const fileInput = document.getElementById('pay-comprobante');
    if (fileInput.files[0]) {
      formData.append('comprobante', fileInput.files[0]);
    }

    $('#btn-save-payment').prop('disabled', true);

    $.ajax({
      url: `/cuentas-por-cobrar/${id}/payments`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
            icon: 'success', title: res.message
          });
          loadPayments(id);
          table.ajax.reload(null, false);
          refreshCxcHeaderSaldo(id);
          
          $('#pay-amount').val('');
          $('#pay-comprobante').val('');
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Error al añadir pago', 'error');
      },
      complete: function() {
        $('#btn-save-payment').prop('disabled', false);
      }
    });
  });

  // Abrir Modal de Edición de Abono
  $(document).on('click', '.btn-edit-payment', function() {
    const p = $(this).data('payment');
    const cxcId = $('#pay-cxc-id').val();

    $('#edit-pay-id').val(p.id);
    $('#edit-pay-cxc-id').val(cxcId);
    $('#edit-pay-amount').val(p.amount);
    $('#edit-pay-date').val(toInputDate(p.date));
    $('#edit-pay-comprobante').val('');

    $('#edit-payment-title').html(`<i class="ri-edit-2-fill text-[#198754]"></i> Editar Abono #${p.id}`);
    $('#btn-open-edit-payment').trigger('click');
  });

  // Guardar Edición de Abono
  $('#edit-cxc-payment-form').on('submit', function(e) {
    e.preventDefault();
    const paymentId = $('#edit-pay-id').val();
    const cxcId = $('#edit-pay-cxc-id').val();

    const formData = new FormData(this);
    formData.append('amount', $('#edit-pay-amount').val());
    formData.append('date', $('#edit-pay-date').val());
    formData.append('_method', 'PUT');

    const fileInput = document.getElementById('edit-pay-comprobante');
    if (fileInput.files[0]) {
      formData.append('comprobante', fileInput.files[0]);
    }

    $('#btn-save-edit-payment').prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i> Guardando...');

    $.ajax({
      url: `/cuentas-por-cobrar/payments/${paymentId}`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
            icon: 'success', title: res.message
          });
          $('#edit-cxc-payment-modal').find('.close-modal').trigger('click');
          loadPayments(cxcId);
          table.ajax.reload(null, false);
          refreshCxcHeaderSaldo(cxcId);
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Error al actualizar el abono', 'error');
      },
      complete: function() {
        $('#btn-save-edit-payment').prop('disabled', false).html('<i class="ri-save-line"></i> Guardar Cambios');
      }
    });
  });

  // Eliminar Abono
  $(document).on('click', '.btn-delete-payment', function() {
    const paymentId = $(this).data('id');
    const cxcId = $('#pay-cxc-id').val();

    Swal.fire({
      title: '¿Eliminar abono?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if(result.isConfirmed) {
        $.ajax({
          url: `/cuentas-por-cobrar/payments/${paymentId}`,
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
          success: function(res) {
            if(res.success) {
              Swal.fire({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                icon: 'success', title: res.message
              });
              loadPayments(cxcId);
              table.ajax.reload(null, false);
              refreshCxcHeaderSaldo(cxcId);
            }
          },
          error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar el abono', 'error');
          }
        });
      }
    });
  });

});
</script>
@endpush
