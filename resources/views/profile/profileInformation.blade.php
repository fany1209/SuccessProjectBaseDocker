{{--
Profile
Fecha de creación: 23-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 23-09-2025
--}}
<section class="flex flex-col items-start gap-4 rounded-lg w-full bg-white shadow-md p-4">
    <div class="flex flex-col items-start gap-2 border-b-2 border-green-300 w-full pb-2">
        <h2 class="text-2xl text-gray-700 tracking-[3px] font-semibold">Profile Information</h2>
        <p class="text-gray-500 text-sm tracking-[2px]">Update your account's profile information and email address.</p>
    </div>
    <form action="{{ route('profile.update-info') }}" method="POST" class="flex flex-col items-center w-full gap-4" id="update-profile-form" enctype="multipart/form-data">
        @csrf
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="" class="mb-1 block font-medium text-md text-gray-700">Name</label>
                <input required value="{{ $user->name }}" type="text" placeholder="Please write your new user name" id="" name="name" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
            </div>
        </div>
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="" class="mb-1 block font-medium text-md text-gray-700">Email</label>
                <input required value="{{ $user->email }}" type="email" placeholder="Please write your new email" id="" name="email" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
            </div>
        </div>
        <div id="edit-images" class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start w-full mt-2">
                <label class="mb-1 block font-medium text-md text-gray-700">Profile Image</label>
                <label for="image-profile" class="flex flex-col items-center justify-center w-full h-38 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                    <div class="flex flex-col items-center justify-center pt-4 pb-6">
                        <svg class="w-8 h-8 mb-2 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5A5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                        </svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o arrastra</p>
                        <p class="text-xs text-gray-500">Solo JPG, JPEG, PNG</p>
                    </div>
                    <input type="file" name="photo" id="image-profile" class="hidden"/>
                </label>
                <div id="preview-container-images" class="flex flex-wrap gap-4 mt-2"></div>
            </div>
        </div>
        <div class="flex justify-end items-center w-full gap-2">
            <x-button type="submit" id="">Save</x-button>
        </div>
    </form>
</section>
@push('js')
<script>
$(document).ready(function(){
    function viewImages(){
        let filesArray = [];
        $('#image-profile').on('change', function (e) {
            $('#preview-container-images').empty();
            filesArray = Array.from(e.target.files);
            filesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const preview = `
                        <div class="relative w-[100px] h-[100px] border rounded overflow-hidden" data-index="${index}">
                            <img src="${event.target.result}" class="object-cover w-full h-full" />
                            <button type="button" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs remove-btn" data-index="${index}">x</button>
                        </div>
                    `;
                    $('#preview-container-images').append(preview);
                };
                reader.readAsDataURL(file);
            });
        });
        $('#preview-container-images').on('click', '.remove-btn', function () {
            const index = $(this).data('index');
            filesArray.splice(index, 1);
            const dataTransfer = new DataTransfer();
            filesArray.forEach(file => dataTransfer.items.add(file));
            $('#image-profile')[0].files = dataTransfer.files;
            $('#image-profile').trigger('change');
        });
    }
    viewImages();
});
</script>
@endpush