<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search_suppliers" placeholder="Search supplier by name, code or product..."></x-input>
        </div>

        <div class="w-full overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
            <table id="suppliers-table" class="display w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase text-[10px] font-black tracking-wider">
                    <tr>
                        <th class="px-3 py-4">Code</th>
                        <th class="px-3 py-4">Supplier Name</th>
                        <th class="px-3 py-4">Product</th>
                        <th class="px-3 py-4">Address</th>
                        <th class="px-3 py-4">Phone</th>
                        <th class="px-3 py-4">Email</th>
                        <th class="px-3 py-4">RFC</th>
                        <th class="px-3 py-4">Contact</th>
                        <th class="px-3 py-4 text-center">Created At</th>
                        <th class="px-3 py-4 text-center text-blue-600">Updated At</th>
                        <th class="px-3 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-gray-800">
                    {{-- DataTables cargará los datos aquí vía AJAX --}}
                </tbody>
            </table>
        </div>
    </div>
</section>

@include('purchases.directory.modals.edit')

@push('js')
<script>
$(document).ready(function () {
    let table; 

    function getSuppliers() {
        // Si ya existe la tabla, la destruimos para evitar errores de reinicialización
        if ($.fn.DataTable.isDataTable('#suppliers-table')) {
            $('#suppliers-table').DataTable().destroy();
        }

        table = $('#suppliers-table').DataTable({
            processing: true,
            serverSide: false,
            autoWidth: false, 
            retrieve: true,
            ajax: {
                url: "{{ route('suppliers.getDirectory') }}",
                dataSrc: 'suppliers'
            },
            columns: [
                { data: 'Code_supplier', className: 'px-3 py-3 font-mono text-[11px] text-gray-500' },
                { data: 'Name', className: 'px-3 py-3 font-bold text-blue-900 uppercase italic text-[12px]' },
                { 
                    data: 'Product',
                    className: 'px-3 py-3',
                    render: function(data) {
                        return data ? `<span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full text-[9px] font-black uppercase whitespace-nowrap">${data}</span>` : '—';
                    }
                },
                { 
                    data: 'Address', 
                    className: 'px-3 py-3 text-[11px] text-gray-500 min-w-[150px]',
                    render: function(data) {
                        return data ? `<div class="line-clamp-1" title="${data}">${data}</div>` : '—';
                    }
                },
                { data: 'Phone', className: 'px-3 py-3 text-[11px] whitespace-nowrap' },
                { data: 'Email', className: 'px-3 py-3 text-[11px] text-blue-600 lowercase' },
                { data: 'RFC', className: 'px-3 py-3 font-bold text-[11px] whitespace-nowrap' },
                { data: 'Contact', className: 'px-3 py-3 text-[11px] font-medium' },
                { 
                    data: 'created_at', 
                    className: 'px-3 py-3 text-[10px] text-gray-400 text-center',
                    render: function(data) {
                        if(!data) return '—';
                        let d = new Date(data);
                        return d.toLocaleDateString('es-MX');
                    }
                },
                { 
                    data: 'updated_at', 
                    className: 'px-3 py-3 text-[10px] text-blue-400 text-center font-medium',
                    render: function(data) {
                        if(!data) return '—';
                        let d = new Date(data);
                        return d.toLocaleDateString('es-MX');
                    }
                },
                {
                    data: null,
                    orderable: false, 
                    className: 'px-3 py-3 text-center whitespace-nowrap',
                    render: function (data, type, row) {
                        return `
                            <div class="flex justify-center gap-1">
                                <button type="button" 
                                   class="edit-supplier-btn bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded shadow-sm transition" title="Edit">
                                    <img width="14" src="{{ asset('images/editar.png') }}">
                                </button>
                                <button data-code="${row.Code_supplier}" data-name="${row.Name}"
                                        class="delete-supplier-btn bg-red-500 hover:bg-red-600 text-white p-1.5 rounded shadow-sm transition" title="Delete">
                                    <img width="14" src="{{ asset('images/borrar.png') }}">
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            dom: 'rtip',
            pageLength: 10,
            // Corregido para evitar errores de CORS con el idioma
            language: {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });

        $('#search_suppliers').on('keyup', function(){
            table.search($(this).val()).draw();
        });
    }

    // Inicialización
    getSuppliers();

    // Re-ajustar columnas al cambiar de pestaña si usas layouts ocultos
    $('.option-btn').on('click', function() {
        if($(this).data('button') === 'directory') {
            setTimeout(() => {
                if(table) table.columns.adjust().responsive.recalc();
            }, 150);
        }
    });

    // --- Lógica de Edición ---
    $(document).on('click', '.edit-supplier-btn', function() {
        $('#edit-supplier-form')[0].reset();
        let tr = $(this).closest('tr');
        let rowData = table.row(tr).data();

        $('#edit_original_code').val(rowData.Code_supplier);
        $('#edit_Code_supplier').val(rowData.Code_supplier);
        $('#edit_Name').val(rowData.Name);
        $('#edit_Product').val(rowData.Product);
        $('#edit_RFC').val(rowData.RFC);
        $('#edit_Phone').val(rowData.Phone);
        $('#edit_Email').val(rowData.Email);
        $('#edit_Contact').val(rowData.Contact);
        $('#edit_Address').val(rowData.Address || '');
        
        $('#edit-supplier').removeClass('hidden').addClass('flex');
    });

    $('#edit-supplier-form').on('submit', function(e) {
        e.preventDefault(); 
        let code = $('#edit_original_code').val();
        let formData = $(this).serialize() + '&_method=PUT';

        $.ajax({
            url: `/purchases/supplier-directory/${code}`, 
            type: 'POST', 
            data: formData,
            success: function(response) {
                $('#edit-supplier').addClass('hidden').removeClass('flex'); 
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'Proveedor actualizado con éxito.',
                    timer: 1500,
                    showConfirmButton: false
                });
                table.ajax.reload(null, false); 
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo actualizar el registro.', 'error');
            }
        });
    });

    // --- Lógica de Eliminación ---
    $(document).on('click', '.delete-supplier-btn', function() {
        let code = $(this).data('code');
        let name = $(this).data('name');
        Swal.fire({
            title: '¿Estás seguro?',
            text: `Se eliminará el proveedor: ${name}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/purchases/supplier-directory/${code}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Eliminado', 'Proveedor borrado.', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush