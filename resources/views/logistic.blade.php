{{--
Logistic
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-25
--}}
@extends('layouts.app')
@section('content')
<div class="flex flex-col lg:flex-row lg:justify-between items-start gap-2 m-2">
    <div class="flex flex-col items-start gap-2 bg-white shadow-md rounded-lg w-full lg:w-[15%] p-2">
        <x-tittle-form class="border-b-2 border-green-700 pb-2">Menu</x-tittle-form>
        <x-nav-button data-button="transport-lines" icon="ri-truck-line" class="option-btn border-2 border-green-500">Transport Lines</x-nav-button>
        <x-nav-button data-button="operators" icon="ri-id-card-line" class="option-btn">Operators</x-nav-button>
        <x-nav-button data-button="vehicles" icon="ri-car-fill" class="option-btn">Vehicles</x-nav-button>
        <x-nav-button data-button="trailers" icon="ri-bus-2-fill" class="option-btn">Trailers</x-nav-button>
    </div>
    <div class="flex flex-col items-start bg-white shadow-md rounded-lg w-full lg:w-[85%] p-2">
        <x-tittle-form id="opt-tittle" class="border-s-2 border-green-700 ps-2 ms-2">Warehouse</x-tittle-form>
        <div id="transport-line-layout" class="">
            @include('logistic.transport-lines.tools')
            @include('logistic.transport-lines.table')
        </div>
        <div id="operator-layout" class="hidden">
            @include('logistic.operators.tools')
            @include('logistic.operators.table')
        </div>
        <div id="vehicle-layout" class="hidden">
            @include('logistic.vehicles.tools')
            @include('logistic.vehicles.table')
        </div>
        <div id="trailer-layout" class="hidden">
            @include('logistic.trailers.tools')
            @include('logistic.trailers.table')
        </div>
        <hr class="border-t-2 border-gray-600 border-dashed w-full my-2">
        <x-tittle-form class="border-b-2 border-green-700 pb-2">Dashboard Transports</x-tittle-form>
        <div class="flex flex-col lg:flex-row lg:justify-center items-center w-full gap-3 my-3">
            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-green-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-id-card-line text-4xl"></i> Operators</h3>                
                <p class="text-2xl text-white font-bold">{{ $count_operators }}</p>
            </span>
            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-yellow-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-truck-line text-4xl"></i> Transport Lines</h3>
                <p class="text-2xl text-white font-bold">{{ $count_tLines }}</p>
            </span>
            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-blue-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-car-fill text-4xl"></i> Vehicles</h3>
                <p class="text-2xl text-white font-bold">{{ $count_vehicles }}</p>
            </span>
            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-orange-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-bus-2-fill text-4xl"></i> Trailers</h3>
                <p class="text-2xl text-white font-bold">{{ $count_trailers }}</p>
            </span>
        </div>
        <div class="flex flex-col lg:flex-row lg:justify-center items-center w-full gap-3 my-3">
            <div id="donutchart-1" class="w-full h-full lg:h-[500px]"></div>
            <div id="donutchart-2" class="w-full h-full lg:h-[500px]"></div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        $.ajax({
            url: "{{ route('logistic.charts') }}",
            dataType: 'json',
            success: function(response) {
                var dataArray = [];
                dataArray.push(['Tailers', 'Quantity']);

                response.trailers_per_tl.forEach(function(item) {
                    dataArray.push([item.name, parseInt(item.trailers)]);
                });

                var data = google.visualization.arrayToDataTable(dataArray);
                var options = {
                    title: 'Trailers per Line',
                    pieHole: 0.4,
                };
    
                var chart = new google.visualization.PieChart(document.getElementById('donutchart-1'));
                chart.draw(data, options);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los datos:", error);
            }
        });
    }
</script>
<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        $.ajax({
            url: "{{ route('logistic.charts') }}",
            dataType: 'json',
            success: function(response) {
                var dataArray = [];
                dataArray.push(['Vehicle', 'Quantity']);

                response.vehicles_per_tl.forEach(function(item) {
                    dataArray.push([item.name, parseInt(item.vehicles)]);
                });

                var data = google.visualization.arrayToDataTable(dataArray);
                var options = {
                    title: 'Vehicles per Line',
                    pieHole: 0.4,
                };
    
                var chart = new google.visualization.PieChart(document.getElementById('donutchart-2'));
                chart.draw(data, options);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los datos:", error);
            }
        });
    }
</script>
<script>
$(function(){
    function logisticReactive(){
        $('.option-btn').on('click',function(){
            const option = $(this).data('button');
            switch(option){
                case 'transport-lines':
                    $('.option-btn').removeClass('border-2 border-green-500');
                    $(this).addClass('border-2 border-green-500');
                    $('#transport-line-layout').removeClass('hidden');
                    $('#operator-layout,#vehicle-layout,#trailer-layout').addClass('hidden');
                    $('#opt-tittle').text('Transport lines');
                    break;
                case 'operators':
                    $('.option-btn').removeClass('border-2 border-green-500');
                    $(this).addClass('border-2 border-green-500');
                    $('#operator-layout').removeClass('hidden');
                    $('#transport-line-layout,#vehicle-layout,#trailer-layout').addClass('hidden');
                    $('#opt-tittle').text('Operators');
                    break;
                case 'vehicles':
                    $('.option-btn').removeClass('border-2 border-green-500');
                    $(this).addClass('border-2 border-green-500');
                    $('#vehicle-layout').removeClass('hidden');
                    $('#transport-line-layout,#operator-layout,#trailer-layout').addClass('hidden');
                    $('#opt-tittle').text('Vehicles');
                    break;
                case 'trailers':
                    $('.option-btn').removeClass('border-2 border-green-500');
                    $(this).addClass('border-2 border-green-500');
                    $('#trailer-layout').removeClass('hidden');
                    $('#transport-line-layout,#operator-layout,#vehicle-layout').addClass('hidden');
                    $('#opt-tittle').text('Trailers');
                    break;
                default:
                    $('.option-btn').removeClass('border-2 border-green-500');
                    $(this).addClass('border-2 border-green-500');
                    break;
            }
        });
    }
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
    logisticReactive();
});
</script>    
@endpush