{{--
Customers
Mostrar Customers
Fecha de creación: 08-09-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 08-01-2025
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex justify-between items-center gap-2 w-full my-2">
            <div class="relative w-full md:w-1/3">
                <input type="text" id="global-search" class="filter-input w-full p-2 pr-10 border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm" placeholder="Search by code, name, email, phone...">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <button id="toggle-filters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md shadow-sm text-gray-700 font-medium flex items-center gap-2" title="Toggle Filters">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>
        
        <div id="advanced-filters" class="hidden w-full bg-gray-50 p-4 rounded-md shadow-sm mb-2 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sector</label>
                    <select id="sector" class="filter-input w-full mt-1 p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                        <option value="">All sectors</option>
                        @foreach ($sectors as $sector)
                            <option value="{{ $sector->sector_id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name</label>
                    <x-input class="filter-input w-full mt-1" id="filter-name" placeholder="Filter by Name"></x-input>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">City</label>
                    <x-input class="filter-input w-full mt-1" id="filter-city" placeholder="Filter by City"></x-input>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Seller</label>
                    <select id="filter-vendedor" class="filter-input w-full mt-1 p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                        <option value="">All Sellers</option>
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->name }}">{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button id="clear-filters" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm">Clear</button>
            </div>
        </div>

        <table id="customers-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4 text-right">CATEGORY</th>
                    <th scope="col" class="px-6 py-4 text-right">CODE</th>
                    <th scope="col" class="px-6 py-4 text-right">NAME</th>
                    <th scope="col" class="px-6 py-4 text-right">CONTACT</th> {{-- Nueva Columna --}}
                    <th scope="col" class="px-6 py-4 text-right">PHONE</th>
                    <th scope="col" class="px-6 py-4 text-right">EMAIL</th>
                    <th scope="col" class="px-6 py-4 text-right">RFC</th>
                    <th scope="col" class="px-6 py-4 text-right">VENDEDOR</th>
                    <th scope="col" class="px-6 py-4 text-right">ADDRESS</th>
                    <th scope="col" class="px-6 py-4 text-right">DELIVERY ADDRESS</th> {{-- Nueva Columna --}}
                    <th scope="col" class="px-6 py-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>

    @can('customers.update')
        @include('customers.modals.editCustomer')
    @endcan
</section>

@push('js')
<script>
$(function(){
    function getCustomers(){
        let table = $('#customers-table').DataTable({
            ajax: {
                url: "{{ route('customers.getCustomers') }}",
                data: function (d) {
                    d.sector = $('#sector').val();
                    d.city = $('#filter-city').val();
                    d.vendedor = $('#filter-vendedor').val();
                    d.name = $('#filter-name').val();
                    d.search = $('#global-search').val();
                },
                dataSrc: 'customers'
            },
            columns: [
                { data: 'sector' },
                { data: 'code' },
                { data: 'name' },
                { data: 'contact' }, // Nueva Columna
                { data: 'phone' },
                { data: 'email' },
                { data: 'rfc' },
                { data: 'vendedor' },
                { data: 'address' },
                { data: 'delivery_address' }, // Nueva Columna
                { data: null }
            ],
            columnDefs: [
                {
                    targets: 10, // Se movió de 8 a 10 por las nuevas columnas
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        let buttons = ``;
                        if(row.canUpdate){
                            buttons += `
                                <button data-id="${row.customer_id}" data-target="edit-customer"
                                    class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2"
                                    title="Edit">
                                    <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                            `;
                        }
                        if(row.canDelete){
                            buttons += `
                                <button data-id="${row.customer_id}"
                                    class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
                                    title="Delete">
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
                info: "Show _START_ to _END_ of _TOTAL_ customers",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                },
            }
        });

        $('.filter-input').on('keyup change', function() {
            table.ajax.reload();
        });

        $('#toggle-filters').on('click', function(e) {
            e.preventDefault();
            $('#advanced-filters').toggleClass('hidden');
        });

        $('#clear-filters').on('click', function(e) {
            e.preventDefault();
            $('#sector').val('');
            $('#filter-city').val('');
            $('#filter-vendedor').val('');
            $('#filter-name').val('');
            $('#global-search').val('');
            table.ajax.reload();
        });

        deleteCustomer(table);
        editCustomer(table);
    }

    function editCustomer(table){
        $('#customers-table tbody').on('click', '.edit-btn', function(){
            $('#name').prop('required', true);
            $('#customer_code').prop('required', true);

            let id = $(this).data('id');

            $.ajax({
                type: 'GET',
                url: `/customers/${id}`,
                success: function(response){
                    $('#edit-customer input#customer_id').val(response.customer['customer_id']);
                    $('#edit-customer #customer_code').text(`Customer code: ${response.customer['customer_code']}`);
                    $('#edit-customer input#vendedor').val(response.customer['vendedor']);
                    $('#edit-customer select#sector_id').val(response.customer['sector_id']);
                    $('#edit-customer input#name').val(response.customer['name']);
                    $('#edit-customer input#contact').val(response.customer['contact']); // Nueva Carga
                    $('#edit-customer input#phone').val(response.customer['phone']);
                    $('#edit-customer input#email').val(response.customer['email']);
                    $('#edit-customer input#rfc').val(response.customer['rfc']);
                    $('#edit-customer input#postal_code').val(response.customer['postal_code']);
                    $('#edit-customer input#state').val(response.customer['state']);
                    $('#edit-customer input#city').val(response.customer['city']);
                    $('#edit-customer input#district').val(response.customer['district']);
                    $('#edit-customer input#address').val(response.customer['address']);
                    $('#edit-customer textarea#delivery_address').val(response.customer['delivery_address']); // Nueva Carga
                    $('#edit-customer input#country').val(response.customer['country']);
                    
                    if ($('#edit-customer input#vendedor').length) {
                        $('#edit-customer input#vendedor').val(response.customer['vendedor'] ?? '');
                    }
                },
                error: function(e){
                    Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while getting the Customer.' });
                }
            });
        });
    }

    function deleteCustomer(table){
        $('#customers-table tbody').on('click', '.delete-btn', function(){
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
                        url: `/customers/${id}`,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            table.ajax.reload();
                            Swal.fire({
                                title: "Deleted!",
                                text: "The Customer has been deleted.",
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

    getCustomers();
});
</script>
@endpush
