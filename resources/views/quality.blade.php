@extends('layouts.app')

@section('content')
@include('quality.formats')

  <div class="flex flex-wrap justify-center my-4 gap-4">
    <button type="button"
      class="open-modal inline-flex items-center gap-2
            bg-[#198754] hover:bg-[#157347] text-white
            text-sm font-medium rounded-full
            px-5 py-2 shadow
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
            px-5 py-2 shadow
            focus:outline-none focus:ring-2 focus:ring-blue-600/30"
      data-target="batch-requests-modal">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      Ver Peticiones de Batch/Lotes
      
      <span id="pending-batch-badge" 
            class="hidden bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
      </span>
    </button>
  </div>

@include('laboratory.modals.lote')

@include('quality.table')

<x-modal id="batch-requests-modal" maxWidth="full">
    <div class="p-2 w-full mx-auto bg-white rounded-lg max-h-[85vh] overflow-y-auto relative">
        <button type="button" class="close-modal absolute top-2 right-2 text-gray-500 hover:text-red-500 font-bold text-xl px-2">
            &times;
        </button>
        @include('formats.laboratory.lot_requests._table')
    </div>
</x-modal>
@can('quality.buttons.show')
  @include('quality.table_certificates')
  @include('quality.grafica')
  @include('quality.certificates_quality')
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
