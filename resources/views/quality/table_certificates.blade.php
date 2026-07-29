<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Supplier Certificates
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="certificates-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4">Supplier</th>
          <th class="px-6 py-4">Product</th>
          <th class="px-6 py-4">File</th>
          <th class="px-6 py-4">Fecha Emisión</th>
          <th class="px-6 py-4">Date</th>
          <th class="px-6 py-4">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs"></tbody>
    </table>
  </div>
</section>

@push('js')
<script>
$(document).ready(function () {
    const ORIGIN = window.location.origin;

    function ensureStorageUrl(pathOrUrl){
        if (!pathOrUrl) return null;
        if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;
        return `${ORIGIN}/storage/${pathOrUrl.replace(/^\/+/, '')}`;
    }

    // DataTable
    const table = $('#certificates-table').DataTable({
        ajax: {
            url: "{{ route('supplier_certificates.index') }}",
            dataSrc: function(json){ return json.certificates ?? []; }
        },
        columns: [
            { data: 'supplier_name' },
            { data: 'product_name' },
            { data: 'file_path', render: function(data){
                const url = ensureStorageUrl(data);
                return url ? `<a href="${url}" target="_blank" class="text-blue-600 underline">Ver PDF</a>` : '';
            }},
            { data: 'fecha_emision', render: function(data){
                return data ? data : 'N/A';
            }},
            { data: 'created_at', render: function(data){
                return new Date(data).toLocaleString();
            }},
            { data: null, render: function(data, type, row){
                return `
                <button data-id="${row.certificate_id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Eliminar">
                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Eliminar"/>
                </button>`;
            }, orderable:false, searchable:false }
        ],
        lengthChange: false,
        searching: false,
        pageLength: 5,
        language: {
            info: "Showing _START_ a _END_ de _TOTAL_ certificates",
            infoEmpty: "No certificates available",
            zeroRecords: "No records found",
            paginate: { first:"Firts", last:"Last", next:"Next", previous:"Previous" }
        }
    });

    // Delete
    $('#certificates-table tbody').on('click', '.delete-btn', function(){
        const id = $(this).data('id');

        Swal.fire({
            title: 'You are sure?',
            text: "You will not be able to reverse this action!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/supplier-certificates/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function () {
                        Swal.fire('Eliminated!', 'The certificate was deleted.', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Error', 'The certificate could not be deleted.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
