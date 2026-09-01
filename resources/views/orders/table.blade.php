<section class="col-span-12 w-full flex flex-col items-center px-1 sm:px-4">
    <div class="flex flex-col justify-center items-center w-full">
        
        <div class="mt-6 text-center w-full">
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#16a34a]">
                Gestión de Pedidos (Orders)
            </h1>
            <div class="mt-2 h-px bg-gradient-to-r from-[#16a34a]/50 via-[#16a34a]/20 to-transparent"></div>
        </div>

        <div class="w-full flex flex-col md:flex-row justify-between items-center mt-8 mb-4 gap-4 px-2">
            <div id="search-container" class="w-full md:flex-1 md:max-w-sm"></div>
            
            <div class="w-full md:w-auto flex justify-end">
                <x-button data-target="create-order" 
                        class="open-modal w-full md:w-auto bg-green-500 hover:bg-green-600 text-white shadow-sm transition duration-150 py-2 px-4 rounded-lg text-sm font-semibold">
                    + Nuevo Pedido
                </x-button>
            </div>
        </div>

        <div class="w-full overflow-x-auto bg-white rounded-xl shadow-sm border border-gray-100">
            <table id="orders-table" 
                   data-url="{{ route('orders.json') }}" 
                   data-img-borrar="{{ asset('images/borrar.png') }}"
                   data-img-editar="{{ asset('images/editar.png') }}"
                   class="display w-full divide-y divide-gray-200 text-sm text-left">
              <thead class="bg-gray-50 text-gray-700 uppercase text-[10px] tracking-wider">
                <tr>
                  <th class="px-4 py-4 min-w-[140px]">Empresa</th>
                  <th class="px-4 py-4 min-w-[180px]">Productos</th>
                  <th class="px-4 py-4 text-center w-24">Cant.</th>
                  
                  <th class="px-4 py-4 text-center min-w-[100px]">F. Carga</th>
                  <th class="px-4 py-4 text-center min-w-[100px]">F. Envío</th>
                  <th class="px-4 py-4 text-center min-w-[100px]">F. Requerida</th>

                  <th class="px-4 py-4 text-center min-w-[130px]">Almacén</th> 
                  <th class="px-4 py-4 text-center min-w-[130px]">Calidad</th> 
                  <th class="px-4 py-4 text-center min-w-[130px]">Admin.</th>
                  
                  <th class="px-4 py-4 text-center w-12">PDF</th>

                  <th class="px-4 py-4 min-w-[180px]">Comentarios</th> 
                  <th class="px-4 py-4 text-right w-24">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-[11px]"></tbody>
            </table>
        </div>

        @include('orders.modals.edit_order')
        @include('orders.modals.create_order')
        @include('orders.modals.view_order')
        
        <script>
        (function(){
            const userRoles = @json(auth()->user()->roles->pluck('name'));
            const isAdmin = userRoles.includes('Admin');
            const currentUserId = {{ auth()->id() ?? 'null' }};
            const availableProducts = @json($productos->pluck('name'));
            let editItemIndex = 0;

            function buildEditProductOptions(selectedValue = '') {
                let opts = '<option value="" disabled ' + (selectedValue ? '' : 'selected') + '>Seleccione un producto...</option>';
                availableProducts.forEach(name => {
                    const isSel = (selectedValue === name) ? 'selected' : '';
                    opts += `<option value="${name}" ${isSel}>${name}</option>`;
                });
                return opts;
            }

            function addEditProductRow(producto = '', cantidad = '') {
                const idx = editItemIndex++;
                const html = `
                    <div class="edit-order-item-row flex flex-col md:flex-row items-center gap-2 bg-white p-2.5 rounded-lg border border-gray-200 shadow-xs">
                        <div class="w-full md:flex-1">
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-0.5">Producto</label>
                            <select name="items[${idx}][producto]" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-xs py-1.5" required>
                                ${buildEditProductOptions(producto)}
                            </select>
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-0.5">Cantidad</label>
                            <input type="text" name="items[${idx}][cantidad]" value="${cantidad || ''}" placeholder="Ej: 500 kg, 20 L" class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-xs py-1.5" required>
                        </div>
                        <div class="w-full md:w-auto flex md:self-end pt-1 md:pt-0">
                            <button type="button" class="remove-edit-item-btn p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-md transition" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                $('#edit-order-items-container').append(html);
            }

            $('#edit-add-product-btn').on('click', function(){
                addEditProductRow();
            });

            $(document).on('click', '.remove-edit-item-btn', function(){
                if ($('#edit-order-items-container .edit-order-item-row').length > 1) {
                    $(this).closest('.edit-order-item-row').remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'El pedido debe contener al menos un producto.',
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            });

            // Función para formatear fechas de YYYY-MM-DD a DD/MM/YYYY
            function formatDate(dateString) {
                if (!dateString) return '<span class="text-gray-300">-</span>';
                const parts = dateString.split('-');
                if (parts.length !== 3) return dateString;
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }

            function renderGenericStatus(data, id, field, optionsConfig) {
                const val = data ? data.toUpperCase() : 'PENDIENTE';
                const config = optionsConfig[val] || { bg: 'bg-gray-100', textCol: 'text-gray-800' };
                
                let canEdit = false;
                if (field === 'estatus_almacen' && userRoles.includes('Warehouse')) canEdit = true;
                if (field === 'estatus_calidad' && userRoles.includes('Quality')) canEdit = true;
                if (field === 'estatus_administrativo' && isAdmin) canEdit = true;

                const disabledAttr = canEdit ? '' : 'disabled';
                const cursorClass = canEdit ? 'cursor-pointer' : 'opacity-70 cursor-not-allowed';

                let html = `<select data-id="${id}" data-field="${field}" ${disabledAttr} 
                            class="change-status-select text-[9px] font-bold border rounded-md px-1 py-1 ${config.bg} ${config.textCol} ${cursorClass} focus:outline-none w-full">`;
                
                for (const [key, opt] of Object.entries(optionsConfig)) {
                    html += `<option value="${opt.originalValue}" ${val === key ? 'selected' : ''} class="bg-white text-gray-800">${key}</option>`;
                }
                html += `</select>`;
                return html;
            }

            function renderActions(data, type, row){
                const id = row.id ?? '';
                const isOwner = currentUserId && row.user_id === currentUserId;
                const canManage = isAdmin || isOwner;
                const tableEl = document.getElementById('orders-table');
                const imgEditar = tableEl.getAttribute('data-img-editar');
                const imgBorrar = tableEl.getAttribute('data-img-borrar');
                
                let html = `<div class="flex justify-end gap-1 items-center">
                    <button data-id="${id}" class="view-order-btn bg-cyan-500 p-1.5 rounded-sm shadow-sm hover:bg-cyan-600" title="View Details">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>`;

                if(canManage) {
                    html += `
                        <button data-id="${id}" class="edit-order-btn bg-blue-500 p-1.5 rounded-sm shadow-sm hover:bg-blue-600" title="Edit">
                            <img width="14" height="14" src="${imgEditar}"/>
                        </button>
                        <button data-id="${id}" class="delete-order-btn bg-red-500 p-1.5 rounded-sm shadow-sm hover:bg-red-600" title="Delete">
                            <img width="14" height="14" src="${imgBorrar}"/>
                        </button>`;
                }
                return html + `</div>`;
            }

            document.addEventListener('DOMContentLoaded', function () {
                let tableColumns = [
                    { data: 'empresa' },
                    { 
                        data: 'items', 
                        className: 'min-w-[180px]',
                        render: function(data, type, row) {
                            if (data && data.length > 0) {
                                return data.map(item => `<div class="text-[11px] font-semibold text-gray-800 leading-tight py-0.5"><span class="text-green-600 font-bold">•</span> ${item.producto} <span class="text-gray-500 font-normal text-[10px]">(${item.cantidad || 'S/C'})</span></div>`).join('');
                            }
                            if (row.producto) {
                                return `<div class="text-[11px] font-semibold text-gray-800 leading-tight py-0.5">${row.producto} <span class="text-gray-500 font-normal text-[10px]">(${row.cantidad || 'S/C'})</span></div>`;
                            }
                            return '<span class="text-gray-300">-</span>';
                        }
                    },
                    { 
                        data: 'items', 
                        className: 'text-center whitespace-nowrap',
                        render: function(data, type, row) {
                            if (data && data.length > 1) {
                                return `<span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-green-200">${data.length} prods.</span>`;
                            } else if (data && data.length === 1) {
                                return `<b>${data[0].cantidad || '-'}</b>`;
                            }
                            return `<b>${row.cantidad ?? '-'}</b>`;
                        }
                    },
                    
                    // --- COLUMNAS DE FECHAS ---
                    { data: 'fecha_de_carga', className: 'text-center whitespace-nowrap', render: (d) => formatDate(d) },
                    { data: 'fecha_de_envio', className: 'text-center whitespace-nowrap', render: (d) => formatDate(d) },
                    { data: 'fecha_requerida_por_el_cliente', className: 'text-center whitespace-nowrap', render: (d) => formatDate(d) },

                    { 
                        data: 'estatus_almacen', 
                        className: 'text-center',
                        render: (d, t, r) => renderGenericStatus(d, r.id, 'estatus_almacen', {
                            'PENDIENTE': { originalValue: 'Pendiente', bg: 'bg-red-100', textCol: 'text-red-700' },
                            'TERMINADO': { originalValue: 'Terminado', bg: 'bg-green-100', textCol: 'text-green-700' }
                        })
                    },
                    { 
                        data: 'estatus_calidad', 
                        className: 'text-center',
                        render: (d, t, r) => renderGenericStatus(d, r.id, 'estatus_calidad', {
                            'PENDIENTE': { originalValue: 'Pendiente', bg: 'bg-red-100', textCol: 'text-red-700' },
                            'LIBERADO': { originalValue: 'Liberado', bg: 'bg-green-100', textCol: 'text-green-700' }
                        })
                    },
                    { 
                        data: 'estatus_administrativo', 
                        className: 'text-center',
                        render: (d, t, r) => renderGenericStatus(d, r.id, 'estatus_administrativo', {
                            'DOCUMENTACION PENDIENTE': { originalValue: 'Documentacion Pendiente', bg: 'bg-red-100', textCol: 'text-red-700' },
                            'DOCUMENTACION COMPLETA': { originalValue: 'Documentacion Completa', bg: 'bg-green-100', textCol: 'text-green-700' }
                        })
                    },
                    { 
                        data: 'pdf_path', 
                        className: 'text-center',
                        render: function(data) {
                            if (!data) return '<span class="text-gray-300 text-[10px]">-</span>';
                            return `<a href="/orders_pdf/${data}" target="_blank" class="inline-flex items-center justify-center p-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition" title="Ver PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </a>`;
                        }
                    }
                ];

                tableColumns.push({ 
                    data: 'comentarios', 
                    className: 'text-[10px] text-gray-600 px-2',
                    render: (data) => data ? `<div class="whitespace-normal break-words line-clamp-2 hover:line-clamp-none transition-all duration-300 cursor-help">${data}</div>` : ''
                });
                tableColumns.push({ data: null, render: renderActions, orderable: false, className: 'text-right' });

                const table = $('#orders-table').DataTable({
                    ajax: { url: $('#orders-table').attr('data-url'), dataSrc: (json) => json.data || json },
                    dom: 'rtip',
                    pageLength: 25,
                    columns: tableColumns,
                    initComplete: function() {
                        const searchInput = $('.dataTables_filter').detach();
                        $('#search-container').append(searchInput);
                        $('.dataTables_filter input').addClass('w-full border-gray-300 rounded-md text-sm').attr('placeholder', 'Search order...');
                    },
                    language: { url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
                });

                // --- EVENTOS (Update Status, Delete, Edit, View) ---

                $(document).on('change', '.change-status-select', async function() {
                    const $el = $(this);
                    try {
                        const resp = await fetch(`/orders/${$el.data('id')}/status`, {
                            method: 'PATCH',
                            body: JSON.stringify({ [$el.data('field')]: $el.val() }),
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                        });
                        if (resp.ok) {
                            Swal.fire({ icon: 'success', title: 'Status updated', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                            table.ajax.reload(null, false); 
                        }
                    } catch (err) { Swal.fire('Error', 'Update failed', 'error'); }
                });

               $(document).on('click', '.delete-order-btn', async function() {
                    const id = $(this).data('id');
                    const res = await Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (res.isConfirmed) {
                        try {
                            const response = await fetch(`/orders/${id}`, { 
                                method: 'DELETE', 
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                            });

                            if (response.ok) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: 'The order has been removed.', timer: 1500, showConfirmButton: false });
                                table.ajax.reload(null, false); 
                            } else {
                                Swal.fire('Error', 'No autorizado o no se pudo eliminar', 'error');
                            }
                        } catch (err) { Swal.fire('Error', 'Deletion failed', 'error'); }
                    }
                });

                $(document).on('click', '.edit-order-btn', async function() {
                    const id = $(this).data('id');
                    try {
                        const resp = await fetch(`/orders/${id}/edit`);
                        if (!resp.ok) {
                            Swal.fire('Error', 'No autorizado para editar este pedido', 'error');
                            return;
                        }
                        const order = await resp.json();
                        $('#edit_id').val(order.id);
                        $('#edit_label_po').text(order.po ? `PO: ${order.po}` : 'Sin PO');
                        $('#edit_año').val(order.año);
                        $('#edit_semana').val(order.semana);
                        $('#edit_empresa').val(order.empresa);
                        $('#edit_po').val(order.po);
                        
                        // Cargar items de productos dinámicos
                        $('#edit-order-items-container').empty();
                        editItemIndex = 0;
                        if (order.items && order.items.length > 0) {
                            order.items.forEach(item => {
                                addEditProductRow(item.producto, item.cantidad);
                            });
                        } else if (order.producto) {
                            addEditProductRow(order.producto, order.cantidad);
                        } else {
                            addEditProductRow();
                        }

                        $('#edit_fecha_de_carga').val(order.fecha_de_carga);
                        $('#edit_hora').val(order.hora);
                        $('#edit_fecha_de_envio').val(order.fecha_de_envio);
                        $('#edit_fecha_requerida_por_el_cliente').val(order.fecha_requerida_por_el_cliente);
                        $('#edit_transporte').val(order.transporte);
                        $('#edit_estatus_almacen').val(order.estatus_almacen);
                        $('#edit_estatus_calidad').val(order.estatus_calidad);
                        $('#edit_estatus_administrativo').val(order.estatus_administrativo);
                        $('#edit_comentarios').val(order.comentarios);

                        $('#pdf_status').html(order.pdf_path ? '<span class="text-green-600 font-bold">✓ PDF Uploaded</span>' : '<span class="text-gray-400">No file</span>');

                        $('.edit-doc-check').prop('checked', false);
                        if(order.documentacion_requerida) {
                            order.documentacion_requerida.split(', ').forEach(doc => {
                                $(`.edit-doc-check[value="${doc}"]`).prop('checked', true);
                            });
                        }
                        $('#edit-order-modal').removeClass('hidden').show();
                    } catch (err) { Swal.fire('Error', 'Information could not be loaded', 'error'); }
                });

                $(document).on('click', '.view-order-btn', async function() {
                    const id = $(this).data('id');
                    try {
                        const resp = await fetch(`/orders/${id}/edit`);
                        const order = await resp.json();
                        $('#view_po_badge').text(order.po ? `PO: ${order.po}` : 'NO PO');
                        $('#view_fecha_info').text(`${order.año} / Week ${order.semana}`);
                        $('#view_empresa').text(order.empresa);

                        // Renderizar lista de productos
                        let prodsHtml = '';
                        if (order.items && order.items.length > 0) {
                            prodsHtml = '<div class="divide-y divide-gray-100 bg-white rounded-md border overflow-hidden">';
                            order.items.forEach((item, index) => {
                                prodsHtml += `
                                    <div class="flex justify-between items-center px-3 py-2 text-xs">
                                        <span class="font-semibold text-gray-800"><span class="text-green-600 font-bold mr-1.5">${index + 1}.</span> ${item.producto}</span>
                                        <span class="bg-green-50 text-green-700 font-bold px-2.5 py-0.5 rounded border border-green-200 text-[11px]">${item.cantidad || 'S/C'}</span>
                                    </div>
                                `;
                            });
                            prodsHtml += '</div>';
                        } else if (order.producto) {
                            prodsHtml = `
                                <div class="flex justify-between items-center bg-white px-3 py-2 rounded-md border text-xs">
                                    <span class="font-semibold text-gray-800">${order.producto}</span>
                                    <span class="bg-green-50 text-green-700 font-bold px-2.5 py-0.5 rounded border border-green-200 text-[11px]">${order.cantidad || 'S/C'}</span>
                                </div>
                            `;
                        } else {
                            prodsHtml = '<span class="text-gray-400 text-xs italic">Sin productos especificados</span>';
                        }
                        $('#view_products_container').html(prodsHtml);

                        $('#view_envio').text(formatDate(order.fecha_de_envio) ?? 'Pending');
                        $('#view_hora').text(order.hora ?? 'N/A');
                        $('#view_transporte').text(order.transporte ?? 'Not assigned');
                        $('#view_creador').text(order.user ? order.user.name : (order.user_id ? `Usuario #${order.user_id}` : 'General / Sistema'));
                        $('#view_comentarios').text(order.comentarios ?? 'No comments.');

                        let docsHtml = '';
                        if(order.documentacion_requerida) {
                            order.documentacion_requerida.split(', ').forEach(doc => {
                                docsHtml += `<span class="bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded border border-blue-200 font-bold">${doc}</span>`;
                            });
                        } else { docsHtml = '<span class="text-gray-400">None</span>'; }
                        $('#view_docs').html(docsHtml);

                        $('#view-order-modal').removeClass('hidden').show();
                    } catch (err) { Swal.fire('Error', 'Details could not be loaded', 'error'); }
                });

                $('#update-order').click(function() {
                    const id = $('#edit_id').val();
                    const form = document.getElementById('edit-order-form');
                    const data = new FormData(form);
                    $.ajax({
                        type: 'POST',
                        url: `/orders/${id}/update`,
                        data: data,
                        processData: false,
                        contentType: false,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function() {
                            Swal.fire({icon: 'success', title: 'Success!', timer: 1500, showConfirmButton: false}).then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Update failed', 'error');
                        }
                    });
                });
            });
        })();
        </script>
    </div>
</section>