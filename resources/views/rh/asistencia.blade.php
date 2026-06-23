@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard de Asistencias - Success CEDIS</h1>

    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-body">
            <form action="{{ route('attendance.index') }}" method="GET" class="row">
                <div class="col-md-4">
                    <label>Trabajador</label>
                    <select name="empleado" class="form-control">
                        <option value="">Todos los empleados</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp }}" {{ $empleadoSeleccionado == $emp ? 'selected' : '' }}>{{ $emp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label>Inicio</label><input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}"></div>
                <div class="col-md-3"><label>Fin</label><input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}"></div>
                <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
            </form>
        </div>
    </div>

  <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm bg-success text-white"> 
                <div class="card-body text-center">
                    <h5 class="card-title text-uppercase font-weight-bold">Total Tiempo Extra</h5>
                    <h2 class="display-5 mb-0">{{ $textoTiempoExtraTotal }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-warning text-dark">
                <div class="card-body text-center">
                    <h5 class="card-title text-uppercase font-weight-bold">Omisiones de Checada</h5>
                    <h2 class="display-5 mb-0">{{ $omisionesChecada }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm bg-danger text-white">
                <div class="card-body text-center">
                    <h5 class="card-title text-uppercase font-weight-bold">Total Tiempo Perdido</h5>
                    <h2 class="display-5 mb-0">{{ $textoTiempoPerdidoTotal }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">Horas Efectivas Trabajadas</div>
        <div class="card-body"><div id="chart_div" style="width: 100%; height: 300px;"></div></div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white">Análisis de Retardos y Tiempo Extra (Minutos)</div>
        <div class="card-body"><div id="chart_incidencias_div" style="width: 100%; height: 350px;"></div></div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-danger text-white">Alertas de Indisciplina: Omisiones de Checada por Día</div>
        <div class="card-body"><div id="chart_omisiones_div" style="width: 100%; height: 250px;"></div></div>
    </div>

    <div class="card shadow-sm border-success">
        <div class="card-body">
            <form action="{{ route('attendance.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit" class="btn btn-success mt-2">Importar CSV</button>
            </form>
        </div>
    </div>
</div>
@include('rh.table_asis')


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var data1 = google.visualization.arrayToDataTable({!! json_encode($chartData) !!});
            var options1 = {
                title: 'Rendimiento Total en Horas',
                colors: ['#17a2b8'],
                legend: { position: 'none' },
                hAxis: { slantedText: true, slantedTextAngle: 45, textStyle: { fontSize: 11 } },
                chartArea: { width: '90%', height: '65%' } 
            };
            var chart1 = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart1.draw(data1, options1);

            var data2 = google.visualization.arrayToDataTable({!! json_encode($chartDataIncidencias) !!});
            var options2 = {
                title: 'Minutos de Incidencias por Día',
                colors: ['#ffc107', '#fd7e14', '#28a745'], 
                legend: { position: 'top' },
                hAxis: { slantedText: true, slantedTextAngle: 45, textStyle: { fontSize: 11 } },
                vAxis: { title: 'Minutos' },
                chartArea: { width: '90%', height: '60%' }, 
                isStacked: false
            };
            var chart2 = new google.visualization.ColumnChart(document.getElementById('chart_incidencias_div'));
            chart2.draw(data2, options2);

            var data3 = google.visualization.arrayToDataTable({!! json_encode($chartDataOmisiones) !!});
            var options3 = {
                title: 'Cantidad de Checadas Faltantes',
                colors: ['#dc3545'], 
                legend: { position: 'none' },
                hAxis: { slantedText: true, slantedTextAngle: 45, textStyle: { fontSize: 11 } },
                vAxis: { 
                    title: 'Omisiones', 
                    format: '0', 
                    minValue: 0 
                },
                chartArea: { width: '90%', height: '60%' }
            };
            var chart3 = new google.visualization.ColumnChart(document.getElementById('chart_omisiones_div'));
            chart3.draw(data3, options3);
        }
    });
</script>
@endsection