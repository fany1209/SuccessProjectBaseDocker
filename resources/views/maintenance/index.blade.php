@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mantenimiento Preventivo (Alertas Pendientes)</h1>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            <i class="fas fa-bell mr-1"></i> Alertas Pendientes
        </a>
        <a href="{{ route('maintenance.history') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
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

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-700">Folio</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Equipo</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Plan</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Fecha Programada</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Estado</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($records as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm text-gray-800">{{ $record->code }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $record->equipment->name ?? 'N/A' }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $record->maintenancePlan->name ?? 'N/A' }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $record->scheduled_date->format('d/m/Y') }}</td>
                        <td class="p-3 text-sm">
                            @if($record->status == 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pendiente</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Impreso (En Proceso)</span>
                            @endif
                        </td>
                        <td class="p-3 text-sm flex gap-2">
                            <a href="{{ route('maintenance.print', $record->id) }}" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs inline-flex items-center" target="_blank">
                                <i class="fas fa-print mr-1"></i> Imprimir
                            </a>
                            
                            @if(auth()->user() && auth()->user()->isAdmin())
                                @if($record->status === 'printed')
                                <button type="button" class="open-modal px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs inline-flex items-center" data-target="completeModal{{ $record->id }}">
                                    <i class="fas fa-check mr-1"></i> Completar
                                </button>
                                @else
                                <button type="button" onclick="Swal.fire('Bloqueado', 'Debes imprimir el formato primero.', 'warning')" class="px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed text-xs inline-flex items-center">
                                    <i class="fas fa-lock mr-1"></i> Completar
                                </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    
                    @if(auth()->user() && auth()->user()->isAdmin() && $record->status === 'printed')
                    <!-- Modal for Completion -->
                    <x-modal id="completeModal{{ $record->id }}" maxWidth="md">
                        <div class="p-2">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h2 class="text-xl font-bold text-gray-800">Completar Folio {{ $record->code }}</h2>
                                <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
                            </div>
                            <form action="{{ route('maintenance.complete', $record->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <p class="text-sm text-gray-600">Sube el checklist físico firmado como evidencia para marcar este mantenimiento como completado.</p>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Evidencia (PDF/Imagen)</label>
                                    <input type="file" name="evidence_file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required accept=".pdf,.png,.jpg,.jpeg">
                                </div>
                                
                                <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                                    <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar y Completar</button>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
