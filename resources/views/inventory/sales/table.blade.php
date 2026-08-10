<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-white uppercase bg-emerald-600">
            <tr>
                <th scope="col" class="px-6 py-3 rounded-tl-lg">
                    Folio
                </th>
                <th scope="col" class="px-6 py-3">
                    Cliente / Prospecto
                </th>
                <th scope="col" class="px-6 py-3">
                    Fecha
                </th>
                <th scope="col" class="px-6 py-3">
                    Vendedor
                </th>
                <th scope="col" class="px-6 py-3">
                    Estatus Almacén
                </th>
                <th scope="col" class="px-6 py-3 rounded-tr-lg text-center">
                    Acción
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($almacen_sales as $sale)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-gray-900">
                        {{ $sale->folio }}
                    </td>
                    <td class="px-6 py-4">
                        @if($sale->is_customer)
                            {{ $sale->customer->name ?? 'N/A' }}
                        @else
                            {{ $sale->prospect->name ?? 'N/A' }}
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $sale->seller ?? ($sale->user->name ?? 'N/A') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($sale->almacen_status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded border border-yellow-300">Pendiente</span>
                        @elseif($sale->almacen_status == 'confirmed')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Confirmada</span>
                        @elseif($sale->almacen_status == 'postponed')
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400">Pospuesta</span>
                        @elseif($sale->almacen_status == 'cancelled')
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Cancelada</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-500">{{ ucfirst($sale->almacen_status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('sales.almacen_detail', $sale->sale_id) }}">
                            <x-button-1 colorBtn="blue" type="button">Detalles</x-button-1>
                        </a>
                    </td>
                </tr>
            @empty
                <tr class="bg-white border-b">
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No hay ventas para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
