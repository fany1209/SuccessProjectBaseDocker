{{--
Vehicles
Agregar Vehicle
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<x-modal id="add-vehicle">
    <form class="flex flex-col items-center w-full gap-2" id="add-vehicle-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Add Vehicle</x-tittle-form>
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
            <x-button-1 id="save-vehicle" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function addVehicle(){
        $("#add-vehicle-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-vehicle-form')[0];
            var data = new FormData(form);
            $('#save-vehicle').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"/vehicle",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Vehicle was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#save-vehicle').prop('disabled',false);
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the Vehicle.',
                        confirmButtonText: 'OK'
                    });
                    $('#save-vehicle').prop('disabled',false);
                }
            });
        });
    }
    addVehicle();
});
</script>
@endpush