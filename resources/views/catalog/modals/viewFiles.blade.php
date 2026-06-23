{{--
Catalog
View Files Product
Fecha de creación: 11-09-2025
Creado por: Jacob
Actualizado por: Fany
Fecha de actualización: 11-03-2026
--}}
<x-modal id="view-files-product">
    <div class="flex flex-col items-start w-full gap-2 mb-4">
        <label class="mb-1 block font-bold text-lg text-gray-700 border-b w-full">Images</label>
        <div id="view-images" class="flex flex-wrap gap-4 mt-2"></div>
    </div>

    <div class="flex flex-col items-start w-full gap-2">
        <label class="mb-1 block font-bold text-lg text-gray-700 border-b w-full">Files (PDF)</label>
        <div id="view-files" class="flex flex-col gap-2 mt-2 w-full"></div>
    </div>

    <div class="flex justify-end w-full mt-4">
        <x-button-1 class="close-modal" colorBtn="red">Close</x-button-1>
    </div>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#view-files-product');

    window.fillViewModal = function(response) {
        $('#view-images').empty();
        response.images.forEach(img => {
            let image_name = img.path.split('/').pop();
            $('#view-images').append(`
                <div class="border rounded p-1">
                    <img src="/image/${image_name}" class="w-20 h-20 object-cover rounded">
                </div>
            `);
        });

        $('#view-files').empty();
        response.files.forEach(file => {
            const fileName = file.path.substring(6);
            const html = `
                <div class="flex items-center justify-between p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition shadow-sm">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/pdf.png') }}" class="w-8 h-8"/>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800 truncate w-48" title="${fileName}">${fileName}</span>
                            <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full w-fit font-bold uppercase">
                                ${file.sector ?? 'General'}
                            </span>
                        </div>
                    </div>
                    <button type="button" 
                            class="file-btn bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-md flex items-center gap-1" 
                            data-path="${file.path}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ver PDF
                    </button>
                </div>
            `;
            $('#view-files').append(html);
        });
    }

    father.on('click', '.file-btn', function(){
        let path = $(this).data('path');
        path = path.substring(6);
        var ruta = `/open-file/${path}`;
        window.open(ruta, '_blank');
    });
});
</script>    
@endpush