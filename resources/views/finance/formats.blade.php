<section class="col-span-12 w-full flex flex-col items-center px-1">
    
<div class="mt-6 text-center w-full">
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
    Formats
  </h1>
  <p class="text-sm text-gray-600">Select the format you want to generate.</p>
  <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
</div>

@php
  $btnBase = 'open-modal text-white bg-[#198754] hover:bg-[#157347] focus:ring-2 focus:outline-none focus:ring-[#198754]/30 font-medium rounded-lg text-xs px-3';
  $btnCard = 'w-full !h-auto min-h-[88px] py-3 flex flex-col items-center justify-center gap-2 text-center';
  $imgCls  = 'h-8 w-8 md:h-9 md:w-9 object-contain pointer-events-none select-none';
@endphp

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

     <a href="{{ route('facturas.index') }}" 
      class="inline-flex items-center  {{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/finance/facturas.png') }}" 
            alt="01" 
            class="{{ $imgCls }}">
        <span>Facturas</span>
    </a>

    <a href="{{ route('cuentas-por-pagar.index') }}" 
      class="inline-flex items-center  {{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/finance/pagos.png') }}" 
            alt="01" 
            class="{{ $imgCls }}">
        <span>Cuentas por pagar</span>
    </a>
    
      <a href="{{ route('precios.index') }}" 
      class="inline-flex items-center  {{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/finance/precios.png') }}" 
            alt="01" 
            class="{{ $imgCls }}">
        <span>Precios</span>
    </a>

    <a href="{{ route('historial-compras.index') }}" 
      class="inline-flex items-center  {{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/finance/historial.png') }}" 
            alt="01" 
            class="{{ $imgCls }}">
        <span>Historial de Compras</span>
    </a>

    <a href="{{ route('cuentas-por-cobrar.index') }}" 
      class="inline-flex items-center  {{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/finance/xcobrar.png') }}" 
            alt="01" 
            class="{{ $imgCls }}">
        <span>Cuentas por cobrar</span>
    </a>
</div>
</section>