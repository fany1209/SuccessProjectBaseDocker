@extends('adminlte::page')

@section('title', 'Bitácora de Movimientos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark"><i class="fas fa-history text-primary"></i> Bitácora de Movimientos</h1>
        </div>
    </div>
@stop

@section('content')
    <!-- Gráfica de Usuarios con más conexiones -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-info card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Usuarios con más conexiones (Top 10)</h3>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="loginsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary card-outline shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Tipo de Registro</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Datos Adicionales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td>{{ $activity->id }}</td>
                                <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if($activity->causer)
                                        <strong>{{ $activity->causer->name }}</strong>
                                    @else
                                        <span class="text-muted">Sistema</span>
                                    @endif
                                </td>
                                <td>{{ $activity->subject_type ? class_basename($activity->subject_type) : 'Autenticación' }}</td>
                                <td>
                                    @php
                                        $color = 'secondary';
                                        switch($activity->event) {
                                            case 'created': $color = 'success'; break;
                                            case 'updated': $color = 'primary'; break;
                                            case 'deleted': $color = 'danger'; break;
                                        }
                                        if($activity->log_name === 'auth') $color = 'info';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">
                                        {{ strtoupper($activity->event ?? $activity->log_name) }}
                                    </span>
                                </td>
                                <td>{{ $activity->description }}</td>
                                <td>
                                    @if($activity->properties->count() > 0)
                                        <button class="btn btn-sm btn-outline-info" onclick='showDetails(@json($activity->properties))'>
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>

    <!-- Modal for details -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h5 class="modal-title text-white" id="detailsModalLabel"><i class="fas fa-info-circle"></i> Detalles del Movimiento</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body bg-light">
            <pre><code id="detailsContent" style="white-space: pre-wrap; font-family: monospace;"></code></pre>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    function showDetails(properties) {
        document.getElementById('detailsContent').textContent = JSON.stringify(properties, null, 2);
        $('#detailsModal').modal('show');
    }

    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('loginsChart').getContext('2d');
        var labels = @json($chartLabels);
        var data = @json($chartData);

        if (labels.length === 0) {
            // Mostrar un mensaje si no hay datos
            var container = document.getElementById('loginsChart').parentElement;
            container.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100 text-muted">Aún no hay registros de inicio de sesión para graficar. Sal y vuelve a entrar para generar datos.</div>';
            return;
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad de Inicios de Sesión',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        });
    });
</script>
@stop
