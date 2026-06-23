{{--
Vehicles
Agregar Vehicle
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex justify-end items-center w-full my-1 gap-2">
        <x-button data-target="add-vehicle" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Vehicle</x-button>@include('logistic.vehicles.modals.addVehicle')
    </div>
</section>