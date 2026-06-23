<x-modal id="add-supplier">
    <div class="flex flex-col items-center w-full gap-2 my-2">
        <form class="flex flex-col items-center w-full gap-2" id="add-supplier-form">
            @csrf
            <x-wrapper-form-1>
                <x-tittle-form>Add New Supplier</x-tittle-form>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="Name">Supplier Name</x-label>
                    <x-input-1 required name="Name" placeholder="Enter supplier name"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="Code_supplier">Supplier Code</x-label>
                    <x-input-1 required type="number" name="Code_supplier" placeholder="0000"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="Product">Main Product</x-label>
                    <x-input-1 name="Product" placeholder="e.g. Chemicals"></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="RFC">RFC</x-label>
                    <x-input-1 name="RFC" placeholder="ABCD123456XYZ"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="Address">Address</x-label>
                    <x-textarea-1 name="Address" placeholder="Full address..."></x-textarea-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="Phone">Phone Number</x-label>
                    <x-input-1 type="tel" name="Phone" placeholder="+52..."></x-input-1>
                </x-wrapper-form-2>

                <x-wrapper-form-2>
                    <x-label for="Email">Email Address</x-label>
                    <x-input-1 type="email" name="Email" placeholder="supplier@example.com"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="Contact">Contact Person</x-label>
                    <x-input-1 name="Contact" placeholder="Name of person in charge"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
                <x-button-1 id="save-supplier" colorBtn="green">Save Supplier</x-button-1>
            </x-wrapper-form-1>
        </form>
    </div>
</x-modal>

@push('js')
<script>
$(document).ready(function(){
    $("#add-supplier-form").submit(function(event) {
        event.preventDefault();
        
        const form = $(this)[0];
        const data = new FormData(form);
        const saveBtn = $('#save-supplier');

        saveBtn.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: "{{ route('supplier_directory.store') }}",
            data: data,
            processData: false,
            contentType: false,
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'The supplier was registered successfully.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) location.reload();
                });
                saveBtn.prop('disabled', false);
            },
            error: function(xhr){
                let message = 'An unexpected error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.error) message = xhr.responseJSON.error;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
                saveBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush