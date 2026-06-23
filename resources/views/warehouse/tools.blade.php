{{--
Warehouse
Warehouse (Tools Section)
Fecha de creación: 26-07-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 24-10-25
--}}
<x-application.section-1>
    <div class="flex justify-end items-center">
        <x-button class="open-modal" data-target="maps">Maps</x-button>@include('warehouse.modals.maps')@include('warehouse.modals.infoLocation')
        <x-button class="open-modal" data-target="summary">Summary</x-button>@include('warehouse.modals.summaryModal')
        <x-button class="open-modal" data-target="add-cli">Do Operation</x-button>@include('warehouse.modals.addOperation')
    </div>
</x-application.section-1>