@extends('layouts.app')

@section('content')
<x-section-1>
    <x-wrapper-h-1>
        <p class="bg-purple-700 text-white p-1 rounded-md font-semibold tracking-[2px] text-sm">Usuarios del Portal</p>
        
        @can('customers.create')
        <x-button data-target="create-portal-user" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Registrar Cliente Portal
        </x-button>
        @include('portal.modals.create')
        @include('portal.modals.edit')
        @include('portal.modals.select-sale') 
        @include('portal.modals.upload')     
        @endcan
    </x-wrapper-h-1>
</x-section-1>

<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        
        <div class="flex justify-end items-center gap-2 w-full my-2">
            <button id="toggle-filters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md shadow-sm text-gray-700 font-medium flex items-center gap-2" title="Toggle Filters">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>
        
        <div id="advanced-filters" class="hidden w-full bg-gray-50 p-4 rounded-md shadow-sm mb-2 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name / Portal Contact</label>
                    <x-input class="filter-input w-full mt-1" id="filter-name" placeholder="Filter by Name or Company"></x-input>
                </div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button id="clear-filters" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md text-sm">Clear</button>
            </div>
        </div>

        <table id="portal-users-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th scope="col" class="px-6 py-4">CLIENT CODE</th>
                    <th scope="col" class="px-6 py-4">SYSTEM NAME</th>
                    <th scope="col" class="px-6 py-4">PORTAL CONTACT</th>
                    <th scope="col" class="px-6 py-4">PORTAL COMPANY</th>
                    <th scope="col" class="px-6 py-4">EMAIL (LOGIN)</th>
                    <th scope="col" class="px-6 py-4">STATUS</th>
                    <th scope="col" class="px-6 py-4 text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs">
            </tbody>
        </table>
    </div>
</section>

@push('js')
<script>
$(function(){
    let portalTable;
    let editUserId = null;
    window.activePortalUserId = null;

    function getPortalUsers(){
        portalTable = $('#portal-users-table').DataTable({
            ajax: {
                url: "{{ route('admin.portal-users.getPortalUsers') }}",
                data: function (d) {
                    d.name = $('#filter-name').val();
                },
                dataSrc: 'users'
            },
            columns: [
                { data: 'customer_code' },
                { data: 'customer_name' },
                { data: 'nombre_contacto' },
                { data: 'empresa' },
                { data: 'email' },
                { 
                    data: 'is_active',
                    render: function(data) {
                        if(data == 1) {
                            return `<span class="bg-green-100 text-green-800 px-2 py-1 rounded-md font-bold text-xs">Active</span>`;
                        }
                        return `<span class="bg-red-100 text-red-800 px-2 py-1 rounded-md font-bold text-xs">Inactive</span>`;
                    }
                },
                { data: null }
            ],
            columnDefs: [
                {
                    targets: [0, 1, 2, 3, 4, 5],
                    className: 'text-left px-6 py-4'
                },
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    className: 'text-right px-6 py-4',
                    render: function(data, type, row) {
                        return `
                            <div class="flex justify-end gap-1">
                                <button data-id="${row.portal_id}" data-target="select-sale-modal" class="open-modal view-sales-btn text-sm text-white bg-amber-500 hover:bg-amber-600 rounded-sm p-2" title="Subir Factura a Venta">
                                    <i class="fas fa-file-upload"></i>
                                </button>

                                <button data-id="${row.portal_id}" data-target="edit-portal-user" class="open-modal edit-portal-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                    <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                                
                                <button data-id="${row.portal_id}" class="delete-portal-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
                                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            lengthChange: false,
            searching: false,
            pageLength: 15,
            responsive: true,
            autoWidth: false,
            language: {
                info: "Show _START_ to _END_ of _TOTAL_ portal users",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
            }
        });

        $('.filter-input').on('keyup change', function() {
            portalTable.ajax.reload();
        });

        $('#toggle-filters').on('click', function(e) {
            e.preventDefault();
            $('#advanced-filters').toggleClass('hidden');
        });

        $('#clear-filters').on('click', function(e) {
            e.preventDefault();
            $('#filter-name').val('');
            portalTable.ajax.reload();
        });
    }
    window.loadClientSales = function(portalUserId) {
        window.activePortalUserId = portalUserId;
        $('#list-client-sales').html('<tr><td colspan="3" class="p-4 text-center text-gray-500">Cargando historial de compras...</td></tr>');
        
        $.ajax({
            type: 'GET',
            url: `/admin/portal-users/${portalUserId}/sales`,
            success: function(response) {
                let html = '';
                if(!response.sales || response.sales.length === 0) {
                    html = '<tr><td colspan="3" class="p-4 text-center text-gray-500">Este cliente no tiene ventas registradas en el sistema.</td></tr>';
                } else {
                    response.sales.forEach(sale => {
                        let badgePdf = sale.has_pdf ? `<span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-xs font-bold mr-1"><i class="fas fa-file-pdf"></i> PDF</span>` : '';
                        let badgeXml = sale.has_xml ? `<span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded text-xs font-bold mr-1"><i class="fas fa-file-code"></i> XML</span>` : '';
                        let badgeCoa = sale.has_coa ? `<span class="bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded text-xs font-bold mr-1"><i class="fas fa-file-pdf"></i> COA</span>` : '';
                        let statusText = (sale.has_pdf || sale.has_xml || sale.has_coa) ? (badgePdf + badgeXml + badgeCoa) : '<span class="text-gray-400 italic text-xs">Sin archivos</span>';

                        let buttonText = (sale.has_pdf || sale.has_xml || sale.has_coa) ? 'Editar / Subir' : 'Elegir';
                        let buttonClass = (sale.has_pdf || sale.has_xml || sale.has_coa) ? 'bg-amber-500 hover:bg-amber-600' : 'bg-green-500 hover:bg-green-600';

                        let hasPdf = sale.has_pdf ? 1 : 0;
                        let pdfPath = sale.pdf_path ?? '';
                        let pdfName = sale.pdf_name ?? '';
                        let hasXml = sale.has_xml ? 1 : 0;
                        let xmlPath = sale.xml_path ?? '';
                        let xmlName = sale.xml_name ?? '';
                        let hasCoa = sale.has_coa ? 1 : 0;
                        let coaPath = sale.coa_path ?? '';
                        let coaName = sale.coa_name ?? '';

                        html += `
                            <tr class="border-b hover:bg-gray-100">
                                <td class="p-2 font-medium text-blue-600">#${sale.folio ?? sale.sale_id}</td>
                                <td class="p-2">${statusText}</td>
                                <td class="p-2 text-right">
                                    <button data-saleid="${sale.sale_id}" 
                                            data-folio="${sale.folio ?? sale.sale_id}" 
                                            data-haspdf="${hasPdf}"
                                            data-pdfpath="${pdfPath}"
                                            data-pdfname="${pdfName}"
                                            data-hasxml="${hasXml}"
                                            data-xmlpath="${xmlPath}"
                                            data-xmlname="${xmlName}"
                                            data-hascoa="${hasCoa}"
                                            data-coapath="${coaPath}"
                                            data-coaname="${coaName}"
                                            class="select-this-sale text-white px-2 py-1 rounded text-xs ${buttonClass} transition">
                                        ${buttonText}
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#list-client-sales').html(html);
            },
            error: function() {
                $('#list-client-sales').html('<tr><td colspan="3" class="p-4 text-center text-red-500">Error al cargar las ventas.</td></tr>');
            }
        });
    }

    $('#portal-users-table tbody').on('click', '.view-sales-btn', function(){
        let portalUserId = $(this).data('id');
        window.loadClientSales(portalUserId);
    });

    $(document).on('click', '.select-this-sale', function(){
        let saleId = $(this).data('saleid');
        let folio = $(this).data('folio');
        let hasPdf = $(this).data('haspdf') == 1;
        let pdfPath = $(this).data('pdfpath');
        let pdfName = $(this).data('pdfname');
        let hasXml = $(this).data('hasxml') == 1;
        let xmlPath = $(this).data('xmlpath');
        let xmlName = $(this).data('xmlname');
        let hasCoa = $(this).data('hascoa') == 1;
        let coaPath = $(this).data('coapath');
        let coaName = $(this).data('coaname');

        $('#select-sale-modal').find('.close-modal').click();
        $('#upload_sale_id').val(saleId);
        $('#txt-folio-venta').text('#' + folio);
        $('#upload-docs-form').attr('action', `/admin/sales/${saleId}/upload-docs`);
        $('#pdf_file').val('');
        $('#pdf-file-name').text('Ningún archivo seleccionado');
        $('#xml_file').val('');
        $('#xml-file-name').text('Ningún archivo seleccionado');
        $('#coa_file').val('');
        $('#coa-file-name').text('Ningún archivo seleccionado');

        if (hasPdf) {
            $('#existing-pdf-name').text(pdfName);
            $('#btn-view-pdf').attr('href', pdfPath);
            $('#existing-pdf-container').removeClass('hidden');
            $('#pdf-input-container').find('label[for="pdf_file"] span').text('Reemplazar PDF');
        } else {
            $('#existing-pdf-container').addClass('hidden');
            $('#pdf-input-container').find('label[for="pdf_file"] span').text('Seleccionar PDF');
        }

        if (hasXml) {
            $('#existing-xml-name').text(xmlName);
            $('#btn-view-xml').attr('href', xmlPath);
            $('#existing-xml-container').removeClass('hidden');
            $('#xml-input-container').find('label[for="xml_file"] span').text('Reemplazar XML');
        } else {
            $('#existing-xml-container').addClass('hidden');
            $('#xml-input-container').find('label[for="xml_file"] span').text('Seleccionar XML');
        }

        if (hasCoa) {
            $('#existing-coa-name').text(coaName);
            $('#btn-view-coa').attr('href', coaPath);
            $('#existing-coa-container').removeClass('hidden');
            $('#coa-input-container').find('label[for="coa_file"] span').text('Reemplazar COA');
        } else {
            $('#existing-coa-container').addClass('hidden');
            $('#coa-input-container').find('label[for="coa_file"] span').text('Seleccionar COA');
        }

        $('#upload-docs-modal').removeClass('hidden'); 
    });

    // EVENTO DE EDITAR
    $('#portal-users-table tbody').on('click', '.edit-portal-btn', function(){
        editUserId = $(this).data('id'); 

        $.ajax({
            type: 'GET',
            url: `/admin/portal-users/${editUserId}/edit`,
            success: function(response) {
                $('#edit-customer_id').val(response.user.customer_id);
                $('#edit-nombre_contacto').val(response.user.nombre_contacto);
                $('#edit-empresa').val(response.user.empresa);
                $('#edit-email').val(response.user.email);
                
                if(response.user.is_active == 1) {
                    $('#edit-is_active').prop('checked', true);
                } else {
                    $('#edit-is_active').prop('checked', false);
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron recuperar los datos del cliente.' });
            }
        });
    });

    $('#edit-portal-user-form').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let url = `/admin/portal-users/${editUserId}`; 

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    form.find('.close-modal').click();
                    form.find('input[type="password"]').val('');
                    portalTable.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while updating.';
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                } else if(xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    });

    // EVENTO DE ELIMINAR
    $('#portal-users-table tbody').on('click', '.delete-portal-btn', function(){
        let id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this! The client will lose access to the portal.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/portal-users/${id}`,
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        portalTable.ajax.reload();
                        Swal.fire({
                            title: "Deleted!",
                            text: "The portal access has been deleted.",
                            icon: "success"
                        });
                    },
                    error: function(xhr){
                        let message = 'An unexpected error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            message = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            }
        });
    });

    getPortalUsers();
});
</script>
@endpush
@endsection