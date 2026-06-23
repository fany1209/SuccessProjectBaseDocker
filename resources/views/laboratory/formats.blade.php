<section class="col-span-12 w-full flex flex-col items-center px-1">
    
<div class="mt-6">
 @can('laboratory.update')
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

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="prueba2">
      <img src="{{ asset('images/laboratory/01.png') }}" alt="01" class="{{ $imgCls }}">
      <span>Recepción de muestras</span>
    </button>
    @include('laboratory.modals.01')
 
    {{--<button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="02">
      <img src="{{ asset('images/laboratory/02.png') }}" alt="02" class="{{ $imgCls }}">
      <span>Solicitud de muestras para clientes</span>
    </button>
    @include('laboratory.modals.02')--}}
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="03">
      <img src="{{ asset('images/laboratory/03.png') }}" alt="03" class="{{ $imgCls }}">
      <span>Salida de muestras</span>
    </button>
    @include('laboratory.modals.03')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="04">
      <img src="{{ asset('images/laboratory/04.png') }}" alt="04" class="{{ $imgCls }}">
      <span>Inventario de muestras</span>
    </button>
    @include('laboratory.modals.04')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="05">
      <img src="{{ asset('images/laboratory/05.png') }}" alt="05" class="{{ $imgCls }}">
      <span>Análisis interno de producto</span>
    </button>
    @include('laboratory.modals.05')
 
    {{--<button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="06">
      <img src="{{ asset('images/laboratory/06.png') }}" alt="06" class="{{ $imgCls }}">
      <span>Análisis interno de suelo</span>
    </button>
    @include('laboratory.modals.06')--}}
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="07">
      <img src="{{ asset('images/laboratory/07.png') }}" alt="07" class="{{ $imgCls }}">
      <span>Instructivo muestreo</span>
    </button>
    @include('laboratory.modals.07')

   {{-- <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="rc">
      <img src="{{ asset('images/laboratory/08.png') }}" alt="08" class="{{ $imgCls }}">
      <span>08</span>
    </button>
    @include('laboratory.modals.08')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="09">
      <img src="{{ asset('images/laboratory/09.png') }}" alt="09" class="{{ $imgCls }}">
      <span>Solicitud de formulación</span>
    </button>
    @include('laboratory.modals.09')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="10">
      <img src="{{ asset('images/laboratory/010.png') }}" alt="10" class="{{ $imgCls }}">
      <span>Formulación</span>
    </button>
    @include('laboratory.modals.10')--}}
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="11">
      <img src="{{ asset('images/laboratory/011.png') }}" alt="11" class="{{ $imgCls }}">
      <span>Plan de trabajo semanal</span>
    </button>
    @include('laboratory.modals.11')

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="12">
        <img src="{{ asset('images/laboratory/012.png') }}" alt="12" class="{{ $imgCls }}">
        <span>Protocolo de seguimiento en campo</span>
    </button>
    @include('laboratory.modals.12')

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="create-material">
        <img src="{{ asset('images/laboratory/material.png') }}" alt="Nuevo Material" class="{{ $imgCls }}">
        <span>Nuevo Material</span>
    </button>
    @include('laboratory.modals.material.create')

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="create-reagent">
        <img src="{{ asset('images/laboratory/reactivo.png') }}" alt="Nuevo Reactivo" class="{{ $imgCls }}">
        <span>Nuevo Reactivo</span>
    </button>
    @include('laboratory.modals.reagents.create')
    
    {{--<button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="13">
        <img src="{{ asset('images/laboratory/0013.png') }}" alt="13" class="{{ $imgCls }}">
        <span>13</span>
    </button>

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="14">
        <img src="{{ asset('images/laboratory/014.png') }}" alt="14" class="{{ $imgCls }}">
        <span>Bitácora</span>
    </button>
      @include('laboratory.modals.14')

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="15">
        <img src="{{ asset('images/laboratory/015.png') }}" alt="15" class="{{ $imgCls }}">
        <span>Orden de producción</span>
    </button>
      @include('laboratory.modals.01pr')--}}
    
 @endcan

 
@php
  $btnBase = 'open-modal text-white bg-[#198754] hover:bg-[#157347] focus:ring-2 focus:outline-none focus:ring-[#198754]/30 font-medium rounded-lg text-xs px-3';
  $btnCard = 'w-full !h-auto min-h-[88px] py-3 flex flex-col items-center justify-center gap-2 text-center';
  $imgCls  = 'h-8 w-8 md:h-9 md:w-9 object-contain pointer-events-none select-none';
@endphp

   @can('laboratory.quality')
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="05">
      <img src="{{ asset('images/laboratory/05.png') }}" alt="05" class="{{ $imgCls }}">
      <span>Análisis interno de producto</span>
    </button>
    @include('laboratory.modals.05')
    @endcan
    
</div>
</section>