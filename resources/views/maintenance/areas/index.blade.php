@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Catálogo de Áreas / Categorías</h1>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
            <i class="fas fa-bell mr-1"></i> Alertas Pendientes
        </a>
        <a href="{{ route('maintenance.history') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50">
            <i class="fas fa-history mr-1"></i> Historial Completado
        </a>
        <a href="{{ route('maintenance.areas.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
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
            <h2 class="text-lg font-semibold text-gray-700">Lista de Categorías / Áreas</h2>
            <button type="button" class="open-modal px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center shadow" data-target="createAreaModal">
                <i class="fas fa-plus mr-2"></i> Nueva Categoría
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-700">Nombre</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Descripción</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Equipos Asignados</th>
                        <th class="p-3 text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($areas as $area)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-sm text-gray-800 font-medium">{{ $area->name }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $area->description ?? 'Sin descripción' }}</td>
                        <td class="p-3 text-sm text-gray-800">{{ $area->equipment_count }} equipos</td>
                        <td class="p-3 text-sm flex gap-2">
                            <button type="button" class="open-modal px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs" data-target="editAreaModal{{ $area->id }}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            
                            <form action="{{ route('maintenance.areas.destroy', $area->id) }}" method="POST" class="inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs" {{ $area->equipment_count > 0 ? 'disabled' : '' }}>
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

@foreach($areas as $area)
<!-- Edit Modal -->
<x-modal id="editAreaModal{{ $area->id }}" maxWidth="lg">
    <div class="p-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Editar Categoría / Área</h2>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
        </div>
        <form action="{{ route('maintenance.areas.update', $area->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ $area->name }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500">{{ $area->description }}</textarea>
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
<x-modal id="createAreaModal" maxWidth="lg">
    <div class="p-2">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">Registrar Nueva Categoría / Área</h2>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
        </div>
        <form action="{{ route('maintenance.areas.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej. Producción">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. Área principal de manufactura"></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Registrar</button>
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
                text: "Esta acción eliminará la categoría.",
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
