@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Catálogo de Equipos</h1>
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
        <a href="{{ route('maintenance.equipment.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
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
            <h2 class="text-lg font-semibold text-gray-700">Lista de Equipos</h2>
            <button type="button" class="open-modal px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center shadow" data-target="createEquipmentModal">
                <i class="fas fa-plus mr-2"></i> Nuevo Equipo
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-700">Código</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Nombre</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Área</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Estado</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($equipments as $eq)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm text-gray-800">{{ $eq->code }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $eq->name }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $eq->area->name ?? 'N/A' }}</td>
                        <td class="p-3 text-sm">
                            @if($eq->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Inactivo</span>
                            @endif
                        </td>
                        <td class="p-3 text-sm flex gap-2">
                            <button type="button" class="open-modal px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs" data-target="editEquipmentModal{{ $eq->id }}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            
                            <form action="{{ route('maintenance.equipment.destroy', $eq->id) }}" method="POST" class="inline form-delete">
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

@foreach($equipments as $eq)
<!-- Edit Modal -->
<x-modal id="editEquipmentModal{{ $eq->id }}" maxWidth="lg">
    <div class="p-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Editar Equipo</h2>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
        </div>
        <form action="{{ route('maintenance.equipment.update', $eq->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">Código</label>
                <input type="text" name="code" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $eq->code }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $eq->name }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Área</label>
                <select name="area_id" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ $eq->area_id == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center mt-4">
                <input type="checkbox" id="is_active_{{ $eq->id }}" name="is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $eq->is_active ? 'checked' : '' }}>
                <label for="is_active_{{ $eq->id }}" class="ml-2 block text-sm text-gray-900">¿Activo?</label>
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
<x-modal id="createEquipmentModal" maxWidth="lg">
    <div class="p-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Registrar Nuevo Equipo</h2>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
        </div>
        <form action="{{ route('maintenance.equipment.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Código</label>
                <input type="text" name="code" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej. MNT-002">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej. Empacadora">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Área</label>
                <select name="area_id" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center mt-4">
                <input type="checkbox" id="is_active_create" name="is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded" checked>
                <label for="is_active_create" class="ml-2 block text-sm text-gray-900">¿Activo?</label>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Registrar Equipo</button>
            </div>
        </form>
    </div>
</x-modal>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el equipo de forma permanente.",
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
