{{--
Prospect
Tools Prospect
Fecha de creación: 09-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
--}}
<x-section-1>
    <div class="flex justify-end items-center w-full my-1 gap-2">
        @can('prospects.create')
        <x-button data-target="add-prospect" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Prospect</x-button>@include('prospect.modals.addProspect')
        @endcan
    </div>
</x-section-1>