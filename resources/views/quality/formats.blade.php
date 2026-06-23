<section class="col-span-12 w-full flex flex-col items-center px-1">
    
{{-- Título de la sección --}}
<div class="mt-6">
 @can('quality.buttons.show')
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

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="prueba">
      <img src="{{ asset('images/quality/1.png') }}" alt="Inspección de Recepción de Producto" class="{{ $imgCls }}">
      <span>Inspección de Recepción de Producto</span>
    </button>
    @include('quality.modals.recepcion01')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="FT">
      <img src="{{ asset('images/quality/2.png') }}" alt="Ficha Técnica" class="{{ $imgCls }}">
      <span>Ficha Técnica</span>
    </button>
    @include('quality.modals.02')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="HS">
      <img src="{{ asset('images/quality/3.png') }}" alt="Hoja de Seguridad" class="{{ $imgCls }}">
      <span>Hoja de Seguridad</span>
    </button>
    @include('quality.modals.03')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="almacen">
      <img src="{{ asset('images/quality/4.png') }}" alt="Inspección de Almacén" class="{{ $imgCls }}">
      <span>Inspección de Almacén</span>
    </button>
    @include('quality.modals.almacen04')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="inc">
      <img src="{{ asset('images/quality/6.png') }}" alt="Reporte de Incidencias" class="{{ $imgCls }}">
      <span>Reporte de Incidencias</span>
    </button>
    @include('quality.modals.incidencias06')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="CC">
      <img src="{{ asset('images/quality/7.png') }}" alt="Certificado de Calidad" class="{{ $imgCls }}">
      <span>Certificado de Calidad</span>
    </button>
    @include('quality.modals.07')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="ins">
      <img src="{{ asset('images/quality/8.png') }}" alt="Inspección de Recepción de Carga" class="{{ $imgCls }}">
      <span>Inspección de Recepción de Carga</span>
    </button>
    @include('quality.modals.inspeccion08')

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="rc">
      <img src="{{ asset('images/quality/9.png') }}" alt="Retroalimentación del Cliente" class="{{ $imgCls }}">
      <span>Retroalimentación del Cliente</span>
    </button>
    @include('quality.modals.retrocli09')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="sp">
      <img src="{{ asset('images/quality/10.png') }}" alt="Inspección de Salida de Producto" class="{{ $imgCls }}">
      <span>Inspección de Salida de Producto</span>
    </button>
    @include('quality.modals.salida10')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="rp">
      <img src="{{ asset('images/quality/11.png') }}" alt="Retroalimentación al Proveedor" class="{{ $imgCls }}">
      <span>Retroalimentación al Proveedor</span>
    </button>
    @include('quality.modals.retropro11')
 
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="qj">
      <img src="{{ asset('images/quality/12.png') }}" alt="Quejas y Sugerencias" class="{{ $imgCls }}">
      <span>PQRS</span>
    </button>
    @include('quality.modals.quejas12')

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="certificadoProveedor">
        <img src="{{ asset('images/certificado.png') }}" alt="Certificado de Calidad" class="{{ $imgCls }}">
        <span>Certificado de Calidad Proveedor</span>
    </button>
    @include('quality.modals.certificado_proveedor')

 @endcan
</div>
</section>