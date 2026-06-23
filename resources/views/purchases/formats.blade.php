<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="mt-6 text-center">
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
    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="01">
      <img src="{{ asset('images/purchases/01.png') }}" alt="01" class="{{ $imgCls }}">
      <span>Orden de compra</span>
    </button>

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="02">
      <img src="{{ asset('images/purchases/02.png') }}" alt="02" class="{{ $imgCls }}">
      <span>Criterio de selección de proveedores</span>
    </button>

    <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="03">
      <img src="{{ asset('images/purchases/03.png') }}" alt="03" class="{{ $imgCls }}">
      <span>Evaluación de proveedores</span>
    </button>

  </div>
    <a href="{{ route('purchases.po.demo') }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded-md text-white" style="background:#198754;">Ver PDF (demo)</a>
</section>
