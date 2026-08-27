@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Mantenimientos</h1>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
            <i class="fas fa-bell mr-1"></i> Alertas Pendientes
        </a>
        <a href="{{ route('maintenance.history') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            <i class="fas fa-history mr-1"></i> Historial Completado
        </a>
        <a href="{{ route('maintenance.areas.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
            <i class="fas fa-tags mr-1"></i> Categorías / Áreas
        </a>
        <a href="{{ route('maintenance.equipment.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
            <i class="fas fa-cogs mr-1"></i> Catálogo de Equipos
        </a>
        <a href="{{ route('maintenance.plans.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
            <i class="fas fa-tasks mr-1"></i> Planes de Mantenimiento
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-700">Folio</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Equipo</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Plan</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Completado En</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Evidencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm text-gray-800">{{ $record->code }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $record->equipment->name ?? 'N/A' }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $record->maintenancePlan->name ?? 'N/A' }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $record->completed_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-sm text-gray-800">
                            @if($record->evidence_file)
                                <a href="{{ Storage::url($record->evidence_file) }}" target="_blank" class="text-blue-600 hover:underline">
                                    <i class="fas fa-file-pdf"></i> Ver Documento
                                </a>
                            @else
                                <span class="text-gray-500">Sin archivo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No hay mantenimientos completados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
