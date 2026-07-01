@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 pb-12">

  <section class="col-span-12 w-full flex flex-col px-1">
    
    <div class="mt-6 text-center w-full relative mb-10">
      <!-- Botón de regreso -->
      <a href="{{ route('cuentas-por-cobrar.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold">
        <i class="ri-arrow-left-s-line text-xl"></i> Volver
      </a>
      
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        <i class="ri-dashboard-line mr-1"></i> Dashboard General
      </h1>
      <p class="text-sm text-gray-600 mt-1">Métricas de Cuentas por Cobrar</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      
      <!-- Facturas Vencidas -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-2xl relative z-10 shrink-0">
          <i class="ri-error-warning-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-gray-500 mb-1">Facturas Vencidas</p>
          <h3 class="text-3xl font-bold text-gray-800">{{ number_format($facturasVencidas) }}</h3>
          <p class="text-xs text-red-500 mt-1 font-medium">Requieren atención inmediata</p>
        </div>
      </div>

      <!-- Clientes con Adeudo -->
      <a href="{{ route('cuentas-por-cobrar.clientes') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5 relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer block">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-2xl relative z-10 shrink-0">
          <i class="ri-team-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-gray-500 mb-1">Clientes con Adeudo</p>
          <h3 class="text-3xl font-bold text-gray-800">{{ number_format($clientesAdeudo) }}</h3>
          <p class="text-xs text-orange-500 mt-1 font-medium group-hover:underline">Ver listado de clientes</p>
        </div>
      </a>

      <!-- Cobranza del Mes -->
      <div class="bg-gradient-to-br from-[#198754] to-[#157347] rounded-2xl shadow-sm border border-[#157347] p-6 flex items-center gap-5 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition-colors duration-300"></div>
        <div class="w-14 h-14 rounded-full bg-white/20 text-white flex items-center justify-center text-2xl relative z-10 shrink-0 border border-white/30">
          <i class="ri-funds-line"></i>
        </div>
        <div class="relative z-10 flex-1">
          <p class="text-sm font-medium text-green-100 mb-1">Cobranza del Mes Actual</p>
          <h3 class="text-3xl font-bold text-white">{{ number_format($currentMonthPct, 1) }}%</h3>
          <p class="text-xs text-green-200 mt-1 font-medium">Recuperación de lo facturado</p>
        </div>
      </div>

    </div>

    <!-- Gráfica -->
    <div class="w-full">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-6 relative z-10">
          <div>
            <h2 class="text-lg font-bold text-gray-800"><i class="ri-bar-chart-line text-[#198754] mr-2"></i>Porcentaje de Cobranza Recuperada</h2>
            <p class="text-sm text-gray-500">Evolución de los últimos 6 meses</p>
          </div>
        </div>
        
        <div class="w-full h-[400px] relative z-10">
          <canvas id="cobranzaChart"></canvas>
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
    const ctx = document.getElementById('cobranzaChart').getContext('2d');
    
    // Gradiente para las barras
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, '#198754');   
    gradient.addColorStop(1, '#a3e4c7');

    const labels = @json($chartLabels);
    const data = @json($chartData);

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: '% de Cobranza Recuperada',
          data: data,
          backgroundColor: gradient,
          borderRadius: 8,
          borderSkipped: false,
          barThickness: 40,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: { size: 14, family: "'Inter', sans-serif" },
            bodyFont: { size: 14, family: "'Inter', sans-serif" },
            padding: 12,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: function(context) {
                return context.parsed.y + '% recuperado';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            grid: {
              color: '#f3f4f6',
              drawBorder: false,
            },
            ticks: {
              font: { family: "'Inter', sans-serif", size: 12 },
              color: '#6b7280',
              callback: function(value) {
                return value + '%';
              }
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              font: { family: "'Inter', sans-serif", size: 13, weight: '500' },
              color: '#4b5563'
            }
          }
        },
        animation: {
          y: {
            duration: 2000,
            easing: 'easeOutElastic'
          }
        }
      }
    });
  });
</script>
@endpush
