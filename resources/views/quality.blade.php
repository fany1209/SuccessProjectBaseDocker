@extends('layouts.app')

@section('content')
@include('quality.formats')

  <div class="flex justify-center my-4">
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
  </div>

@include('laboratory.modals.lote')

@include('quality.table')
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
