<section class="col-span-12 w-full flex flex-col items-center px-1">
    
<div class="mt-6">
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
    Formats
  </h1>
  <p class="text-sm text-gray-600">Select the format you want to generate.</p>
  <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
</div>

@php
  $btnBase = 'open-modal text-white bg-[#198754] hover:bg-[#157347] focus:ring-2 focus:outline-none focus:ring-[#198754]/30 font-medium rounded-lg text-xs px-3';
  $btnCard = 'w-full !h-auto min-h-[88px] py-3 flex flex-col items-center justify-center gap-2 text-center';
  $imgCls  = 'h-8 w-8 md:h-9 md:w-9 object-contain pointer-events-none select-none';
@endphp

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
 
    {{--<button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="02">
      <img src="{{ asset('images/laboratory/02.png') }}" alt="02" class="{{ $imgCls }}">
      <span>Solicitud de muestras para clientes</span>
    </button>
    @include('laboratory.modals.02')--}}
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="09">
      <img src="{{ asset('images/laboratory/09.png') }}" alt="09" class="{{ $imgCls }}">
      <span>Solicitud de formulación</span>
    </button>
    @include('laboratory.modals.09')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="10">
      <img src="{{ asset('images/laboratory/010.png') }}" alt="10" class="{{ $imgCls }}">
      <span>Formulación</span>
    </button>
    @include('laboratory.modals.10')

    {{--<button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="13">
        <img src="{{ asset('images/laboratory/0013.png') }}" alt="13" class="{{ $imgCls }}">
        <span>13</span>
    </button>

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="14">
        <img src="{{ asset('images/laboratory/014.png') }}" alt="14" class="{{ $imgCls }}">
        <span>Bitácora</span>
    </button>
      @include('laboratory.modals.14')--}}

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="15">
        <img src="{{ asset('images/laboratory/015.png') }}" alt="15" class="{{ $imgCls }}">
        <span>Orden de producción</span>
    </button>
      @include('laboratory.modals.01pr')

    <a href="{{ route('production.yeast.index') }}" class="{{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/formats/pecuario.png') }}" alt="Levadura" class="{{ $imgCls }}">
        <span>Producción de Levadura</span>
    </a>
    
    <a href="#" class="{{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/formats/agro.png') }}" alt="Proteamin" class="{{ $imgCls }}">
        <span>Producción de Proteamin</span>
    </a>

    <a href="#" class="{{ $btnBase }} {{ $btnCard }}">
        <img src="{{ asset('images/deli.png') }}" alt="Vitayela" class="{{ $imgCls }}">
        <span>Producción de Vitayela</span>
        
    </a>
</div>
</section>