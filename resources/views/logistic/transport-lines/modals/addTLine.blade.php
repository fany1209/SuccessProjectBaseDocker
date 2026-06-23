{{--
Transport Lines
Agregar Transport Line
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<x-modal id="add-transport-line">
    <form class="flex flex-col items-center w-full gap-2" id="add-transport-line-form">
        <x-wrapper-form-1>
            <x-tittle-form>Add Transport Line</x-tittle-form>
        </x-wrapper-form-1>
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Name</x-label>
                <x-input-1 required name="name" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-transport-line" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function addTLine(){
        $("#add-transport-line-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-transport-line-form')[0];
            var data = new FormData(form);
            $('#save-transport-line').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/transportLine",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Transport line was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-transport-line').prop('disabled',false);
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the Transport line.',
                        confirmButtonText: 'OK'
                    });
                    $('#save-transport-line').prop('disabled',false);
                }
            });
        });
    }
    addTLine();
});
</script>
@endpush