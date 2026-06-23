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
        <x-button-1 type="button" colorBtn="gray" data-target="add-tweak" class="open-modal">Tweaks</x-button-1>
        <x-button-1 type="button" colorBtn="gray" data-target="make-transaction" class="open-modal">Make Transaction</x-button-1>
        <x-button-1 type="button" colorBtn="gray" data-target="movements" class="open-modal">Movements</x-button-1>
    </div>
</section>
@include('inventory.modals.addTweak')
@include('inventory.modals.addTransaction')
@include('inventory.modals.movements')@include('inventory.modals.alterDateModal')@include('inventory.modals.editTransaction')