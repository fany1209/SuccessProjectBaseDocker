{{--
Finance - Historial de Compras
Fecha de creación: 18-06-2026
Creado por: Emilio
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

      {{-- Customer Selector --}}
      <div class="w-full max-w-lg mt-6 mx-auto">
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

      {{-- Loading Spinner --}}
      <div id="loading-spinner" class="hidden mt-8 flex flex-col items-center gap-3">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-[#198754] border-t-transparent"></div>
        <span class="text-sm text-gray-500">Cargando datos del cliente...</span>
      </div>

      {{-- Empty state (no customer selected) --}}
      <div id="empty-state" class="mt-10 text-center">
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

  function showState(state) {
    $('#empty-state, #no-data-state, #results-container, #loading-spinner').addClass('hidden');
    if (state === 'empty') $('#empty-state').removeClass('hidden');
    else if (state === 'nodata') $('#no-data-state').removeClass('hidden');
    else if (state === 'results') $('#results-container').removeClass('hidden');
    else if (state === 'loading') $('#loading-spinner').removeClass('hidden');
  }

  $('#customer-select').on('change', function() {
    const customerId = $(this).val();
    destroyCharts();

    if (!customerId) {
      showState('empty');
      return;
    }

    showState('loading');

    $.ajax({
      url: "{{ route('historial-compras.data', ':id') }}".replace(':id', customerId),
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (!res.success) {
          Swal.fire('Error', res.message || 'No se pudo obtener la información', 'error');
          showState('empty');
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
        showState('empty');
      }
    });
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

});
</script>
@endpush
