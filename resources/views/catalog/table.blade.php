{{--
Catalog
Mostrar Catalog
Fecha de creación: 10-09-2025
Creado por: Jacob
Actualizado por: Fany
Fecha de actualización: 11-03-2026
--}}
<x-application.section-1>
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex justify-center items-center gap-2 w-full my-1">
            <x-input class="w-full p-2" id="search" placeholder="Search..."></x-input>
            <select id="category" class="p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <table id="products-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4 text-right">CATEGORY</th>
                    <th scope="col" class="px-6 py-4 text-right">PRODUCT</th>
                    <th scope="col" class="px-6 py-4 text-right">SAT CODE</th>
                    <th scope="col" class="px-6 py-4 text-right">SKU</th>
                    <th scope="col" class="px-6 py-4 text-right">IMAGE</th>
                    <th scope="col" class="px-6 py-4 text-right">FILES</th>
                    <th scope="col" class="px-6 py-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
        </table>
    </div>
    @can('products.update')
    @include('catalog.modals.editProduct')
    @endcan
    @include('catalog.modals.viewFiles')
</x-application.section-1>
@push('js')
<script>
$(document).ready(function(){
    function getProducts(){
        let table = $('#products-table').DataTable({
            ajax: {
                url: "{{ route('catalog.getProducts') }}",
                data: function (d) {
                    d.category = $('#category').val();
                    d.search = $('#search').val();
                },
                dataSrc: 'products'
            },
            order: [[0, 'desc']],
            columns: [
                { data: 'category' },
                { data: 'name' },
                { data: 'sat_code' },
                { data: 'sku' },
                { data: 'img' },
                { data: 'file' }
            ],
            columnDefs: [
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        let buttons = ``;
                        buttons += `
                            <button data-id="${row.product_id}" data-target="view-files-product" class="open-modal view-btn edit-btn text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm p-2" title="Edit">
                                <img width="18" src="{{ asset('images/ver.png') }}" alt="Edit"/>
                            </button>
                        `;
                        if(row.canUpdate){
                            buttons += `
                                <button data-id="${row.product_id}" data-target="edit-product" class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                    <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                            `;
                        }
                        if(row.canDelete){
                            buttons += `
                                <button data-id="${row.product_id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
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
                info: "Show _START_ to _END_ of _TOTAL_ products",
                lengthMenu: "Show _MENU_ products",
                infoEmpty: "There aren't products available",
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
        $('#category').on('change', function () {
            table.ajax.reload();
        });
        $('#search').on('keyup',function (){
            table.ajax.reload();
        });
        viewFiles();
        deleteProduct(table);
        editProduct(table);
    }

        function viewFiles() {
            $('#products-table tbody').on('click', '.view-btn', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: `/catalogs/${id}`,
                    success: function(response) {
                        $('#view-images').empty();
                        response.images.forEach(img => {
                            let image_name = img.path.indexOf('/') !== -1 ? img.path.substring(img.path.indexOf('/') + 1) : img.path;
                            const preview = `
                                <div class="flex flex-col items-center max-w-sm mx-auto bg-white rounded-md shadow-md transition hover:scale-95 duration-300 p-6 m-1">
                                    <img src="/image/${image_name}" alt="${image_name}" class="object-cover w-16" />
                                </div>
                            `;
                            $('#view-images').append(preview);
                        });

                        $('#view-files').empty();
                        response.files.forEach(file => {
                            const preview = `
                                <div class="file-btn flex flex-col items-center cursor-pointer" data-path="${file.path}">
                                    <div class="flex flex-col items-center max-w-sm mx-auto bg-white rounded-md shadow-md transition hover:scale-95 duration-300 p-4 m-1 w-full">
                                        <img src="images/pdf.png" class="object-cover w-12 mb-2"/>
                                        <p class="truncate w-full text-xs px-1 text-center font-bold" title="${file.path}">${file.path.substring(6)}</p>
                                        
                                        <span class="mt-2 text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full uppercase font-bold tracking-wider">
                                            ${file.sector ?? 'General'}
                                        </span>
                                    </div>
                                </div>
                            `;
                            $('#view-files').append(preview);
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while getting the product.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        }
        function editProduct(table) {
            $('#products-table tbody').on('click', '.edit-btn', function() {
                $('#name').prop('required', true);
                $('#sat_code').prop('required', true);
                $('#sku').prop('required', true);
                
                let id = $(this).data('id');
                
                $.ajax({
                    type: 'GET',
                    url: `/catalogs/${id}`,
                    success: function(response) {
                        $('#edit-product input#product_id').val(response.product['product_id']);
                        $('#edit-product input#name').val(response.product['name']);
                        $('#edit-product input#presentation').val(response.product['presentation']);
                        $('#edit-product input#unit').val(response.product['unit']);
                        $('#edit-product input#batch_code').val(response.product['batch_code']);
                        $('#edit-product input#sat_code').val(response.product['sat_code']);
                        $('#edit-product input#sku').val(response.product['sku']);
                        $('#edit-product input#stock_min').val(response.product['stock_min']);
                        $('#edit-product input#stock_max').val(response.product['stock_max']);
                        $('#edit-product select#category_id').val(response.product['category_id']);
                        $('#edit-product #preview-image-container-edit').empty();
                        response.images.forEach(img => {
                            let image_name = img.path.indexOf('/') !== -1 ? img.path.substring(img.path.indexOf('/') + 1) : img.path;
                            const preview = `
                                <div class="img-wrapper relative w-[100px] h-[100px] border rounded overflow-hidden">
                                    <img src="/image/${image_name}" alt="${image_name}" class="object-cover w-full h-full" />
                                    <button type="button" class="delete-img absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs remove-btn" data-id="${img.image_id}">x</button>
                                </div>
                            `;
                            $('#preview-image-container-edit').append(preview);
                        });

                        $('#preview-files-container-edit').empty();
                        response.files.forEach(file => {
                            const preview = `
                                <div class="file-wrapper flex items-center justify-between w-full p-2 border rounded bg-gray-50 mb-1">
                                    <div class="flex items-center gap-2">
                                        <img src="images/pdf.png" class="w-8 h-8"/>
                                        <div class="flex flex-col">
                                            <p class="truncate text-[10px] font-bold w-40" title="${file.path}">${file.path.substring(6)}</p>
                                            <span class="text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-sm uppercase font-bold">
                                                Sector: ${file.sector ?? 'Sin especificar'}
                                            </span>
                                        </div>
                                    </div>
                                    <button type="button" class="delete-file bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs remove-btn" data-id="${file.file_id}">x</button>
                                </div>
                            `;
                            $('#preview-files-container-edit').append(preview);
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while getting the product info.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        }
    function deleteProduct(table){
        $('#products-table tbody').on('click', '.delete-btn', function(){
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
                        url: `/catalogs/${id}`,
                        method: "DELETE",
                        data: { id: id },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            table.ajax.reload();
                            Swal.fire({
                                title: "Deleted!",
                                text: "The product has been deleted.",
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
    getProducts();
});
</script>
@endpush