@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
    <!-- Navegación y Botón Regresar -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <a href="{{ route('inventory.index', ['tab' => 'sales']) }}" 
           class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition hover:shadow cursor-pointer w-fit">
            <i class="ri-arrow-left-line mr-1.5 text-base text-slate-500"></i> Regresar a Lista de Ventas
        </a>
        <div class="flex items-center space-x-2 text-xs text-slate-400">
            <i class="ri-shield-check-line text-emerald-600"></i>
            <span>Módulo de Validación de Almacén</span>
        </div>
    </div>

    <!-- Tarjeta Principal de Detalle -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden">
        
        <!-- Header con Degradado Moderno y Estatus -->
        <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-teal-700 text-white p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-2xl flex-shrink-0 shadow-inner">
                        <i class="ri-file-list-3-fill"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100 block">
                            Validación de Pedido
                        </span>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2">
                            Folio #{{ $sale->folio }}
                        </h2>
                    </div>
                </div>

                <!-- Badge de Estatus de Almacén -->
                <div class="self-start sm:self-auto">
                    @if($sale->almacen_status == 'pending')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-400 text-amber-950 shadow-sm border border-amber-300">
                            <span class="w-2 h-2 mr-1.5 rounded-full bg-amber-900 animate-pulse"></span>
                            Pendiente en Almacén
                        </span>
                    @elseif($sale->almacen_status == 'confirmed')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-300 text-emerald-950 shadow-sm border border-emerald-200">
                            <i class="ri-checkbox-circle-fill mr-1 text-sm text-emerald-900"></i>
                            Confirmada / Despachada
                        </span>
                    @elseif($sale->almacen_status == 'postponed')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-sky-300 text-sky-950 shadow-sm border border-sky-200">
                            <i class="ri-time-line mr-1 text-sm text-sky-900"></i>
                            Pospuesta
                        </span>
                    @elseif($sale->almacen_status == 'cancelled')
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-rose-300 text-rose-950 shadow-sm border border-rose-200">
                            <i class="ri-close-circle-line mr-1 text-sm text-rose-900"></i>
                            Cancelada
                        </span>
                    @else
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-200 text-slate-800">
                            {{ ucfirst($sale->almacen_status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 lg:p-8">
            <!-- Alerta de Ediciones Recientes -->
            @if(isset($recentEdits) && $recentEdits->isNotEmpty())
            <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-amber-50/90 border border-amber-200/90 shadow-sm" role="alert">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-base flex-shrink-0">
                            <i class="ri-alert-fill"></i>
                        </div>
                        <h4 class="text-sm font-bold text-amber-950">
                            Alerta: Este pedido ha sido editado
                        </h4>
                    </div>
                    <span class="self-start sm:self-auto text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 border border-amber-300">
                        Modificado
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-amber-900 mb-3 leading-relaxed">
                    Se registraron modificaciones posteriores a la creación de este pedido. Por favor verifica los cambios realizados antes de confirmar o despachar la mercancía:
                </p>
                <div class="flex flex-col gap-2">
                    @foreach($recentEdits as $edit)
                    <div class="bg-white p-3 rounded-xl border border-amber-200/70 shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                            <span class="font-bold text-xs sm:text-sm text-slate-800">{{ $edit['message'] }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($edit['created_at'])->diffForHumans() }} ({{ \Carbon\Carbon::parse($edit['created_at'])->format('d/m/Y H:i') }})</span>
                        </div>
                        @if(!empty($edit['changes']))
                        <ul class="mt-1 pl-4 space-y-0.5 text-xs text-slate-600 list-disc">
                            @foreach($edit['changes'] as $change)
                                <li><strong>{{ $change }}</strong></li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Grid de Información de la Venta -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <!-- Tarjeta: Cliente / Prospecto -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 sm:p-4 flex items-start space-x-3 transition hover:border-slate-300">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                        <i class="ri-user-star-line"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block mb-0.5">Cliente / Prospecto</span>
                        <h4 class="text-sm font-bold text-slate-800 truncate" title="{{ $sale->customer ? $sale->customer->name : ($sale->prospect ? $sale->prospect->name : 'N/A') }}">
                            {{ $sale->customer ? $sale->customer->name : ($sale->prospect ? $sale->prospect->name : 'N/A') }}
                        </h4>
                        <span class="text-[11px] font-medium text-slate-500">
                            {{ $sale->is_customer ? 'Cliente Registrado' : 'Prospecto Comercial' }}
                        </span>
                    </div>
                </div>

                <!-- Tarjeta: Vendedor -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 sm:p-4 flex items-start space-x-3 transition hover:border-slate-300">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                        <i class="ri-user-follow-line"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block mb-0.5">Vendedor Asignado</span>
                        <h4 class="text-sm font-bold text-slate-800 truncate" title="{{ $sale->user->name ?? ($sale->seller ?? 'N/A') }}">
                            {{ $sale->user->name ?? ($sale->seller ?? 'N/A') }}
                        </h4>
                        <span class="text-[11px] font-medium text-slate-500">
                            Equipo Comercial
                        </span>
                    </div>
                </div>

                <!-- Tarjeta: Fecha de Venta -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 sm:p-4 flex items-start space-x-3 transition hover:border-slate-300">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block mb-0.5">Fecha del Pedido</span>
                        <h4 class="text-sm font-bold text-slate-800 font-mono">
                            {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}
                        </h4>
                        <span class="text-[11px] font-medium text-slate-500">
                            {{ \Carbon\Carbon::parse($sale->date)->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Tarjeta: Tipo y Condiciones -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 sm:p-4 flex items-start space-x-3 transition hover:border-slate-300">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                        <i class="ri-bank-card-line"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block mb-0.5">Tipo de Venta</span>
                        <h4 class="text-sm font-bold text-slate-800">
                            {{ ucfirst($sale->sale_type ?? 'N/A') }}
                        </h4>
                        <span class="text-[11px] font-medium text-slate-500 truncate block">
                            {{ $sale->term ?: 'Contado' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Banner de Notificación si está Pospuesta o Cancelada -->
            @if($sale->almacen_status == 'postponed')
            <div class="mb-6 p-4 rounded-xl bg-sky-50 border border-sky-200 flex items-start space-x-3">
                <i class="ri-time-line text-sky-600 text-xl mt-0.5"></i>
                <div class="text-xs sm:text-sm text-sky-950">
                    <strong class="font-bold">Venta Pospuesta:</strong> 
                    Recordatorio programado para: <strong class="underline">{{ $sale->almacen_postponed_date }}</strong>
                    @if($sale->almacen_comment)
                        <p class="mt-1 text-sky-800">Motivo: <em>{{ $sale->almacen_comment }}</em></p>
                    @endif
                </div>
            </div>
            @elseif($sale->almacen_status == 'cancelled')
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start space-x-3">
                <i class="ri-close-circle-line text-rose-600 text-xl mt-0.5"></i>
                <div class="text-xs sm:text-sm text-rose-950">
                    <strong class="font-bold">Venta Cancelada:</strong> 
                    @if($sale->almacen_comment)
                        <p class="mt-1 text-rose-800">Motivo: <em>{{ $sale->almacen_comment }}</em></p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Sección de Productos -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                        <i class="ri-box-3-fill text-emerald-600 mr-2 text-lg"></i> Productos a Despachar
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg border border-slate-200">
                        Total: {{ $sale->products->count() }} producto(s)
                    </span>
                </div>

                <!-- Vista de Tabla para Pantallas Medianas y Grandes (Desktop) -->
                <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-200 shadow-xs">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-white uppercase bg-slate-800 tracking-wider">
                            <tr>
                                <th scope="col" class="px-5 py-3.5">Producto</th>
                                <th scope="col" class="px-5 py-3.5">Lote en Pedido</th>
                                @if(isset($output) && $output)
                                    <th scope="col" class="px-5 py-3.5">Lote Despachado (Inventario)</th>
                                @endif
                                <th scope="col" class="px-5 py-3.5 text-right">Cantidad Requerida</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($sale->products as $product)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-800">
                                        {{ $product->pivot->public_product_name ?: $product->name }}
                                    </div>
                                    @if($product->sku)
                                    <span class="text-xs text-slate-400 font-mono">SKU: {{ $product->sku }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-mono font-medium text-slate-700">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-xs">
                                        {{ $product->pivot->public_batch ?: 'Sin Lote Asignado' }}
                                    </span>
                                </td>
                                @if(isset($output) && $output)
                                    <td class="px-5 py-4">
                                        @php
                                            $outProd = $output->products->where('product_id', $product->product_id)->first();
                                        @endphp
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono text-xs font-semibold">
                                            {{ $outProd ? ($outProd->pivot->warehouse_batch ?: 'N/A') : 'N/A' }}
                                        </span>
                                    </td>
                                @endif
                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ number_format((float) $product->pivot->quantity, 2) }} {{ $product->unit ?: 'uds' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Vista de Tarjetas para Dispositivos Móviles (Celulares) -->
                <div class="block sm:hidden space-y-3">
                    @foreach($sale->products as $product)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">
                                    {{ $product->pivot->public_product_name ?: $product->name }}
                                </h4>
                                @if($product->sku)
                                    <span class="text-[11px] text-slate-400 font-mono block">SKU: {{ $product->sku }}</span>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex-shrink-0">
                                {{ number_format((float) $product->pivot->quantity, 2) }} {{ $product->unit ?: 'uds' }}
                            </span>
                        </div>
                        
                        <div class="pt-2 border-t border-slate-200/80 flex flex-col gap-1.5 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Lote Pedido:</span>
                                <span class="font-mono font-bold text-slate-700 px-2 py-0.5 rounded bg-white border border-slate-200">
                                    {{ $product->pivot->public_batch ?: 'N/A' }}
                                </span>
                            </div>
                            @if(isset($output) && $output)
                                @php
                                    $outProd = $output->products->where('product_id', $product->product_id)->first();
                                @endphp
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 font-medium">Lote Despachado:</span>
                                    <span class="font-mono font-bold text-emerald-700 px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">
                                        {{ $outProd ? ($outProd->pivot->warehouse_batch ?: 'N/A') : 'N/A' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Botones de Acción (Almacén / Admin) -->
            @if($sale->almacen_status == 'pending' && Auth::user()->hasAnyRole(['Warehouse', 'Admin']))
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5">
                @if(Auth::user()->hasRole('Admin'))
                    <button type="button" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-rose-300 hover:bg-rose-50 text-rose-700 rounded-xl text-xs sm:text-sm font-semibold transition text-center cursor-pointer open-modal shadow-xs" data-target="cancelModal">
                        <i class="ri-close-circle-line mr-1 text-sm"></i> Cancelar Venta
                    </button>
                @endif
                <button type="button" class="w-full sm:w-auto px-4 py-2.5 bg-amber-50 border border-amber-300 hover:bg-amber-100 text-amber-800 rounded-xl text-xs sm:text-sm font-semibold transition text-center cursor-pointer open-modal shadow-xs" data-target="postponeModal">
                    <i class="ri-time-line mr-1 text-sm"></i> Posponer Venta
                </button>
                <button type="button" class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 active:scale-95 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition-all text-center cursor-pointer flex items-center justify-center" id="btnConfirmSale">
                    <i class="ri-checkbox-circle-fill mr-1.5 text-base"></i> Confirmar y Descontar Inventario
                </button>
            </div>
            @endif

            @if($sale->almacen_status == 'postponed' && Auth::user()->hasAnyRole(['Warehouse', 'Admin']))
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5">
                @if(Auth::user()->hasRole('Admin'))
                    <button type="button" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-rose-300 hover:bg-rose-50 text-rose-700 rounded-xl text-xs sm:text-sm font-semibold transition text-center cursor-pointer open-modal shadow-xs" data-target="cancelModal">
                        <i class="ri-close-circle-line mr-1 text-sm"></i> Cancelar Venta
                    </button>
                @endif
                <button type="button" class="w-full sm:w-auto px-4 py-2.5 bg-amber-50 border border-amber-300 hover:bg-amber-100 text-amber-800 rounded-xl text-xs sm:text-sm font-semibold transition text-center cursor-pointer open-modal shadow-xs" data-target="postponeModal">
                    <i class="ri-time-line mr-1 text-sm"></i> Posponer de Nuevo
                </button>
                <button type="button" class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 active:scale-95 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition-all text-center cursor-pointer flex items-center justify-center" id="btnMarkReady">
                    <i class="ri-check-double-line mr-1.5 text-base"></i> Mercancía Lista - Confirmar Salida
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Posponer -->
<x-modal id="postponeModal" maxWidth="lg">
    <div class="w-full">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-base">
                    <i class="ri-time-line"></i>
                </div>
                <h5 class="text-base sm:text-lg font-bold text-slate-800">Posponer Venta</h5>
            </div>
            <button type="button" class="close-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Fecha de recordatorio</label>
                <p class="text-xs text-slate-500 mb-2">Se notificará en el sistema en esta fecha para revisar la salida.</p>
                <input type="date" id="postponeDate" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-amber-500 focus:ring-amber-500 text-xs sm:text-sm p-3">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Motivo de aplazamiento</label>
                <textarea id="postponeReason" rows="3" placeholder="Describe brevemente por qué se pospone..." class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-amber-500 focus:ring-amber-500 text-xs sm:text-sm p-3"></textarea>
            </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row justify-end mt-6 gap-2 pt-3 border-t border-slate-100">
            <button type="button" class="close-modal w-full sm:w-auto px-4 py-2.5 bg-slate-100 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-slate-200 transition cursor-pointer">
                Cancelar
            </button>
            <button type="button" id="btnSavePostpone" class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:bg-amber-600 shadow-sm transition cursor-pointer">
                Guardar y Posponer
            </button>
        </div>
    </div>
</x-modal>

<!-- Modal Cancelar -->
<x-modal id="cancelModal" maxWidth="lg">
    <div class="w-full">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-base">
                    <i class="ri-close-circle-line"></i>
                </div>
                <h5 class="text-base sm:text-lg font-bold text-slate-800">Cancelar Venta</h5>
            </div>
            <button type="button" class="close-modal text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Motivo de cancelación</label>
            <p class="text-xs text-slate-500 mb-2">Ingresa una justificación para el registro de auditoría de almacén.</p>
            <textarea id="cancelReason" rows="3" placeholder="Escribe el motivo de la cancelación..." class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-rose-500 focus:ring-rose-500 text-xs sm:text-sm p-3"></textarea>
        </div>
        <div class="flex flex-col-reverse sm:flex-row justify-end mt-6 gap-2 pt-3 border-t border-slate-100">
            <button type="button" class="close-modal w-full sm:w-auto px-4 py-2.5 bg-slate-100 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl hover:bg-slate-200 transition cursor-pointer">
                Cerrar
            </button>
            <button type="button" id="btnSaveCancel" class="w-full sm:w-auto px-5 py-2.5 bg-rose-600 text-white text-xs sm:text-sm font-bold rounded-xl hover:bg-rose-700 shadow-sm transition cursor-pointer">
                Confirmar Cancelación
            </button>
        </div>
    </div>
</x-modal>

<!-- Modal Selección de Lotes Responsivo y Estilizado -->
<x-modal id="lotSelectionModal" maxWidth="3xl">
    <div class="w-full">
        <!-- Cabecera del Marco de Lotes -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-3 mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-green-500 text-white flex items-center justify-center font-bold text-xl shadow-xs flex-shrink-0">
                    <i class="ri-archive-stack-fill"></i>
                </div>
                <div>
                    <h4 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">
                        Seleccionar Lotes para Descontar
                    </h4>
                    <p class="text-xs text-slate-500">
                        Asigna el lote y ubicación física de donde se extraerá el producto
                    </p>
                </div>
            </div>
            <button type="button" class="close-modal text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Banner de Ayuda Informativa -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 rounded-xl p-3 sm:p-3.5 mb-4 flex items-start space-x-2.5">
            <i class="ri-information-line text-emerald-700 text-lg flex-shrink-0 mt-0.5"></i>
            <p class="text-xs text-emerald-900 leading-relaxed">
                Selecciona la barcina/tarima de almacén correspondiente. El sistema valida automáticamente que el stock físico cubra la cantidad requerida del pedido.
            </p>
        </div>

        <!-- Contenedor Dinámico de Productos y Lotes -->
        <div id="lotSelectionBody" class="space-y-4 max-h-[60vh] sm:max-h-[65vh] overflow-y-auto pr-1">
            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                <i class="ri-loader-4-line text-3xl animate-spin mb-2 text-emerald-600"></i>
                <p class="text-xs sm:text-sm font-medium">Cargando existencias y lotes disponibles en almacén...</p>
            </div>
        </div>

        <!-- Footer con Acciones -->
        <div class="flex flex-col-reverse sm:flex-row justify-end mt-6 gap-2.5 pt-4 border-t border-slate-100">
            <button type="button" class="close-modal w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer text-center">
                Cancelar
            </button>
            <button type="button" id="btnConfirmLots" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 active:scale-95 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition cursor-pointer flex items-center justify-center">
                <i class="ri-check-line mr-1.5 text-base"></i> Confirmar y Descontar
            </button>
        </div>
    </div>
</x-modal>
@endsection

@push('js')
<script>
$(document).ready(function() {
    function sendAction(action, data = {}) {
        data.action = action;
        data._token = '{{ csrf_token() }}';
        
        $.ajax({
            url: '{{ route('sales.almacen_action', $sale->sale_id) }}',
            type: 'POST',
            data: data,
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        title: '¡Operación Exitosa!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Atención',
                        text: res.message,
                        icon: 'error',
                        confirmButtonColor: '#198754'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error en Servidor',
                    text: xhr.responseJSON?.message || 'Ocurrió un error inesperado al procesar la solicitud.',
                    icon: 'error',
                    confirmButtonColor: '#198754'
                });
            }
        });
    }

    // Al dar clic en Confirmar, cargamos los lotes disponibles
    $('#btnConfirmSale').click(function() {
        $('#lotSelectionBody').html(`
            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                <i class="ri-loader-4-line text-3xl animate-spin mb-2 text-emerald-600"></i>
                <p class="text-xs sm:text-sm font-medium">Cargando existencias y lotes disponibles en almacén...</p>
            </div>
        `);
        $('#lotSelectionModal').removeClass('hidden');

        $.get('{{ route("sales.almacen_lots", $sale->sale_id) }}', function(res) {
            if (!res.products || res.products.length === 0) {
                $('#lotSelectionBody').html(`
                    <div class="p-6 text-center bg-rose-50 rounded-2xl border border-rose-200">
                        <i class="ri-inbox-line text-3xl text-rose-500 mb-2"></i>
                        <p class="text-rose-700 font-bold text-sm">No se encontraron productos registrados para esta venta.</p>
                    </div>
                `);
                return;
            }

            let html = '';
            res.products.forEach((product, index) => {
                let lotsHtml = '';
                const hasLots = product.lots && product.lots.length > 0;

                if (!hasLots) {
                    lotsHtml = `
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs flex items-center space-x-2">
                            <i class="ri-alert-line text-base text-rose-600 flex-shrink-0"></i>
                            <span>No hay barcinas/lotes con stock disponible en ubicaciones de almacén para este producto.</span>
                        </div>
                    `;
                } else {
                    lotsHtml = `
                        <div class="relative">
                            <select name="lot_${index}" class="lot-select block w-full rounded-xl border border-slate-300 bg-white text-slate-800 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-xs sm:text-sm p-3 transition appearance-none cursor-pointer pr-10" data-product-id="${product.product_id}" data-quantity="${product.quantity_required}">
                                <option value="">-- Selecciona lote y ubicación física --</option>
                    `;
                    product.lots.forEach(lot => {
                        const netWeight = parseFloat(lot.net_weight) || 0;
                        const qty = parseFloat(lot.quantity) || 0;
                        const wpu = parseFloat(lot.weight_per_unit) || 0;
                        const bagText = lot.bag_number ? `Barcina #${lot.bag_number} | ` : '';
                        lotsHtml += `<option value="${lot.cli_id}" data-stock="${netWeight}">
                            ${bagText}Lote: ${lot.batch} → ${lot.location_name} (Disp: ${netWeight.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})} kg)
                        </option>`;
                    });
                    lotsHtml += `
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <i class="ri-arrow-down-s-line text-lg"></i>
                            </div>
                        </div>
                    `;
                }

                html += `
                    <div class="p-3.5 sm:p-4 rounded-xl border-2 ${!hasLots ? 'bg-rose-50/50 border-rose-200' : 'bg-slate-50 border-slate-200/90'} transition-all shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-2.5 border-b border-slate-200/80">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                    <i class="ri-box-3-line"></i>
                                </div>
                                <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-snug">
                                    ${window.escapeHtml ? window.escapeHtml(product.product_name) : product.product_name}
                                </h4>
                            </div>
                            <span class="inline-flex items-center self-start sm:self-auto px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200 flex-shrink-0">
                                <i class="ri-scales-3-line mr-1 text-blue-600"></i> Requerido: ${parseFloat(product.quantity_required).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </span>
                        </div>
                        
                        ${lotsHtml}

                        <!-- Mensajes de feedback visual de stock -->
                        <div class="lot-stock-feedback mt-2 hidden"></div>
                    </div>
                `;
            });

            $('#lotSelectionBody').html(html);

            // Validar stock reactivamente al seleccionar lote
            $(document).off('change', '.lot-select').on('change', '.lot-select', function() {
                const selected = $(this).find(':selected');
                const stock = parseFloat(selected.data('stock')) || 0;
                const required = parseFloat($(this).data('quantity')) || 0;
                const feedback = $(this).closest('.p-3.5, .p-4').find('.lot-stock-feedback');

                if (!selected.val()) {
                    feedback.addClass('hidden').html('');
                    return;
                }

                if (stock < required) {
                    feedback.removeClass('hidden').html(`
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                            <i class="ri-error-warning-fill text-rose-600 text-sm flex-shrink-0"></i>
                            <span>Stock insuficiente: Disponible en esta ubicación <strong>${stock.toLocaleString('es-MX')} kg</strong> vs Requerido <strong>${required.toLocaleString('es-MX')} kg</strong>.</span>
                        </div>
                    `);
                } else {
                    feedback.removeClass('hidden').html(`
                        <div class="p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
                            <i class="ri-checkbox-circle-fill text-emerald-600 text-sm flex-shrink-0"></i>
                            <span>Stock disponible verificado (<strong>${stock.toLocaleString('es-MX')} kg</strong> cubren la solicitud).</span>
                        </div>
                    `);
                }
            });
        }).fail(function() {
            $('#lotSelectionBody').html(`
                <div class="p-6 text-center bg-rose-50 rounded-2xl border border-rose-200">
                    <i class="ri-error-warning-line text-3xl text-rose-500 mb-2"></i>
                    <p class="text-rose-700 font-bold text-sm">Error al cargar existencias de almacén. Intenta nuevamente.</p>
                </div>
            `);
        });
    });

    // Confirmar con lotes seleccionados
    $('#btnConfirmLots').click(function() {
        let lotAssignments = [];
        let valid = true;

        if ($('.lot-select').length === 0) {
            Swal.fire({
                title: 'Sin Existencias',
                text: 'No hay productos con lotes disponibles para procesar esta venta.',
                icon: 'warning',
                confirmButtonColor: '#198754'
            });
            return;
        }

        $('.lot-select').each(function() {
            let cliId = $(this).val();
            let quantity = $(this).data('quantity');
            let selected = $(this).find(':selected');
            let stock = parseFloat(selected.data('stock')) || 0;

            if (!cliId) {
                valid = false;
                Swal.fire({
                    title: 'Selección Incompleta',
                    text: 'Debes seleccionar un lote y ubicación física para cada producto de la venta.',
                    icon: 'warning',
                    confirmButtonColor: '#198754'
                });
                return false;
            }

            if (stock < quantity) {
                valid = false;
                Swal.fire({
                    title: 'Stock Insuficiente',
                    text: `El lote seleccionado tiene disponible ${stock} kg, lo cual es menor a los ${quantity} kg requeridos.`,
                    icon: 'error',
                    confirmButtonColor: '#198754'
                });
                return false;
            }

            lotAssignments.push({
                cli_id: cliId,
                quantity: quantity
            });
        });

        if (!valid) return;

        Swal.fire({
            title: '¿Confirmar salida de almacén?',
            text: "Se descontará el inventario del lote y ubicación física seleccionados.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, confirmar y descontar',
            cancelButtonText: 'Revisar'
        }).then((result) => {
            if (result.isConfirmed) {
                sendAction('confirm', { lot_assignments: lotAssignments });
            }
        });
    });

    $('#btnSavePostpone').click(function() {
        let date = $('#postponeDate').val();
        let reason = $('#postponeReason').val();
        if(!date || !reason) {
            return Swal.fire({
                title: 'Campos Obligatorios',
                text: 'Por favor ingresa tanto la fecha de recordatorio como el motivo.',
                icon: 'warning',
                confirmButtonColor: '#198754'
            });
        }
        sendAction('postpone', {date, reason});
    });

    $('#btnSaveCancel').click(function() {
        let reason = $('#cancelReason').val();
        if(!reason) {
            return Swal.fire({
                title: 'Motivo Requerido',
                text: 'Por favor describe el motivo de la cancelación de la venta.',
                icon: 'warning',
                confirmButtonColor: '#198754'
            });
        }
        sendAction('cancel', {reason});
    });

    // Marcar como lista (cambia de postponed → pending para poder confirmar)
    $('#btnMarkReady').click(function() {
        Swal.fire({
            title: '¿Mercancía lista?',
            text: "La venta pasará a estado pendiente y podrás confirmar la salida del inventario.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, marcar como lista',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                sendAction('mark_ready');
            }
        });
    });
});
</script>
@endpush
