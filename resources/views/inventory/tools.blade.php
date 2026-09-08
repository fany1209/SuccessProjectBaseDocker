{{--
Inventory
Tools
Fecha de creación: xx-xx-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 08-10-2025
--}}

<section>
    <div class="flex justify-end items-center gap-2">
        <a href="{{ route('export-protein') }}" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold flex items-center gap-1 shadow-sm transition-colors cursor-pointer" target="_blank">
            <i class="ri-file-excel-2-line"></i> Reporte Proteína
        </a>
        <x-button-1 type="button" colorBtn="gray" data-target="add-tweak" class="open-modal">Tweaks</x-button-1>
        <x-button-1 type="button" colorBtn="gray" data-target="make-transaction" class="open-modal">Make Transaction</x-button-1>
        <x-button-1 type="button" colorBtn="gray" data-target="movements" class="open-modal">Movements</x-button-1>
        <a href="{{ route('inventory.pallets.pending') }}" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold flex items-center gap-1 shadow-sm transition-colors cursor-pointer">
            <i class="ri-inbox-archive-line"></i> Tarimas por Recibir
        </a>
    </div>
</section>
@include('inventory.modals.addTweak')
@include('inventory.modals.addTransaction')
@include('inventory.modals.movements')@include('inventory.modals.alterDateModal')@include('inventory.modals.editTransaction')