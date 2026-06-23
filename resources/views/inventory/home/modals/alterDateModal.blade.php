{{--
Inventario
Inventario (Botón Date Modal)
Fecha de creación: 15-08-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 15-08-25
--}}
<x-modal id="alterDate">
    <form class="flex flex-col items-center w-full gap-2" id="alter-date-form">
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="tittle"></x-tittle-form>
                <p class="my-2 text-md text-red-500 row-span-1 row-start-2">Movement Date: <b id="date"></b></p>
                <x-input-1 type="hidden" name="id"></x-input-1>
                <x-input-1 type="hidden" name="type"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Select New Date</x-label>
                <x-input-1 type="date" name="updated_at"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 type="reset" class="close-modal" colorBtn="orange">Do Nothing</x-button-1>
            <x-button-1 class="update-date close-modal" colorBtn="blue">Update</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#alterDate');
    function updateDate(){
        father.find("#alter-date-form").submit(function(event) {
            event.preventDefault();
            var form = father.find('#alter-date-form')[0];
            var data = new FormData(form);
            data.append('_method', 'PUT');
            $.ajax({
                type:'POST',
                url: "{{ route('inventory.updateDate') }}",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The date was updated successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error:function(xhr){
                    console.log(xhr);
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
        });
    }
    updateDate();
});
</script>
@endpush