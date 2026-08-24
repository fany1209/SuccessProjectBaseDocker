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

  <div class="w-full mt-6 flex justify-center gap-4">
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

    <button type="button"
      class="open-modal inline-flex items-center gap-2 relative
            bg-blue-600 hover:bg-blue-700 text-white
            text-sm font-medium rounded-full
            px-6 py-2 shadow
            focus:outline-none focus:ring-2 focus:ring-blue-600/30"
      data-target="batch-requests-modal">
      <span id="badge-lotes" class="hidden absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow ring-2 ring-white"></span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      Ver Peticiones de Batch/Lotes
    </button>
  </div>

<x-modal id="batch-requests-modal" maxWidth="full">
    <div class="p-2 w-full mx-auto bg-white rounded-lg max-h-[85vh] overflow-y-auto relative">
        <button type="button" class="close-modal absolute top-2 right-2 text-gray-500 hover:text-red-500 font-bold text-xl px-2">
            &times;
        </button>
        @include('formats.laboratory.lot_requests._table')
    </div>
</x-modal>

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

  // Notificación de nuevos lotes para el laboratorio
  function checkNewLotes() {
      $.get("{{ route('lot.request.count_completed') }}", function(res) {
          const count = res.count || 0;
          const lastSeen = parseInt(localStorage.getItem('lab_seen_lotes_count')) || 0;
          const diff = count - lastSeen;
          
          if (diff > 0) {
              $('#badge-lotes').removeClass('hidden').text(diff > 9 ? '9+' : diff);
          } else {
              $('#badge-lotes').addClass('hidden');
          }
      });
  }

  // Polling cada 30 segundos
  setInterval(checkNewLotes, 30000);
  // Chequeo inicial
  checkNewLotes();

  // Resetear el globo cuando se abre el modal
  $(document).on('click', '[data-target="batch-requests-modal"]', function() {
      $.get("{{ route('lot.request.count_completed') }}", function(res) {
          localStorage.setItem('lab_seen_lotes_count', res.count);
          $('#badge-lotes').addClass('hidden');
      });
  });
});
</script>
@endpush