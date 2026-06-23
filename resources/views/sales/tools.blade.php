{{--
Sales
Tools Sales
Fecha de creación: 24-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 24-09-2025
--}}
<section class="flex justify-center items-center w-full">
    <div class="flex justify-between items-center w-full my-1 gap-2">
        <div class="flex justify-start items-center w-full gap-2">
            <p class="bg-purple-700 text-white p-1 rounded-md font-semibold tracking-[5px]">All Sales: {{ $total_sales }}</p>
        </div>
        <div class="flex justify-end items-center w-full gap-2">
            <label class="relative inline-flex cursor-pointer items-center">
                <input type="checkbox" id="toggleSalesView" class="peer sr-only" checked />
                <div class="mov-edit-toggleSecurity peer flex w-full items-center justify-center py-2 px-3 rounded-md bg-blue-700 
                            after:absolute after:left-0 after:h-full after:w-1/2 after:rounded-md after:bg-white/40 
                            after:transition-all after:translate-x-full after:content-[''] peer-checked:bg-purple-500 
                            peer-checked:after:translate-x-0 duration-300 text-sm text-white gap-2">
                    <span>Sales</span>
                    <span class="ms-4">Quotes</span>
                </div>
            </label>
            @can('sales.create')
            <x-button id="add-sale" class="open-modal bg-purple-500 hover:bg-purple-600 focus:bg-purple-600 active:bg-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Sale</x-button>
            @endcan
        </div>
    </div>
</section>
@push('js')
<script>
$(document).ready(function(){
    function reactiveToolsFinances(){
        $('#add-sale').on('click',function(){
            $('#add-sale-blade').removeClass('hidden');
            $('#update-sale-blade').addClass('hidden');
        });
    }
    
    reactiveToolsFinances();
});
</script>
@endpush