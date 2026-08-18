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
    <button id="btn-open-docs-cxp" type="button" class="open-modal hidden" data-target="documents-cxp"></button>

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
        <div class="flex items-center gap-2">
          <label for="filter-semana" class="text-sm font-medium text-gray-600">Semana Fiscal:</label>
          <select id="filter-semana" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
            <option value="">Todas</option>
            @for ($i = 1; $i <= 53; $i++)
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
                <th class="px-2 py-2 text-right">Cantidad</th>
                <th class="px-2 py-2">Motivo</th>
                <th class="px-2 py-2">Factura</th>
                <th class="px-2 py-2">Fecha de factura</th>
                <th class="px-2 py-2">Fecha de pago</th>
                <th class="px-2 py-2 text-center">Semana fiscal</th>
                <th class="px-2 py-2">Banco</th>
                <th class="px-2 py-2">Estatus</th>
                <th class="px-2 py-2">Comentarios</th>
                <th class="px-2 py-2">Departamento</th>
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

  const renderBancoBadge = (banco) => {
    if (!banco || banco === '—' || banco === 'N/A') return `<span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">—</span>`;
    
    let bgColor = 'bg-gray-100';
    let textColor = 'text-gray-800';
    let iconClass = 'ri-bank-line';

    const b = banco.toLowerCase();
    if (b.includes('bbva')) { bgColor = 'bg-[#072146]'; textColor = 'text-white'; }
    else if (b.includes('santander')) { bgColor = 'bg-[#ec0000]'; textColor = 'text-white'; }
    else if (b.includes('banorte')) { bgColor = 'bg-[#eb0029]'; textColor = 'text-white'; }
    else if (b.includes('banamex') || b.includes('citi')) { bgColor = 'bg-[#00529b]'; textColor = 'text-white'; }
    else if (b.includes('hsbc')) { bgColor = 'bg-[#db0011]'; textColor = 'text-white'; }
    else if (b.includes('scotiabank')) { bgColor = 'bg-[#ec111a]'; textColor = 'text-white'; }
    else if (b.includes('inbursa')) { bgColor = 'bg-[#005596]'; textColor = 'text-white'; }
    else if (b.includes('afirme')) { bgColor = 'bg-[#009b3e]'; textColor = 'text-white'; }
    else if (b.includes('banbajio') || b.includes('banbajío')) { bgColor = 'bg-[#004b87]'; textColor = 'text-white'; }
    else if (b.includes('banregio')) { bgColor = 'bg-[#e24a2c]'; textColor = 'text-white'; }
    else if (b.includes('hey')) { bgColor = 'bg-[#000000]'; textColor = 'text-white'; }
    else if (b.includes('nu')) { bgColor = 'bg-[#8A05BE]'; textColor = 'text-white'; }

    return `<span class="px-2 py-1 ${bgColor} ${textColor} rounded text-[10px] font-bold shadow-sm uppercase tracking-wider inline-flex items-center gap-1">
              <i class="${iconClass}"></i> ${banco}
            </span>`;
  };

  // Datatable Init
  const table = $('#cxp-table').DataTable({
    ajax: {
      url: "{{ route('cuentas-por-pagar.datatable') }}",
      dataSrc: 'data',
      data: function(d) {
        d.month = $('#filter-month').val();
        d.year = $('#filter-year').val();
        d.semana = $('#filter-semana').val();
      }
    },
    createdRow: function(row, data, dataIndex) {
      if (data.is_canceled) {
        $(row).addClass('row-canceled');
      }
    },
    columns: [
      { data: 'empresa', render: data => `<span class="font-bold text-gray-700">${data}</span>` },
      { data: 'total', className: 'text-right font-medium', render: formatCurrency },
      { data: 'motivo', render: data => `<span class="text-xs truncate max-w-[150px] block" title="${data}">${data || '—'}</span>` },
      { data: 'folio_factura', render: data => `<span class="font-semibold text-blue-800">${data || '—'}</span>` },
      { data: 'fecha_factura', render: data => `<span class="text-xs text-gray-500">${formatDate(data)}</span>` },
      { data: 'fecha_pago', render: data => `<span class="text-xs text-gray-800 font-medium">${formatDate(data)}</span>` },
      { data: 'semana', className: 'text-center', render: data => data ? `<span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">Semana ${data}</span>` : '—' },
      { data: 'banco', render: renderBancoBadge },
      { data: 'estatus', render: function(data, type, row){
          if (row.is_canceled) return '<span class="font-bold text-red-600">CANCELADO</span>';
          let color = 'bg-gray-100 text-gray-800';
          if(data === 'PAGADO') color = 'bg-green-100 text-green-800';
          if(data === 'PENDIENTE') color = 'bg-blue-100 text-blue-800';
          if(data === 'PARCIAL') color = 'bg-yellow-100 text-yellow-800';
          return `<span class="px-2 py-1 rounded text-xs font-semibold uppercase ${color}">${data}</span>`;
      }},
      { data: 'comentarios', render: function(data, type, row) {
          let html = `<span class="text-xs text-gray-600 break-words max-w-[150px] block" title="${data}">${data || '—'}</span>`;
          if (row.comentario_img) {
            html += `<a href="/${row.comentario_img}" target="_blank" class="text-[10px] text-blue-600 hover:underline flex items-center gap-1 mt-1"><i class="ri-attachment-line"></i> Ver adjunto</a>`;
          }
          return html;
      }},
      { data: 'departamento', render: data => `<span class="text-sm text-gray-600">${data || '—'}</span>` },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(row){
          const disabledAttr = row.is_canceled ? 'disabled opacity-50 cursor-not-allowed' : '';
          
          return `
            <div class="flex items-center justify-center gap-2">
              <button type="button" class="btn-docs-cxp p-1.5 rounded bg-orange-100 text-orange-600 hover:bg-orange-200 transition" title="Documentos" data-id="${row.cxp_id}" data-row='${escapeHtml(JSON.stringify(row))}' ${disabledAttr}>
                <i class="ri-file-upload-line text-lg"></i>
              </button>
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
    const s = $('#filter-semana').val();
    let url = "{{ route('cuentas-por-pagar.export-excel') }}?";
    if (m) url += `month=${m}&`;
    if (y) url += `year=${y}&`;
    if (s) url += `semana=${s}`;
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
    $('#edit-semana').val(row.semana);
    $('#edit-banco').val(row.banco);
    $('#edit-departamento-cxp').val(row.departamento);
    $('#edit-comentarios-cxp').val(row.comentarios);
    $('#edit-comentario-img').val('');
    
    $('#btn-open-edit-cxp').trigger('click');
  });

  // Abrir Modal de Documentos
  $(document).on('click', '.btn-docs-cxp', function() {
    const id = $(this).data('id');
    const row = $(this).data('row');
    $('#docs-cxp-id').val(id);
    
    // Clear previous inputs
    $('#docs-pdf').val('');
    $('#docs-xml').val('');
    
    if (row.pdf_path) {
      $('#current-pdf-link').attr('href', '/' + row.pdf_path).removeClass('hidden');
    } else {
      $('#current-pdf-link').addClass('hidden');
    }

    if (row.xml_path) {
      $('#current-xml-link').attr('href', '/' + row.xml_path).removeClass('hidden');
    } else {
      $('#current-xml-link').addClass('hidden');
    }
    
    $('#btn-open-docs-cxp').trigger('click');
  });

  // Enviar Documentos
  $('#upload-docs-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#docs-cxp-id').val();
    const formData = new FormData();
    
    const pdfFile = document.getElementById('docs-pdf').files[0];
    const xmlFile = document.getElementById('docs-xml').files[0];
    
    if (!pdfFile && !xmlFile) {
        Swal.fire('Atención', 'Selecciona al menos un documento para subir.', 'warning');
        return;
    }
    
    if (pdfFile) formData.append('pdf_file', pdfFile);
    if (xmlFile) formData.append('xml_file', xmlFile);

    $('#btn-save-docs').prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i> Subiendo...');

    $.ajax({
      url: `/cuentas-por-pagar/${id}/documents`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire('Subidos', res.message, 'success');
          table.ajax.reload(null, false);
          $('#documents-cxp').find('.close-modal').trigger('click');
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Error al subir los documentos', 'error');
      },
      complete: function() {
        $('#btn-save-docs').prop('disabled', false).html('<i class="ri-upload-cloud-2-line"></i> Subir Documentos');
      }
    });
  });

  // Guardar Edición
  $('#edit-cxp-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#edit-cxp-id').val();
    
    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('semana', $('#edit-semana').val());
    formData.append('banco', $('#edit-banco').val());
    formData.append('departamento', $('#edit-departamento-cxp').val());
    formData.append('comentarios', $('#edit-comentarios-cxp').val());

    const imgFile = $('#edit-comentario-img')[0].files[0];
    if (imgFile) formData.append('comentario_img', imgFile);

    $('#btn-save-cxp').prop('disabled', true).text('Guardando...');

    $.ajax({
      url: `/cuentas-por-pagar/${id}/update`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
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
    $('#pay-banco').val('');
    $('#pay-metodo').val('');
    $('#pay-comprobante').val('');
    $('#pay-notas').val('');
    
    if (parseFloat(row.saldo) <= 0) {
      $('#add-payment-container input, #add-payment-container select, #add-payment-container textarea, #add-payment-container button').prop('disabled', true);
      $('#btn-save-payment').text('Cuenta Pagada').removeClass('hover:bg-[#157347]').addClass('opacity-50 cursor-not-allowed');
    } else {
      $('#add-payment-container input, #add-payment-container select, #add-payment-container textarea, #add-payment-container button').prop('disabled', false);
      $('#btn-save-payment').html('<i class="ri-save-line"></i> Registrar').removeClass('opacity-50 cursor-not-allowed').addClass('hover:bg-[#157347]');
    }

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
            const bancoMetodo = `${renderBancoBadge(p.banco)} <span class="text-gray-500 ml-1">${p.metodo_pago || ''}</span>`;
            tbody.append(`
              <tr>
                <td class="px-4 py-2 text-gray-500">#${p.id}</td>
                <td class="px-4 py-2">${formatDate(p.date)}</td>
                <td class="px-4 py-2">${bancoMetodo}</td>
                <td class="px-4 py-2"><span class="text-xs text-gray-700">${escapeHtml(p.user_name || '—')}</span></td>
                <td class="px-4 py-2 truncate max-w-[150px]" title="${escapeHtml(p.notas)}">${escapeHtml(p.notas) || '—'}</td>
                <td class="px-4 py-2">
                  ${p.comprobante ? `<a href="/${p.comprobante}" target="_blank" class="text-blue-600 hover:underline"><i class="ri-file-text-line"></i> Ver</a>` : '<span class="text-gray-400">N/A</span>'}
                </td>
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
    const formData = new FormData(this);
    formData.append('amount', $('#pay-amount').val());
    formData.append('date', $('#pay-date').val());
    formData.append('notas', $('#pay-notas').val());
    formData.append('banco', $('#pay-banco').val());
    formData.append('metodo_pago', $('#pay-metodo').val());
    
    const fileInput = document.getElementById('pay-comprobante');
    if (fileInput.files[0]) {
      formData.append('comprobante', fileInput.files[0]);
    }

    $('#btn-save-payment').prop('disabled', true);

    $.ajax({
      url: `/cuentas-por-pagar/${id}/payments`,
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
