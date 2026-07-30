@extends('layouts.app')

@section('content')
@php
    $totalEncuestas = count($resultados);
    $promedioGlobal = 0;
    
    // Arrays para guardar la suma de cada pregunta y calcular el promedio
    $sumas = [
        'q1' => 0, 'q2' => 0, 'q3' => 0, 'q4' => 0, 'q5' => 0,
        'q6' => 0, 'q7' => 0, 'q8' => 0, 'q9' => 0, 'q10' => 0, 'q11' => 0
    ];

    if ($totalEncuestas > 0) {
        $sumaTotal = 0;
        foreach ($resultados as $res) {
            $sumas['q1'] += $res->q1_ambiente;
            $sumas['q2'] += $res->q2_respeto;
            $sumas['q3'] += $res->q3_comunicacion_oportuna;
            $sumas['q4'] += $res->q4_comunicacion_escucha;
            $sumas['q5'] += $res->q5_liderazgo;
            $sumas['q6'] += $res->q6_reconocimiento;
            $sumas['q7'] += $res->q7_desarrollo;
            $sumas['q8'] += $res->q8_motivacion;
            $sumas['q9'] += $res->q9_satisfaccion;
            $sumas['q10'] += $res->q10_bienestar_carga;
            $sumas['q11'] += $res->q11_bienestar_preocupacion;
            
            $sumaTotal += ($res->q1_ambiente + $res->q2_respeto + $res->q3_comunicacion_oportuna +
                           $res->q4_comunicacion_escucha + $res->q5_liderazgo + $res->q6_reconocimiento +
                           $res->q7_desarrollo + $res->q8_motivacion + $res->q9_satisfaccion +
                           $res->q10_bienestar_carga + $res->q11_bienestar_preocupacion) / 11;
        }
        $promedioGlobal = number_format($sumaTotal / $totalEncuestas, 2);
        
        foreach ($sumas as $key => $val) {
            $sumas[$key] = number_format($val / $totalEncuestas, 2);
        }
    }
@endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-10 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">
                            Respuestas de Encuestas de Clima Laboral
                        </h3>
                        <a href="{{ route('expediente.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Volver a RH
                        </a>
                    </div>

                    <!-- KPIs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-5 border border-blue-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">Total Encuestas</p>
                                <p class="text-3xl font-bold text-blue-900 mt-1">{{ $totalEncuestas }}</p>
                            </div>
                            <div class="p-3 bg-blue-200 rounded-full text-blue-700">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-5 border border-green-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Promedio Global</p>
                                <p class="text-3xl font-bold text-green-900 mt-1">{{ $promedioGlobal }} <span class="text-lg font-normal text-green-700">/ 5</span></p>
                            </div>
                            <div class="p-3 bg-green-200 rounded-full text-green-700">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-5 border border-purple-100 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-600 uppercase tracking-wider">Estado General</p>
                                <p class="text-2xl font-bold text-purple-900 mt-1">
                                    @if($promedioGlobal >= 4.5) Excelente
                                    @elseif($promedioGlobal >= 4.0) Bueno
                                    @elseif($promedioGlobal >= 3.0) Regular
                                    @elseif($totalEncuestas > 0) Crítico
                                    @else N/A
                                    @endif
                                </p>
                            </div>
                            <div class="p-3 bg-purple-200 rounded-full text-purple-700">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    @if($totalEncuestas > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <h4 class="text-center text-gray-700 font-bold mb-4">Promedio por Pregunta</h4>
                            <div style="height: 300px; width: 100%;">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <h4 class="text-center text-gray-700 font-bold mb-4">Análisis por Dimensión</h4>
                            <div class="flex justify-center">
                                <div style="height: 300px; width: 100%; max-width: 400px;">
                                    <canvas id="radarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif


                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Promedio de las 11 preguntas (Escala 1-5)">Promedio</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q1: Ambiente">Q1</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q2: Respeto">Q2</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q3: Comunicación oportuna">Q3</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q4: Escucha">Q4</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q5: Liderazgo">Q5</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q6: Reconocimiento">Q6</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q7: Desarrollo">Q7</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q8: Motivación">Q8</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q9: Satisfacción">Q9</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q10: Carga de trabajo">Q10</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" title="Q11: Bienestar">Q11</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sugerencias (Q12)</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($resultados as $res)
                                    @php
                                        $suma = $res->q1_ambiente + $res->q2_respeto + $res->q3_comunicacion_oportuna +
                                                $res->q4_comunicacion_escucha + $res->q5_liderazgo + $res->q6_reconocimiento +
                                                $res->q7_desarrollo + $res->q8_motivacion + $res->q9_satisfaccion +
                                                $res->q10_bienestar_carga + $res->q11_bienestar_preocupacion;
                                        $promedio = number_format($suma / 11, 2);
                                        $color = $promedio >= 4 ? 'text-green-600 bg-green-100' : ($promedio >= 3 ? 'text-yellow-600 bg-yellow-100' : 'text-red-600 bg-red-100');
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-medium">#{{ $res->id }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ $promedio }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q1_ambiente }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q2_respeto }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q3_comunicacion_oportuna }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q4_comunicacion_escucha }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q5_liderazgo }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q6_reconocimiento }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q7_desarrollo }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q8_motivacion }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q9_satisfaccion }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q10_bienestar_carga }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 text-center">{{ $res->q11_bienestar_preocupacion }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="{{ $res->q12_sugerencias }}">
                                            {{ $res->q12_sugerencias ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $res->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="px-4 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No hay resultados de encuestas de clima laboral todavía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($totalEncuestas > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labelsBar = [
                'Q1 Ambiente', 'Q2 Respeto', 'Q3 Com. Oport.', 'Q4 Escucha', 
                'Q5 Liderazgo', 'Q6 Reconoci.', 'Q7 Desarrollo', 'Q8 Motivación', 
                'Q9 Satisfac.', 'Q10 Carga', 'Q11 Bienestar'
            ];
            const dataBar = [
                {{ $sumas['q1'] }}, {{ $sumas['q2'] }}, {{ $sumas['q3'] }}, {{ $sumas['q4'] }}, 
                {{ $sumas['q5'] }}, {{ $sumas['q6'] }}, {{ $sumas['q7'] }}, {{ $sumas['q8'] }}, 
                {{ $sumas['q9'] }}, {{ $sumas['q10'] }}, {{ $sumas['q11'] }}
            ];

            // Gráfico de Barras
            new Chart(document.getElementById('barChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labelsBar,
                    datasets: [{
                        label: 'Puntaje Promedio (1 a 5)',
                        data: dataBar,
                        backgroundColor: 'rgba(34, 197, 94, 0.6)',
                        borderColor: 'rgb(22, 163, 74)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, max: 5 }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Gráfico de Radar
            new Chart(document.getElementById('radarChart').getContext('2d'), {
                type: 'radar',
                data: {
                    labels: ['Ambiente/Respeto', 'Comunicación', 'Liderazgo', 'Desarrollo', 'Motivación', 'Bienestar'],
                    datasets: [{
                        label: 'Promedio Dimensional',
                        data: [
                            ({{ $sumas['q1'] }} + {{ $sumas['q2'] }}) / 2,
                            ({{ $sumas['q3'] }} + {{ $sumas['q4'] }}) / 2,
                            ({{ $sumas['q5'] }} + {{ $sumas['q6'] }}) / 2,
                            {{ $sumas['q7'] }},
                            ({{ $sumas['q8'] }} + {{ $sumas['q9'] }}) / 2,
                            ({{ $sumas['q10'] }} + {{ $sumas['q11'] }}) / 2
                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgb(37, 99, 235)',
                        pointBackgroundColor: 'rgb(37, 99, 235)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(37, 99, 235)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: 5
                        }
                    }
                }
            });
        });
    </script>
    @endif
@endsection
