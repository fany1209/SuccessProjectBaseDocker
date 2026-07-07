@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 pb-12">

  <section class="col-span-12 w-full flex flex-col px-1">
    
    <div class="mt-6 text-center w-full relative mb-10">
      <a href="{{ route('cuentas-por-pagar.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold">
        <i class="ri-arrow-left-s-line text-xl"></i> Volver
      </a>
      
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        <i class="ri-dashboard-line mr-1"></i> Dashboard General
      </h1>
      <p class="text-sm text-gray-600 mt-1">Métricas de Cuentas por Pagar</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    <!-- Filtros (Se mantiene el filtro del mes para "Pagado este mes" y vista histórica) -->
    <div class="mb-6 flex justify-end">
      <form method="GET" action="{{ route('cuentas-por-pagar.dashboard') }}" class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl p-3 shadow-sm flex items-center gap-3">
        <div class="flex items-center gap-2">
          <label for="month" class="text-sm font-medium text-gray-600">Mes:</label>
          <select name="month" id="month" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
            @for ($i = 1; $i <= 12; $i++)
              <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                {{ ucfirst(\Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}
              </option>
            @endfor
          </select>
        </div>
        <div class="flex items-center gap-2">
          <label for="year" class="text-sm font-medium text-gray-600">Año:</label>
          <select name="year" id="year" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
            @for ($i = now()->year; $i >= 2020; $i--)
              <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>
        <button type="submit" class="bg-[#198754] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#157347] transition flex items-center gap-1">
          <i class="ri-filter-3-line"></i> Filtrar
        </button>
      </form>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      
      <!-- Total por pagar -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl relative z-10 shrink-0">
          <i class="ri-wallet-3-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-gray-500 mb-1">Total por Pagar</p>
          <h3 class="text-2xl font-bold text-gray-800">${{ number_format($totalPorPagar, 2) }}</h3>
          <p class="text-xs text-blue-500 mt-1 font-medium">Deuda activa total</p>
        </div>
      </div>

      <!-- Pagos Vencidos -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-2xl relative z-10 shrink-0">
          <i class="ri-alert-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-gray-500 mb-1">Pagos Vencidos</p>
          <h3 class="text-2xl font-bold text-gray-800">${{ number_format($pagosVencidos, 2) }}</h3>
          <p class="text-xs text-red-500 mt-1 font-medium">Requieren pago urgente</p>
        </div>
      </div>

      <!-- Próximos a vencer -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-2xl relative z-10 shrink-0">
          <i class="ri-time-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-gray-500 mb-1">Próximos a Vencer</p>
          <h3 class="text-2xl font-bold text-gray-800">${{ number_format($proximosVencer, 2) }}</h3>
          <p class="text-xs text-orange-500 mt-1 font-medium">Vencen en 3 días</p>
        </div>
      </div>

      <!-- Pagado este mes -->
      <div class="bg-gradient-to-br from-[#198754] to-[#157347] rounded-2xl shadow-sm border border-[#157347] p-6 flex items-center gap-5 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition-colors duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-white/20 text-white flex items-center justify-center text-2xl relative z-10 shrink-0 border border-white/30">
          <i class="ri-hand-coin-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-green-100 mb-1">Pagado Este Mes</p>
          <h3 class="text-2xl font-bold text-white">${{ number_format($pagadoEsteMes, 2) }}</h3>
          <p class="text-xs text-green-200 mt-1 font-medium">Total de abonos emitidos</p>
        </div>
      </div>

    </div>

    <!-- Gráficas Principales (Proveedor y Meses) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      
      <!-- Pagos por proveedor -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-6 relative z-10">
          <div>
            <h2 class="text-lg font-bold text-gray-800"><i class="ri-building-line text-[#198754] mr-2"></i>Saldos Pendientes por Proveedor (Top 10)</h2>
            <p class="text-sm text-gray-500">Empresas con mayor deuda acumulada</p>
          </div>
        </div>
        <div class="w-full h-[320px] relative z-10">
          <canvas id="proveedoresChart"></canvas>
        </div>
      </div>

      <!-- Pagos por mes -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-6 relative z-10">
          <div>
            <h2 class="text-lg font-bold text-gray-800"><i class="ri-bar-chart-line text-[#198754] mr-2"></i>Pagos Realizados por Mes</h2>
            <p class="text-sm text-gray-500">Evolución de los últimos 6 meses</p>
          </div>
        </div>
        <div class="w-full h-[320px] relative z-10">
          <canvas id="mesesChart"></canvas>
        </div>
      </div>

    </div>

    <!-- Gráfica Secundaria (Antigüedad) -->
    <div class="grid grid-cols-1 mb-6">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-6 relative z-10">
          <div>
            <h2 class="text-lg font-bold text-gray-800"><i class="ri-pie-chart-line text-[#198754] mr-2"></i>Antigüedad de Cuentas por Pagar</h2>
            <p class="text-sm text-gray-500">Saldos agrupados por días desde la emisión de la factura</p>
          </div>
        </div>
        <div class="w-full h-[320px] relative z-10 flex justify-center">
          <canvas id="agingChart"></canvas>
        </div>
      </div>
    </div>

  </section>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    
    // Variables compartidas
    const formatCurrency = (val) => {
      return '$' + parseFloat(val || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    // --- 1. Gráfica de Proveedores (Barras Horizontales) ---
    const ctxProv = document.getElementById('proveedoresChart').getContext('2d');
    const provLabels = @json($proveedoresLabels);
    const provData = @json($proveedoresData);

    new Chart(ctxProv, {
      type: 'bar',
      data: {
        labels: provLabels,
        datasets: [{
          label: 'Saldo Pendiente',
          data: provData,
          backgroundColor: '#3b82f6', // blue
          borderRadius: 4,
          barThickness: 20,
        }]
      },
      options: {
        indexAxis: 'y', // Horizontal bar chart
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            callbacks: {
              label: function(context) { return ' ' + formatCurrency(context.parsed.x); }
            }
          }
        },
        scales: {
          x: {
            grid: { color: '#f3f4f6' },
            ticks: {
              callback: function(value) {
                if(value >= 1000) return '$' + (value/1000).toFixed(0) + 'k';
                return '$' + value;
              }
            }
          },
          y: {
            grid: { display: false }
          }
        }
      }
    });

    // --- 2. Gráfica de Pagos por Mes (Líneas/Area) ---
    const ctxMeses = document.getElementById('mesesChart').getContext('2d');
    const mesesLabels = @json($mesesLabels);
    const mesesData = @json($mesesData);

    let gradientMeses = ctxMeses.createLinearGradient(0, 0, 0, 400);
    gradientMeses.addColorStop(0, 'rgba(25, 135, 84, 0.5)'); // Green transparent
    gradientMeses.addColorStop(1, 'rgba(25, 135, 84, 0)');

    new Chart(ctxMeses, {
      type: 'line',
      data: {
        labels: mesesLabels,
        datasets: [{
          label: 'Monto Pagado',
          data: mesesData,
          backgroundColor: gradientMeses,
          borderColor: '#198754',
          borderWidth: 3,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#198754',
          pointBorderWidth: 2,
          pointRadius: 4,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            callbacks: {
              label: function(context) { return ' ' + formatCurrency(context.parsed.y); }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f3f4f6' },
            ticks: {
              callback: function(value) {
                if(value >= 1000) return '$' + (value/1000).toFixed(0) + 'k';
                return '$' + value;
              }
            }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });

    // --- 3. Gráfica de Antigüedad (Pie/Doughnut) ---
    const ctxAging = document.getElementById('agingChart').getContext('2d');
    const agingData = @json($agingData);

    new Chart(ctxAging, {
      type: 'doughnut',
      data: {
        labels: ['0-30 días', '31-60 días', '61-90 días', 'Más de 90 días'],
        datasets: [{
          data: agingData,
          backgroundColor: [
            '#10b981', // green (0-30)
            '#fbbf24', // yellow (31-60)
            '#f97316', // orange (61-90)
            '#ef4444'  // red (90+)
          ],
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
          legend: {
            position: 'right',
            labels: {
              font: { family: "'Inter', sans-serif", size: 13 },
              usePointStyle: true,
              padding: 20
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            callbacks: {
              label: function(context) { 
                return ' ' + context.label + ': ' + formatCurrency(context.parsed); 
              }
            }
          }
        }
      }
    });

  });
</script>
@endpush
