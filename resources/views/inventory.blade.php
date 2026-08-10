{{--
Inventario
Inventario (Make transaction)
Fecha de creación: 13-08-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 26-01-2026
--}}
@extends('layouts.app')

@section('content')

<div id="inventory-module" class="flex flex-col lg:flex-row lg:justify-between items-start gap-2 m-2">

    <div class="flex flex-col gap-2 bg-white shadow-md rounded-lg w-full lg:w-[15%] p-2">
        <x-tittle-form class="border-b-2 border-green-700 pb-2">
            Menu
        </x-tittle-form>

        <x-nav-button data-button="inventory" icon="ri-file-list-fill"
            class="option-btn border-2 border-green-500">
            Inventory
        </x-nav-button>

        <x-nav-button data-button="make-input" icon="ri-contract-right-fill"
            class="option-btn">
            Make Input
        </x-nav-button>

        @hasanyrole('Warehouse|Admin')
        <x-nav-button data-button="sales" icon="ri-shopping-cart-fill"
            class="option-btn">
            Ventas
        </x-nav-button>
        @endhasanyrole

        <div class="relative w-full">
            <x-nav-button
                data-button="batch"
                icon="ri-file-list-fill"
                class="option-btn w-full">
                Batch
            </x-nav-button>

            @if(isset($pendingLotRequests) && $pendingLotRequests > 0)
                <span
                    id="pending-batch-badge"
                    class="absolute top-1 right-2
                        bg-red-600 text-white text-xs font-bold
                        rounded-full min-w-[18px] h-[18px]
                        flex items-center justify-center px-1">
                    {{ $pendingLotRequests }}
                </span>
            @endif
        </div>

        @if ($quarantine->isNotEmpty())
            <x-nav-button data-button="quarantine" icon="ri-error-warning-line"
                class="option-btn">
                Quarantine
            </x-nav-button>
        @endif
    </div>

    
    <div class="flex flex-col bg-white shadow-md rounded-lg w-full lg:w-[85%] p-2 overflow-hidden">

        <div id="inventory-layout" class="w-full">
            <x-tittle-form id="opt-tittle" class="border-s-2 border-green-700 ps-2 ms-2">
                Inventory
            </x-tittle-form>

            @include('inventory.home.tools')
            @include('inventory.home.inventoryAvailaible')

            <div class="flex flex-col lg:flex-row lg:justify-center items-center mt-3 gap-2">
                @include('inventory.home.lowStock')
                @include('inventory.home.hightStock')
            </div>

            @include('inventory.home.modals.viewStock')
        </div>

        <div id="make-input-layout" class="hidden w-full">
            @include('inventory.inputs.makeInput')
        </div>

        <div id="quarantine-layout" class="hidden w-full">
            <div class="w-full overflow-x-auto">
                @include('inventory.quarantine.table')
            </div>
        </div>

        <div id="batch-layout" class="hidden w-full">
            <x-tittle-form class="border-s-2 border-green-700 ps-2 ms-2">
                Batch / Lotes
            </x-tittle-form>

            <div class="w-full overflow-x-auto mt-3">
                @include('formats.laboratory.lot_requests._table')
            </div>
        </div>

        <div id="sales-layout" class="hidden w-full">
            <x-tittle-form class="border-s-2 border-green-700 ps-2 ms-2">
                Ventas
            </x-tittle-form>

            <div class="w-full overflow-x-auto mt-3">
                @include('inventory.sales.table')
            </div>
        </div>

    </div>
</div>

<a href="{{ route('export-inventory') }}" target="_blank">
    <div class="sheet-widget" title="Exportar Inventario">
        <i class="fas fa-file-alt fa-xl" style="color: white;"></i>
    </div>
</a>
@endsection

@push('js')
<script>
$(function () {
    const inventory_module = $('#inventory-module');

    inventory_module.on('click', '.option-btn', function () {
        const option = $(this).data('button');

        inventory_module.find('.option-btn')
            .removeClass('border-2 border-green-500');

        $(this).addClass('border-2 border-green-500');

        inventory_module.find(
            '#inventory-layout, #make-input-layout, #quarantine-layout, #batch-layout, #sales-layout'
        ).addClass('hidden');

        switch (option) {
            case 'inventory':
                $('#inventory-layout').removeClass('hidden');
                $('#opt-tittle').text('Inventory');
                break;

            case 'make-input':
                $('#make-input-layout').removeClass('hidden');
                $('#opt-tittle').text('Make Input');
                break;

            case 'quarantine':
                $('#quarantine-layout').removeClass('hidden');
                $('#opt-tittle').text('Quarantine');
                break;

            case 'batch':
                $('#batch-layout').removeClass('hidden');
                $('#opt-tittle').text('Batch');
                break;
                
            case 'sales':
                $('#sales-layout').removeClass('hidden');
                $('#opt-tittle').text('Ventas');
                break;
        }
    });
});
</script>
@endpush

@push('css')
<style>
.sheet-widget {
    position: fixed;
    bottom: 0.75rem;
    left: 0.75rem;
    width: 55px;
    height: 55px;
    background-color: #ee4e0f;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-bottom: 3px;
    padding-left: 1px;
    cursor: pointer;
    box-shadow: 0 10px 13px rgba(0, 0, 0, 0.562);
}
</style>
@endpush