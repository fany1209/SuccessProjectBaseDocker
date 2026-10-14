<x-application.section-1>
    <div class="flex justify-end items-center gap-2">
        <x-button class="open-modal" data-target="maps">Maps</x-button>@include('warehouse.modals.maps')@include('warehouse.modals.infoLocation')
        <x-button class="open-modal" data-target="summary">Summary</x-button>@include('warehouse.modals.summaryModal')
        <x-button class="open-modal bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1.5" data-target="bigbags-summary"><i class="ri-inbox-line"></i> BigBags</x-button>@include('warehouse.modals.bigbagsModal')
        <x-button class="open-modal" data-target="add-cli">Do Operation</x-button>@include('warehouse.modals.addOperation')
        <a href="{{ route('warehouse.pendingTransfers') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">Recepciones de Producción</a>
    </div>
</x-application.section-1>