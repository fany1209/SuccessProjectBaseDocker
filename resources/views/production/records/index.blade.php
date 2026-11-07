@extends('layouts.app')

@section('content')
@php
    $fertilProdCount = $productions->where('area', 'fertil')->count();
    $vitayelaProdCount = $productions->where('area', 'vitayela')->count();
    $lowStockItems = $inventories->filter(function($i) {
        return $i->cantidad <= $i->stock_min && $i->stock_min > 0;
    });

    $todayStr = \Carbon\Carbon::today()->format('Y-m-d');

    $buildProdKpi = function($collection) use ($todayStr) {
        $todayCount = $collection->filter(function($p) use ($todayStr) {
            $prepDate = $p->fecha_preparacion ? \Carbon\Carbon::parse($p->fecha_preparacion)->format('Y-m-d') : null;
            $createdDate = $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('Y-m-d') : null;
            return $prepDate === $todayStr || $createdDate === $todayStr;
        })->count();

        return [
            'registros'     => $collection->count(),
            'registros_hoy' => $todayCount,
            'kg_preparados' => (float) $collection->sum('kg_preparados'),
            'kg_ensacados'  => (float) $collection->sum('kg_ensacados'),
            'total_sacos'   => (int) $collection->sum('num_sacos'),
        ];
    };

    $buildInvKpi = function($collection) {
        $lowCount = $collection->filter(function($i) {
            return $i->cantidad <= $i->stock_min && $i->stock_min > 0;
        })->count();

        return [
            'total_insumos'     => $collection->count(),
            'stock_bajo'        => $lowCount,
            'total_existencias' => (float) $collection->sum('cantidad'),
        ];
    };

    $kpiStats = [
        'all' => [
            'prod' => $buildProdKpi($productions),
            'inv'  => $buildInvKpi($inventories),
        ],
        'fertil' => [
            'prod' => $buildProdKpi($productions->where('area', 'fertil')),
            'inv'  => $buildInvKpi($inventories->where('area', 'fertil')),
        ],
        'vitayela' => [
            'prod' => $buildProdKpi($productions->where('area', 'vitayela')),
            'inv'  => $buildInvKpi($inventories->where('area', 'vitayela')),
        ],
    ];
@endphp

<div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Encabezado Principal -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                Módulo de Producción
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">Fertil &amp; Vitayela</span>
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Bitácora de preparación, ensacado, control de stock local y envíos a almacén en un solo lugar.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openCreateProduction()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow flex items-center gap-1.5 cursor-pointer transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Registrar Producción
            </button>
            <button type="button" onclick="openRequestModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow flex items-center gap-1.5 cursor-pointer transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Solicitar Material a Almacén
            </button>
            <a href="{{ url('production') }}" class="text-gray-700 hover:text-gray-900 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg font-semibold text-sm shadow transition-colors">
                Volver a Formatos
            </a>
        </div>
    </div>

    <!-- Banner Compacto No Intrusivo de Stock Bajo -->
    @if($lowStockItems->count() > 0)
        <div id="low-stock-banner" class="mb-5 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <h4 class="text-sm font-bold text-amber-900">Atención: {{ $lowStockItems->count() }} producto(s) con stock mínimo alcanzado</h4>
                    <div class="text-xs text-amber-800 mt-1 flex flex-wrap gap-2">
                        @foreach($lowStockItems as $item)
                            <span class="inline-flex items-center gap-1 bg-white border border-amber-300 px-2 py-0.5 rounded font-medium">
                                <span class="uppercase text-[10px] font-bold px-1 rounded {{ $item->area === 'fertil' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800' }}">{{ $item->area }}</span>
                                {{ $item->producto_descripcion }}: <strong class="text-red-600">{{ $item->cantidad }}</strong> / mín {{ $item->stock_min }} {{ $item->unidad }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openRequestModalWithLowStock()" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3 py-2 rounded-md shadow-xs cursor-pointer">
                    Solicitar Faltantes
                </button>
                <button type="button" onclick="document.getElementById('low-stock-banner').classList.add('hidden')" class="text-amber-700 hover:text-amber-900 text-xs font-semibold px-2 py-1">
                    Ocultar
                </button>
            </div>
        </div>
    @endif

    <!-- Barra Unificada de Filtro por Área + Pestañas de Vista -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 mb-5 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <!-- Selector Rápido de Línea (Pills) -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 mr-1">Línea:</span>
            <button type="button" id="filter-area-all" onclick="setAreaFilter('all')" class="area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-800 text-white shadow-xs">
                Todas ({{ $productions->count() }})
            </button>
            <button type="button" id="filter-area-fertil" onclick="setAreaFilter('fertil')" class="area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-green-600 inline-block"></span>
                Fertil ({{ $fertilProdCount }})
            </button>
            <button type="button" id="filter-area-vitayela" onclick="setAreaFilter('vitayela')" class="area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>
                Vitayela ({{ $vitayelaProdCount }})
            </button>
        </div>

        <!-- Selector de Pestañas (Producción vs Inventario) -->
        <div class="flex items-center bg-gray-100 p-1 rounded-lg w-full lg:w-auto">
            <button type="button" id="produccion-tab" onclick="switchTab('produccion')" class="flex-1 lg:flex-initial px-4 py-2 rounded-md text-sm font-bold transition-all cursor-pointer bg-white text-green-700 shadow-xs">
                1. Bitácora de Producción
            </button>
            <button type="button" id="inventario-tab" onclick="switchTab('inventario')" class="flex-1 lg:flex-initial px-4 py-2 rounded-md text-sm font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                2. Inventario y Envíos a Almacén ({{ $inventories->count() }})
            </button>
        </div>
    </div>

    <!-- SECCIÓN DE KPIs DINÁMICOS (Debajo del selector de línea) -->
    <!-- KPIs Sección 1: Bitácora de Producción -->
    <div id="kpi-produccion" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <!-- Registros Diarios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Registros Diarios</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 id="kpi-prod-registros" class="text-2xl font-extrabold text-gray-900">{{ number_format($kpiStats['all']['prod']['registros']) }}</h3>
                    <span id="kpi-prod-registros-hoy" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                        Hoy: {{ $kpiStats['all']['prod']['registros_hoy'] }}
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 mt-0.5">Corridas registradas en bitácora</p>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
        </div>

        <!-- Kg Preparados -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Kg Preparados</p>
                <h3 id="kpi-prod-kg-preparados" class="text-2xl font-extrabold text-amber-600 mt-1">
                    {{ number_format($kpiStats['all']['prod']['kg_preparados'], 2) }} <span class="text-sm font-semibold text-gray-500">kg</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Volumen total mezclado</p>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
        </div>

        <!-- Kg Ensacados -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Kg Ensacados</p>
                <h3 id="kpi-prod-kg-ensacados" class="text-2xl font-extrabold text-emerald-600 mt-1">
                    {{ number_format($kpiStats['all']['prod']['kg_ensacados'], 2) }} <span class="text-sm font-semibold text-gray-500">kg</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Producto terminado empacado</p>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Total de Sacos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total de Sacos</p>
                <h3 id="kpi-prod-total-sacos" class="text-2xl font-extrabold text-indigo-600 mt-1">
                    {{ number_format($kpiStats['all']['prod']['total_sacos']) }} <span class="text-sm font-semibold text-gray-500">sacos</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Unidades totales ensacadas</p>
            </div>
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>
    </div>

    <!-- KPIs Sección 2: Inventario de Insumos y Stock Local -->
    <div id="kpi-inventario" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5 hidden">
        <!-- Total de Insumos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total de Insumos</p>
                <h3 id="kpi-inv-total-insumos" class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($kpiStats['all']['inv']['total_insumos']) }} <span class="text-sm font-semibold text-gray-500">productos</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Ítems registrados en catálogo local</p>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </div>
        </div>

        <!-- Insumos con Stock Bajo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Insumos con Stock Bajo</p>
                <h3 id="kpi-inv-stock-bajo" class="text-2xl font-extrabold {{ $kpiStats['all']['inv']['stock_bajo'] > 0 ? 'text-red-600' : 'text-emerald-600' }} mt-1">
                    {{ number_format($kpiStats['all']['inv']['stock_bajo']) }} <span class="text-sm font-semibold text-gray-500">en alerta</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Productos en o debajo del mínimo</p>
            </div>
            <div id="kpi-inv-stock-bajo-icon" class="p-3 rounded-xl {{ $kpiStats['all']['inv']['stock_bajo'] > 0 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>

        <!-- Total de Existencias -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total de Existencias</p>
                <h3 id="kpi-inv-total-existencias" class="text-2xl font-extrabold text-teal-600 mt-1">
                    {{ number_format($kpiStats['all']['inv']['total_existencias'], 2) }} <span class="text-sm font-semibold text-gray-500">unidades / kg</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Suma acumulada de stock disponible</p>
            </div>
            <div class="p-3 rounded-xl bg-teal-50 text-teal-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <span class="font-medium">¡Éxito!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Contenido de Pestañas -->
    <div id="unifiedTabContent">
        <!-- TAB 1: Bitácora de Producción -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border border-gray-100" id="produccion">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Registros de Producción</h3>
                    <p class="text-xs text-gray-500">Puedes registrar primero la preparación y completar el ensacado después con un solo clic.</p>
                </div>
                <button type="button" onclick="openCreateProduction()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-xs cursor-pointer">
                    + Nuevo Registro de Producción
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="production-table" class="display w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th>ID</th>
                            <th>Línea / Área</th>
                            <th>Producto / Descripción</th>
                            <th>Fecha Preparación</th>
                            <th>Kg Preparados</th>
                            <th>Fecha Ensacado</th>
                            <th>Kg Ensacados</th>
                            <th># Sacos</th>
                            <th>Estatus</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productions as $p)
                        @php
                            $isBagged = !empty($p->fecha_ensacado) || (!empty($p->num_sacos) && $p->num_sacos > 0) || (!empty($p->kg_ensacados) && $p->kg_ensacados > 0);
                        @endphp
                        <tr>
                            <td class="font-semibold text-gray-900">#{{ $p->production_record_id }}</td>
                            <td>
                                @if($p->area === 'fertil')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300">
                                        FERTIL
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                        VITAYELA
                                    </span>
                                @endif
                            </td>
                            <td class="font-medium text-gray-900">{{ $p->descripcion ?: 'Sin descripción' }}</td>
                            <td>{{ $p->fecha_preparacion ? \Carbon\Carbon::parse($p->fecha_preparacion)->format('d/m/Y') : '-' }}</td>
                            <td class="font-semibold">{{ $p->kg_preparados !== null ? number_format((float)$p->kg_preparados, 2) . ' kg' : '-' }}</td>
                            <td>{{ $p->fecha_ensacado ? \Carbon\Carbon::parse($p->fecha_ensacado)->format('d/m/Y') : '-' }}</td>
                            <td class="font-semibold text-emerald-700">{{ $p->kg_ensacados !== null ? number_format((float)$p->kg_ensacados, 2) . ' kg' : '-' }}</td>
                            <td class="font-bold text-gray-900">{{ $p->num_sacos !== null ? $p->num_sacos : '-' }}</td>
                            <td>
                                @if($isBagged)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Ensacado
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        En Preparación
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end items-center gap-1.5">
                                    @if(!$isBagged)
                                        <button type="button" onclick='editProduction(@json($p), true)' class="text-white bg-emerald-600 hover:bg-emerald-700 rounded-md px-2.5 py-1.5 text-xs font-bold flex items-center gap-1 cursor-pointer shadow-2xs" title="Completar Ensacado">
                                            Ensacar
                                        </button>
                                    @endif
                                    <button type="button" onclick='editProduction(@json($p), false)' class="text-white bg-blue-500 hover:bg-blue-600 rounded-md px-2.5 py-1.5 text-xs font-semibold flex items-center gap-1 cursor-pointer" title="Editar">
                                        <img class="w-3.5 h-3.5" src="{{ asset('images/editar.png') }}" alt="Editar"/>
                                        <span class="hidden xl:inline">Editar</span>
                                    </button>
                                    <form action="{{ route('production.records.destroyProduction', $p->production_record_id) }}" method="POST" class="m-0 inline-flex" onsubmit="confirmDelete(event, this, 'registro');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-1.5 flex items-center justify-center cursor-pointer" title="Eliminar">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: Inventario y Stock Local -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border border-gray-100 hidden" id="inventario">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Inventario Local y Envíos a Almacén</h3>
                    <p class="text-xs text-gray-500">Gestiona las existencias en piso de producción, registra salidas o envía producto terminado a almacén.</p>
                </div>
                <button type="button" onclick="openCreateInventory()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-xs cursor-pointer">
                    + Nuevo Producto en Inventario
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="inventory-table" class="display w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th>ID</th>
                            <th>Línea / Área</th>
                            <th>Descripción o Producto</th>
                            <th>Stock Actual</th>
                            <th>Unidad</th>
                            <th>Stock Mínimo</th>
                            <th class="text-right">Acciones Rápidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inv)
                        <tr>
                            <td class="font-semibold text-gray-900">#{{ $inv->production_inventory_id }}</td>
                            <td>
                                @if($inv->area === 'fertil')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300">
                                        FERTIL
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                        VITAYELA
                                    </span>
                                @endif
                            </td>
                            <td class="font-semibold text-gray-900">{{ $inv->producto_descripcion }}</td>
                            <td>
                                @if($inv->cantidad <= $inv->stock_min && $inv->stock_min > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 font-bold border border-red-200">
                                        {{ $inv->cantidad }} (Bajo)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                        {{ $inv->cantidad }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $inv->unidad ?: '-' }}</td>
                            <td>{{ $inv->stock_min }}</td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end items-center gap-1.5">
                                    <button type="button" onclick='openOutputInventory(@json($inv))' class="text-white bg-emerald-600 hover:bg-emerald-700 rounded-md px-2.5 py-1.5 text-xs font-semibold flex items-center gap-1 cursor-pointer" title="Registrar Salida de Stock">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        <span>Salida</span>
                                    </button>
                                    <button type="button" onclick='openTransferModal(@json($inv))' class="text-white bg-teal-600 hover:bg-teal-700 rounded-md px-2.5 py-1.5 text-xs font-semibold flex items-center gap-1 cursor-pointer" title="Enviar Producto Terminado a Almacén">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        <span>Enviar a Almacén</span>
                                    </button>
                                    <button type="button" onclick="openHistoryModal({{ $inv->production_inventory_id }}, '{{ addslashes($inv->producto_descripcion) }}')" class="text-white bg-amber-500 hover:bg-amber-600 rounded-md px-2.5 py-1.5 text-xs font-semibold flex items-center gap-1 cursor-pointer" title="Ver Historial de Movimientos">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="hidden xl:inline">Historial</span>
                                    </button>
                                    <button type="button" onclick='editInventory(@json($inv))' class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-1.5 flex items-center justify-center cursor-pointer" title="Editar Producto">
                                        <img class="w-4 h-4" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                    </button>
                                    <form action="{{ route('production.records.destroyInventory', $inv->production_inventory_id) }}" method="POST" class="m-0 inline-flex" onsubmit="confirmDelete(event, this, 'producto');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-1.5 flex items-center justify-center cursor-pointer" title="Eliminar Producto">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('production.records.modals.production_modal')
@include('production.records.modals.inventory_modal')
@include('production.records.modals.output_modal')
@include('production.records.modals.history_modal')
@include('production.records.modals.request_modal')
@include('production.records.modals.transfer_modal')

@endsection

<script>
    let prodDataTable = null;
    let invDataTable = null;
    const kpiStatsByArea = @json($kpiStats);
    window.currentAreaFilter = "{{ $selectedArea ?: '' }}" || sessionStorage.getItem('prod_unified_area') || 'all';

    function formatNumber(num, decimals = 0) {
        return Number(num || 0).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function updateKpiCards(area) {
        const stats = kpiStatsByArea[area] || kpiStatsByArea['all'];
        if (!stats) return;

        // Actualizar KPIs de Producción
        document.getElementById('kpi-prod-registros').innerText = formatNumber(stats.prod.registros, 0);
        document.getElementById('kpi-prod-registros-hoy').innerText = `Hoy: ${formatNumber(stats.prod.registros_hoy, 0)}`;
        document.getElementById('kpi-prod-kg-preparados').innerHTML = `${formatNumber(stats.prod.kg_preparados, 2)} <span class="text-sm font-semibold text-gray-500">kg</span>`;
        document.getElementById('kpi-prod-kg-ensacados').innerHTML = `${formatNumber(stats.prod.kg_ensacados, 2)} <span class="text-sm font-semibold text-gray-500">kg</span>`;
        document.getElementById('kpi-prod-total-sacos').innerHTML = `${formatNumber(stats.prod.total_sacos, 0)} <span class="text-sm font-semibold text-gray-500">sacos</span>`;

        // Actualizar KPIs de Inventario / Insumos
        document.getElementById('kpi-inv-total-insumos').innerHTML = `${formatNumber(stats.inv.total_insumos, 0)} <span class="text-sm font-semibold text-gray-500">productos</span>`;
        
        const lowEl = document.getElementById('kpi-inv-stock-bajo');
        const lowIconEl = document.getElementById('kpi-inv-stock-bajo-icon');
        lowEl.innerHTML = `${formatNumber(stats.inv.stock_bajo, 0)} <span class="text-sm font-semibold text-gray-500">en alerta</span>`;
        if (stats.inv.stock_bajo > 0) {
            lowEl.className = 'text-2xl font-extrabold text-red-600 mt-1';
            lowIconEl.className = 'p-3 rounded-xl bg-red-50 text-red-600';
        } else {
            lowEl.className = 'text-2xl font-extrabold text-emerald-600 mt-1';
            lowIconEl.className = 'p-3 rounded-xl bg-emerald-50 text-emerald-600';
        }

        document.getElementById('kpi-inv-total-existencias').innerHTML = `${formatNumber(stats.inv.total_existencias, 2)} <span class="text-sm font-semibold text-gray-500">unidades / kg</span>`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        prodDataTable = $('#production-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25
        });

        invDataTable = $('#inventory-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25
        });

        // Restaurar pestaña activa tras recarga
        const savedTab = sessionStorage.getItem('prod_unified_tab') || 'produccion';
        switchTab(savedTab);

        // Aplicar filtro de línea inicial
        setAreaFilter(window.currentAreaFilter);
    });

    function setAreaFilter(area) {
        if (!['all', 'fertil', 'vitayela'].includes(area)) {
            area = 'all';
        }
        window.currentAreaFilter = area;
        sessionStorage.setItem('prod_unified_area', area);

        // Actualizar estilos de los botones pill
        const btnAll = document.getElementById('filter-area-all');
        const btnFertil = document.getElementById('filter-area-fertil');
        const btnVitayela = document.getElementById('filter-area-vitayela');

        btnAll.className = 'area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-100 text-gray-700 hover:bg-gray-200';
        btnFertil.className = 'area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-100 text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center gap-1.5';
        btnVitayela.className = 'area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 flex items-center gap-1.5';

        if (area === 'all') {
            btnAll.className = 'area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-gray-800 text-white shadow-xs';
        } else if (area === 'fertil') {
            btnFertil.className = 'area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-green-600 text-white shadow-xs flex items-center gap-1.5';
        } else if (area === 'vitayela') {
            btnVitayela.className = 'area-pill px-4 py-2 rounded-lg text-sm font-bold transition-all cursor-pointer bg-indigo-600 text-white shadow-xs flex items-center gap-1.5';
        }

        const searchTerm = area === 'all' ? '' : area.toUpperCase();
        if (prodDataTable) {
            prodDataTable.column(1).search(searchTerm).draw();
        }
        if (invDataTable) {
            invDataTable.column(1).search(searchTerm).draw();
        }

        updateKpiCards(area);
    }

    function switchTab(tabName) {
        sessionStorage.setItem('prod_unified_tab', tabName);

        document.getElementById('produccion').classList.add('hidden');
        document.getElementById('inventario').classList.add('hidden');
        document.getElementById('kpi-produccion').classList.add('hidden');
        document.getElementById('kpi-inventario').classList.add('hidden');

        const prodTab = document.getElementById('produccion-tab');
        const invTab = document.getElementById('inventario-tab');

        prodTab.className = "flex-1 lg:flex-initial px-4 py-2 rounded-md text-sm font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900";
        invTab.className = "flex-1 lg:flex-initial px-4 py-2 rounded-md text-sm font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900";

        document.getElementById(tabName).classList.remove('hidden');

        if (tabName === 'produccion') {
            document.getElementById('kpi-produccion').classList.remove('hidden');
            prodTab.className = "flex-1 lg:flex-initial px-4 py-2 rounded-md text-sm font-bold transition-all cursor-pointer bg-white text-green-700 shadow-xs";
            if (prodDataTable) prodDataTable.columns.adjust();
        } else {
            document.getElementById('kpi-inventario').classList.remove('hidden');
            invTab.className = "flex-1 lg:flex-initial px-4 py-2 rounded-md text-sm font-bold transition-all cursor-pointer bg-white text-green-700 shadow-xs";
            if (invDataTable) invDataTable.columns.adjust();
        }
    }

    function confirmDelete(event, form, type) {
        event.preventDefault();
        const textMsg = type === 'producto'
            ? '¿Estás seguro de eliminar este producto del inventario?'
            : '¿Estás seguro de eliminar este registro de producción?';

        Swal.fire({
            title: '¿Estás seguro?',
            text: textMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(form);
                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message || 'El registro ha sido eliminado correctamente.',
                            timer: 1400,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Ocurrió un problema al intentar eliminar.'
                        });
                    }
                });
            }
        });
    }
</script>

