{{--
Inventory
Tools
Fecha de creación: xx-xx-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 08-10-2025
--}}
<section class="flex justify-end items-center w-full gap-2 mt-1">
    <a href="{{ route('export-protein') }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer" target="_blank">
        <i class="ri-file-excel-2-line mr-1"></i> Reporte Proteína
    </a>
    <x-button data-target="add-tweak" class="open-modal">Tweaks</x-button>@include('inventory.home.modals.addTweak')
    <x-button data-target="make-transaction" class="open-modal">Make Transaction</x-button>@include('inventory.home.modals.addTransaction')
    <x-button data-target="movements" class="open-modal">Movements</x-button>@include('inventory.home.modals.movements')@include('inventory.home.modals.alterDateModal')@include('inventory.home.modals.editTransaction')
    @can('warehouse.show')
    <x-button-1 data-target="export-reports" class="open-modal" colorBtn="green"><i class="ri-file-excel-2-line"></i>Reports</x-button-1>@include('inventory.home.modals.exportReports')
    @endcan
    <a href="{{ route('inventory.pallets.pending') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-400 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
        <i class="ri-inbox-archive-line mr-1"></i> Tarimas por Recibir
    </a>
</section>