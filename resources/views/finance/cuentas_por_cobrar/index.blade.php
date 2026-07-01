@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 pb-12">

  <section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <div class="mt-6 text-center w-full">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Cuentas por cobrar
      </h1>
      <p class="text-sm text-gray-600">Selecciona el apartado que deseas visualizar.</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    @php
      $btnBase = 'text-white bg-[#198754] hover:bg-[#157347] focus:ring-2 focus:outline-none focus:ring-[#198754]/30 font-medium rounded-lg text-xs px-3 transition-colors';
      $btnCard = 'w-full !h-auto min-h-[120px] py-4 flex flex-col items-center justify-center gap-3 text-center shadow-md hover:shadow-lg';
      $imgCls  = 'h-12 w-12 object-contain pointer-events-none select-none';
    @endphp

    <div class="w-full max-w-2xl mt-8">
      <div class="grid gap-4 sm:grid-cols-2">

        <a href="{{ route('cuentas-por-cobrar.dashboard') }}" 
          class="inline-flex items-center {{ $btnBase }} {{ $btnCard }}">
            <img src="{{ asset('images/finance/general.png') }}" 
                alt="Dashboard general" 
                class="{{ $imgCls }}">
            <span class="text-base font-semibold">Dashboard General</span>
        </a>

        <a href="{{ route('cuentas-por-cobrar.clientes') }}" 
          class="inline-flex items-center {{ $btnBase }} {{ $btnCard }}">
            <img src="{{ asset('images/finance/especifico.png') }}" 
                alt="Clientes" 
                class="{{ $imgCls }}">
            <span class="text-base font-semibold">Clientes</span>
        </a>

      </div>
    </div>

  </section>

</div>
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    @if(isset($pendingPaymentsCount) && $pendingPaymentsCount > 0)
      Swal.fire({
        title: '¡Atención!',
        text: 'Tienes {{ $pendingPaymentsCount }} factura(s) a crédito vencida(s). Por favor revisa el dashboard general.',
        icon: 'warning',
        confirmButtonColor: '#198754',
        confirmButtonText: 'Entendido'
      });
    @endif
  });
</script>
@endpush
