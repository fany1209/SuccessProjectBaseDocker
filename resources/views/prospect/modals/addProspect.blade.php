{{--
Prospects
Agregar Prospect
Fecha de creación: 08-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
--}}
<x-modal id="add-prospect">
    <form class="flex flex-col items-center w-full gap-2" id="add-prospect-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Add Prospect</x-tittle-form>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Name</x-label>
                <x-input-1 required name="name" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="sector">Sector</x-label>
                <x-select-1 required name="sector_id">
                    <option value="">Select a sector</option>
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->sector_id }}">{{ $sector->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="email">Email</x-label>
                <x-input-1 type="email" name="email" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="phone">Phone</x-label>
                <x-input-1 name="phone" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="rfc">RFC</x-label>
                <x-input-1 name="rfc" minlength="12" maxlength="12"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="address">Address</x-label>
                <x-input-1 name="address" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="state">State</x-label>
                <x-input-1 name="state" maxlength="80"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="city">City</x-label>
                <x-input-1 name="city" maxlength="80"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="district">District</x-label>
                <x-input-1 name="district" maxlength="80"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-prospect" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#add-prospect');
    function addProspect(){
        father.find("#add-prospect-form").submit(function(event) {
            event.preventDefault();
            var form = father.find('#add-prospect-form')[0];
            var data = new FormData(form);
            father.find('#save-prospect').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/prospects",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The prospect was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    father.find('#save-prospect').prop('disabled',false);
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
                    father.find('#save-prospect').prop('disabled',false);
                }
            });
        });
    }
    addProspect();
});
</script>
@endpush