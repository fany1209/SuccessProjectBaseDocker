{{--
Vehicles
Mostrar Vehicles
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search_vehicle" placeholder="Search..."></x-input>
            <select id="t_line_vehicle" class="p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                <option value="">All transport lines</option>
                @foreach ($tLines as $tLine)
                    <option value="{{ $tLine->transport_line_id }}">{{ $tLine->name }}</option>
                @endforeach
            </select>
        </div>
        <table id="vehicle-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4 text-right">TYPE</th>
                    <th scope="col" class="px-6 py-4 text-right">UNIT NUMBER</th>
                    <th scope="col" class="px-6 py-4 text-right">PLATE</th>
                    <th scope="col" class="px-6 py-4 text-right">COLOR</th>
                    <th scope="col" class="px-6 py-4 text-right">TRANSPORT LINE</th>
                    <th scope="col" class="px-6 py-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>
    @include('logistic.vehicles.modals.editVehicle')
</section>
@push('js')
<script>
$(document).ready(function(){
    function getVehicle(){
        let table = $('#vehicle-table').DataTable({
            ajax: {
                url: "{{ route('vehicle.getVehicles') }}",
                data: function (d) {
                    d.t_line = $('#t_line_vehicle').val();
                    d.search = $('#search_vehicle').val();
                },
                dataSrc: 'vehicles'
            },
            columns: [
                { data: 'type' },
                { data: 'unit_number' },
                { data: 'plate' },
                { data: 'color' },
                { data: 'name' },
            ],
            columnDefs: [
                {
                    targets: 5,
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        let buttons = ``;
                        buttons += `
                            <button data-id="${row.vehicle_id}" data-target="edit-vehicle" class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                            </button>
                        `;
                        buttons += `
                            <button data-id="${row.vehicle_id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
                                <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                            </button>
                        `;
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
                info: "Show _START_ to _END_ of _TOTAL_ vehicles",
                lengthMenu: "Show _MENU_ vehicles",
                infoEmpty: "There aren't vehicles available",
                zeroRecords: "No results found",
                infoFiltered: "(filtered on _MAX_ total records)",
                paginate: {
                    first:      "First",
                    last:       "Last",
                    next:       "Next",
                    previous:   "Previous"
                },
            }
        });
        $('#t_line_vehicle').on('change', function () {
            table.ajax.reload();
        });
        $('#search_vehicle').on('keyup',function (){
            table.ajax.reload();
        });
        editVehicle(table);
        deleteVehicle(table);
    }
    function editVehicle(table){
        $('#vehicle-table tbody').on('click', '.edit-btn', function(){
            let id = $(this).data('id');
            $.ajax({
                type: 'GET',
                url: `/vehicle/${id}`,
                success: function(response){
                    $('input#vehicle_id').val(response.vehicle['vehicle_id']);
                    $('input#type').val(response.vehicle['type']);
                    $('input#unit_number').val(response.vehicle['unit_number']);
                    $('input#plate').val(response.vehicle['plate']);
                    $('input#color').val(response.vehicle['color']);
                    $('select#transport_line_id').val(response.vehicle['transport_line_id']);
                },
                error: function(e){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while got the Vehicle.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
    function deleteVehicle(table){
        $('#vehicle-table tbody').on('click', '.delete-btn', function(){
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
                        url: `/vehicle/${id}`,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            table.ajax.reload();
                            Swal.fire({
                                title: "Deleted!",
                                text: "The Vehicle has been deleted.",
                                icon: "success"
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleted the Vehicle.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }                
            }); 
        });
    }
    getVehicle();
});
</script>
@endpush