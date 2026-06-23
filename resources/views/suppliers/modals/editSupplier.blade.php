{{--
Suppliers
Actualizar Supplier
Fecha de creación: 09-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 13-10-2025
--}}
<x-modal id="edit-supplier">
    <form class="flex flex-col items-center w-full gap-2" id="edit-supplier-form">
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Edit Supplier</x-tittle-form>
                <x-input-1 type="hidden" name="supplier_id" id="supplier_id"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="sector">Sector</x-label>
                <x-select-1 required name="sector_id" id="sector_id">
                    <option value="">Select a sector</option>
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->sector_id }}">{{ $sector->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Name</x-label>
                <x-input-1 required name="name" id="name" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="contact">Contact Person</x-label> {{-- Nuevo Campo --}}
                <x-input-1 name="contact" id="contact" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="phone">Phone</x-label>
                <x-input-1 name="phone" id="phone" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="email">Email</x-label>
                <x-input-1 type="email" name="email" id="email" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="rfc">RFC</x-label>
                <x-input-1 name="rfc" id="rfc" minlength="12" maxlength="13"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="state">State</x-label>
                <x-input-1 name="state" id="state" maxlength="80"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="city">City</x-label>
                <x-input-1 name="city" id="city" maxlength="80"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="district">District</x-label>
                <x-input-1 name="district" id="district" maxlength="80"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="address">Address</x-label>
                <x-input-1 name="address" id="address" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="update-supplier" colorBtn="blue">Update</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#edit-supplier')
    function updateSupplier(){
        father.find("#edit-supplier-form").submit(function(event) {
            event.preventDefault();
            var form = father.find('#edit-supplier-form')[0];
            var data = new FormData(form);
            var id = father.find('#supplier_id').val();
            data.append('_method', 'PUT');
            $.ajax({
                type:'POST',
                url:`/suppliers/${id}`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The supplier was updated successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
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
                }
            });
        });
    }
    updateSupplier();
});
</script>
@endpush