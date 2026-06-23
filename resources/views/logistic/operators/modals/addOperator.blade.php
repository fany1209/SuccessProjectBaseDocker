{{--
Operators
Agregar Operator
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<x-modal id="add-operator">
    <form class="flex flex-col items-center w-full gap-2" id="add-operator-form">
        <x-wrapper-form-1>
            <x-tittle-form>Add Operator</x-tittle-form>
        </x-wrapper-form-1>
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Name</x-label>
                <x-input-1 required name="name" maxlength="100"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="license">License</x-label>
                <x-input-1 required name="license" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-operator" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function addOperator(){
        $("#add-operator-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-operator-form')[0];
            var data = new FormData(form);
            $('#save-operator').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/operator",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Operator was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-operator').prop('disabled',false);
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
                    $('#save-operator').prop('disabled',false);
                }
            });
        });
    }
    addOperator();
});
</script>
@endpush