@extends('layouts.app')

@section('content')

@php
    $currentTab = request('tab', 'inventory');
@endphp

<div id="inventory-module" class="flex flex-col lg:flex-row lg:justify-between items-start gap-2 m-2">

    <div class="flex flex-col gap-2 bg-white shadow-md rounded-lg w-full lg:w-[15%] p-2">
        <x-tittle-form class="border-b-2 border-green-700 pb-2">
            Menu
        </x-tittle-form>

        <x-nav-button data-button="inventory" icon="ri-file-list-fill"
            class="option-btn {{ $currentTab === 'inventory' ? 'border-2 border-green-500' : '' }}">
            Inventory
        </x-nav-button>

        <x-nav-button data-button="make-input" icon="ri-contract-right-fill"
            class="option-btn {{ $currentTab === 'make-input' ? 'border-2 border-green-500' : '' }}">
            Make Input
        </x-nav-button>

        @hasanyrole('Warehouse|Admin')
        <x-nav-button data-button="sales" icon="ri-shopping-cart-fill"
            class="option-btn {{ $currentTab === 'sales' ? 'border-2 border-green-500' : '' }}">
            Sales
        </x-nav-button>
        @endhasanyrole



        @if ($quarantine->isNotEmpty())
            <x-nav-button data-button="quarantine" icon="ri-error-warning-line"
                class="option-btn {{ $currentTab === 'quarantine' ? 'border-2 border-green-500' : '' }}">
                Quarantine
            </x-nav-button>
        @endif
    </div>

    
    <div class="flex flex-col bg-white shadow-md rounded-lg w-full lg:w-[85%] p-2 overflow-hidden">

        <div id="inventory-layout" class="{{ $currentTab === 'inventory' ? '' : 'hidden' }} w-full">
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

        <div id="make-input-layout" class="{{ $currentTab === 'make-input' ? '' : 'hidden' }} w-full">
            @include('inventory.inputs.makeInput')
        </div>

        <div id="quarantine-layout" class="{{ $currentTab === 'quarantine' ? '' : 'hidden' }} w-full">
            <div class="w-full overflow-x-auto">
                @include('inventory.quarantine.table')
            </div>
        </div>



        <div id="sales-layout" class="{{ $currentTab === 'sales' ? '' : 'hidden' }} w-full">
            <x-tittle-form class="border-s-2 border-green-700 ps-2 ms-2">
                Sales
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

    function switchTab(option) {
        inventory_module.find('.option-btn')
            .removeClass('border-2 border-green-500');

        inventory_module.find(`.option-btn[data-button="${option}"]`)
            .addClass('border-2 border-green-500');

        inventory_module.find(
            '#inventory-layout, #make-input-layout, #quarantine-layout, #sales-layout'
        ).addClass('hidden');

        switch (option) {
            case 'inventory':
                $('#inventory-layout').removeClass('hidden');
                $('#opt-tittle').text('Inventory');
                if ($.fn.dataTable) {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                }
                break;

            case 'make-input':
                $('#make-input-layout').removeClass('hidden');
                break;

            case 'quarantine':
                $('#quarantine-layout').removeClass('hidden');
                break;

            case 'sales':
                $('#sales-layout').removeClass('hidden');
                break;
        }
    }

    inventory_module.on('click', '.option-btn', function () {
        const option = $(this).data('button');
        switchTab(option);

        const url = new URL(window.location);
        url.searchParams.set('tab', option);
        window.history.pushState({ tab: option }, '', url);
    });

    window.addEventListener('popstate', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'inventory';
        switchTab(tab);
    });

    const initialParams = new URLSearchParams(window.location.search);
    const initialTab = initialParams.get('tab');
    if (initialTab && ['inventory', 'make-input', 'quarantine', 'sales'].includes(initialTab)) {
        switchTab(initialTab);
    }
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