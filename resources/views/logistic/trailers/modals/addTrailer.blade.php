{{--
Trailers
Agregar Trailer
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<x-modal id="add-trailer">
    <form class="flex flex-col items-center w-full gap-2" id="add-trailer-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Add Trailer</x-tittle-form>
            <x-wrapper-form-2>
                <x-label for="transport_line_id">Transport Line</x-label>
                <x-select-1 required name="transport_line_id">
                    <option value="">Select a transport line</option>
                    @foreach ($tLines as $tLine)
                    <option value="{{ $tLine->transport_line_id }}">{{ $tLine->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="type">Type</x-label>
                <x-input-1 required name="type" maxlength="70"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="unit_number">Unit Number</x-label>
                <x-input-1 name="unit_number" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="plate">Plate</x-label>
                <x-input-1 required name="plate" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="color">Color</x-label>
                <x-input-1 name="color" maxlength="30"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-trailer" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function addTrailer(){
        $("#add-trailer-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-trailer-form')[0];
            var data = new FormData(form);
            $('#save-trailer').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/trailer",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Trailer was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-trailer').prop('disabled',false);
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the Trailer.',
                        confirmButtonText: 'OK'
                    });
                    $('#save-trailer').prop('disabled',false);
                }
            });
        });
    }
    addTrailer();
});
</script>
@endpush