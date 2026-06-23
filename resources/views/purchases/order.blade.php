<div class="flex flex-col justify-center items-center w-full mt-2">

    <div class="flex justify-end items-center gap-2 w-full my-1">
        <input type="text" id="search_orders" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 w-1/3 text-sm" placeholder="Search Orders...">
        <input type="date" id="order_date_filter" class="p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-sm">
    </div>

    <table id="purchase-orders-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left shadow-sm rounded-lg overflow-hidden" style="width:100%">
        <thead class="bg-gray-50 text-gray-700 uppercase text-md">
            <tr>
                <th scope="col" class="px-6 py-4 text-right">FOLIO</th>
                <th scope="col" class="px-6 py-4 text-right">SUPPLIER</th>
                <th scope="col" class="px-6 py-4 text-right">APPLICANT</th>
                <th scope="col" class="px-6 py-4 text-center">REQ. DATE</th>
                <th scope="col" class="px-6 py-4 text-center">DELIVERY</th>
                <th scope="col" class="px-6 py-4 text-center">GUIDE</th>
                <th scope="col" class="px-6 py-4 text-right">TOTAL</th>
                <th scope="col" class="px-6 py-4 text-center">ACTIONS</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words"></tbody>
    </table>
</div>

@include('purchases.modals.oEdit')

<script>
    window.initOrdersTable = function() {
        if ($.fn.DataTable.isDataTable('#purchase-orders-table')) {
            $('#purchase-orders-table').DataTable().columns.adjust().responsive.recalc();
            return;
        }

        let ordersTable = $('#purchase-orders-table').DataTable({
            lengthChange: false, 
            searching: false,    
            bInfo: true,         
            ajax: {
                url: "{{ route('purchases.getPurchaseOrders') }}",
                data: function (d) {
                    d.search_orders = $('#search_orders').val();
                    d.date_filter = $('#order_date_filter').val();
                },
                dataSrc: 'orders'
            },
            columns: [
                { 
                    data: 'id',
                    className: 'px-6 py-3 text-right font-semibold',
                    render: function(data){
                        return data; 
                    }
                },
                { 
                    data: 'supplier',
                    className: 'px-6 py-3 text-right',
                    render: function(data) {
                        return data ? data.name : '<span class="text-gray-400 italic">No supplier</span>';
                    }
                },
                { data: 'applicant', className: 'px-6 py-3 text-right' },
                { 
                    data: 'application_date',
                    className: 'px-6 py-3 text-center',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('es-MX') : '—';
                    }
                },
                { 
                    data: 'delivery_date',
                    className: 'px-6 py-3 text-center',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('es-MX') : '—';
                    }
                },
                { 
                    data: 'guia',
                    className: 'px-6 py-3 text-center',
                    render: function(data) {
                        return data ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">${data}</span>` : '—';
                    }
                },
                { 
                    data: 'price', 
                    className: 'px-6 py-3 text-right font-bold text-green-700',
                    render: $.fn.dataTable.render.number(',', '.', 2, '$')
                },
                { 
                    data: null,
                    className: 'px-6 py-3 text-center align-middle',
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="flex justify-center gap-2">
                                <button data-id="${row.id}"
                                    class="edit-order-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 transition transform hover:scale-105"
                                    title="Edit">
                                    <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>

                                <a href="/purchases/order-pdf/${row.id}" target="_blank"
                                    class="text-sm text-white bg-orange-500 hover:bg-orange-600 rounded-sm p-2 transition transform hover:scale-105"
                                    title="PDF">
                                    <i class="ri-file-pdf-line text-[18px]"></i>
                                </a>

                                <button data-id="${row.id}"
                                    class="delete-order-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2 transition transform hover:scale-105"
                                    title="Delete">
                                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            order: [[0, 'desc']],
            language: { 
                emptyTable: "No purchase orders found",
                info: "Show _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "There aren't entries available",
                zeroRecords: "No results found",
                paginate: { next: "Next", previous: "Previous" }
            }
        });

        $('#search_orders').on('keyup', function() { 
            ordersTable.ajax.reload(); 
        });

        $('#order_date_filter').on('change', function() { 
            ordersTable.ajax.reload(); 
        });

        // --- EDITAR ---
        $('#purchase-orders-table tbody').on('click', '.edit-order-btn', function() {
            let id = $(this).data('id');
            $.get(`/purchases/orders/${id}/edit`, function(order) {
                $('#edit_id').val(order.id); 
                $('#edit_applicant').val(order.applicant);
                $('#edit_supplier_id').val(order.supplier_id);
                $('#edit_contact').val(order.contact);
                $('#edit_delivery_time').val(order.delivery_time);
                $('#edit_delivery_date').val(order.delivery_date);
                $('#edit_guia').val(order.guia);
                $('#edit_cfdi').val(order.cfdi);
                $('#edit_payment_method').val(order.payment_method);
                $('#edit_method_payment').val(order.method_payment);
                $('#edit_application_date').val(order.application_date);
                $('#edit_total_price').val(order.price);

                $('#form-edit-order').attr('action', `/purchases/orders/${id}`);

                if(typeof window.fillEditProductsTable === 'function'){
                    window.fillEditProductsTable(order.details);
                }

                const modal = document.getElementById('modal-oEdit');
                if(modal) modal.classList.remove('hidden');
            });
        });

        // --- ELIMINAR ---
        $('#purchase-orders-table tbody').on('click', '.delete-order-btn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6', 
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/purchases/orders/${id}`,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            Swal.fire('Deleted!', 'The order has been deleted.', 'success');
                            ordersTable.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Could not delete the order.', 'error');
                        }
                    });
                }
            })
        });
    };
</script>