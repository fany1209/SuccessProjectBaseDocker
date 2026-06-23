@extends('layouts.app')
@section('content')

<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Laboratory Samples
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="lab-samples-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4">FOLIO</th>
          <th class="px-6 py-4">TIPO</th>
          <th class="px-6 py-4">PRODUCTO</th>
          <th class="px-6 py-4">SKU</th>

          <!-- NUEVAS COLUMNAS -->
          <th class="px-6 py-4">PROVEEDOR</th>
          <th class="px-6 py-4">LOTE</th>

          <th class="px-6 py-4">UBICACIÓN</th>
          <th class="px-6 py-4">STOCK INICIAL (g)</th>
          <th class="px-6 py-4">SALIDA (g)</th>
          <th class="px-6 py-4">STOCK FINAL (g)</th>
          <th class="px-6 py-4">F. ENTRADA</th>
          <th class="px-6 py-4">F. SALIDA</th>
          <th class="px-6 py-4">ACTIONS</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
  @include('laboratory.modals.edit-sample')

<script>
(function(){
  const BASE = @json(url('/laboratory/lab-samples'));

  function labDebugDataSrc(json){
    if (Array.isArray(json)) return json;
    if (Array.isArray(json?.data)) return json.data;
    if (Array.isArray(json?.rows)) return json.rows;
    console.warn('[Lab] Unexpected JSON for DataTables:', json);
    return [];
  }

  function renderLabActions(data, type, row){
    const id = row.id ?? '';
    const showUrl   = row.show_url   ?? `${BASE}/${id}`;
    const deleteUrl = row.delete_url ?? `${BASE}/${id}`;

    return `
      <div class="flex justify-end items-center gap-2">
        @can('laboratory.update')
        <button type="button"
                data-id="${id}"
                data-show-url="${showUrl}"
                class="edit-lab-btn open-modal text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 inline-flex items-center"
                data-target="edit-lab-sample"
                title="Edit">
          <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
        </button>
        @endcan

        @can('laboratory.delete')
        <button type="button"
                data-id="${id}"
                data-delete-url="${deleteUrl}"
                class="delete-lab-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
                title="Delete">
          <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
        </button>
        @endcan
      </div>
    `;
  }

  function setVal(id, v){
    const el = document.getElementById(id);
    if (!el) return;
    el.value = (v ?? '');
  }

   document.addEventListener('DOMContentLoaded', function () {

    window.labTable = $('#lab-samples-table').DataTable({
      ajax: {
        url: "{{ route('lab.samples.datatable') }}",
        dataSrc: labDebugDataSrc
      },
      columns: [
        { data: 'folio' },
        { data: 'tipo_muestra' },
        { data: 'producto' },
        { data: 'sku' },
        { data: 'proveedor', render: d => d ?? '—' },
        { data: 'lote',      render: d => d ?? '—' },
        { data: 'ubicacion' },
        { data: 'stock_inicial', className:'text-right' },
        { data: 'salida',        className:'text-right' },
        { data: 'stock_final',   className:'text-right' },
        { data: 'fecha_entrada' },
        { data: 'fecha_salida' },
        { data: null, render: renderLabActions, orderable:false, searchable:true }
      ],
      order: [[0,'asc']],
      pageLength: 10,
      searching: true,
      lengthChange: false,
      language: {
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        zeroRecords: "Sin resultados",
        paginate: {
          next: "Siguiente",
          previous: "Anterior"
        }
      }
    });

    $(document).off('click.labEdit', '.edit-lab-btn').on('click.labEdit', '.edit-lab-btn', async function(){
      const id = this.dataset.id;
      const showUrl = this.dataset.showUrl || `${BASE}/${id}`;

      try {
        const resp = await fetch(showUrl, { headers: { 'Accept':'application/json' }});
        if (!resp.ok) {
          let msg = `Error ${resp.status}`;
          try { msg = (await resp.json()).message || msg; }
          catch { const t = await resp.text(); if (t) msg = t; }
          throw new Error(msg);
        }

        const json = await resp.json();
        const r = json?.data ?? json;

        setVal('edit-lab-id', r.id ?? id);
        setVal('edit-folio', r.folio);
        setVal('edit-tipo_muestra', r.tipo_muestra);
        setVal('edit-producto', r.producto);
        setVal('edit-sku', r.sku);
        setVal('edit-ubicacion_stock', r.ubicacion_stock ?? r.ubicacion);
        setVal('edit-stock_inicial', r.stock_inicial);
        setVal('edit-cantidad_salida', r.cantidad_salida ?? r.salida);
        setVal('edit-stock_final', r.stock_final);
        setVal('edit-fecha_entrada', r.fecha_entrada_raw ?? r.fecha_entrada);
        setVal('edit-fecha_salida',  r.fecha_salida_raw  ?? r.fecha_salida);
        setVal('edit-proveedor', r.proveedor);
        setVal('edit-presentacion', r.presentacion);
        setVal('edit-motivo_salida', r.motivo_salida);
        setVal('edit-solicitante', r.solicitante);
        setVal('edit-recolector', r.recolector);
        setVal('edit-cliente', r.cliente);

        document.getElementById('edit-stock_inicial')?.dispatchEvent(new Event('input'));

        const form = document.getElementById('edit-lab-sample-form');
        if (form) form.dataset.updateUrl = `${BASE}/${id}`;

        const modalRoot = document.getElementById('edit-lab-sample');
        if (modalRoot) {
          modalRoot.classList.add('open');
          modalRoot.style.display = '';
        }

      } catch (err) {
        console.error('[Lab] Edit load error:', err);
        Swal?.fire?.('Error', err?.message || 'No se pudo cargar el registro', 'error');
      }
    });

    // ===== Delete =====
    $(document).off('click.labDel', '.delete-lab-btn').on('click.labDel', '.delete-lab-btn', async function(){
      const id = this.dataset.id;
      const rowData = window.labTable.row($(this).closest('tr')).data();

      if (!id || !rowData) {
        Swal?.fire?.('Ups', 'The record was not found.', 'warning');
        return;
      }

      const res = await Swal.fire({
        title: '¿Delete record?',
        html: `<p style="margin-top:8px;">This action cannot be undone.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true
      });
      if (!res.isConfirmed) return;

      const url = this.dataset.deleteUrl || rowData.delete_url || `${BASE}/${id}`;

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
          try { msg = (await resp.json()).message || msg; }
          catch { const t = await resp.text(); if (t) msg = t; }
          throw new Error(msg);
        }

        window.labTable.ajax.reload(null, false);
        Swal.fire({title:'Delete', text:'Registry deleted successfully.', icon:'success', timer:1400, showConfirmButton:false});
      } catch (err) {
        console.error('[Lab] Delete error:', err);
        Swal.fire('Could not delete', err?.message || 'An error occurred.', 'error');
      }
    });

  });
})();
</script>
</section>

@endsection
