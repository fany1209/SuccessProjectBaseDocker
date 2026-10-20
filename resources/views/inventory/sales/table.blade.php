@if(request()->filled('product_id'))
    @php
        $filteredProduct = \App\Models\Product::find(request('product_id'));
    @endphp
    <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="p-2.5 bg-amber-500 text-white rounded-lg shadow-sm flex-shrink-0">
                <i class="ri-filter-3-line text-xl"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-900">
                    Filtro por Producto Escaneado (Salida de Almacén)
                </h4>
                <p class="text-xs text-amber-800">
                    Mostrando únicamente ventas con estatus <span class="font-bold uppercase tracking-wider text-amber-900 bg-amber-200/70 px-1.5 py-0.5 rounded">Pendiente</span> que incluyen: 
                    <strong class="text-slate-900 underline">{{ $filteredProduct ? $filteredProduct->name : ('ID #' . request('product_id')) }}</strong>
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('inventory.index', ['tab' => 'sales']) }}" 
               class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-amber-100 border border-amber-300 rounded-lg text-xs font-semibold text-amber-900 shadow-sm transition">
                <i class="ri-close-circle-line mr-1.5 text-base text-amber-700"></i> Quitar filtro y ver todas
            </a>
        </div>
    </div>
@endif

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
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="ri-inbox-line text-4xl text-gray-300 mb-2"></i>
                            <p class="font-medium">
                                @if(request()->filled('product_id'))
                                    No se encontraron ventas pendientes para el producto seleccionado.
                                @else
                                    No hay ventas para mostrar.
                                @endif
                            </p>
                            @if(request()->filled('product_id'))
                                <a href="{{ route('inventory.index', ['tab' => 'sales']) }}" class="mt-2 text-xs text-emerald-600 hover:text-emerald-700 font-semibold underline">
                                    Ver todas las ventas de almacén
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
