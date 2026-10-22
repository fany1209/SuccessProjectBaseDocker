<section class="flex justify-end items-center w-full gap-2 mt-1">
    <x-button data-target="add-tweak" class="open-modal">Tweaks</x-button>@include('inventory.home.modals.addTweak')
    <x-button data-target="make-transaction" class="open-modal">Make Transaction</x-button>@include('inventory.home.modals.addTransaction')
    <x-button data-target="bigbags-summary" class="open-modal bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1"><i class="ri-inbox-line"></i> BigBags</x-button>@include('warehouse.modals.bigbagsModal')
    <x-button data-target="movements" class="open-modal">Movements</x-button>@include('inventory.home.modals.movements')@include('inventory.home.modals.alterDateModal')@include('inventory.home.modals.editTransaction')
    @can('warehouse.show')
    <x-button-1 data-target="export-reports" class="open-modal" colorBtn="green"><i class="ri-file-excel-2-line"></i>Reports</x-button-1>@include('inventory.home.modals.exportReports')
    @endcan

    <a href="{{ route('warehouse.productionRequests') }}" class="relative inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer">
        <i class="ri-file-list-3-line mr-1"></i> Solicitudes Producción
        @php
            $pendingReqs = \App\Models\ProductionMaterialRequest::where('status', 'Pendiente')->count();
        @endphp
        @if($pendingReqs > 0)
            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-md border-2 border-white">
                {{ $pendingReqs }}
            </span>
        @endif
    </a>
    <a href="{{ route('inventory.pallets.pending') }}" class="relative inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-400 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
        <i class="ri-inbox-archive-line mr-1"></i> Tarimas por Recibir
        @php
            $pendingCount = \Illuminate\Support\Facades\Schema::hasTable('pallets')
                ? \App\Models\Pallet::where('inventory_status', 'Enviada')->count()
                : 0;
        @endphp
        @if($pendingCount > 0)
            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-md border-2 border-white">
                {{ $pendingCount }}
            </span>
        @endif
    </a>
</section>