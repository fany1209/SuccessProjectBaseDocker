{{--
Inventory
Tools
Fecha de creación: xx-xx-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 08-10-2025
--}}
<section class="flex justify-end items-center w-full gap-2 mt-1">
    <x-button data-target="add-tweak" class="open-modal">Tweaks</x-button>@include('inventory.home.modals.addTweak')
    <x-button data-target="make-transaction" class="open-modal">Make Transaction</x-button>@include('inventory.home.modals.addTransaction')
    <x-button data-target="movements" class="open-modal">Movements</x-button>@include('inventory.home.modals.movements')@include('inventory.home.modals.alterDateModal')@include('inventory.home.modals.editTransaction')
    @can('warehouse.show')
    <x-button-1 data-target="export-reports" class="open-modal" colorBtn="green"><i class="ri-file-excel-2-line"></i>Reports</x-button-1>@include('inventory.home.modals.exportReports')
    @endcan
</section>