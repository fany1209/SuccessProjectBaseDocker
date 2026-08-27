@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Planes de Mantenimiento</h1>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
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
        <a href="{{ route('maintenance.plans.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
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
    
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: `
                        <ul class="text-left">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `
                });
            });
        </script>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-700">Lista de Planes</h2>
            <button type="button" class="open-modal px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center shadow" data-target="createPlanModal">
                <i class="fas fa-plus mr-2"></i> Nuevo Plan
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-700">Equipo</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Nombre del Plan</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Frecuencia (Días)</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Tipo</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Puntos a Revisar</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($plans as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm text-gray-800">{{ $plan->equipment->name ?? 'N/A' }} ({{ $plan->equipment->code ?? '' }})</td>
                        <td class="p-3 text-sm text-gray-800">{{ $plan->name }}</td>
                        <td class="p-3 text-sm text-gray-800">Cada {{ $plan->frequency_days }} días</td>
                        <td class="p-3 text-sm text-gray-800">{{ $plan->type == 'frequent' ? 'Frecuente' : 'Profundo' }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $plan->checklistItems->count() }} puntos</td>
                        <td class="p-3 text-sm flex gap-2">
                            <button type="button" class="open-modal px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs" data-target="editPlanModal{{ $plan->id }}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            
                            <form action="{{ route('maintenance.plans.destroy', $plan->id) }}" method="POST" class="inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($plans as $plan)
<!-- Edit Modal -->
<x-modal id="editPlanModal{{ $plan->id }}" maxWidth="2xl">
    <div class="p-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Editar Plan de Mantenimiento</h2>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
        </div>
        <form action="{{ route('maintenance.plans.update', $plan->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Equipo</label>
                    <select name="equipment_id" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->id }}" {{ $plan->equipment_id == $eq->id ? 'selected' : '' }}>{{ $eq->name }} ({{ $eq->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Plan</label>
                    <input type="text" name="name" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $plan->name }}" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Frecuencia (Días)</label>
                    <input type="number" name="frequency_days" min="1" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $plan->frequency_days }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select name="type" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="frequent" {{ $plan->type == 'frequent' ? 'selected' : '' }}>Frecuente</option>
                        <option value="deep" {{ $plan->type == 'deep' ? 'selected' : '' }}>Profundo</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h6 class="text-md font-bold text-gray-800">Puntos de Revisión (Checklist Físico)</h6>
                <p class="text-sm text-gray-500 mb-3">Estas preguntas aparecerán en el PDF que el técnico imprimirá.</p>
                
                <div id="checklist-container-edit-{{ $plan->id }}" class="space-y-2">
                    @foreach($plan->checklistItems as $item)
                    <div class="flex gap-2 checklist-row">
                        <input type="text" name="checklist_items[]" class="w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $item->description }}" required>
                        <button type="button" class="remove-checklist-btn px-3 py-2 bg-red-100 text-red-600 rounded border border-red-200 hover:bg-red-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                
                <button type="button" class="add-checklist-btn mt-3 px-3 py-1.5 bg-green-100 text-green-700 text-sm font-semibold rounded border border-green-300 hover:bg-green-200" data-container="checklist-container-edit-{{ $plan->id }}">
                    <i class="fas fa-plus mr-1"></i> Añadir Punto
                </button>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Cambios</button>
            </div>
        </form>
    </div>
</x-modal>
@endforeach

<!-- Create Modal -->
<x-modal id="createPlanModal" maxWidth="2xl">
    <div class="p-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Registrar Nuevo Plan de Mantenimiento</h2>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
        </div>
        <form action="{{ route('maintenance.plans.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Equipo</label>
                    <select name="equipment_id" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione un equipo...</option>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Plan</label>
                    <input type="text" name="name" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej. Preventivo Semanal">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Frecuencia (Días)</label>
                    <input type="number" name="frequency_days" min="1" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej. 7 para semanal">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select name="type" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="frequent">Frecuente (Rutina)</option>
                        <option value="deep">Profundo (Correctivo/Detalle)</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h6 class="text-md font-bold text-gray-800">Puntos de Revisión (Checklist Físico)</h6>
                <p class="text-sm text-gray-500 mb-3">Agrega los puntos clave que el técnico debe revisar e indicar Bien/Mal.</p>
                
                <div id="checklist-container-create" class="space-y-2">
                    <div class="flex gap-2 checklist-row">
                        <input type="text" name="checklist_items[]" class="w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. Revisar niveles de aceite" required>
                        <button type="button" class="remove-checklist-btn px-3 py-2 bg-red-100 text-red-600 rounded border border-red-200 hover:bg-red-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-checklist-btn mt-3 px-3 py-1.5 bg-green-100 text-green-700 text-sm font-semibold rounded border border-green-300 hover:bg-green-200" data-container="checklist-container-create">
                    <i class="fas fa-plus mr-1"></i> Añadir Punto
                </button>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar Plan</button>
            </div>
        </form>
    </div>
</x-modal>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Add checklist item logic
        $('.add-checklist-btn').click(function() {
            var containerId = $(this).data('container');
            var newRow = `
                <div class="flex gap-2 checklist-row mt-2">
                    <input type="text" name="checklist_items[]" class="w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe el punto de revisión..." required>
                    <button type="button" class="remove-checklist-btn px-3 py-2 bg-red-100 text-red-600 rounded border border-red-200 hover:bg-red-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            $('#' + containerId).append(newRow);
        });

        // Remove checklist item logic
        $(document).on('click', '.remove-checklist-btn', function() {
            $(this).closest('.checklist-row').remove();
        });

        // SweetAlert for delete
        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el plan y todos sus puntos de revisión.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
