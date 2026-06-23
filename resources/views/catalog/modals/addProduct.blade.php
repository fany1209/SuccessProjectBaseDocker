{{--
Catalog
Agregar Product
Fecha de creación: 10-09-2025
Creado por: Jacob
Actualizado por: Fany
Fecha de actualización: 11-03-2026
--}}
<x-modal id="add-product">
    <form class="flex flex-col items-center w-full gap-2" id="add-product-form" enctype="multipart/form-data">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Add Product</x-tittle-form>
            <div class="flex justify-center items-center gap-2">
                <button type="button" id="add-images-btn" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md tracking-widest hover:bg-green-400 active:bg-green-600 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <img width="24" src="{{ asset('images/addImage.png') }}">
                </button>
                <button type="button" id="add-files-btn" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md tracking-widest hover:bg-green-400 active:bg-green-600 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <img width="24" src="{{ asset('images/addFile.png') }}">
                </button>
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Product name</x-label>
                <x-input-1 required name="name" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="category_id">Category</x-label>
                <x-select-1 required name="category_id">
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
                <x-input-1 required name="sat_code" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="sku">SKU</x-label>
                <x-input-1 required name="sku" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="unit">UoM</x-label>
                <x-input-1 name="unit" maxlength="10"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="batch_code">Batch Code</x-label>
                <x-input-1 name="batch_code" maxlength="10"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="presentation">Presentation</x-label>
                <x-input-1 name="presentation" maxlength="100"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="stock_min">Stock min</x-label>
                <x-input-1 type="number" name="stock_min" min="0"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="stock_max">Stock max</x-label>
                <x-input-1 type="number" name="stock_max" min="0"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div id="add-images" class="flex flex-col w-full gap-2 hidden border-t pt-2">
            <x-label>Images</x-label>
            <x-images-1 id="image-input" name="imgs[]" label="Sube tus imagenes" info="Solo JPG, JPEG, PNG" class="mt-2"/>
            <div id="preview-container-imgs" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div id="add-files" class="flex flex-col w-full gap-2 hidden border-t pt-2">
            <x-label>Files (PDF)</x-label>
            <x-files-1 id="file-input" name="files[]" label="Sube tus documentos" info="Solo PDF permitidos" class="mt-2"/>
            <div id="preview-container-files" class="flex flex-col gap-2 mt-2 w-full">
                </div>
        </div>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-product" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#add-product');
    let imagesArray = [];
    let filesArray = [];

    function reactiveAddProductForm(){
        father.on('click','#add-images-btn', function() {
            father.find('#add-images-btn').toggleClass('bg-green-400');
            father.find('#add-images').toggleClass('hidden');
        });

        father.on('click','#add-files-btn', function() {
            father.find('#add-files-btn').toggleClass('bg-green-400');
            father.find('#add-files').toggleClass('hidden');
        });

        father.on('change','#image-input', function (e) {
            imagesArray = Array.from(e.target.files);
            const container = father.find('#preview-container-imgs');
            container.empty();

            imagesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const preview = `
                        <div class="relative w-[100px] h-[100px] border rounded overflow-hidden">
                            <img src="${event.target.result}" class="object-cover w-full h-full"/>
                            <button type="button" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs remove-img" data-index="${index}">x</button>
                        </div>
                    `;
                    container.append(preview);
                };
                reader.readAsDataURL(file);
            });
        });

        father.on('click', '.remove-img', function () {
            const index = $(this).data('index');
            imagesArray.splice(index, 1);
            const dataTransfer = new DataTransfer();
            imagesArray.forEach(file => dataTransfer.items.add(file));
            father.find('#image-input')[0].files = dataTransfer.files;
            father.find('#image-input').trigger('change');
        });

        father.on('change','#file-input', function (e) {
            const container = father.find('#preview-container-files');
            container.empty();
            filesArray = Array.from(e.target.files);

            filesArray.forEach((file, index) => {
                const preview = `
                    <div class="flex items-center gap-4 p-2 border rounded-md bg-gray-50 relative" data-index="${index}">
                        <div class="flex items-center gap-2 w-1/3">
                            <img src="{{ asset('images/pdf.png') }}" class="w-8 h-8"/>
                            <p class="truncate text-xs font-medium w-full" title="${file.name}">${file.name}</p>
                        </div>
                        <div class="flex-1">
                            <input type="text" 
                                   name="file_sectors[]" 
                                   placeholder="Nombre del sector (ej. Agro)" 
                                   class="w-full text-xs border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                   required>
                        </div>
                        <button type="button" class="bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs remove-file" data-index="${index}">x</button>
                    </div>
                `;
                container.append(preview);
            });
        });

        father.on('click', '.remove-file', function () {
            const index = $(this).data('index');
            filesArray.splice(index, 1);
            const dataTransfer = new DataTransfer();
            filesArray.forEach(file => dataTransfer.items.add(file));
            father.find('#file-input')[0].files = dataTransfer.files;
            father.find('#file-input').trigger('change');
        });
    }

    function addProduct(){
        father.find("#add-product-form").submit(function(event) {
            event.preventDefault();
            var form = this;
            var data = new FormData(form);
            
            father.find('#save-product').prop('disabled',true);
            
            $.ajax({
                type:'POST',
                url: `/catalogs`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The product was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error:function(xhr){
                    let message = xhr.responseJSON?.error ?? 'Ocurrió un error inesperado.';
                    Swal.fire({ icon: 'error', title: 'Error', text: message });
                    father.find('#save-product').prop('disabled',false);
                }
            });
        });
    }

    reactiveAddProductForm();
    addProduct();
});
</script>
@endpush