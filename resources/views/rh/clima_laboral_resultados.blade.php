@extends('layouts.app')

@section('content')
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
@endsection
