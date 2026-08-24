<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
          Warehouse Inspection
        </h1>
        <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
      </div>
    <table id="inspection-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th scope="col" class="px-6 py-4 text-right">DATE</th>
          <th scope="col" class="px-6 py-4 text-right">STATUS</th>
          <th scope="col" class="px-6 py-4 text-right">INSPECTOR</th>
          <th scope="col" class="px-6 py-4 text-right">RESPONSABLE</th>
          <th scope="col" class="px-6 py-4">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>
  </div>

  @can('quality.update')
    @include('quality.modals.inspectionWH.editInspectionWH')
  @endcan

  @can('quality.updateW')
   @include('quality.modals.inspectionWH.editWH')
  @endcan

  @include('quality.modals.inspectionWH.viewWH')

</section>

{{--imagen  --}}
<div id="img-viewer" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
  <div class="relative max-w-5xl w-full">
    <button type="button"
            class="absolute -top-10 right-0 text-white text-sm px-3 py-1 rounded bg-black/50 hover:bg-black/70"
            data-close-viewer>&times; Close</button>
    <img id="img-viewer-src" src="" alt="Evidencia" class="w-full max-h-[80vh] object-contain rounded shadow-lg">
  </div>
</div>

@push('js')
<script>
$(document).ready(function () {
  $.fn.dataTable.ext.errMode = 'none';
  const ORIGIN = window.location.origin;

  function debugDataSrc(json){
    return json?.inspections ?? [];
  }

  function chip(text){
    return `<span class="inline-block text-[11px] px-2 py-1 mr-1 mb-1 rounded-full bg-gray-100 border border-gray-200 text-gray-700">${text}</span>`;
  }

  function isImageUrl(url){
    if (!url) return false;
    const q = url.split('?')[0].toLowerCase();
    return (/\.(png|jpe?g|gif|webp|bmp|svg)$/i).test(q);
  }

  function ensureStorageUrl(pathOrUrl){
    if (!pathOrUrl) return null;
    if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;   
    if (pathOrUrl.startsWith('/storage/')) return ORIGIN + pathOrUrl;
    return `${ORIGIN}/storage/${pathOrUrl.replace(/^\/+/, '')}`;
  }

  function forceOriginIfStorage(absUrl){
    try {
      const u = new URL(absUrl);
      if (u.origin !== ORIGIN && u.pathname.startsWith('/storage/')) {
        return ORIGIN + u.pathname + u.search + u.hash;
      }
      return absUrl;
    } catch { return absUrl; }
  }

  function thumb(url, title){
    const safeTitle = title || 'Evidencia';
    return `
      <img src="${url}"
           alt="${safeTitle}"
           loading="lazy"
           class="w-12 h-12 object-cover rounded border cursor-zoom-in mr-1 mb-1 inline-block"
           data-full="${url}"
           title="Click para ampliar"
           onerror="this.onerror=null; console.warn('[IMG ERROR]', this.src); this.replaceWith('<a href=\\'${url}\\' target=\\'_blank\\' class=\\'text-xs text-blue-600 underline\\'>abrir evidencia</a>');"
      />
    `;
  }

  function iconLink(href, title){
    if (!href) return '';
    return `<a href="${href}" target="_blank" rel="noopener" class="inline-flex items-center ml-1 text-blue-600 underline text-xs">${title || 'ver'}</a>`;
  }
  const CAN_SHOW_PDF = @json(auth()->user()->can('quality.buttons.show'));

  function renderActions(data, type, row){
      let buttons = ``;

      buttons += `
        <button 
          data-id="${row.id}" 
          class="view-warehouse-btn bg-gray-600 hover:bg-gray-700 text-white p-2 rounded-sm"
          title="Ver inspección">
          <img width="18" src="{{ asset('images/ver.png') }}" alt="Ver"/>
        </button>
      `;

      if (row.canUpdate){
        buttons += `
          <button data-id="${row.id}" data-target="edit-inspection" class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
            <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
          </button>
        `;
      }

      if (row.canUpdateW){ 
      buttons += `
        <button 
            data-id="${row.id}" 
            data-status="${row.status}" 
            class="edit-warehouse-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2" 
            title="Edit Warehouse">
          <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit Warehouse"/>
        </button>
      `;
      }

      if (row.canDelete){
        buttons += `
          <button data-id="${row.id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
            <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
          </button>
        `;
      }
    if (CAN_SHOW_PDF) {
    buttons += `<button data-id="${row.id}" class="generate-pdf-btn bg-green-600 text-white p-2 rounded-sm" title="Generar PDF">
                  <img width="18" src="{{ asset('images/pdf.png') }}" alt="PDF"/>
                </button>`;
  }

      return buttons || '';
  }

  // ====== DataTable ======
const table = $('#inspection-table').DataTable({
    ajax: {
        url: "{{ route('quality.getInspections') }}",
        dataSrc: debugDataSrc,
        error: function (xhr, status, err) {
            console.error('DataTables AJAX error:', status, err);
            console.error('Status code:', xhr.status);
            console.error('Response text:', xhr.responseText);
        },
        headers: { 'Accept': 'application/json' }
    },
    columns: [
        { data: 'fecha_inspeccion' },
        { 
            data: 'status',
                render: function(data, type, row) {
        if (data == 1) {
            return '<span style="background-color:#4ade80; color:white; padding:2px 6px; border-radius:4px; font-weight:bold;">Terminado</span>';
        } else {
            return '<span style="background-color:#f87171; color:white; padding:2px 6px; border-radius:4px; font-weight:bold;">Pendiente</span>';
        }
    }
        },
        { data: 'inspector' },
        { data: 'responsable' },
        { data: null, render: renderActions, orderable:false, searchable:false, className:'text-right' }
    ],
    lengthChange: false,
    searching: false,
    pageLength: 5,
    serverSide: false,
    language: {
        info: "Show _START_ to _END_ of _TOTAL_ inspections",
        lengthMenu: "Show _MENU_ inspections",
        infoEmpty: "There aren't inspections available",
        zeroRecords: "No results found",
        infoFiltered: "(filtered on _MAX_ total records)",
        paginate: { first:"First", last:"Last", next:"Next", previous:"Previous" }
    }
});

  $('#inspection-table tbody').on('click','img[data-full]', function(){
    const src = this.getAttribute('data-full');
    const viewer = document.getElementById('img-viewer');
    const img    = document.getElementById('img-viewer-src');
    img.src = src;
    viewer.classList.remove('hidden');
    viewer.classList.add('flex');
  });

  document.querySelector('#img-viewer [data-close-viewer]')?.addEventListener('click', () => {
    const viewer = document.getElementById('img-viewer');
    const img    = document.getElementById('img-viewer-src');
    img.src = '';
    viewer.classList.add('hidden');
    viewer.classList.remove('flex');
  });

  document.getElementById('img-viewer')?.addEventListener('click', (e) => {
    if (e.target.id === 'img-viewer') {
      const viewer = e.currentTarget;
      const img    = document.getElementById('img-viewer-src');
      img.src = '';
      viewer.classList.add('hidden');
      viewer.classList.remove('flex');
    }
  });

  // Modal de edición
  $('#inspection-table tbody').on('click', '.edit-btn', function(){
    const id = $(this).data('id');
    $('#inspection-id').val(id);
  });

  $('#inspection-table tbody').on('click', '.generate-pdf-btn', function() {
    const id = $(this).data('id');

    const form = $('<form>', {
        action: "{{ route('quality.pdf4') }}",
        method: 'POST',
        target: '_blank' 
    });

    form.append(`@csrf`);
    form.append(`<input type="hidden" name="inspection_id" value="${id}">`);
    $('body').append(form);
    form.submit();
    form.remove();
});

  // Delete 
  $('#inspection-table tbody').on('click', '.delete-btn', function(){
    const id = $(this).data('id');

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ url('quality/inspections') }}/" + id,
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (res) {
            Swal.fire(
              'Deleted!',
              'The inspection has been deleted successfully.',
              'success'
            );
            $('#inspection-table').DataTable().ajax.reload();
          },
          error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire(
              'Error',
              'An error occurred while deleting the inspection.',
              'error'
            );
          }
        });
      }
    });
  });
   // Warehouse alerts have been moved to the end of the script to execute sequentially.

// inspection WH - VIEW
$(document).on('click', '.view-warehouse-btn', function () {
  const id = $(this).data('id');

  $.ajax({
    url: "{{ route('quality.getInspectionWView') }}",
    type: 'GET',
    data: { id },
    success: function (wh) {

      $('#vw-responsable').val(wh.responsable ?? '');
      $('#vw-inspector').val(wh.inspector ?? '');
      $('#vw-comentarios').val(wh.comentarios ?? '');

      const $tbody = $('#vw-observaciones');
      $tbody.empty();

      const observaciones = wh.observaciones ?? [];

      if (observaciones.length > 0) {

        observaciones.forEach(obs => {

          const nombre    = obs.name ?? '';
          const ubicacion = obs.ubicacion ?? '';
          const fecha     = obs.fecha ?? '—';

          let evidenciaHtml = '—';

          if (obs.ev_corr_path) {
            const url = `/${obs.ev_corr_path.replace(/^\/+/, '')}`;

            evidenciaHtml = `
              <img src="${url}"
                   class="w-20 h-20 object-cover rounded border cursor-pointer hover:opacity-90"
                   title="Click para abrir imagen"
                   onclick="window.open('${url}', '_blank')">
            `;
          }

          $tbody.append(`
            <tr>
              <td class="px-3 py-2 align-top">${nombre}</td>
              <td class="px-3 py-2 align-top">${ubicacion}</td>
              <td class="px-3 py-2 align-top">${fecha}</td>
              <td class="px-3 py-2 align-top">${evidenciaHtml}</td>
            </tr>
          `);
        });

      } else {
        $tbody.append(`
          <tr>
            <td colspan="4" class="text-center py-3 text-gray-500">
              Sin observaciones registradas
            </td>
          </tr>
        `);
      }

      // Abrir modal
      const modal = document.getElementById('view-warehouse');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo cargar la inspección'
      });
    }
  });
});

// CERRAR MODAL
$(document).on('click', '#view-warehouse .close-modal', function () {
  const modal = document.getElementById('view-warehouse');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
});

   $(document).ready(async function () {
    // Helper function to show alerts sequentially
    async function showAlerts() {
      try {
        const resIns = await $.get("{{ route('quality.checkPendingWarehouseInspections') }}");
        if(resIns.pending && resIns.pending.length > 0) {
          await Swal.fire({
            icon: 'info',
            title: 'Pending Inspections',
            html: `You have ${resIns.pending.length} pending warehouse inspection(s).`,
            confirmButtonText: 'Accept'
          });
        }
      } catch (err) {
        console.error('Error checking warehouse inspections pending', err);
      }

      try {
        const resQual = await $.get("{{ route('quality.checkPendingQuality') }}");
        const totalQ = resQual?.count ?? (resQual?.pending?.length || 0);
        if (totalQ > 0) {
          await Swal.fire({
            icon: 'info',
            title: 'Pending sample receptions',
            html: `You have <b>${totalQ}</b> pending sample reception(s) in Laboratory.`,
            confirmButtonText: 'Accept'
          });
        }
      } catch (err) {
        console.error('Error checking Quality pending', err);
      }

      try {
        const resLot = await $.get("{{ route('lot.request.check') }}");
        const totalL = resLot?.count ?? (resLot?.pending?.length || 0);
        if (totalL > 0) {
          $('#pending-batch-badge').text(totalL).removeClass('hidden');
          await Swal.fire({
            icon: 'info',
            title: 'Pending Batch Requests',
            html: `You have <b>${totalL}</b> pending batch request(s).`,
            confirmButtonText: 'Accept'
          });
        }
      } catch (err) {
        console.error('Error checking Lot Requests pending', err);
      }
    }

    await showAlerts();
  });
});
</script>
@endpush
