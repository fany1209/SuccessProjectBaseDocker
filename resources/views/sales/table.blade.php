{{--
Sales
Mostrar Sales
Fecha de creación: 24-09-2025
Creado por: Jacob
Actualizado por: FANY
Fecha de actualización: 09-03-2026
Actualizado por: Emilio
Fecha de actualización: 15-06-2026
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

    <div class="flex flex-col justify-center items-center w-full">

        <div class="flex justify-between items-center gap-2 w-full my-2">
            <div class="relative w-full md:w-1/3">
                <input type="text" id="global-search" class="filter-input w-full p-2 pr-10 border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm" placeholder="Search by customer, seller, folio...">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <button id="toggle-filters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md shadow-sm text-gray-700 font-medium flex items-center gap-2" title="Toggle Filters">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>

        <div id="advanced-filters" class="hidden w-full bg-gray-50 p-4 rounded-md shadow-sm mb-2 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                    <label class="block text-sm font-medium text-gray-700">Seller</label>
                    <select id="filter-seller" class="filter-input w-full mt-1 p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                        <option value="">All Sellers</option>
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->name }}">{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select id="filter-type" class="filter-input w-full mt-1 p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                        <option value="">All Types</option>
                        <option value="Cash">Cash</option>
                        <option value="Credit">Credit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date From</label>
                    <x-input type="date" class="filter-input w-full mt-1" id="filter-date-from"></x-input>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date To</label>
                    <x-input type="date" class="filter-input w-full mt-1" id="filter-date-to"></x-input>
                </div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button id="clear-filters" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm">Clear</button>
            </div>
        </div>

        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
            <table id="sales-table" class="min-w-full divide-y divide-gray-200 text-left">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs md:text-sm">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">FOLIO</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">DATE</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">SELLER</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap hidden md:table-cell">CATEGORY</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">CUSTOMER</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap hidden sm:table-cell">TYPE</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">INVOICE</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">ORDER</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">PRODUCTS</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">STATUS</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm">
                </tbody>
            </table>
        </div>

    </div>
</section>

@push('js')
    <script>
        $(document).ready(function () {
            function getSales() {
                let table = $('#sales-table').DataTable({
                    ajax: {
                        url: "{{ route('sales.getSales') }}",
                        data: function (d) {
                            d.sector = $('#sector').val();
                            d.clients = 1; // Default to Customers since filter was removed
                            d.seller = $('#filter-seller').val();
                            d.sale_type = $('#filter-type').val();
                            d.date_from = $('#filter-date-from').val();
                            d.date_to = $('#filter-date-to').val();
                            d.search = $('#global-search').val();
                        },
                        dataSrc: 'sales'
                    },
                    columns: [
                        { data: 'folio' },
                        { data: 'date' },
                        { data: 'seller' },
                        { data: 'sName' },
                        { data: 'cName' },
                        { data: 'sale_type' },
                        { data: 'invoice' },
                        { data: 'purchase_order' },
                        { data: 'products' },
                        { data: 'sales_status_id' }
                    ],
                    columnDefs: [
                        {
                            targets: 9,
                            orderable: false,
                            searchable: false,
                            className: 'text-right',
                            render: function (data, type, row) {
                                let options = '';
                                @foreach($statuses as $status)
                                    options += `<option value="{{ $status->sales_status_id }}" ${data == {{ $status->sales_status_id }} ? 'selected' : ''}>{{ $status->name }}</option>`;
                                @endforeach

                            return `
                                <select data-id="${row.sale_id}" class="status-change-select p-1 w-full text-xs border-gray-300 rounded-md focus:ring-green-500">
                                    ${options}
                                </select>
                            `;
                            }
                        },
                        {
                            targets: 10,
                            orderable: false,
                            searchable: false,
                            className: 'text-right',
                            render: function (data, type, row) {
                                let buttons = `<div class="flex justify-center items-center space-x-2">`;
                                buttons += `
                                <button data-id="${row.sale_id}" class="edit-btn flex items-center justify-center w-8 h-8 text-white bg-blue-500 hover:bg-blue-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="Edit">
                                    <img width="16" src="{{ asset('images/editar.png') }}" alt="Edit" class="icon-white"/>
                                </button>
                            `;
                                buttons += `
                                <button data-id="${row.sale_id}" data-target="view-more" class="open-modal view-more-btn flex items-center justify-center w-8 h-8 text-white bg-sky-500 hover:bg-sky-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="View more">
                                    <img width="16" src="{{ asset('images/ver.png') }}" alt="View more" class="icon-white"/>
                                </button>
                            `;
                                buttons += `
                                <button data-id="${row.sale_id}" class="delivery-note-btn flex items-center justify-center w-8 h-8 text-white bg-yellow-500 hover:bg-yellow-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="Delivery note">
                                    <img width="16" src="{{ asset('images/archivo.png') }}" alt="Delivery note" class="icon-white"/>
                                </button>
                            `;
                                buttons += `
                                <button data-id="${row.sale_id}" class="delete-btn flex items-center justify-center w-8 h-8 text-white bg-red-500 hover:bg-red-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="Delete">
                                    <img width="16" src="{{ asset('images/borrar.png') }}" alt="Delete" class="icon-white"/>
                                </button>
                            `;
                                buttons += `</div>`;
                                return buttons;
                            }
                        }
                    ],
                    lengthChange: false,
                    searching: false,
                    pageLength: 10,
                    serverSide: false,
                    language: {
                        info: "Show _START_ to _END_ of _TOTAL_ sales",
                        lengthMenu: "Show _MENU_ sales",
                        infoEmpty: "There aren't sales available",
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
                    $('#filter-seller').val('');
                    $('#filter-type').val('');
                    $('#filter-date-from').val('');
                    $('#filter-date-to').val('');
                    $('#global-search').val('');
                    table.ajax.reload();
                });

                deleteSale();
                editSale(table);
                viewMore();
                deliveryNote();
                updateStatusTable();
            }

            function updateStatusTable() {
                $('#sales-table tbody').on('change', '.status-change-select', function () {
                    let saleId = $(this).data('id');
                    let newStatusId = $(this).val();
                    let selectElement = $(this);

                    selectElement.prop('disabled', true).css('opacity', '0.5');

                    $.ajax({
                        url: `/sales/${saleId}/update-status`,
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            sales_status_id: newStatusId
                        },
                        success: function (response) {
                            selectElement.prop('disabled', false).css('opacity', '1');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Status updated successfully',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        },
                        error: function () {
                            selectElement.prop('disabled', false).css('opacity', '1');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating the status.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
            }

            function deliveryNote(table) {
                $('#sales-table tbody').on('click', '.delivery-note-btn', function () {
                    let id = $(this).data('id');
                    var ruta = 'delivery-note/' + id;
                    window.open(ruta, '_blank');
                });
            }

            function viewMore(table) {
                $('#sales-table tbody').on('click', '.view-more-btn', function () {
                    let id = $(this).data('id');
                    $('#sale').val(id);
                });
            }

            function editSale(table) {
                $('#sales-table tbody').on('click', '.edit-btn', function () {
                    let id = $(this).data('id');
                    const father = $('#update-sale-blade');

                    $('#update-sale-form #products-sales').empty();

                    father.removeClass('hidden');
                    $('#add-sale-blade').addClass('hidden');

                    $.ajax({
                        type: 'GET',
                        url: `/sales/${id}`,
                        success: function (response) {

                            father.find('#sale-id').val(response.sale['sale_id']);
                            father.find('#sales-status').val(response.sale['sales_status_id']);
                            father.find('#sector').val(response.sale['sector_id']);
                            father.find('#sector-selected').text(response.sale['sector_name']);
                            father.find('#folio').val(response.sale['folio']);
                            father.find('#seller').val(response.sale['seller']);
                            father.find('#user_id').val(response.sale['user_id']);
                            father.find('#purchase-order').val(response.sale['purchase_order']);
                            father.find('#date').val(response.sale['date']);
                            father.find('#invoice').val(response.sale['invoice']);


                            if (parseInt(response.sale['is_customer']) === 1) {
                                father.find('#is-customer').val('1');
                                father.find('#client-tag').text('Customer name');

                                father.find('#customer-1')
                                    .val(`${response.sale.customer_id} - ${response.sale.client_name}`);
                                father.find('#prospect-2').val('');


                                father.find('#customer-1-id').val(response.sale.customer_id);
                                father.find('#prospect-2-id').val('');

                                father.find('#customer-btn')
                                    .text('Yes')
                                    .addClass('border-green-400 bg-green-400 text-white pointer-events-none opacity-60')
                                    .removeClass('border-gray-400 hover:bg-green-300 text-gray-700');
                                
                                father.find('#customer-1').prop('disabled', false).removeClass('hidden');
                                father.find('#prospect-2').prop('disabled', true).addClass('hidden');

                            } else {
                                father.find('#is-customer').val('0');
                                father.find('#client-tag').text('Prospect name');

                                father.find('#prospect-2')
                                    .val(`${response.sale.prospect_id} - ${response.sale.client_name}`);
                                father.find('#customer-1').val('');

                                father.find('#prospect-2-id').val(response.sale.prospect_id);
                                father.find('#customer-1-id').val('');

                                father.find('#customer-btn')
                                    .text('No')
                                    .removeClass('border-green-400 bg-green-400 text-white pointer-events-none opacity-60')
                                    .addClass('border-gray-400 hover:bg-green-300 text-gray-700');
                                
                                father.find('#prospect-2').prop('disabled', false).removeClass('hidden');
                                father.find('#customer-1').prop('disabled', true).addClass('hidden');
                            }

                            father.find('#client-name, #client-selected').text(response.sale['client_name']);
                            father.find('#email').val(response.sale['email']);
                            father.find('#phone').val(response.sale['phone']);
                            father.find('#rfc').val(response.sale['rfc']);
                            father.find('#address').val(response.sale['address']);
                            father.find('#city').val(response.sale['city']);
                            father.find('#country').val(response.sale['country']);
                            father.find('#district').val(response.sale['district']);

                            if (response.sale['invoice'] === null) {
                                father.find('#invoice-btn')
                                    .text('No')
                                    .removeClass('border-green-400 bg-green-400 text-white')
                                    .addClass('border-gray-400 hover:bg-green-300 text-gray-700');

                                father.find('#invoice').prop('disabled', true);
                            } else {
                                father.find('#invoice-btn')
                                    .text('Yes')
                                    .addClass('border-green-400 bg-green-400 text-white')
                                    .removeClass('border-gray-400 hover:bg-green-300 text-gray-700');

                                father.find('#invoice').prop('disabled', false);
                            }

                            if (response.sale['sale_type'] === 'Credit') {
                                father.find('#payment-metod')
                                    .prop('checked', false)
                                    .trigger('change');

                                father.find('#sale-type-input')
                                    .prop('disabled', false)
                                    .val(response.sale['term']);
                                
                                father.find('#payment-status-container').removeClass('hidden');
                                father.find('#payment-status').val(response.sale['payment_status'] || 'PENDING');
                            } else {
                                father.find('#payment-metod')
                                    .prop('checked', true)
                                    .trigger('change');

                                father.find('#sale-type-input')
                                    .prop('disabled', true)
                                    .val('');
                                
                                father.find('#payment-status-container').addClass('hidden');
                            }

                            response.sale_detail.forEach(item => {
                                const newProduct = $('#product-template-update .wrapper').clone();

                                newProduct.find('.sale-detail').val(item['sale_detail_id']);
                                newProduct.find('.remove-btn-2').attr('data-id', item['sale_detail_id']);
                                newProduct.find('.product-id').val(item['product_id']);
                                newProduct.find('.quantity').val(item['quantity']);
                                newProduct.find('.cost').val(item['cost']);
                                newProduct.find('.public-product-name').val(item['public_product_name']);
                                newProduct.find('.public-batch').val(item['public_batch']);

                                newProduct.find('.product-name').text(`Description: ${item['original_product_name']}`);
                                newProduct.find('.product-sku').text(`SKU: ${item['sku']}`);

                                if (item['has_tax'] == 1) {
                                    newProduct.find('.has-tax').val('1');
                                    newProduct.find('.tax-btn-2')
                                        .text('Yes')
                                        .removeClass('border-gray-400 text-gray-700')
                                        .addClass('border-blue-400 bg-blue-400 text-white');
                                } else {
                                    newProduct.find('.has-tax').val('0');
                                    newProduct.find('.tax-btn-2').text('No');
                                }

                                const invoiceVal = (item['invoice_val'] == 1 || item['invoice_val'] === true || item['invoice_val'] === '1') ? '1' : '0';
                                newProduct.find('.invoice-val-select').val(invoiceVal);

                                if (invoiceVal === '1') {
                                    newProduct.find('.cost').addClass('hidden');
                                    newProduct.find('.cost-tag').text('Cost: Invoice value');
                                } else {
                                    newProduct.find('.cost').removeClass('hidden');
                                    newProduct.find('.cost-tag').text('Cost');
                                }

                                $('#update-sale-form #products-sales').append(newProduct);

                                if (typeof window.updateWrapperUpdate === 'function') {
                                    window.updateWrapperUpdate(newProduct);
                                }
                            });

                            father.find('#products-count').text(response.sale_detail.length);

                            if (typeof window.updateGrandTotalUpdate === 'function') {
                                window.updateGrandTotalUpdate();
                            }
                        },
                        error: function () {
                            console.log("Error al cargar los datos de la venta");
                        }
                    });
                });
            }

            function deleteSale() {
                $('#sales-table tbody').on('click', '.delete-btn', function () {
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
                                url: `/sales/${id}`,
                                method: "DELETE",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (data) {
                                    $('#sales-table').DataTable().ajax.reload();
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "The Sale has been deleted.",
                                        icon: "success"
                                    });
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'An error occurred while deleted the Sale.',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                });
            }

            getSales();
        });
    </script>
@endpush