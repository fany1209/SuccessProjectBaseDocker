@extends('layouts.app')
@section('content')
<x-section-1>
    @include('sales.tools')
    @include('sales.table')
    @include('sales.addSale')
    @include('sales.modals.viewMore')  
    @include('sales.updateSale')
    @can('sales.show.all')
    @include('sales.grafica')
    @include('sales.grafica2')
    @endcan
</x-section-1>
@endsection
@push('js')
{{-- Cambio de ventana a Quotes --}}
<script>
$(function(){
    $('#toggleSalesView').on('change', function () {
        if (!$(this).is(':checked')) {
            window.location.href = '/quotes';
        }
    });
    function modals(){
        $(document).on('click','.open-modal',function(){
            const target = $(this).data('target');
            $('#' + target).removeClass('hidden');
        });
        $(document).on('click','.close-modal',function(){
            $(this).closest('.fixed').addClass('hidden');
        });
        $(document).on('click','.fixed',function(e){
            if ($(e.target).is('.fixed')) {
                $(this).addClass('hidden');
            }
        });
    }
    modals();

    // Alerta emergente de retención de clientes
    @if(auth()->check() && auth()->user()->unreadNotifications->where('type', 'App\Notifications\CustomerRetentionAlert')->count() > 0)
        let retentionAlerts = {!! json_encode(auth()->user()->unreadNotifications->where('type', 'App\Notifications\CustomerRetentionAlert')->pluck('data.message')) !!};
        let htmlContent = '<div style="text-align: left; font-size: 14px; max-height: 200px; overflow-y: auto;">';
        retentionAlerts.forEach(function(msg) {
            htmlContent += '<div style="padding: 10px; border-bottom: 1px solid #eee; margin-bottom: 5px;">⚠️ ' + msg + '</div>';
        });
        htmlContent += '</div>';

        Swal.fire({
            icon: 'warning',
            title: '¡Atención Equipo de Ventas!',
            html: '<strong>Tienes clientes frecuentes que no han comprado recientemente:</strong><br><br>' + htmlContent,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    @endif
});
</script>
@endpush
