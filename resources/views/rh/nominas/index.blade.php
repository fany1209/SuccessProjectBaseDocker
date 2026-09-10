@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="w-full px-2 sm:px-4 pb-12">
  <section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <!-- Header -->
    <div class="mt-5 text-center w-full relative">
      <a href="{{ route('rh.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold text-xs sm:text-sm">
        <i class="ri-arrow-left-s-line text-lg"></i> Volver a RH
      </a>

      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754] flex items-center justify-center gap-2">
        <i class="ri-team-line"></i> Registro de Nóminas
      </h1>
      <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Gestión de Expedientes y Nóminas de Personal</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    <!-- Modales Incluidos -->
    @include('rh.nominas.modals.create')
    @include('rh.nominas.modals.edit')
    @include('rh.nominas.modals.view')

    <!-- Botones ocultos para disparar modales -->
    <button id="btn-open-create-modal" type="button" class="open-modal hidden" data-target="create-nomina-modal"></button>
    <button id="btn-open-edit-modal" type="button" class="open-modal hidden" data-target="edit-nomina-modal"></button>
    <button id="btn-open-view-modal" type="button" class="open-modal hidden" data-target="view-nomina-modal"></button>

    <!-- Barra de Herramientas y Filtros -->
    <div class="mb-3 mt-6 flex flex-wrap justify-between items-center gap-3 w-full">
      
      <div>
        <button type="button" id="btn-new-nomina" class="bg-[#198754] text-white px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold hover:bg-[#157347] transition flex items-center gap-1.5 shadow-sm">
          <i class="ri-user-add-line text-base"></i> Registrar Empleado
        </button>
      </div>

      <!-- Filtros estilo facturas.blade.php -->
      <div class="bg-white/95 backdrop-blur-md border border-gray-200 rounded-xl p-2 shadow-sm flex flex-wrap items-center gap-2.5">
        <div class="flex items-center gap-1.5">
          <label for="filter-estatus" class="text-xs font-semibold text-gray-600">Estatus:</label>
          <select id="filter-estatus" class="text-xs border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754] py-1 px-2">
            <option value="">Todos</option>
            <option value="Activo">Activo</option>
            <option value="Baja">Baja</option>
          </select>
        </div>

        <div class="flex items-center gap-1.5">
          <label for="filter-puesto" class="text-xs font-semibold text-gray-600">Puesto:</label>
          <select id="filter-puesto" class="text-xs border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754] max-w-[160px] py-1 px-2">
            <option value="">Todos</option>
            @foreach ($puestos as $puesto)
              <option value="{{ $puesto }}">{{ $puesto }}</option>
            @endforeach
          </select>
        </div>

        <div class="flex items-center gap-1.5">
          <label for="filter-year" class="text-xs font-semibold text-gray-600">Año Ingreso:</label>
          <select id="filter-year" class="text-xs border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754] py-1 px-2">
            <option value="">Todos</option>
            @foreach ($years as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>

        <button type="button" id="btn-filter" class="bg-[#198754] text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-[#157347] transition flex items-center gap-1 shadow-sm">
          <i class="ri-filter-3-line"></i> Filtrar
        </button>

        <button type="button" id="btn-export" class="bg-[#198754] text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-[#157347] transition flex items-center gap-1 shadow-sm">
          <i class="ri-file-excel-2-line"></i> Exportar
        </button>
      </div>

    </div>

    <!-- Tabla de Nóminas (Optimizada para 100% de ancho sin scroll horizontal) -->
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-2">
      <table id="nominas-table" class="w-full divide-y divide-gray-200 text-left">
        <thead class="bg-gray-50 text-gray-700 uppercase font-bold text-[10px] tracking-tight">
          <tr>
            <th style="width: 8.5%;" class="px-1 py-2">Nombre</th>
            <th style="width: 6.5%;" class="px-1 py-2">CURP</th>
            <th style="width: 5%;" class="px-1 py-2">RFC</th>
            <th style="width: 4.5%;" class="px-1 py-2">NSS</th>
            <th style="width: 6.5%;" class="px-1 py-2">Puesto</th>
            <th style="width: 4.5%;" class="px-1 py-2 text-center">F. Ingreso</th>
            <th style="width: 4.5%;" class="px-1 py-2 text-center">F. Baja</th>
            <th style="width: 3%;" class="px-1 py-2 text-center">Edad</th>
            <th style="width: 5.5%;" class="px-1 py-2">Antigüedad</th>
            <th style="width: 3.5%;" class="px-1 py-2 text-center">Sexo</th>
            <th style="width: 4.5%;" class="px-1 py-2">Edo. Civil</th>
            <th style="width: 4.5%;" class="px-1 py-2 text-center">F. Nac.</th>
            <th style="width: 6.5%;" class="px-1 py-2">Beneficiario</th>
            <th style="width: 4.5%;" class="px-1 py-2">Parentesco</th>
            <th style="width: 7.5%;" class="px-1 py-2">Domicilio</th>
            <th style="width: 3%;" class="px-1 py-2 text-center">CP</th>
            <th style="width: 5.5%;" class="px-1 py-2">Teléfono</th>
            <th style="width: 6%;" class="px-1 py-2">Correo</th>
            <th style="width: 6%;" class="px-1 py-2 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-800 text-[11px]">
        </tbody>
      </table>
    </div>

  </section>
</div>
@endsection

@push('css')
<style>
  #nominas-table {
    table-layout: fixed !important;
    width: 100% !important;
  }
  #nominas-table th, #nominas-table td {
    padding: 5px 3px !important;
    font-size: 11px !important;
    line-height: 1.15 !important;
    vertical-align: middle !important;
  }
  #nominas-table th {
    font-size: 10px !important;
    white-space: normal !important;
    word-break: break-word !important;
    text-align: center !important;
  }
  #nominas-table_wrapper .dataTables_filter input {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
  #nominas-table_wrapper .dataTables_filter input:focus {
    border-color: #198754;
    outline: none;
    box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.2);
  }
  #nominas-table_wrapper .dataTables_length select {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.25rem 1.5rem 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
  #nominas-table_wrapper .dataTables_info,
  #nominas-table_wrapper .dataTables_paginate {
    font-size: 0.75rem !important;
    padding-top: 0.5rem !important;
  }
  .row-baja td {
    background-color: #fef2f2 !important;
  }
</style>
@endpush

@push('js')
<script>
$(function(){

  const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

  // Helper de Formato de Fechas Completo (dd/mm/aaaa)
  const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    return isNaN(d.getTime()) ? dateStr : d.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
  };

  // Helper de Formato de Fechas (formato compacto dd/mm/aa)
  const formatDateCompact = (dateStr) => {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = String(d.getFullYear()).slice(-2);
    return `${day}/${month}/${year}`;
  };

  const escapeHtml = (text) => {
    if (!text) return '';
    return text.toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  };

  // Funciones de Cálculo Automático
  function calculateAge(birthDateStr) {
    if (!birthDateStr) return '';
    const birth = new Date(birthDateStr + 'T00:00:00');
    if (isNaN(birth.getTime())) return '';
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
      age--;
    }
    return age >= 0 ? age : 0;
  }

  function calculateAntiguedad(ingresoStr, bajaStr) {
    if (!ingresoStr) return '';
    const start = new Date(ingresoStr + 'T00:00:00');
    if (isNaN(start.getTime())) return '';
    const end = bajaStr ? new Date(bajaStr + 'T00:00:00') : new Date();
    if (isNaN(end.getTime()) || start > end) return '0d';

    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    if (days < 0) {
      months--;
      const prevMonth = new Date(end.getFullYear(), end.getMonth(), 0);
      days += prevMonth.getDate();
    }
    if (months < 0) {
      years--;
      months += 12;
    }

    const parts = [];
    if (years > 0) parts.push(`${years}a`);
    if (months > 0) parts.push(`${months}m`);
    if (parts.length === 0) parts.push(`${days}d`);
    return parts.join(' ');
  }

  // Inicialización de DataTable con layout 100% fijo
  const table = $('#nominas-table').DataTable({
    autoWidth: false,
    ajax: {
      url: "{{ route('rh.nominas.datatable') }}",
      dataSrc: 'data',
      data: function(d) {
        d.estatus = $('#filter-estatus').val();
        d.puesto = $('#filter-puesto').val();
        d.year = $('#filter-year').val();
      }
    },
    createdRow: function(row, data, dataIndex) {
      if (data.estatus === 'Baja') {
        $(row).addClass('row-baja');
      }
    },
    columns: [
      { 
        data: 'nombre', 
        render: data => `<span class="font-bold text-gray-800 truncate block text-[11px]" title="${escapeHtml(data)}">${data}</span>` 
      },
      { 
        data: 'curp', 
        render: data => data && data !== '—' 
          ? `<span class="font-mono text-[10px] text-gray-700 truncate block tracking-tighter" title="${escapeHtml(data)}">${data}</span>` 
          : '<span class="text-gray-400 text-center block">—</span>' 
      },
      { 
        data: 'rfc', 
        render: data => data && data !== '—' 
          ? `<span class="font-mono text-[10px] text-gray-700 truncate block tracking-tighter" title="${escapeHtml(data)}">${data}</span>` 
          : '<span class="text-gray-400 text-center block">—</span>' 
      },
      { 
        data: 'nss', 
        render: data => data && data !== '—' 
          ? `<span class="font-mono text-[10px] text-gray-700 truncate block tracking-tighter" title="${escapeHtml(data)}">${data}</span>` 
          : '<span class="text-gray-400 text-center block">—</span>' 
      },
      { 
        data: 'puesto', 
        render: data => `<span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-1 py-0.5 rounded border border-emerald-100 truncate block text-center" title="${escapeHtml(data)}">${data || '—'}</span>` 
      },
      { 
        data: 'fecha_ingreso', 
        className: 'text-center',
        render: data => `<span class="font-mono text-[10px] text-gray-600 block">${formatDateCompact(data)}</span>` 
      },
      { 
        data: 'fecha_baja', 
        className: 'text-center',
        render: data => data 
          ? `<span class="font-mono text-[10px] text-red-600 font-bold block">${formatDateCompact(data)}</span>` 
          : '<span class="text-gray-400 block">—</span>' 
      },
      { 
        data: 'edad', 
        className: 'text-center',
        render: data => data !== '—' && data !== null && data !== '' 
          ? `<span class="text-blue-700 font-bold text-[11px]">${data}</span>` 
          : '<span class="text-gray-400">—</span>' 
      },
      { 
        data: 'antiguedad', 
        render: data => data && data !== '—' 
          ? `<span class="text-[10px] font-medium text-emerald-900 truncate block" title="${escapeHtml(data)}">${data}</span>` 
          : '<span class="text-gray-400">—</span>' 
      },
      { 
        data: 'sexo', 
        className: 'text-center',
        render: data => {
          if (!data || data === '—') return '<span class="text-gray-400">—</span>';
          const initial = data.charAt(0).toUpperCase();
          return `<span class="text-[10px] font-semibold text-gray-700" title="${escapeHtml(data)}">${initial === 'M' ? 'Masc' : (initial === 'F' ? 'Fem' : data)}</span>`;
        } 
      },
      { 
        data: 'estado_civil', 
        render: data => `<span class="text-[10px] text-gray-600 truncate block" title="${escapeHtml(data)}">${data || '—'}</span>` 
      },
      { 
        data: 'fecha_nacimiento', 
        className: 'text-center',
        render: data => `<span class="font-mono text-[10px] text-gray-500 block">${formatDateCompact(data)}</span>` 
      },
      { 
        data: 'nombre_beneficiario', 
        render: data => `<span class="text-[10px] text-gray-700 truncate block" title="${escapeHtml(data)}">${data || '—'}</span>` 
      },
      { 
        data: 'parentesco', 
        render: data => `<span class="text-[10px] text-gray-600 truncate block" title="${escapeHtml(data)}">${data || '—'}</span>` 
      },
      { 
        data: 'domicilio', 
        render: data => `<span class="text-[10px] text-gray-600 truncate block" title="${escapeHtml(data)}">${data || '—'}</span>` 
      },
      { 
        data: 'cp', 
        className: 'text-center',
        render: data => `<span class="font-mono text-[10px] text-gray-600">${data || '—'}</span>` 
      },
      { 
        data: 'telefono', 
        render: data => data && data !== '—' 
          ? `<a href="tel:${data}" class="text-[10px] text-blue-600 hover:underline truncate block" title="${escapeHtml(data)}">${data}</a>` 
          : '<span class="text-gray-400 text-center block">—</span>' 
      },
      { 
        data: 'correo', 
        render: data => data && data !== '—' 
          ? `<a href="mailto:${data}" class="text-[10px] text-blue-600 hover:underline truncate block" title="${escapeHtml(data)}">${data}</a>` 
          : '<span class="text-gray-400 text-center block">—</span>' 
      },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(row){
          const isBaja = row.estatus === 'Baja';
          const statusBadge = isBaja
            ? '<span class="px-1 py-0.2 bg-red-100 text-red-700 rounded text-[9px] font-bold tracking-tighter block uppercase">BAJA</span>'
            : '<span class="px-1 py-0.2 bg-green-100 text-green-700 rounded text-[9px] font-bold tracking-tighter block uppercase">ACTIVO</span>';

          return `
            <div class="flex items-center justify-center gap-0.5">
              <button type="button" class="btn-view-nomina p-1 rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition" title="Ver Detalle" data-id="${row.id}">
                <i class="ri-eye-line text-sm"></i>
              </button>
              <button type="button" class="btn-edit-nomina p-1 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition" title="Editar" data-id="${row.id}">
                <i class="ri-edit-2-line text-sm"></i>
              </button>
              <button type="button" class="btn-delete-nomina p-1 rounded bg-red-100 text-red-600 hover:bg-red-200 transition" title="Eliminar" data-id="${row.id}" data-nombre="${escapeHtml(row.nombre)}">
                <i class="ri-delete-bin-line text-sm"></i>
              </button>
            </div>
            <div class="mt-0.5 flex justify-center">
              ${statusBadge}
            </div>
          `;
        }
      }
    ],
    pageLength: 10,
    responsive: false,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
    }
  });

  // Filtros
  $('#btn-filter').on('click', function() {
    table.ajax.reload();
  });

  $('#btn-export').on('click', function() {
    const estatus = $('#filter-estatus').val();
    const puesto = $('#filter-puesto').val();
    const year = $('#filter-year').val();
    window.location.href = `{{ route('rh.nominas.export-excel') }}?estatus=${encodeURIComponent(estatus)}&puesto=${encodeURIComponent(puesto)}&year=${encodeURIComponent(year)}`;
  });

  // Abrir Modal de Creación
  $('#btn-new-nomina').on('click', function(e) {
    e.preventDefault();
    $('#create-nomina-form')[0].reset();
    $('#create-edad').val('');
    $('#create-antiguedad').val('');
    $('#create-nomina-modal').removeClass('hidden');
  });

  // Cálculos en tiempo real en formulario de CREACIÓN
  $('#create-fecha-nacimiento').on('change input', function() {
    const age = calculateAge($(this).val());
    $('#create-edad').val(age !== '' ? age + ' años' : '');
  });

  $('#create-fecha-ingreso, #create-fecha-baja').on('change input', function() {
    const ingreso = $('#create-fecha-ingreso').val();
    const baja = $('#create-fecha-baja').val();
    const anti = calculateAntiguedad(ingreso, baja);
    $('#create-antiguedad').val(anti);
    
    // Auto-sugerir estatus
    if (baja) {
      $('#create-estatus').val('Baja');
    } else if ($('#create-estatus').val() === 'Baja' && !baja) {
      $('#create-estatus').val('Activo');
    }
  });

  // Submit Creación
  $('#create-nomina-form').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btn-submit-create-nomina');
    btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i> Registrando...');

    $.ajax({
      url: "{{ route('rh.nominas.store') }}",
      method: "POST",
      data: $(this).serialize(),
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Registrado!',
            text: res.message,
            confirmButtonColor: '#198754'
          });
          table.ajax.reload(null, false);
          $('#create-nomina-modal').find('.close-modal').trigger('click');
          $('#create-nomina-form')[0].reset();
        }
      },
      error: function(xhr) {
        let msg = 'Ocurrió un error al registrar el empleado.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Error de Validación',
          html: msg,
          confirmButtonColor: '#dc3545'
        });
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="ri-save-line"></i> Registrar Empleado');
      }
    });
  });

  // Abrir Modal de Edición
  $(document).on('click', '.btn-edit-nomina', function() {
    const id = $(this).data('id');
    
    $.get(`/rh/nominas/${id}`, function(res) {
      if(res.success && res.nomina) {
        const n = res.nomina;
        $('#edit-nomina-id').val(n.id);
        $('#edit-nombre').val(n.nombre);
        $('#edit-curp').val(n.curp || '');
        $('#edit-rfc').val(n.rfc || '');
        $('#edit-nss').val(n.nss || '');
        $('#edit-sexo').val(n.sexo || '');
        $('#edit-estado-civil').val(n.estado_civil || '');
        $('#edit-fecha-nacimiento').val(n.fecha_nacimiento || '');
        
        // Vista previa dinámica no editable de edad y antigüedad
        const computedAge = calculateAge(n.fecha_nacimiento) || n.edad;
        $('#edit-preview-edad').text(computedAge ? computedAge + ' años' : '—');

        $('#edit-puesto').val(n.puesto || '');
        $('#edit-fecha-ingreso').val(n.fecha_ingreso || '');
        $('#edit-fecha-baja').val(n.fecha_baja || '');
        $('#edit-estatus').val(n.estatus || 'Activo');

        const computedAnti = calculateAntiguedad(n.fecha_ingreso, n.fecha_baja) || n.antiguedad;
        $('#edit-preview-antiguedad').text(computedAnti || '—');

        $('#edit-nombre-beneficiario').val(n.nombre_beneficiario || '');
        $('#edit-parentesco').val(n.parentesco || '');
        $('#edit-domicilio').val(n.domicilio || '');
        $('#edit-cp').val(n.cp || '');
        $('#edit-telefono').val(n.telefono || '');
        $('#edit-correo').val(n.correo || '');

        $('#edit-modal-title').text(`Editar Nómina - ${n.nombre}`);
        $('#edit-nomina-modal').removeClass('hidden');
      }
    }).fail(function() {
      Swal.fire('Error', 'No se pudo cargar la información del empleado.', 'error');
    });
  });

  // Cálculos dinámicos en tiempo real en formulario de EDICIÓN (actualizan las insignias de vista previa)
  $('#edit-fecha-nacimiento').on('change input', function() {
    const age = calculateAge($(this).val());
    $('#edit-preview-edad').text(age !== '' ? age + ' años' : '—');
  });

  $('#edit-fecha-ingreso, #edit-fecha-baja').on('change input', function() {
    const ingreso = $('#edit-fecha-ingreso').val();
    const baja = $('#edit-fecha-baja').val();
    const anti = calculateAntiguedad(ingreso, baja);
    $('#edit-preview-antiguedad').text(anti || '—');

    if (baja) {
      $('#edit-estatus').val('Baja');
    }
  });

  // Submit Edición
  $('#edit-nomina-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#edit-nomina-id').val();
    const btn = $('#btn-submit-edit-nomina');
    btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i> Guardando...');

    $.ajax({
      url: `/rh/nominas/${id}/update`,
      method: "POST",
      data: $(this).serialize(),
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
      success: function(res) {
        if(res.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: res.message,
            confirmButtonColor: '#198754'
          });
          table.ajax.reload(null, false);
          $('#edit-nomina-modal').find('.close-modal').trigger('click');
        }
      },
      error: function(xhr) {
        let msg = 'Ocurrió un error al actualizar el registro.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Error de Validación',
          html: msg,
          confirmButtonColor: '#dc3545'
        });
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="ri-check-line"></i> Guardar Cambios');
      }
    });
  });

  // Abrir Modal de Vista de Detalle
  $(document).on('click', '.btn-view-nomina', function() {
    const id = $(this).data('id');

    $.get(`/rh/nominas/${id}`, function(res) {
      if(res.success && res.nomina) {
        const n = res.nomina;
        
        $('#view-nombre').text(n.nombre);
        $('#view-puesto').text(n.puesto);
        $('#view-puesto-field').text(n.puesto);
        
        const isBaja = n.estatus === 'Baja';
        const badgeCls = isBaja ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200';
        $('#view-estatus-badge').attr('class', `px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider ${badgeCls}`).text(n.estatus);

        $('#view-curp').text(n.curp || '—');
        $('#view-rfc').text(n.rfc || '—');
        $('#view-nss').text(n.nss || '—');
        $('#view-sexo').text(n.sexo || '—');
        $('#view-estado-civil').text(n.estado_civil || '—');
        $('#view-fecha-nacimiento').text(formatDate(n.fecha_nacimiento));
        $('#view-edad').text(n.edad !== null && n.edad !== '' ? n.edad + ' años' : '—');

        $('#view-fecha-ingreso').text(formatDate(n.fecha_ingreso));
        $('#view-fecha-baja').text(formatDate(n.fecha_baja));
        $('#view-antiguedad').text(n.antiguedad || '—');
        $('#view-antiguedad-header').text(n.antiguedad ? 'Antigüedad: ' + n.antiguedad : '');

        $('#view-beneficiario').text(n.nombre_beneficiario || '—');
        $('#view-parentesco').text(n.parentesco || '—');

        $('#view-domicilio').text(n.domicilio || '—');
        $('#view-cp').text(n.cp || '—');
        $('#view-telefono').text(n.telefono || '—');
        $('#view-correo').text(n.correo || '—');

        $('#view-nomina-modal').removeClass('hidden');
      }
    }).fail(function() {
      Swal.fire('Error', 'No se pudo cargar el expediente del empleado.', 'error');
    });
  });

  // Eliminar Registro con SweetAlert2
  $(document).on('click', '.btn-delete-nomina', function() {
    const id = $(this).data('id');
    const nombre = $(this).data('nombre');

    Swal.fire({
      title: '¿Eliminar registro?',
      html: `Se eliminará permanentemente la información de nómina de <strong>${escapeHtml(nombre)}</strong>.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="ri-delete-bin-line"></i> Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if(result.isConfirmed) {
        $.ajax({
          url: `/rh/nominas/${id}`,
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
          success: function(res) {
            if(res.success) {
              Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: res.message,
                confirmButtonColor: '#198754'
              });
              table.ajax.reload(null, false);
            }
          },
          error: function() {
            Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
          }
        });
      }
    });
  });

});
</script>
@endpush
