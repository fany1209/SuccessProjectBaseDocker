{{--
Customers
Agregar Customer
Fecha de creación: xx-xx-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex justify-end items-center w-full my-1 gap-2">
        <x-button data-target="add-transport-line" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Transport Line</x-button>@include('logistic.transport-lines.modals.addTLine')
    </div>
</section>