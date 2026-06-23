{{--
Customers
Actualizar Customer
Fecha de creación: 08-09-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 08-01-2025
--}}
<x-modal id="edit-customer">
    <form class="flex flex-col items-center w-full gap-2" id="edit-customer-form">
        @csrf

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Edit customer</x-tittle-form>
                <p id="customer_code" class="text-sm text-gray-500"></p>
                <x-input-1 type="hidden" name="customer_id" id="customer_id"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="sector_id">Sector</x-label>
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
                <x-label for="phone">Phone</x-label>
                <x-input-1 name="phone" id="phone" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="email">Email</x-label>
                <x-input-1 type="email" name="email" id="email" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="rfc">RFC</x-label>
                <x-input-1 name="rfc" id="rfc" maxlength="15"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="postal_code">Postal code</x-label>
                <x-input-1 name="postal_code" id="postal_code" maxlength="10"></x-input-1>
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
                <x-label for="address">Address (Fiscal)</x-label>
                <x-input-1 name="address" id="address" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="country">Country</x-label>
                <x-input-1 name="country" id="country" maxlength="100"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="contact">Contact Person</x-label>
                <x-input-1 name="contact" id="contact" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="vendedor">Vendedor</x-label>
                <x-input-1 name="vendedor" id="vendedor" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="delivery_address">Delivery Address</x-label>
                <x-input-1 name="delivery_address" id="delivery_address"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="update-customer" colorBtn="blue">Update</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#edit-customer');
    const customers_per_sector = @json($customers_per_sector);

    function reactiveEditCustomerForm(){
      father.on('change','#sector_id',function(){
        const sector = $(this).val();
        const item = customers_per_sector.find(x => x.sector_id == sector);

        if(sector && item){
          const next = parseInt(item.last_number) + 1;
          father.find('#customer_code').text(`New Customer Code: SC${item.code}${next} (Preview)`);
        } else {
          father.find('#customer_code').text('');
        }
      });
    }

    function updateCustomer(){
        father.find("#edit-customer-form").submit(function(event) {
            event.preventDefault();

            var form = father.find('#edit-customer-form')[0];
            var data = new FormData(form);
            var id = father.find('#customer_id').val();

            data.append('_method', 'PUT');

            $.ajax({
                type:'POST',
                url:`/customers/${id}`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The customer was updated successfully.',
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

    reactiveEditCustomerForm();
    updateCustomer();
});
</script>
@endpush