@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-10 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">
                            Cursos Programados
                        </h3>
                        <div class="flex gap-2">
                            <button onclick="exportTableToExcel('cursos-table', 'Cursos_Programados')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Descargar Excel
                            </button>
                            <a href="{{ route('rh.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                Volver a RH
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table id="cursos-table" class="min-w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left">
                                    <th class="px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">FECHA</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">SEDE</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">HORARIO</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">CURSO</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">OBJETIVO</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">ASISTENTES</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($cursos as $c)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $c->fecha->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $c->sede }}</td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $c->horario }}</td>
                                    <td class="px-4 py-3 text-gray-800 font-medium">{{ $c->curso }}</td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $c->objetivo }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-xs">
                                        @if($c->asistentes && is_array($c->asistentes) && count($c->asistentes) > 0)
                                            <ul class="list-disc list-inside">
                                                @foreach($c->asistentes as $asistente)
                                                    <li>{{ $asistente }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-gray-400 italic">Sin asistentes</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No hay cursos registrados aún.
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

@push('js')
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<script>
    function exportTableToExcel(tableID, filename = ''){
        var table = document.getElementById(tableID);
        // Convierte la tabla HTML directamente a un libro de Excel (formato real)
        var wb = XLSX.utils.table_to_book(table, {sheet: "Cursos"});
        
        // Descarga el archivo generado con formato real .xlsx
        XLSX.writeFile(wb, (filename ? filename : 'datos') + '.xlsx');
    }
</script>
@endpush
