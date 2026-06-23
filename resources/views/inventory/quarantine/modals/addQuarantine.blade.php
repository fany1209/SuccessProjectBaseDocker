{{--
Inventario
Quarantine
Fecha de creación: 22-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 22-09-2025
--}}
<x-modal id="add-quarantine">
    <div class="flex justify-between items-center mb-2">
        <div class="flex justify-between items-center gap-2 bg-red-500 p-2 rounded-md">
            <img width="20" src="{{ asset('images/quarantine.svg') }}" alt="quarantine"/>
            <h2 class="text-white text-md font-semibold tracking-[2px]">Add Quarantine</h2>
        </div>
        <div class="flex flex-col lg:flex-row lg:justify-between items-center">
            <h3 class="text-gray-700 text-sm lg:text-lg font-semibold tracking-[2px]" id="product-tittle"></h3>
            <span id="batch-tittle" class="bg-green-500 text-white text-sm lg:text-lg font-semibold tracking-[2px] px-2 rounded-md lg:ml-1"></span>
        </div>
    </div>
    <form id="add-quarantine-form" class="flex flex-col items-center w-full gap-2">
        @csrf
        <input type="hidden" id="inventory-id" name="inventory_id">
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="quantity" class="mb-1 block font-medium text-md text-gray-700">Quantity</label>
                <input required type="number" min="0" id="quantity" name="quantity" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                <p id="stock-available" class="text-sm text-gray-500"></p>
            </div>
        </div>
        <div class="flex justify-between items-center w-full gap-2">
            <div class="flex flex-col items-start gap-1 w-full">
                <label for="notes" class="mb-1 block font-medium text-md text-gray-700">Why is this product in quarantine?</label>
                <textarea required id="notes" name="notes" cols="30" rows="3" class="resize-none block w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm px-2 py-2"></textarea>
            </div>
        </div>
        <div class="flex justify-end items-center gap-2 w-full">                
            <x-button class="close-modal bg-red-500 hover:bg-red-600 focus:bg-red-600 active:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">Cancel</x-button>
            <x-button type="submit" id="add-quarantine" class="bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Save</x-button>
        </div>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function addQuarantine(){
        $("#add-quarantine-form").submit(function(event) {
            event.preventDefault();
            var form = $('#add-quarantine-form')[0];
            var data = new FormData(form);
            $('#add-quarantine').prop('disabled',true);
            $.ajax({
                type:'POST',
                url:"{{ route('inventory.addQuarantine') }}",
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The product was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    $('#add-quarantine').prop('disabled',false);
                },
                error:function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the product.',
                        confirmButtonText: 'OK'
                    });
                    $('#add-quarantine').prop('disabled',false);
                }
            });
        });
    }
    addQuarantine();
});
</script>
@endpush