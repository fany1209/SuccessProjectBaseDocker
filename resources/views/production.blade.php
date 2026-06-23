@extends('layouts.app')

@section('content')
@include('production.formats')

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
