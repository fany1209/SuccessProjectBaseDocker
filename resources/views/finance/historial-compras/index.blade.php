{{--
Finance - Historial de Compras
Fecha de creación: 18-06-2026
Creado por: Emilio
Actualizado por: Antigravity
Fecha de actualización: 23-06-2026
--}}
@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 pb-12">

  <section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

      {{-- Header --}}
      <div class="mt-6 text-center w-full">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
          <i class="ri-shopping-bag-line mr-1"></i> Historial de Compras
        </h1>
        <p class="text-sm text-gray-500 mt-1">Selecciona un cliente para ver sus métricas de compra</p>
        <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
      </div>

      {{-- Filters --}}
      <div class="w-full max-w-3xl mt-6 mx-auto flex flex-col sm:flex-row gap-4">
        {{-- Customer Selector --}}
        <div class="flex-1">
          <label for="customer-select" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="ri-user-search-line mr-1 text-[#198754]"></i> Seleccionar Cliente
          </label>
          <select id="customer-select"
                  class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-4 py-3 text-gray-700 bg-white shadow-sm transition-all duration-200 text-sm">
            <option value="">-- Seleccionar cliente --</option>
            @foreach ($customers as $c)
              <option value="{{ $c->customer_id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Year Selector (Only visible for individual customer) --}}
        <div id="year-filter-container" class="w-full sm:w-48 hidden">
          <label for="year-select" class="block text-sm font-semibold text-gray-700 mb-2">
            <i class="ri-calendar-line mr-1 text-[#198754]"></i> Año
          </label>
          <select id="year-select"
                  class="w-full rounded-lg border-2 border-gray-200 focus:border-[#198754] focus:ring-2 focus:ring-[#198754]/20 px-4 py-3 text-gray-700 bg-white shadow-sm transition-all duration-200 text-sm">
            <option value="">Histórico Total</option>
            @php
              $currentYear = date('Y');
              $startYear = 2020; // Assuming records might go back a few years
            @endphp
            @for ($y = $currentYear; $y >= $startYear; $y--)
              <option value="{{ $y }}">{{ $y }}</option>
            @endfor
          </select>
        </div>
      </div>

      {{-- ============================================================= --}}
      {{-- DASHBOARD GENERAL (visible por defecto, se oculta al buscar) --}}
      {{-- ============================================================= --}}
      <div id="dashboard-container" class="w-full mt-8 space-y-6">

        {{-- Dashboard Loading --}}
        <div id="dashboard-loading" class="flex flex-col items-center gap-3 py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-4 border-[#198754] border-t-transparent"></div>
          <span class="text-sm text-gray-500">Cargando dashboard general...</span>
        </div>

        {{-- Dashboard Content (hidden until data loads) --}}
        <div id="dashboard-content" class="hidden space-y-6">

          {{-- Row 1: Clientes Frecuentes + Clientes Inactivos --}}
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Clientes Frecuentes --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden dash-card" style="animation-delay: 0.1s">
              <div class="bg-gradient-to-r from-[#198754] to-[#20c997] px-5 py-3.5 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                  <i class="ri-vip-crown-fill text-white text-lg"></i>
                </div>
                <div class="flex-1">
                  <h3 class="text-white font-bold text-sm">Clientes Frecuentes</h3>
                  <p class="text-white/70 text-xs">Top 10 por número de compras</p>
                </div>
                <button type="button" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 btn-graficar" data-target="frecuentes">
                  <i class="ri-pie-chart-fill"></i> Graficar
                </button>
              </div>
              <div class="p-4 overflow-x-auto">
                <table class="w-full text-sm" id="tbl-frequent">
                  <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                      <th class="pb-2.5 pr-2">#</th>
                      <th class="pb-2.5 pr-2">Cliente</th>
                      <th class="pb-2.5 pr-2 text-center">Compras</th>
                      <th class="pb-2.5 pr-2 text-right">Monto Total</th>
                      <th class="pb-2.5 text-right">Última Compra</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-frequent" class="divide-y divide-gray-50"></tbody>
                </table>
                <div id="frequent-empty" class="hidden text-center py-6 text-gray-400 text-sm">
                  <i class="ri-user-line text-2xl block mb-1"></i> Sin datos disponibles
                </div>
              </div>
            </div>

            {{-- Clientes Inactivos --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden dash-card" style="animation-delay: 0.2s">
              <div class="bg-gradient-to-r from-[#dc3545] to-[#ff6b6b] px-5 py-3.5 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                  <i class="ri-error-warning-fill text-white text-lg"></i>
                </div>
                <div class="flex-1">
                  <h3 class="text-white font-bold text-sm">Clientes Inactivos</h3>
                  <p class="text-white/70 text-xs">Top 10 (Más de 100 días sin comprar)</p>
                </div>
                <button type="button" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 btn-graficar" data-target="inactivos">
                  <i class="ri-pie-chart-fill"></i> Graficar
                </button>
              </div>
              <div class="p-4 overflow-x-auto">
                <table class="w-full text-sm" id="tbl-inactive">
                  <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                      <th class="pb-2.5 pr-2">Cliente</th>
                      <th class="pb-2.5 pr-2 text-center">Días</th>
                      <th class="pb-2.5 pr-2">Última Compra</th>
                      <th class="pb-2.5 text-right">Vendedor</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-inactive" class="divide-y divide-gray-50"></tbody>
                </table>
                <div id="inactive-empty" class="hidden text-center py-6 text-gray-400 text-sm">
                  <i class="ri-check-double-line text-2xl block mb-1"></i> No hay clientes inactivos
                </div>
              </div>
            </div>

          </div>

          {{-- Row 2: Ranking Mejores Clientes + Productos Más Vendidos --}}
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Ranking de Mejores Clientes --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden dash-card" style="animation-delay: 0.3s">
              <div class="bg-gradient-to-r from-[#ffc107] to-[#ffca2c] px-5 py-3.5 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                  <i class="ri-trophy-fill text-white text-lg"></i>
                </div>
                <div class="flex-1">
                  <h3 class="text-white font-bold text-sm" style="text-shadow: 0 1px 2px rgba(0,0,0,0.15)">Ranking Mejores Clientes</h3>
                  <p class="text-white/80 text-xs" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1)">Top 10 por monto total</p>
                </div>
                <button type="button" class="bg-black/10 hover:bg-black/20 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 btn-graficar" style="text-shadow: 0 1px 2px rgba(0,0,0,0.15)" data-target="mejores">
                  <i class="ri-pie-chart-fill"></i> Graficar
                </button>
              </div>
              <div class="p-4 overflow-x-auto">
                <table class="w-full text-sm" id="tbl-ranking">
                  <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                      <th class="pb-2.5 pr-2">#</th>
                      <th class="pb-2.5 pr-2">Cliente</th>
                      <th class="pb-2.5 pr-2 text-right">Monto Total</th>
                      <th class="pb-2.5 text-center">Compras</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-ranking" class="divide-y divide-gray-50"></tbody>
                </table>
                <div id="ranking-empty" class="hidden text-center py-6 text-gray-400 text-sm">
                  <i class="ri-trophy-line text-2xl block mb-1"></i> Sin datos disponibles
                </div>
              </div>
            </div>

            {{-- Productos Más Vendidos --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden dash-card" style="animation-delay: 0.4s">
              <div class="bg-gradient-to-r from-[#0d6efd] to-[#6ea8fe] px-5 py-3.5 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                  <i class="ri-shopping-cart-fill text-white text-lg"></i>
                </div>
                <div class="flex-1">
                  <h3 class="text-white font-bold text-sm">Productos Más Vendidos</h3>
                  <p class="text-white/70 text-xs">Top 10 por cantidad vendida</p>
                </div>
                <button type="button" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 btn-graficar" data-target="productos">
                  <i class="ri-pie-chart-fill"></i> Graficar
                </button>
              </div>
              <div class="p-4 overflow-x-auto">
                <table class="w-full text-sm" id="tbl-products">
                  <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                      <th class="pb-2.5 pr-2">#</th>
                      <th class="pb-2.5 pr-2">Producto</th>
                      <th class="pb-2.5 pr-2 text-right">Cant. Vendida</th>
                      <th class="pb-2.5 pr-2 text-right">Ingreso</th>
                      <th class="pb-2.5 text-center">Clientes</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-products" class="divide-y divide-gray-50"></tbody>
                </table>
                <div id="products-empty" class="hidden text-center py-6 text-gray-400 text-sm">
                  <i class="ri-shopping-cart-line text-2xl block mb-1"></i> Sin datos disponibles
                </div>
              </div>
            </div>

          </div>

        </div>{{-- end #dashboard-content --}}

        {{-- Chart Modal for Dashboard --}}
        <div id="dashboard-chart-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] overflow-y-auto transform transition-all">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 sticky top-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#198754]/10 flex items-center justify-center">
                  <i class="ri-bar-chart-2-fill text-[#198754] text-xl"></i>
                </div>
                <div>
                  <h3 id="dash-modal-title" class="text-lg font-bold text-gray-800">Gráficas</h3>
                  <p id="dash-modal-subtitle" class="text-sm text-gray-500">Representación visual de los datos</p>
                </div>
              </div>
              <button type="button" id="btn-close-dash-modal" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                <i class="ri-close-line text-2xl"></i>
              </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Pie Chart Column --}}
                <div class="flex flex-col items-center">
                  <h4 class="text-sm font-bold text-gray-700 mb-4 bg-gray-100 px-4 py-1.5 rounded-full inline-flex items-center gap-2">
                    <i class="ri-pie-chart-2-fill text-blue-500"></i> Distribución (Pastel)
                  </h4>
                  <div class="relative w-full" style="height: 350px;">
                    <canvas id="dash-modal-pie"></canvas>
                  </div>
                </div>
                
                {{-- Bar Chart Column --}}
                <div class="flex flex-col items-center">
                  <h4 class="text-sm font-bold text-gray-700 mb-4 bg-gray-100 px-4 py-1.5 rounded-full inline-flex items-center gap-2">
                    <i class="ri-bar-chart-grouped-fill text-green-500"></i> Comparativa (Barras)
                  </h4>
                  <div class="relative w-full" style="height: 350px;">
                    <canvas id="dash-modal-bar"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
              <button type="button" id="btn-close-dash-modal-footer" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                Cerrar
              </button>
            </div>
          </div>
        </div>

      </div>{{-- end #dashboard-container --}}


      {{-- ============================================== --}}
      {{-- SECCIÓN INDIVIDUAL POR CLIENTE (ya existente) --}}
      {{-- ============================================== --}}

      {{-- Loading Spinner --}}
      <div id="loading-spinner" class="hidden mt-8 flex flex-col items-center gap-3">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-[#198754] border-t-transparent"></div>
        <span class="text-sm text-gray-500">Cargando datos del cliente...</span>
      </div>

      {{-- Empty state (no customer selected) - HIDDEN because dashboard replaces it --}}
      <div id="empty-state" class="hidden mt-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
          <i class="ri-file-search-line text-3xl text-gray-400"></i>
        </div>
        <p class="text-gray-400 text-sm">Selecciona un cliente para visualizar su historial</p>
      </div>

      {{-- No data state --}}
      <div id="no-data-state" class="hidden mt-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-50 mb-4">
          <i class="ri-inbox-line text-3xl text-amber-400"></i>
        </div>
        <p class="text-amber-600 font-medium">Este cliente no tiene compras registradas</p>
        <p class="text-gray-400 text-sm mt-1">Selecciona otro cliente para ver su historial</p>
      </div>

      {{-- Results Container --}}
      <div id="results-container" class="hidden w-full mt-8 space-y-8">

        {{-- Customer name banner --}}
        <div class="bg-gradient-to-r from-[#198754] to-[#20c997] rounded-xl px-6 py-4 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
              <i class="ri-user-3-fill text-white text-xl"></i>
            </div>
            <div>
              <h2 id="customer-name-banner" class="text-white font-bold text-xl tracking-wide"></h2>
              <p class="text-white/70 text-sm">Resumen de compras</p>
            </div>
          </div>

          {{-- Contact Info --}}
          <div class="flex flex-wrap items-center gap-3 md:gap-4 text-white/90 bg-black/10 px-4 py-2.5 rounded-lg border border-white/10">
             <div class="hidden sm:block w-px h-4 bg-white/20"></div>
             <div class="flex items-center gap-1.5" title="Teléfono">
                 <i class="ri-phone-line text-white/70"></i>
                 <span id="customer-contact-phone" class="text-sm"></span>
             </div>
             <div class="hidden sm:block w-px h-4 bg-white/20"></div>
             <div class="flex items-center gap-1.5" title="Correo">
                 <i class="ri-mail-line text-white/70"></i>
                 <span id="customer-contact-email" class="text-sm"></span>
             </div>
          </div>
        </div>

        {{-- Metrics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

          {{-- Total Comprado --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Comprado</p>
                <p id="metric-total" class="text-2xl font-bold text-gray-800">$0.00</p>
              </div>
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <i class="ri-money-dollar-circle-fill text-xl text-emerald-500"></i>
              </div>
            </div>
          </div>

          {{-- Última Compra --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Última Compra</p>
                <p id="metric-last-purchase" class="text-2xl font-bold text-gray-800">—</p>
              </div>
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ri-calendar-check-fill text-xl text-blue-500"></i>
              </div>
            </div>
          </div>

          {{-- Frecuencia de Compra --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Frecuencia de Compra</p>
                <p id="metric-frequency" class="text-2xl font-bold text-gray-800">—</p>
              </div>
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <i class="ri-repeat-fill text-xl text-violet-500"></i>
              </div>
            </div>
          </div>

          {{-- Producto más comprado --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
            <div class="flex items-start justify-between">
              <div class="min-w-0 flex-1 pr-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Producto Más Comprado</p>
                <p id="metric-top-product" class="text-lg font-bold text-gray-800 truncate" title="">—</p>
              </div>
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center">
                <i class="ri-star-fill text-xl text-orange-500"></i>
              </div>
            </div>
          </div>

          {{-- Días sin comprar --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Días Sin Comprar</p>
                <p id="metric-days" class="text-2xl font-bold text-gray-800">—</p>
              </div>
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center">
                <i class="ri-time-fill text-xl text-rose-500"></i>
              </div>
            </div>
          </div>

          {{-- Vendedor asignado --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
            <div class="flex items-start justify-between">
              <div class="min-w-0 flex-1 pr-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Vendedor Asignado</p>
                <p id="metric-seller" class="text-lg font-bold text-gray-800 truncate" title="">—</p>
              </div>
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center">
                <i class="ri-user-star-fill text-xl text-teal-500"></i>
              </div>
            </div>
          </div>

        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

          {{-- Monthly Purchases Bar Chart --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
              <i class="ri-bar-chart-box-fill text-[#198754]"></i>
              Compras Mensuales (últimos 12 meses)
            </h3>
            <div class="relative" style="height: 280px;">
              <canvas id="chart-monthly"></canvas>
            </div>
          </div>

          {{-- Top Products Doughnut --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
              <i class="ri-donut-chart-fill text-[#198754]"></i>
              Top 5 Productos Más Comprados
            </h3>
            <div class="relative" style="height: 280px;">
              <canvas id="chart-products"></canvas>
            </div>
          </div>

          {{-- Average Ticket Line Chart --}}
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
              <i class="ri-line-chart-fill text-[#198754]"></i>
              Ticket Promedio Mensual
            </h3>
            <div class="relative" style="height: 280px;">
              <canvas id="chart-ticket"></canvas>
            </div>
          </div>

        </div>

      </div>

    </div>
  </section>

</div>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  /* Custom Select2 Tailwind Styling */
  .select2-container--default .select2-selection--single {
    height: 48px;
    border-radius: 0.5rem;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
  }
  .select2-container--default .select2-selection--single:focus,
  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #198754;
    outline: none;
    box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.2);
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px;
    right: 10px;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #374151;
    font-size: 0.875rem;
    line-height: normal;
  }

  .metric-pulse {
    animation: metricPulse 0.5s ease-out;
  }

  @keyframes metricPulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); opacity: 1; }
  }

  /* Dashboard card animations */
  .dash-card {
    animation: dashSlideUp 0.5s ease-out both;
  }

  @keyframes dashSlideUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
  }

  /* Dashboard table row hover */
  #dashboard-content tbody tr {
    transition: background-color 0.15s ease;
  }
  #dashboard-content tbody tr:hover {
    background-color: #f9fafb;
  }

  /* Progress bar for ranking */
  .rank-bar {
    height: 6px;
    border-radius: 3px;
    background: #e5e7eb;
    overflow: hidden;
    margin-top: 4px;
  }
  .rank-bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Badge styles */
  .badge-danger {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 9999px;
    font-size: 0.7rem; font-weight: 600;
    background: #fee2e2; color: #dc2626;
  }
  .badge-warning {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 9999px;
    font-size: 0.7rem; font-weight: 600;
    background: #fef3c7; color: #d97706;
  }
  .badge-success {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 9999px;
    font-size: 0.7rem; font-weight: 600;
    background: #d1fae5; color: #059669;
  }

  /* Medal styles */
  .medal {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px; border-radius: 50%;
    font-size: 0.75rem; font-weight: 700;
  }
  .medal-gold { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3); }
  .medal-silver { background: linear-gradient(135deg, #d1d5db, #9ca3af); color: #fff; box-shadow: 0 2px 4px rgba(156, 163, 175, 0.3); }
  .medal-bronze { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; box-shadow: 0 2px 4px rgba(180, 83, 9, 0.3); }
  .medal-default { background: #f3f4f6; color: #6b7280; }

  /* Smooth hide/show transitions */
  .dash-fade-out {
    animation: dashFadeOut 0.3s ease-out forwards;
  }
  .dash-fade-in {
    animation: dashFadeIn 0.4s ease-out forwards;
  }
  @keyframes dashFadeOut {
    0% { opacity: 1; transform: translateY(0); }
    100% { opacity: 0; transform: translateY(-10px); }
  }
  @keyframes dashFadeIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
  }

  /* Custom scrollbar for inactive table */
  #dashboard-content .overflow-y-auto::-webkit-scrollbar { width: 4px; }
  #dashboard-content .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
  #dashboard-content .overflow-y-auto::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
  // Initialize Select2 for searchable dropdown
  $('#customer-select').select2({
    placeholder: "-- Seleccionar cliente --",
    allowClear: true,
    width: '100%'
  });

  const MONTHS_ES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
  const CHART_COLORS = [
    'rgba(25, 135, 84, 0.85)',
    'rgba(32, 201, 151, 0.85)',
    'rgba(13, 110, 253, 0.85)',
    'rgba(111, 66, 193, 0.85)',
    'rgba(255, 153, 0, 0.85)',
    'rgba(220, 53, 69, 0.85)',
    'rgba(255, 193, 7, 0.85)',
    'rgba(108, 117, 125, 0.85)',
  ];

  let chartMonthly = null;
  let chartProducts = null;
  let chartTicket = null;

  function destroyCharts() {
    if (chartMonthly) { chartMonthly.destroy(); chartMonthly = null; }
    if (chartProducts) { chartProducts.destroy(); chartProducts = null; }
    if (chartTicket) { chartTicket.destroy(); chartTicket = null; }
  }

  function formatCurrency(val) {
    return '$' + parseFloat(val || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  function formatQty(val) {
    const n = parseFloat(val || 0);
    return n % 1 === 0 ? n.toLocaleString('es-MX') : n.toLocaleString('es-MX', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
  }

  // ===================== DASHBOARD GENERAL =====================

  function loadDashboard() {
    $('#dashboard-loading').removeClass('hidden');
    $('#dashboard-content').addClass('hidden');

    $.ajax({
      url: "{{ route('historial-compras.dashboard') }}",
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (!res.success) {
          $('#dashboard-loading').addClass('hidden');
          return;
        }
        window.dashboardData = res.data; // Store globally for charts
        renderDashboard(res.data);
        $('#dashboard-loading').addClass('hidden');
        $('#dashboard-content').removeClass('hidden');
      },
      error: function() {
        $('#dashboard-loading').addClass('hidden');
        // Fallback: show empty state if dashboard fails
        $('#dashboard-container').addClass('hidden');
        $('#empty-state').removeClass('hidden');
      }
    });
  }

  function renderDashboard(data) {
    // 1. Clientes Frecuentes
    renderFrequentCustomers(data.frequent_customers);
    // 2. Clientes Inactivos
    renderInactiveCustomers(data.inactive_customers);
    // 3. Ranking Mejores Clientes
    renderRankingCustomers(data.best_customers, data.max_amount);
    // 4. Productos Más Vendidos
    renderTopProducts(data.top_products, data.max_qty);
  }

  function renderFrequentCustomers(customers) {
    const tbody = $('#tbody-frequent');
    tbody.empty();

    if (!customers || customers.length === 0) {
      $('#frequent-empty').removeClass('hidden');
      $('#tbl-frequent').addClass('hidden');
      return;
    }

    $('#frequent-empty').addClass('hidden');
    $('#tbl-frequent').removeClass('hidden');

    customers.forEach(function(c, i) {
      const rank = i + 1;
      tbody.append(`
        <tr class="cursor-pointer" data-customer-id="${c.customer_id}">
          <td class="py-2.5 pr-2 font-semibold text-gray-400 text-xs">${rank}</td>
          <td class="py-2.5 pr-2">
            <span class="font-medium text-gray-700">${escapeHtml(c.name)}</span>
          </td>
          <td class="py-2.5 pr-2 text-center">
            <span class="badge-success">${c.total_purchases}</span>
          </td>
          <td class="py-2.5 pr-2 text-right font-semibold text-gray-700">${formatCurrency(c.total_amount)}</td>
          <td class="py-2.5 text-right text-gray-500 text-xs">${formatDate(c.last_purchase)}</td>
        </tr>
      `);
    });

    // Click row to load that customer
    tbody.find('tr[data-customer-id]').on('click', function() {
      const id = $(this).data('customer-id');
      $('#customer-select').val(id).trigger('change');
    });
  }

  function renderInactiveCustomers(customers) {
    const tbody = $('#tbody-inactive');
    tbody.empty();

    if (!customers || customers.length === 0) {
      $('#inactive-empty').removeClass('hidden');
      $('#tbl-inactive').addClass('hidden');
      return;
    }

    $('#inactive-empty').addClass('hidden');
    $('#tbl-inactive').removeClass('hidden');

    customers.forEach(function(c) {
      const days = parseInt(c.days_without_purchase);
      let badge = '';
      if (days > 200) {
        badge = `<span class="badge-danger"><i class="ri-alert-fill"></i> ${days} días</span>`;
      } else {
        badge = `<span class="badge-warning"><i class="ri-time-fill"></i> ${days} días</span>`;
      }

      tbody.append(`
        <tr class="cursor-pointer" data-customer-id="${c.customer_id}">
          <td class="py-2.5 pr-2">
            <span class="font-medium text-gray-700">${escapeHtml(c.name)}</span>
          </td>
          <td class="py-2.5 pr-2 text-center">${badge}</td>
          <td class="py-2.5 pr-2 text-gray-500 text-xs">${formatDate(c.last_purchase)}</td>
          <td class="py-2.5 text-right text-gray-500 text-xs">${escapeHtml(c.vendedor || 'Sin asignar')}</td>
        </tr>
      `);
    });

    tbody.find('tr[data-customer-id]').on('click', function() {
      const id = $(this).data('customer-id');
      $('#customer-select').val(id).trigger('change');
    });
  }

  function renderRankingCustomers(customers, maxAmount) {
    const tbody = $('#tbody-ranking');
    tbody.empty();

    if (!customers || customers.length === 0) {
      $('#ranking-empty').removeClass('hidden');
      $('#tbl-ranking').addClass('hidden');
      return;
    }

    $('#ranking-empty').addClass('hidden');
    $('#tbl-ranking').removeClass('hidden');

    customers.forEach(function(c, i) {
      const rank = i + 1;
      let medalClass = 'medal-default';
      let medalContent = rank;
      if (rank === 1) { medalClass = 'medal-gold'; medalContent = '🥇'; }
      else if (rank === 2) { medalClass = 'medal-silver'; medalContent = '🥈'; }
      else if (rank === 3) { medalClass = 'medal-bronze'; medalContent = '🥉'; }

      const pct = maxAmount > 0 ? Math.round((parseFloat(c.total_amount) / parseFloat(maxAmount)) * 100) : 0;
      const barColor = rank <= 3 ? '#198754' : (rank <= 6 ? '#20c997' : '#6ee7b7');

      tbody.append(`
        <tr class="cursor-pointer" data-customer-id="${c.customer_id}">
          <td class="py-2.5 pr-2">
            <span class="medal ${medalClass}">${medalContent}</span>
          </td>
          <td class="py-2.5 pr-2">
            <span class="font-medium text-gray-700">${escapeHtml(c.name)}</span>
            <div class="rank-bar"><div class="rank-bar-fill" style="width: ${pct}%; background: ${barColor}"></div></div>
          </td>
          <td class="py-2.5 pr-2 text-right font-semibold text-gray-700">${formatCurrency(c.total_amount)}</td>
          <td class="py-2.5 text-center text-gray-500">${c.total_purchases}</td>
        </tr>
      `);
    });

    tbody.find('tr[data-customer-id]').on('click', function() {
      const id = $(this).data('customer-id');
      $('#customer-select').val(id).trigger('change');
    });
  }

  function renderTopProducts(products, maxQty) {
    const tbody = $('#tbody-products');
    tbody.empty();

    if (!products || products.length === 0) {
      $('#products-empty').removeClass('hidden');
      $('#tbl-products').addClass('hidden');
      return;
    }

    $('#products-empty').addClass('hidden');
    $('#tbl-products').removeClass('hidden');

    products.forEach(function(p, i) {
      const rank = i + 1;
      const pct = maxQty > 0 ? Math.round((parseFloat(p.total_qty) / parseFloat(maxQty)) * 100) : 0;
      const barColor = rank <= 3 ? '#0d6efd' : (rank <= 6 ? '#6ea8fe' : '#93c5fd');
      const productName = p.public_product_name || 'Sin nombre';
      const displayName = productName.length > 30 ? productName.substring(0, 27) + '...' : productName;

      tbody.append(`
        <tr>
          <td class="py-2.5 pr-2 font-semibold text-gray-400 text-xs">${rank}</td>
          <td class="py-2.5 pr-2">
            <span class="font-medium text-gray-700" title="${escapeHtml(productName)}">${escapeHtml(displayName)}</span>
            <div class="rank-bar"><div class="rank-bar-fill" style="width: ${pct}%; background: ${barColor}"></div></div>
          </td>
          <td class="py-2.5 pr-2 text-right font-semibold text-gray-700">${formatQty(p.total_qty)}</td>
          <td class="py-2.5 pr-2 text-right text-gray-500">${formatCurrency(p.total_revenue)}</td>
          <td class="py-2.5 text-center">
            <span class="badge-success">${p.unique_customers}</span>
          </td>
        </tr>
      `);
    });
  }

  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // ===================== STATE MANAGEMENT =====================

  function showState(state) {
    // Hide everything first
    $('#empty-state, #no-data-state, #results-container, #loading-spinner').addClass('hidden');

    if (state === 'dashboard') {
      $('#year-filter-container').addClass('hidden');
      $('#year-select').val(''); // Reset year filter when going to dashboard
      $('#dashboard-container').removeClass('hidden dash-fade-out').addClass('dash-fade-in');
      $('#results-container').addClass('hidden');
    } else {
      $('#year-filter-container').removeClass('hidden');
      
      // Hide dashboard when showing individual customer data
      $('#dashboard-container').addClass('dash-fade-out');
      setTimeout(function() {
        $('#dashboard-container').addClass('hidden').removeClass('dash-fade-out dash-fade-in');
      }, 300);

      if (state === 'empty') {
          $('#empty-state').removeClass('hidden');
          $('#year-filter-container').addClass('hidden');
      }
      else if (state === 'nodata') $('#no-data-state').removeClass('hidden');
      else if (state === 'results') $('#results-container').removeClass('hidden');
      else if (state === 'loading') $('#loading-spinner').removeClass('hidden');
    }
  }

  // ===================== CUSTOMER & YEAR SELECT HANDLERS =====================

  function loadCustomerData() {
    const customerId = $('#customer-select').val();
    const year = $('#year-select').val();
    
    destroyCharts();

    if (!customerId) {
      // No customer selected: show dashboard again
      $('#empty-state, #no-data-state, #results-container, #loading-spinner').addClass('hidden');
      showState('dashboard');
      return;
    }

    // Customer selected: hide dashboard, show loading
    showState('loading');

    let url = "{{ route('historial-compras.data', ':id') }}".replace(':id', customerId);
    if (year) {
      url += `?year=${year}`;
    }

    $.ajax({
      url: url,
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (!res.success) {
          Swal.fire('Error', res.message || 'No se pudo obtener la información', 'error');
          showState('dashboard');
          return;
        }

        const d = res.data;

        if (d.total_purchased === 0 && !d.last_purchase) {
          showState('nodata');
          return;
        }

        // Populate metrics
        $('#customer-name-banner').text(d.customer_name);
        $('#customer-contact-name').text(d.contact_name);
        $('#customer-contact-phone').text(d.contact_phone);
        
        // Provide mailto link if email is valid, otherwise just text
        if (d.contact_email && d.contact_email !== 'N/A' && d.contact_email !== 'Sin especificar') {
            $('#customer-contact-email').html(`<a href="mailto:${d.contact_email}" class="hover:underline">${d.contact_email}</a>`);
        } else {
            $('#customer-contact-email').text(d.contact_email);
        }

        $('#metric-total').text(formatCurrency(d.total_purchased)).addClass('metric-pulse');
        $('#metric-last-purchase').text(formatDate(d.last_purchase)).addClass('metric-pulse');
        $('#metric-frequency').html(d.purchase_frequency !== null ? `Cada <strong>${d.purchase_frequency}</strong> días` : 'Única compra').addClass('metric-pulse');
        
        const topProd = d.top_product || '—';
        $('#metric-top-product').text(topProd).attr('title', topProd).addClass('metric-pulse');
        
        const days = d.days_without_purchase !== null ? Math.floor(d.days_without_purchase) : null;
        let daysHtml = '—';
        let daysBadge = '';
        if (days !== null) {
          if (days <= 30) daysBadge = '<span class="inline-block ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Activo</span>';
          else if (days <= 90) daysBadge = '<span class="inline-block ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Moderado</span>';
          else daysBadge = '<span class="inline-block ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Inactivo</span>';
          daysHtml = `${days} días ${daysBadge}`;
        }
        $('#metric-days').html(daysHtml).addClass('metric-pulse');
        
        const seller = d.assigned_seller || 'Sin asignar';
        $('#metric-seller').text(seller).attr('title', seller).addClass('metric-pulse');

        setTimeout(() => {
          $('.metric-pulse').removeClass('metric-pulse');
        }, 600);

        // === RENDER CHARTS ===
        renderMonthlyChart(d.charts.monthly);
        renderProductsChart(d.charts.top_products);
        renderTicketChart(d.charts.avg_ticket);

        showState('results');
      },
      error: function(xhr) {
        const msg = xhr?.responseJSON?.message || 'Error al obtener datos';
        Swal.fire('Error', msg, 'error');
        showState('dashboard');
      }
    });
  }

  $('#customer-select, #year-select').on('change', function() {
    loadCustomerData();
  });

  // ===================== CHART RENDERERS =====================

  function renderMonthlyChart(data) {
    const ctx = document.getElementById('chart-monthly').getContext('2d');
    const labels = data.map(d => `${MONTHS_ES[d.month - 1]} ${d.year}`);
    const values = data.map(d => parseFloat(d.total));

    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(25, 135, 84, 0.9)');
    gradient.addColorStop(1, 'rgba(32, 201, 151, 0.6)');

    chartMonthly = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Total ($)',
          data: values,
          backgroundColor: gradient,
          borderRadius: 6,
          borderSkipped: false,
          maxBarThickness: 40,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            padding: 12,
            titleFont: { size: 13 },
            bodyFont: { size: 12 },
            callbacks: {
              label: ctx => formatCurrency(ctx.parsed.y)
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.04)' },
            ticks: {
              callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
              font: { size: 11 }
            }
          },
          x: {
            grid: { display: false },
            ticks: { font: { size: 10 }, maxRotation: 45 }
          }
        }
      }
    });
  }

  function renderProductsChart(data) {
    const ctx = document.getElementById('chart-products').getContext('2d');
    const labels = data.map(d => {
      const name = d.public_product_name || 'Sin nombre';
      return name.length > 25 ? name.substring(0, 22) + '...' : name;
    });
    const values = data.map(d => parseFloat(d.total_qty));

    chartProducts = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: values,
          backgroundColor: CHART_COLORS.slice(0, data.length),
          borderWidth: 2,
          borderColor: '#fff',
          hoverOffset: 8,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '55%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: { padding: 15, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } }
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            padding: 12,
            callbacks: {
              label: function(ctx) {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                return ` ${ctx.label}: ${ctx.parsed} uds (${pct}%)`;
              }
            }
          }
        }
      }
    });
  }

  function renderTicketChart(data) {
    const ctx = document.getElementById('chart-ticket').getContext('2d');
    const labels = data.map(d => `${MONTHS_ES[d.month - 1]} ${d.year}`);
    const values = data.map(d => parseFloat(d.avg_ticket));

    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.15)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0.01)');

    chartTicket = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Ticket Promedio ($)',
          data: values,
          borderColor: 'rgba(13, 110, 253, 1)',
          backgroundColor: gradient,
          borderWidth: 2.5,
          pointBackgroundColor: '#fff',
          pointBorderColor: 'rgba(13, 110, 253, 1)',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
          fill: true,
          tension: 0.4,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            padding: 12,
            callbacks: {
              label: ctx => 'Promedio: ' + formatCurrency(ctx.parsed.y)
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.04)' },
            ticks: {
              callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v.toFixed(0)),
              font: { size: 11 }
            }
          },
          x: {
            grid: { display: false },
            ticks: { font: { size: 10 }, maxRotation: 45 }
          }
        }
      }
    });
  }

  // ===================== DASHBOARD CHARTS MODAL =====================

  let dashModalPieChart = null;
  let dashModalBarChart = null;

  function destroyDashModalCharts() {
    if (dashModalPieChart) { dashModalPieChart.destroy(); dashModalPieChart = null; }
    if (dashModalBarChart) { dashModalBarChart.destroy(); dashModalBarChart = null; }
  }

  // Open modal handler
  $('.btn-graficar').on('click', function(e) {
    e.preventDefault();
    const target = $(this).data('target');
    if (!window.dashboardData) return;

    let pieLabels = [], pieValues = [], barLabels = [], barValues = [];
    let title = '', subtitle = '', valLabel = '';

    const formatFn = (target === 'productos' || target === 'inactivos') ? formatQty : formatCurrency;

    if (target === 'frecuentes') {
      title = 'Clientes Frecuentes';
      subtitle = 'Análisis del Top 10 por número de compras';
      valLabel = 'Monto Total ($)';
      const d = window.dashboardData.frequent_customers || [];
      d.forEach(i => {
        pieLabels.push(i.name.length > 20 ? i.name.substring(0, 18) + '...' : i.name);
        pieValues.push(i.total_amount);
        barLabels.push(i.name.length > 20 ? i.name.substring(0, 18) + '...' : i.name);
        barValues.push(i.total_purchases);
      });
    } else if (target === 'inactivos') {
      title = 'Clientes Inactivos';
      subtitle = 'Días sin comprar (Top 10)';
      valLabel = 'Días Inactivos';
      const d = window.dashboardData.inactive_customers ? window.dashboardData.inactive_customers.slice(0, 10) : [];
      d.forEach(i => {
        pieLabels.push(i.name.length > 20 ? i.name.substring(0, 18) + '...' : i.name);
        pieValues.push(i.days_without_purchase);
        barLabels.push(i.name.length > 20 ? i.name.substring(0, 18) + '...' : i.name);
        barValues.push(i.days_without_purchase);
      });
    } else if (target === 'mejores') {
      title = 'Mejores Clientes';
      subtitle = 'Análisis del Top 10 por monto total';
      valLabel = 'Monto Total ($)';
      const d = window.dashboardData.best_customers || [];
      d.forEach(i => {
        pieLabels.push(i.name.length > 20 ? i.name.substring(0, 18) + '...' : i.name);
        pieValues.push(i.total_amount);
        barLabels.push(i.name.length > 20 ? i.name.substring(0, 18) + '...' : i.name);
        barValues.push(i.total_amount);
      });
    } else if (target === 'productos') {
      title = 'Productos Más Vendidos';
      subtitle = 'Análisis del Top 10 por cantidad';
      valLabel = 'Cantidad Vendida';
      const d = window.dashboardData.top_products || [];
      d.forEach(i => {
        const pName = i.public_product_name || 'Sin nombre';
        const label = pName.length > 20 ? pName.substring(0, 18) + '...' : pName;
        pieLabels.push(label);
        pieValues.push(i.total_qty);
        barLabels.push(label);
        barValues.push(i.total_qty);
      });
    }

    $('#dash-modal-title').text(title);
    $('#dash-modal-subtitle').text(subtitle);

    destroyDashModalCharts();

    // Render Pie
    const ctxPie = document.getElementById('dash-modal-pie').getContext('2d');
    dashModalPieChart = new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: pieLabels,
        datasets: [{
          data: pieValues,
          backgroundColor: CHART_COLORS.slice(0, pieLabels.length),
          borderWidth: 1,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } },
          tooltip: {
            callbacks: { label: c => ' ' + c.label + ': ' + (target === 'inactivos' ? c.parsed + ' días' : formatFn(c.parsed)) }
          }
        }
      }
    });

    // Render Bar
    const ctxBar = document.getElementById('dash-modal-bar').getContext('2d');
    dashModalBarChart = new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: barLabels,
        datasets: [{
          label: valLabel + (target === 'frecuentes' ? ' (Monto) / Compras en pastel' : ''),
          data: barValues,
          backgroundColor: 'rgba(25, 135, 84, 0.7)',
          borderRadius: 4
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => (target === 'inactivos' ? v : formatFn(v)) } },
          x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
        }
      }
    });

    // If 'frecuentes', let's fix the bar chart to show the purchases, but we made pie show amount.
    // Actually, let's keep it simple: Pie and Bar show the primary metric.
    if (target === 'frecuentes') {
        dashModalBarChart.data.datasets[0].label = 'Número de Compras';
        dashModalBarChart.update();
        dashModalPieChart.data.datasets[0].label = 'Monto Total';
        dashModalPieChart.update();
    }

    $('#dashboard-chart-modal').removeClass('hidden');
  });

  $('#btn-close-dash-modal, #btn-close-dash-modal-footer').on('click', function() {
    $('#dashboard-chart-modal').addClass('hidden');
  });

  // Close on outside click
  $('#dashboard-chart-modal').on('click', function(e) {
    if (e.target === this) { $(this).addClass('hidden'); }
  });

  // ===================== INIT: Load dashboard on page load =====================
  loadDashboard();

});
</script>
@endpush
