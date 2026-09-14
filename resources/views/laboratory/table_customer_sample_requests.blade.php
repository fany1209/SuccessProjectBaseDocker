@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--open {
        z-index: 99999 !important;
    }
</style>
@endpush

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--open {
        z-index: 99999 !important;
    }
</style>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#16a34a]">
        Sample Requests (Customers)
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#16a34a]/50 via-[#16a34a]/20 to-transparent"></div>
    </div>
    
    <div class="w-full flex justify-end mt-4 px-4 md:px-0">
        <x-button id="open-create-modal" 
                class="bg-green-500 hover:bg-green-600 text-white shadow-sm transition duration-150 py-2 px-6 rounded-lg text-sm font-semibold">
            <i class="fas fa-plus mr-2"></i> Add Sample Request
        </x-button>
    </div>

    <table id="customer-requests-table" 
           data-url="{{ route('laboratory.customer_requests.json') }}" 
           data-img-borrar="{{ asset('images/borrar.png') }}"
           data-img-pdf="{{ asset('images/pdf.png') }}"
           data-img-editar="{{ asset('images/editar.png') }}"
           class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4 text-left">FOLIO</th>
          <th class="px-6 py-4 text-left">REQUEST DATE</th>
          <th class="px-6 py-4 text-left">PRODUCT</th>
          <th class="px-6 py-4 text-left">CUSTOMER</th>
          <th class="px-6 py-4 text-center">STATUS</th> 
          <th class="px-6 py-4 text-right">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words"></tbody>
    </table>

    @include('laboratory.modals.02')       
    @include('laboratory.modals.02_edit')  

    <script>
    (function(){
      function renderStatus(data, type, row) {
        const status = parseInt(data) || 0;
        const id = row.id;
        const options = [
            { val: 0, text: 'PENDING', bg: 'bg-yellow-100', textCol: 'text-yellow-800' },
            { val: 1, text: 'IN PROGRESS', bg: 'bg-blue-100', textCol: 'text-blue-800' },
            { val: 2, text: 'COMPLETED', bg: 'bg-green-100', textCol: 'text-green-800' }
        ];

        const canEditStatus = {{ auth()->user()->hasRole(['Admin', 'Laboratory']) ? 'true' : 'false' }};
        const disabledAttr = canEditStatus ? '' : 'disabled';
        const cursorClass = canEditStatus ? 'cursor-pointer' : 'cursor-not-allowed opacity-80';
        const currentOpt = options.find(o => o.val === status) || options[0];
        
        let selectHtml = `<select data-id="${id}" ${disabledAttr} class="change-status-select text-[10px] font-bold border rounded-md px-1 py-1 focus:outline-none transition-colors shadow-sm ${cursorClass} `;
        selectHtml += `${currentOpt.bg} ${currentOpt.textCol}">`;
        
        options.forEach(opt => {
            const selected = (status === opt.val) ? 'selected' : '';
            selectHtml += `<option value="${opt.val}" ${selected} class="bg-white text-gray-800">${opt.text}</option>`;
        });
        selectHtml += `</select>`;
        return selectHtml;
      }

      function renderActions(data, type, row){
        const id = row.id ?? '';
        const tableEl = document.getElementById('customer-requests-table');
        const urlBorrar = tableEl.getAttribute('data-img-borrar');
        const urlPdf    = tableEl.getAttribute('data-img-pdf');
        const urlEditar = tableEl.getAttribute('data-img-editar');

        return `
          <div class="flex justify-end gap-2 items-center">
            <button data-id="${id}" class="edit-request-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 flex items-center justify-center" title="Edit">
              <img width="18" height="18" src="${urlEditar}" alt="Edit"/>
            </button>
            <button data-id="${id}" class="delete-request-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2 flex items-center justify-center" title="Delete">
              <img width="18" height="18" src="${urlBorrar}" alt="Delete"/>
            </button>
            <button data-id="${id}" class="pdf-request-btn bg-green-600 hover:bg-green-700 text-white p-2 rounded-sm flex items-center justify-center" title="Generate PDF">
              <img width="18" height="18" src="${urlPdf}" alt="PDF"/>
            </button>
          </div>
        `;
      }

      document.addEventListener('DOMContentLoaded', function () {
        const tableEl = document.getElementById('customer-requests-table');
        if (!tableEl) return;

        const table = $('#customer-requests-table').DataTable({
          ajax: {
            url: tableEl.getAttribute('data-url'), 
            dataSrc: (json) => json.data || json,
            headers: { 'Accept': 'application/json' }
          },
          columns: [
            { data: 'folio', defaultContent: '' },   
            { data: 'fecha_solicitud', defaultContent: '' },  
            { data: 'producto_nombre', defaultContent: 'N/A' }, 
            { data: 'cliente_nombre', defaultContent: '' },  
            { data: 'status', render: renderStatus, className: 'text-center' },
            { data: null, render: renderActions, orderable: false, searchable: false, className: 'text-right' } 
          ],
          order: [[1, 'desc']],
          lengthChange: false,
          pageLength: 20,
          language: { url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
        });

        $(document).on('click', '#open-create-modal', function() {
            const modalCreate = $('#02');
            if(modalCreate.length) {
                modalCreate.find('form')[0].reset(); 
                if ($('#customer_id').length && typeof $('#customer_id').select2 === 'function') {
                    $('#customer_id').val('').trigger('change.select2');
                }
                modalCreate.removeClass('hidden').show();
                if (typeof window.initSampleForm02 === 'function') {
                    window.initSampleForm02();
                }
            }
        });

        $(document).on('change', '.change-status-select', async function() {
            const id = $(this).data('id');
            const newStatus = $(this).val();
            const selectEl = $(this);
            selectEl.prop('disabled', true).addClass('opacity-50');
            try {
                const resp = await fetch(`/laboratory/customer-request/${id}/status`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status: newStatus }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                if (!resp.ok) throw new Error('Update failed');
                Swal.fire({ icon: 'success', title: 'Status Updated', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                table.ajax.reload(null, false);
            } catch (err) {
                Swal.fire('Error', 'Could not update status', 'error');
                table.ajax.reload(null, false);
            } finally {
                selectEl.prop('disabled', false).removeClass('opacity-50');
            }
        });

        $(document).on('click', '.edit-request-btn', async function(){
            const id = $(this).data('id');
            try {
                const resp = await fetch(`/laboratory/customer-request/${id}`);
                const result = await resp.json();
                const d = result.data;

                $('#edit_id').val(d.id);
                $('#edit_fecha_solicitud').val(d.fecha_solicitud);
                $('#edit_fecha_recoleccion').val(d.fecha_recoleccion);
                $('#edit_customer_id').val(d.customer_id).trigger('change');
                $('#edit_cliente_nombre').val(d.cliente_nombre);
                $('#edit_cliente_direccion').val(d.cliente_direccion);
                $('#edit_cliente_correo').val(d.cliente_correo);
                $('#edit_cliente_telefono').val(d.cliente_telefono);
                $('#edit_paq_nombre').val(d.paq_nombre);
                $('#edit_paq_guia').val(d.paq_guia);
                $('#edit_observaciones').val(d.observaciones);
                $('#edit_solicitante_nombre').val(d.solicitante_nombre);
                $('#edit_entrega_otro_txt').val(d.entrega_otro_txt);

                if (typeof window.renderEditSamples === "function") {
                    window.renderEditSamples(d.items || []);
                }

                if(d.cliente_estatus){
                    $(`input[name="cliente_estatus"][value="${d.cliente_estatus}"]`).prop('checked', true);
                }

                const cbs = ['entrega_paqueteria','entrega_personal_empresa','entrega_recoleccion_planta','entrega_otro'];
                cbs.forEach(cb => { $(`#edit_${cb}`).prop('checked', d[cb] == 1); });

                const otros = [{ chk: 'edit_entrega_otro_chk', txt: 'edit_entrega_otro_txt' }];
                otros.forEach(o => {
                    if (typeof window.editToggleText === "function") {
                        window.editToggleText($(`#${o.chk}`).is(':checked'), o.txt);
                    }
                });

                $('#edit-02').removeClass('hidden').show(); 
            } catch (err) {
                Swal.fire('Error', 'Could not load record.', 'error');
            }
        });

        $(document).on('click', '.close-modal', function(){
            $('#02, #edit-02').addClass('hidden').hide();
        });

        $(document).off('click', '.delete-request-btn').on('click', '.delete-request-btn', async function(){
          const id = $(this).data('id');
          const res = await Swal.fire({ title: 'Delete record?', text: "This action cannot be undone.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel' });
          if (res.isConfirmed) {
            try {
              const resp = await fetch(`/laboratory/customer-request/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
              if (!resp.ok) throw new Error();
              table.ajax.reload(null, false);
              Swal.fire({ icon: 'success', title: 'Deleted', timer: 1500, showConfirmButton: false });
            } catch (err) { Swal.fire('Error', 'Could not delete', 'error'); }
          }
        });

        $(document).on('click', '.pdf-request-btn', function(){
          const id = $(this).data('id');
          if (id) window.open(`/laboratory/customer-request/${id}/pdf`, '_blank');
        });

        $(document).on('reloadTable', function() {
            table.ajax.reload(null, false);
        });
      });
    })();
    </script>
  </div>
</section>