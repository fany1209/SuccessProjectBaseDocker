
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        
        <div class="mt-6 text-center">
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
                Muestras de Salida
            </h1>
            <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
        </div>

        <div class="w-full flex flex-col md:flex-row justify-between items-center mt-8 mb-4 gap-4 px-2">
            <div class="w-full md:flex-1 md:max-w-sm">
                <x-input class="w-full p-2" id="search-muestras" placeholder="Buscar reporte..."></x-input>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <select id="filtro-motivo" class="border-gray-300 focus:border-[#198754] focus:ring-[#198754] rounded-md shadow-sm text-sm p-2">
                    <option value="">TODOS LOS MOTIVOS</option>
                    <option value="cliente">CLIENTE</option>
                    <option value="analisis">ANÁLISIS</option>
                    <option value="desarrollo">DESARROLLO</option>
                    <option value="caducado">CADUCADO</option>
                    <option value="exposicion">EXPOSICIÓN</option>
                    <option value="otro">OTRO</option>
                </select>
            </div>
        </div>

        <table id="muestras-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
            <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                <tr>
                    <th class="px-6 py-4">FOLIO</th>
                    <th class="px-6 py-4">FECHA SALIDA</th>
                    <th class="px-6 py-4">PRODUCTO / SKU</th>
                    <th class="px-6 py-4">LOTE</th>
                    <th class="px-6 py-4">CANTIDAD</th>
                    <th class="px-6 py-4 text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words"></tbody>
        </table>
    </div>
</section>

@push('js')
<script>
(function(){
    function dataSrcMuestras(json){
        return json.muestras || [];
    }

    function renderActions(data, type, row){
        const id = row.id ?? '';
        return `
            <div class="flex gap-1 justify-end">
                @can('laboratory.delete')
                <button data-id="${id}"
                        class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
                        title="Delete">
                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                </button>
                @endcan

                <button data-id="${id}"
                        class="pdf-btn bg-[#198754] hover:bg-[#157347] text-white p-2 rounded-sm"
                        title="Generate PDF">
                    <img width="18" src="{{ asset('images/pdf.png') }}" alt="PDF"/>
                </button>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#muestras-table').DataTable({
            ajax: {
                url: "{{ route('laboratory.getMuestras') }}",
                data: function(d) {
                    d.motivo = $('#filtro-motivo').val();
                    d.search = $('#search-muestras').val();
                },
                dataSrc: dataSrcMuestras
            },
            columns: [
                { data: 'folio_muestra' },
                { 
                    data: 'fecha_salida',
                    render: (d) => d ? d.split('-').reverse().join('/') : ''
                },
                { 
                    data: null,
                    render: function(data) {
                        let prod = data.nombre_producto_original || data.nombre_comercial || 'N/A';
                        return `<b>${prod}</b><br><small class="text-blue-600">SKU: ${data.sku || ''}</small>`;
                    }
                },
                { data: 'lote' },
                { 
                    data: null,
                    render: (data) => `<b>${data.cantidad || ''}</b> ${data.um || ''}`
                },
                { data: null, render: renderActions, orderable:false, searchable:false, className:'text-right' }
            ],
            order: [[1,'desc']],
            dom: 'rt', 
            lengthChange: false,
            searching: false,
            pageLength: 50,
            serverSide: false,
            language: {
                zeroRecords: "Sin resultados",
                infoEmpty: "Sin registros"
            }
        });

        $('#filtro-motivo, #search-muestras').on('change keyup', () => table.ajax.reload());

        $(document).on('click', '.pdf-btn', function(){
            const id = $(this).data('id');
            
            let url = "{{ route('laboratory.reimprimirPdf', ':id') }}";
            url = url.replace(':id', id);
            
            console.log("Abriendo PDF en: " + url);
            window.open(url, '_blank');
        });

        $(document).on('click', '.delete-btn', async function(){
            const id = this.dataset.id;
            const $btn = $(this);

            const res = await Swal.fire({
                title: 'Delete record?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            });

            if (res.isConfirmed) {
                let url = "{{ route('laboratory.destroyMuestra', ':id') }}";
                url = url.replace(':id', id);

                try {
                    const resp = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (resp.ok) {
                        $('#muestras-table').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            title: 'Deleted', 
                            text: 'The record has been successfully removed.', 
                            icon: 'success', 
                            timer: 1500, 
                            showConfirmButton: false
                        });
                    } else {
                        const errorData = await resp.json();
                        Swal.fire('Error', errorData.error || 'Could not delete the record.', 'error');
                    }
                } catch (err) {
                    console.error('Error:', err);
                    Swal.fire('Error', 'Communication failure occurred.', 'error');
                }
            }
        });
    });
})();
</script>
@endpush
