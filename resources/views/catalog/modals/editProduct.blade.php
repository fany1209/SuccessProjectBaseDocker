{{--
Products
Actualizar Product
Fecha de creación: 08-09-2025
Creado por: Jacob
Actualizado por: Fany
Fecha de actualización: 11-03-2026
--}}
<x-modal id="edit-product">
    <form class="flex flex-col items-center w-full gap-2" id="edit-product-form" enctype="multipart/form-data">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Edit Product</x-tittle-form>
            <x-input-1 type="hidden" name="product_id" id="product_id"></x-input-1>
            <div class="flex justify-center items-center gap-2">
                <button type="button" id="edit-images-btn" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md tracking-widest hover:bg-green-400 active:bg-green-600 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <img width="24" src="{{ asset('images/addImage.png') }}">
                </button>
                <button type="button" id="edit-files-btn" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md tracking-widest hover:bg-green-400 active:bg-green-600 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <img width="24" src="{{ asset('images/addFile.png') }}">
                </button>
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Product name</x-label>
                <x-input-1 required name="name" id="name" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="category_id">Category</x-label>
                <x-select-1 required name="category_id" id="category_id">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="sat_code">Sat code</x-label>
                <x-input-1 required name="sat_code" id="sat_code" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="sku">SKU</x-label>
                <x-input-1 required name="sku" id="sku" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="unit">UoM</x-label>
                <x-input-1 name="unit" id="unit" maxlength="10"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="batch_code">Batch Code</x-label>
                <x-input-1 name="batch_code" id="batch_code" maxlength="10"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="presentation">Presentation</x-label>
                <x-input-1 name="presentation" id="presentation" maxlength="100"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="stock_min">Stock min</x-label>
                <x-input-1 type="number" name="stock_min" id="stock_min" min="0"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="stock_max">Stock max</x-label>
                <x-input-1 type="number" name="stock_max" id="stock_max" min="0"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="border-t pt-2 mt-2">
            <x-wrapper-form-2>
                <x-label>Current Images</x-label>
                <div id="preview-image-container-edit" class="flex flex-wrap gap-2 mt-2"></div>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label>Current Files</x-label>
                <div id="preview-files-container-edit" class="flex flex-col gap-2 mt-2"></div>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div id="edit-images" class="flex flex-col w-full gap-2 hidden border-2 border-dashed p-2 rounded-md">
            <x-label>Upload New Images</x-label>
            <input type="file" name="imgs[]" id="image-input-edit" class="hidden" multiple accept="image/*" />
            <label for="image-input-edit" class="bg-gray-100 p-4 text-center cursor-pointer rounded-md hover:bg-gray-200 text-sm">Click to add images</label>
            <div id="preview-container-images-update" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div id="edit-files" class="flex flex-col w-full gap-2 hidden border-2 border-dashed p-2 rounded-md">
            <x-label>Upload New Files (PDF)</x-label>
            <input type="file" name="files[]" id="file-input-edit" class="hidden" multiple accept="application/pdf" />
            <label for="file-input-edit" class="bg-gray-100 p-4 text-center cursor-pointer rounded-md hover:bg-gray-200 text-sm">Click to add PDF files</label>
            <div id="preview-container-files-update" class="flex flex-col gap-2 mt-2 w-full"></div>
        </div>

        <div class="flex justify-end items-center gap-2 w-full mt-4">            
            <x-button type="submit" id="update-product" class="bg-sky-500 hover:bg-sky-600">Update Product</x-button>
        </div>
    </form>
</x-modal>

@push('js')
<script>
$(document).ready(function(){
    let newImagesArray = [];
    let newFilesArray = [];

    function deleteFile(){
        $(document).on('click','.delete-file',function(){
            var fileId = $(this).data('id');
            var wrapper = $(this).closest('.file-wrapper');
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('catalog.deleteFile') }}",
                        method: "DELETE",
                        data: { id: fileId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function () {
                            wrapper.remove();
                            Swal.fire("Deleted!", "File removed.", "success");
                        }
                    });
                }
            });
        });
    }

    function deleteImg(){
        $(document).on('click','.delete-img',function(){
            var wrapper = $(this).closest('.img-wrapper');
            var imageId = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('catalog.deleteImage') }}",
                        method: "DELETE",
                        data: { id: imageId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function () {
                            wrapper.remove();
                            Swal.fire("Deleted!", "Image removed.", "success");
                        }
                    });
                }                
            });
        });
    }

    function viewImages(){
        $('#image-input-edit').on('change', function (e) {
            $('#preview-container-images-update').empty();
            newImagesArray = Array.from(e.target.files);
            newImagesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const preview = `
                        <div class="relative w-[80px] h-[80px] border rounded overflow-hidden">
                            <img src="${event.target.result}" class="object-cover w-full h-full" />
                            <button type="button" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] remove-new-img" data-index="${index}">x</button>
                        </div>
                    `;
                    $('#preview-container-images-update').append(preview);
                };
                reader.readAsDataURL(file);
            });
        });

        $(document).on('click', '.remove-new-img', function () {
            const index = $(this).data('index');
            newImagesArray.splice(index, 1);
            const dataTransfer = new DataTransfer();
            newImagesArray.forEach(file => dataTransfer.items.add(file));
            $('#image-input-edit')[0].files = dataTransfer.files;
            $('#image-input-edit').trigger('change');
        });
    }

    function viewFiles(){
        $('#file-input-edit').on('change', function (e) {
            $('#preview-container-files-update').empty();
            newFilesArray = Array.from(e.target.files);
            newFilesArray.forEach((file, index) => {
                const preview = `
                    <div class="flex items-center gap-2 p-2 border rounded bg-gray-50 relative">
                        <img src="{{ asset('images/pdf.png') }}" class="w-6 h-6"/>
                        <p class="truncate text-[10px] w-1/4" title="${file.name}">${file.name}</p>
                        <input type="text" name="file_sectors[]" placeholder="Sector (ej. Agro)" class="flex-1 text-xs border-gray-300 rounded-md p-1" required>
                        <button type="button" class="bg-red-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] remove-new-file" data-index="${index}">x</button>
                    </div>
                `;
                $('#preview-container-files-update').append(preview);
            });
        });

        $(document).on('click', '.remove-new-file', function () {
            const index = $(this).data('index');
            newFilesArray.splice(index, 1);
            const dataTransfer = new DataTransfer();
            newFilesArray.forEach(file => dataTransfer.items.add(file));
            $('#file-input-edit')[0].files = dataTransfer.files;
            $('#file-input-edit').trigger('change');
        });
    }

    function btnsToggle(){
        $('#edit-images-btn').on('click', function() {
            $(this).toggleClass('bg-green-400');
            $('#edit-images').toggleClass('hidden');
        });
        $('#edit-files-btn').on('click', function() {
            $(this).toggleClass('bg-green-400');
            $('#edit-files').toggleClass('hidden');
        });
    }

    function updateProduct(){
        $("#edit-product-form").submit(function(event) {
            event.preventDefault();
            var data = new FormData(this);
            var id = $('#product_id').val();
            data.append('_method', 'PUT');

            $.ajax({
                type:'POST',
                url:`/catalogs/${id}`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire("Success", "Product updated", "success").then(() => location.reload());
                },
                error: function(xhr){
                    Swal.fire("Error", xhr.responseJSON?.error || "Error", "error");
                }
            });
        });
    }

    viewFiles();
    viewImages();
    btnsToggle();
    deleteFile();
    deleteImg();
    updateProduct();
});
</script>
@endpush