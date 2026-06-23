<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Weekly Plans
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

<table id="plan-semanal" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
  <thead class="bg-gray-50 text-gray-700 uppercase text-md">
    <tr>
      <th class="px-6 py-4 text-left">REPORT</th>
      <th class="px-6 py-4 text-left">FECHA INGRESO</th>
      <th class="px-6 py-4 text-left">FECHA EMISIÓN</th>
      <th class="px-6 py-4 text-left">NOMBRE DEL PRODUCTOR</th>
      <th class="px-6 py-4">ACTIONS</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
</table>

<script>
(function(){
  function soilDebugDataSrc(json){
    if (Array.isArray(json)) return json;
    if (Array.isArray(json?.data)) return json.data;
    if (Array.isArray(json?.rows)) return json.rows;
    console.warn('[Soil] Unexpected JSON for DataTables:', json);
    return [];
  }

  function renderSoilActions(data, type, row){
    const id = row.id ?? '';
    return `
      @can('laboratory.delete')
      <button data-id="${id}"
              class="delete-soil-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
              title="Delete">
        <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
      </button>
      @endcan

      <button data-id="${id}"
              class="pdf-soil-btn bg-green-600 hover:bg-green-700 text-white p-2 rounded-sm"
              title="Generate PDF">
        <img width="18" src="{{ asset('images/pdf.png') }}" alt="PDF"/>
      </button>
    `;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const soilTable = $('#plan-semanal').DataTable({
      ajax: {
      url: "{{ route('weekly.plans.json') }}", 
        dataSrc: soilDebugDataSrc,
        headers: { 'Accept': 'application/json' },
        error: function (xhr, status, err) {
          console.error('[Soil] DataTables AJAX error:', status, err);
          console.error('HTTP', xhr.status, '->', xhr.responseText);
        }
      },
      columns: [
        { data: 'report_code' },   
        { data: 'entry_date' },  
        { data: 'issue_date' },    
        { data: 'client_name' },  
        { data: null, render: renderSoilActions, orderable:false, searchable:false, className:'text-right' } 
      ],
      order: [[1,'desc']],
      lengthChange: false,
      searching: false,
      pageLength: 5,
      serverSide: false,
      language: {
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        lengthMenu: "Mostrar _MENU_",
        infoEmpty: "Sin registros",
        zeroRecords: "Sin resultados",
        infoFiltered: "(filtrado de _MAX_ total)",
        paginate: { first:"Primero", last:"Último", next:"Next", previous:"Previous" }
      }
    });

    // ===== Delete =====
    $(document).off('click.soil', '.delete-soil-btn').on('click.soil', '.delete-soil-btn', async function(){
      const id = this.dataset.id;
      const rowData = soilTable.row($(this).closest('tr')).data();
      if (!id || !rowData) {
        Swal.fire('Ups', 'The record was not found.', 'warning');
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

      const url = rowData.delete_url ?? `/laboratory/soil-analyses/${id}`;
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
        soilTable.ajax.reload(null, false);
        Swal.fire({title:'Delete', text:'Registry deleted successfully.', icon:'success', timer:1400, showConfirmButton:false});
      } catch (err) {
        console.error('[Soil] Delete error:', err);
        Swal.fire('Could not delete', err?.message || 'An error occurred.', 'error');
      }
    });

    // ===== PDF =====
    $(document).off('click.soil', '.pdf-soil-btn').on('click.soil', '.pdf-soil-btn', function(){
      const id = this.dataset.id;
      const rowData = soilTable.row($(this).closest('tr')).data();
      if (!id && !rowData?.id) {
        Swal?.fire?.('Ups', 'There is no registration required to generate PDFs.', 'warning');
        return;
      }
      const url = rowData.pdf_url ?? `/laboratory/soil-analyses/${id || rowData.id}/pdf`;
      window.open(url, '_blank');
    });
  });
})();
</script>
</section>
