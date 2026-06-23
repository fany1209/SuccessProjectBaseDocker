{{--
Customers
Actualizar Customer
Fecha de creación: 08-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 13-09-2025
--}}
<x-modal id="edit-transport-line">
    <form class="flex flex-col items-center w-full gap-2" id="edit-transport-line-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Edit Transport Line</x-tittle-form>
            <x-input-1 type="hidden" name="transport_line_id"></x-input-1>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Name</x-label>
                <x-input-1 required name="name" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="update-transport-line" colorBtn="blue">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function updateTLine(){
        $("#edit-transport-line-form").submit(function(event) {
            event.preventDefault();
            var form = $('#edit-transport-line-form')[0];
            var data = new FormData(form);
            var id = $('#transport_line_id').val();
            data.append('_method', 'PUT');
            $.ajax({
                type:'POST',
                url:`/transportLine/${id}`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Transport line was updated successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updated the Transport line.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
    updateTLine();
});
</script>
@endpush