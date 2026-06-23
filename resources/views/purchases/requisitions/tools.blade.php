{{--
Requisitions
Agregar Requisition
Fecha de creación: 15-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 15-10-2025
--}}
<x-section-1>
    <x-wrapper-h-1>
        @can('purchases.requisitions.create')
        @include('purchases.requisitions.modals.addRequisition')
        <x-button data-target="add-requisition" class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Add Requisition</x-button>
        @endcan
    </x-wrapper-h-1>
</x-section-1>