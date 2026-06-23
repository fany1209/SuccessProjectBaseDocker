{{--
Transport Lines
Mostrar Transport Lines
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        <table id="transport-lines-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4 text-right">NAME</th>
                    <th scope="col" class="px-6 py-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>
    @include('logistic.transport-lines.modals.editTLine')
</section>
@push('js')
<script>
$(document).ready(function(){
    function getTransportLines(){
        let table = $('#transport-lines-table').DataTable({
            ajax: {
                url: "{{ route('transportLine.getTransportLines') }}",
                dataSrc: 'transport_lines'
            },
            columns: [
                { data: 'name' }
            ],
            columnDefs: [
                {
                    targets: 1,
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        let buttons = ``;
                        buttons += `
                            <button data-id="${row.transport_line_id}" data-target="edit-transport-line" class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                            </button>
                        `;
                        buttons += `
                            <button data-id="${row.transport_line_id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
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
                info: "Show _START_ to _END_ of _TOTAL_ transport lines",
                lengthMenu: "Show _MENU_ transport lines",
                infoEmpty: "There aren't transport lines available",
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
        editTLine(table);
        deleteTLine(table);
    }
    function editTLine(table){
        $('#transport-lines-table tbody').on('click', '.edit-btn', function(){
            let id = $(this).data('id');
            $.ajax({
                type: 'GET',
                url: `/transportLine/${id}`,
                success: function(response){
                    $('input#transport_line_id').val(response.transport_line['transport_line_id']);
                    $('input#name').val(response.transport_line['name']);
                },
                error: function(e){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while got the Transport line.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
    function deleteTLine(table){
        $('#transport-lines-table tbody').on('click', '.delete-btn', function(){
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
                        url: `/transportLine/${id}`,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            table.ajax.reload();
                            Swal.fire({
                                title: "Deleted!",
                                text: "The Transport line has been deleted.",
                                icon: "success"
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleted the Transport line.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }                
            }); 
        });
    }
    getTransportLines();
});
</script>
@endpush