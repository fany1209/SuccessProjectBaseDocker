{{--
Warehouse
Fecha de creación: 21-07-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 27-01-26
--}}
@extends('layouts.app')
@section('content')
@include('warehouse.tools')
@include('warehouse.table')
@include('warehouse.temperature')

@endsection
@push('js')
<script>
$(function(){
    //Modales
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
});
</script>
@endpush