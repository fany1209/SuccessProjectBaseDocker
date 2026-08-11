@extends('layouts.app')
@section('content')
@include('laboratory.formats')
@can('laboratory.update')
  <div class="w-full mt-4 flex flex-wrap justify-center gap-3">
    {{--<a href="{{ route('laboratory.monitoring.csv') }}"
      class="inline-flex items-center gap-2
              bg-[#198754] hover:bg-[#157347] text-white
              text-sm font-medium rounded-lg
              px-4 py-2 shadow
              focus:outline-none focus:ring-2 focus:ring-[#198754]/30">
      <img src="{{ asset('images/excel.png') }}" alt="Excel" class="w-4 h-4">
      <span>Descargar inventario de suelos</span>
    </a>--}}

    <a href="{{ route('laboratory_samples.export') }}"
      class="inline-flex items-center gap-2
              bg-[#198754] hover:bg-[#157347] text-white
              text-sm font-medium rounded-lg
              px-4 py-2 shadow
              focus:outline-none focus:ring-2 focus:ring-[#198754]/30">
      <img src="{{ asset('images/excel.png') }}" alt="Excel" class="w-4 h-4">
      <span>Descargar inventario de muestras</span>
    </a>

    <a href="{{ route('lab.samples.inv_samples') }}"
      class="inline-flex items-center gap-2
              bg-[#198754] hover:bg-[#157347] text-white
              text-sm font-medium rounded-lg
              px-4 py-2 shadow
              focus:outline-none focus:ring-2 focus:ring-[#198754]/30">
      Ver inventario de muestras
    </a>
  </div>

  <div class="w-full mt-6 flex justify-center">
    <button type="button"
      class="open-modal inline-flex items-center gap-2
            bg-[#198754] hover:bg-[#157347] text-white
            text-sm font-medium rounded-full
            px-6 py-2 shadow
            focus:outline-none focus:ring-2 focus:ring-[#198754]/30"
      data-target="20">

      <svg xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
      </svg>
      Solicitar lote y/o producto
    </button>
  </div>

@include('laboratory.modals.lote')

@endcan
@include('laboratory.table_RS')
@include('laboratory.table_customer_sample_requests')
@can('laboratory.update')
{{--@include('laboratory.table_AIS')
@include('laboratory.table_PTS')
--}}
@include('laboratory.table_react')
@include('laboratory.table_materials')
@include('laboratory.table_equipments')
@include('muestras.index')
@include('laboratory.grafica')
@endcan

@endsection
@push('js')
<script>
$(function () {
  $(document).on('click', '.open-modal', function () {
    const target = $(this).data('target');
    if (target) $('#' + target).removeClass('hidden');
  });
  $(document).on('click', '.close-modal', function () {
    $(this).closest('.fixed').addClass('hidden');
  });
  $(document).on('click', '.fixed', function (e) {
    if ($(e.target).is('.fixed')) $(this).addClass('hidden');
  });
});
</script>
@endpush