{{--
Suppliers
Tool's Suppliers
Fecha de creación: 09-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex justify-between items-center w-full my-1 gap-2">
        <p class="bg-purple-700 text-white p-1 rounded-md font-semibold tracking-[5px]">All Suppliers: {{ $total_suppliers }}</p>
        @can('suppliers.create')
        <x-button data-target="add-supplier" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Supplier</x-button>@include('suppliers.modals.addSupplier')
        @endcan
    </div>
</section>