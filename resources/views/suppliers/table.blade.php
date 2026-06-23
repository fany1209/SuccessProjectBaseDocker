{{--
Suppliers
Table Suppliers
Fecha de creación: 09-09-2025
Creado por: Jacob
Actualizado por: fany
Fecha de actualización: 2026-03-19
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search" placeholder="Search..."></x-input>
            <select id="sector" class="p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                <option value="">All sectors</option>
                @foreach ($sectors as $sector)
                    <option value="{{ $sector->sector_id }}">{{ $sector->name }}</option>
                @endforeach
            </select>
        </div>

        <table id="suppliers-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4 text-right">CATEGORY</th>
                    <th scope="col" class="px-6 py-4 text-right">CODE</th>
                    <th scope="col" class="px-6 py-4 text-right">NAME</th>
                    <th scope="col" class="px-6 py-4 text-right">CONTACT</th> {{-- Nueva columna --}}
                    <th scope="col" class="px-6 py-4 text-right">PHONE</th>
                    <th scope="col" class="px-6 py-4 text-right">EMAIL</th>
                    <th scope="col" class="px-6 py-4 text-right">RFC</th>
                    <th scope="col" class="px-6 py-4 text-right">ADDRESS</th>
                    <th scope="col" class="px-6 py-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>

    @can('suppliers.update')
        @include('suppliers.modals.editSupplier')
    @endcan
</section>

@push('js')
<script>
$(document).ready(function(){
    function getSuppliers(){
        let table = $('#suppliers-table').DataTable({
            ajax: {
                url: "{{ route('suppliers.getSuppliers') }}",
                data: function (d) {
                    d.sector = $('#sector').val();
                    d.search = $('#search').val();
                },
                dataSrc: 'suppliers'
            },
            columns: [
                { data: 'sector' },
                { data: 'code' },
                { data: 'name' },
                { data: 'contact' }, // Nueva columna
                { data: 'phone' },
                { data: 'email' },
                { data: 'rfc' },
                { data: 'address' },
                { data: null }
            ],
            columnDefs: [
                {
                    targets: 8, // Se movió de 7 a 8 por la nueva columna
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        let buttons = ``;
                        if(row.canUpdate){
                            buttons += `
                                <button data-id="${row.supplier_id}" data-target="edit-supplier" class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                    <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                            `;
                        }
                        if(row.canDelete){
                            buttons += `
                                <button data-id="${row.supplier_id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
                                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                                </button>
                            `;
                        }
                        return buttons || '';
                    }
                }
            ],
            lengthChange: false,
            searching: false,
            pageLength: 15,
            serverSide: false,
            responsive: true,
            autoWidth: false,
            language: {
                info: "Show _START_ to _END_ of _TOTAL_ suppliers",
                lengthMenu: "Show _MENU_ suppliers",
                infoEmpty: "There aren't suppliers available",
                zeroRecords: "No results found",
                infoFiltered: "(filtered on _MAX_ total records)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                },
            }
        });

        $('#sector').on('change', function () { table.ajax.reload(); });
        $('#search').on('keyup', function (){ table.ajax.reload(); });

        deleteSupplier(table);
        editSupplier(table);
    }

    function editSupplier(table){
        $('#suppliers-table tbody').on('click', '.edit-btn', function(){
            $('#name').prop('required', true);
            $('#supplier_code').prop('required', true);
            let id = $(this).data('id');

            $.ajax({
                type: 'GET',
                url: `/suppliers/${id}`,
                success: function(response){
                    $('#edit-supplier input#supplier_id').val(response.supplier['supplier_id']);
                    $('#edit-supplier input#supplier_code').val(response.supplier['supplier_code']);
                    $('#edit-supplier select#sector_id').val(response.supplier['sector_id']);
                    $('#edit-supplier input#name').val(response.supplier['name']);
                    $('#edit-supplier input#contact').val(response.supplier['contact']); // Nueva columna
                    $('#edit-supplier input#phone').val(response.supplier['phone']);
                    $('#edit-supplier input#email').val(response.supplier['email']);
                    $('#edit-supplier input#rfc').val(response.supplier['rfc']);
                    $('#edit-supplier input#state').val(response.supplier['state']);
                    $('#edit-supplier input#city').val(response.supplier['city']);
                    $('#edit-supplier input#district').val(response.supplier['district']);
                    $('#edit-customer input#address').val(response.supplier['address']);
                },
                error: function(e){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while got the supplier.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }

    function deleteSupplier(table){
        $('#suppliers-table tbody').on('click', '.delete-btn', function(){
            let id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/suppliers/${id}`,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            table.ajax.reload();
                            Swal.fire({
                                title: "Deleted!",
                                text: "The supplier has been deleted.",
                                icon: "success"
                            });
                        },
                        error:function(xhr){
                            let message = 'Ocurrió un error inesperado.';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                message = xhr.responseJSON.error;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error', 
                                text: message,
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }                
            }); 
        });
    }
    getSuppliers();
});
</script>
@endpush