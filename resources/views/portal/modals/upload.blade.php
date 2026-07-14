<x-modal id="upload-docs-modal">
    <div class="px-6 py-5 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-green-600"></div>

        <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-5 mt-2">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl flex-shrink-0">
                    <i class="fas fa-cloud-upload-alt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Documentos de Venta <span id="txt-folio-venta" class="text-emerald-600"></span></h3>
                    <p class="text-sm text-gray-500 mt-0.5">Sube o administra los archivos PDF, XML y COA de esta transacción.</p>
                </div>
            </div>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="upload-docs-form" method="POST" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="sale_id" id="upload_sale_id">

            <div class="space-y-5">
                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-red-500"></i> Documento Factura (PDF)
                    </label>
                    
                    <div id="existing-pdf-container" class="hidden mb-3 p-3 bg-red-50/50 border border-red-100 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="p-2 bg-red-100 rounded-lg text-red-600">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <span id="existing-pdf-name" class="text-sm text-gray-700 font-medium truncate max-w-[12rem] md:max-w-[16rem]"></span>
                        </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a id="btn-view-pdf" href="#" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 text-xs px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium">
                                <i class="fas fa-eye text-blue-500"></i> Ver
                            </a>
                            <button type="button" id="btn-delete-pdf" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium">
                                <i class="fas fa-trash-alt"></i> Borrar
                            </button>
                        </div>
                    </div>
                    
                    <div id="pdf-input-container" class="mt-1 flex flex-col items-center justify-center px-6 py-6 border-2 border-dashed border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-emerald-300 transition-all group">
                        <i class="fas fa-cloud-upload-alt text-gray-300 group-hover:text-emerald-400 text-3xl mb-3 transition-colors"></i>
                        <div class="flex text-sm text-gray-600 justify-center mb-1">
                            <label for="pdf_file" class="relative cursor-pointer bg-emerald-50 px-3 py-1 rounded-md font-semibold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-100 transition-colors">
                                <span>Seleccionar PDF</span>
                                <input id="pdf_file" name="pdf_file" type="file" accept=".pdf" class="sr-only">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 font-medium" id="pdf-file-name">Ningún archivo seleccionado</p>
                    </div>
                </div>

                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-file-code text-blue-500"></i> Archivo Fiscal (XML)
                    </label>
                    
                    <div id="existing-xml-container" class="hidden mb-3 p-3 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                <i class="fas fa-file-code"></i>
                            </div>
                            <span id="existing-xml-name" class="text-sm text-gray-700 font-medium truncate max-w-[12rem] md:max-w-[16rem]"></span>
                        </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a id="btn-view-xml" href="#" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 text-xs px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium">
                                <i class="fas fa-eye text-blue-500"></i> Ver
                            </a>
                            <button type="button" id="btn-delete-xml" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium">
                                <i class="fas fa-trash-alt"></i> Borrar
                            </button>
                        </div>
                    </div>
                    
                    <div id="xml-input-container" class="mt-1 flex flex-col items-center justify-center px-6 py-6 border-2 border-dashed border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-blue-300 transition-all group">
                        <i class="fas fa-cloud-upload-alt text-gray-300 group-hover:text-blue-400 text-3xl mb-3 transition-colors"></i>
                        <div class="flex text-sm text-gray-600 justify-center mb-1">
                            <label for="xml_file" class="relative cursor-pointer bg-blue-50 px-3 py-1 rounded-md font-semibold text-blue-600 hover:text-blue-700 hover:bg-blue-100 transition-colors">
                                <span>Seleccionar XML</span>
                                <input id="xml_file" name="xml_file" type="file" accept=".xml" class="sr-only">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 font-medium" id="xml-file-name">Ningún archivo seleccionado</p>
                    </div>
                </div>

                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-certificate text-teal-500"></i> Certificado de Calidad (COA)
                    </label>
                    
                    <div id="existing-coa-container" class="hidden mb-3 p-3 bg-teal-50/50 border border-teal-100 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="p-2 bg-teal-100 rounded-lg text-teal-600">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <span id="existing-coa-name" class="text-sm text-gray-700 font-medium truncate max-w-[12rem] md:max-w-[16rem]"></span>
                        </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a id="btn-view-coa" href="#" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 text-xs px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium">
                                <i class="fas fa-eye text-blue-500"></i> Ver
                            </a>
                            <button type="button" id="btn-delete-coa" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium">
                                <i class="fas fa-trash-alt"></i> Borrar
                            </button>
                        </div>
                    </div>
                    
                    <div id="coa-input-container" class="mt-1 flex flex-col items-center justify-center px-6 py-6 border-2 border-dashed border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-teal-300 transition-all group">
                        <i class="fas fa-cloud-upload-alt text-gray-300 group-hover:text-teal-400 text-3xl mb-3 transition-colors"></i>
                        <div class="flex text-sm text-gray-600 justify-center mb-1">
                            <label for="coa_file" class="relative cursor-pointer bg-teal-50 px-3 py-1 rounded-md font-semibold text-teal-600 hover:text-teal-700 hover:bg-teal-100 transition-colors">
                                <span>Seleccionar COA</span>
                                <input id="coa_file" name="coa_file" type="file" accept=".pdf" class="sr-only">
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 font-medium" id="coa-file-name">Ningún archivo seleccionado</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-100">
                <button type="button" class="close-modal bg-white text-gray-600 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-gray-800 transition-all font-semibold shadow-sm">
                    Cancelar
                </button>
                <button type="submit" class="bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-2.5 rounded-xl hover:from-emerald-500 hover:to-green-500 transition-all font-semibold shadow-md shadow-emerald-500/30 flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Subir y Guardar
                </button>
            </div>
        </form>
    </div>
</x-modal>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('pdf_file').addEventListener('change', function(e) {
        let name = e.target.files[0] ? e.target.files[0].name : "Ningún archivo seleccionado";
        document.getElementById('pdf-file-name').textContent = name;
    });

    document.getElementById('xml_file').addEventListener('change', function(e) {
        let name = e.target.files[0] ? e.target.files[0].name : "Ningún archivo seleccionado";
        document.getElementById('xml-file-name').textContent = name;
    });

    document.getElementById('coa_file').addEventListener('change', function(e) {
        let name = e.target.files[0] ? e.target.files[0].name : "Ningún archivo seleccionado";
        document.getElementById('coa-file-name').textContent = name;
    });

    function deleteDocument(type) {
        let saleId = document.getElementById('upload_sale_id').value;
        let typeName = type.toUpperCase();

        Swal.fire({
            title: `¿Eliminar archivo ${typeName}?`,
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, borrarlo",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/sales/${saleId}/delete-doc/${type}`,
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text: response.message,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $(`#existing-${type}-container`).addClass('hidden');
                            
                            $(`#${type}-input-container`).find(`label[for="${type}_file"] span`).text(`Seleccionar ${typeName}`);

                            if (window.loadClientSales && window.activePortalUserId) {
                                window.loadClientSales(window.activePortalUserId);
                            }
                        }
                    },
                    error: function(xhr){
                        let message = 'No se pudo eliminar el archivo.';
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
    }

    document.getElementById('btn-delete-pdf').addEventListener('click', function() {
        deleteDocument('pdf');
    });

    document.getElementById('btn-delete-xml').addEventListener('click', function() {
        deleteDocument('xml');
    });

    document.getElementById('btn-delete-coa').addEventListener('click', function() {
        deleteDocument('coa');
    });

    document.getElementById('upload-docs-form').addEventListener('submit', function(e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);

        $.ajax({
            type: 'POST',
            url: form.getAttribute('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    form.querySelector('.close-modal').click();
                    
                    form.reset();
                    document.getElementById('pdf-file-name').textContent = "Ningún archivo seleccionado";
                    document.getElementById('xml-file-name').textContent = "Ningún archivo seleccionado";
                    document.getElementById('coa-file-name').textContent = "Ningún archivo seleccionado";

                    if (window.loadClientSales && window.activePortalUserId) {
                        window.loadClientSales(window.activePortalUserId);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Subidos!',
                        text: response.message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Ocurrió un error al subir los archivos.';
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
});
</script>