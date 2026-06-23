{{--
Customers
Agregar Customer
Fecha de creación: 08-09-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 08-01-2026
--}}
<x-modal id="add-customer">
    <form class="flex flex-col items-center w-full gap-2" id="add-customer-form">
        @csrf

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Add Customer</x-tittle-form>
                <p id="customer_code" class="text-sm text-gray-500">Choice a Sector</p>
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
                <x-input-1 required name="name" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="phone">Phone</x-label>
                <x-input-1 name="phone" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="email">Email</x-label>
                <x-input-1 type="email" name="email" maxlength="150"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="rfc">RFC</x-label>
                <x-input-1 name="rfc" minlength="12" maxlength="13"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="postal_code">Postal code</x-label>
                <x-input-1 name="postal_code" maxlength="10"></x-input-1>
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
            <x-wrapper-form-2>
                <x-label for="address">Address (Fiscal)</x-label>
                <x-input-1 name="address" maxlength="200"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="country">Country</x-label>
                <x-input-1 name="country" maxlength="100"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="contact">Contact Person</x-label>
                <x-input-1 name="contact" id="contact" maxlength="150" placeholder="Full name of contact"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="vendedor">Vendedor</x-label>
                <x-input-1
                    name="vendedor"
                    id="vendedor"
                    maxlength="150"
                    value="{{ auth()->user()->name ?? '' }}"
                ></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="delivery_address">Delivery Address</x-label>
                <x-input-1 name="delivery_address" id="delivery_address" placeholder="Specify if different from fiscal address"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-customer" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#add-customer');
    const customers_per_sector = @json($customers_per_sector);

    function reactiveAddCustomerForm(){
      father.on('change','#sector_id',function(){
        const sector = $(this).val();
        const item = customers_per_sector.find(x => x.sector_id == sector);

        if(sector && item){
          const next = parseInt(item.last_number) + 1;
          father.find('#customer_code').text(`Customer Code: SC${item.code}${next}`);
        }else{
          father.find('#customer_code').text('Choice a sector');
        }
      });
    }

    function addCustomer(){
        father.find("#add-customer-form").submit(function(event) {
            event.preventDefault();
            var form = father.find('#add-customer-form')[0];
            var data = new FormData(form);

            father.find('#save-customer').prop('disabled',true);

            $.ajax({
                type:'POST',
                url:"/customers",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The customer was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    father.find('#save-customer').prop('disabled',false);
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
                    father.find('#save-customer').prop('disabled',false);
                }
            });
        });
    }

    reactiveAddCustomerForm();
    addCustomer();
});
</script>
@endpush