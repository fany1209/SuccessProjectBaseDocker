@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 relative">
    <!-- Decoración de fondo -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-br from-teal-600 via-emerald-600 to-green-700 opacity-90 rounded-b-[4rem] shadow-xl z-0 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 text-white">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight mb-2 drop-shadow-md">
                    <i class="ri-tools-fill mr-2"></i> Mantenimiento de Equipos
                </h1>
                <p class="text-emerald-100 text-lg max-w-2xl">
                    Gestiona y programa los mantenimientos preventivos de maquinaria y equipo de forma inteligente.
                </p>
            </div>
            @if(auth()->user()->hasRole('Admin'))
            <div class="mt-4 md:mt-0">
                <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="bg-white text-emerald-700 hover:bg-emerald-50 hover:text-emerald-800 font-bold py-3 px-6 rounded-full shadow-lg transform transition hover:scale-105 hover:-translate-y-1 flex items-center">
                    <i class="ri-add-line text-xl mr-1"></i> Agregar Equipo
                </button>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="bg-white/80 backdrop-blur-md border-l-4 border-emerald-500 p-4 mb-8 rounded-r-lg shadow-sm animate-fade-in-down">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="ri-checkbox-circle-fill text-emerald-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-emerald-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Grid de Equipos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($equipments as $equipment)
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 border border-gray-100 flex flex-col group">
                    <div class="h-48 overflow-hidden relative">
                        @if($equipment->image_path)
                            <img src="{{ Storage::url($equipment->image_path) }}" alt="{{ $equipment->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-gray-200 to-gray-100 flex items-center justify-center transition duration-500 group-hover:scale-110">
                                <i class="ri-image-line text-5xl text-gray-400"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-4 left-4">
                            <h3 class="text-xl font-bold text-white">{{ $equipment->name }}</h3>
                            <p class="text-sm text-gray-200">{{ $equipment->model ?? 'Sin modelo' }}</p>
                        </div>
                    </div>
                    
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="ri-time-line mr-1"></i> Cada {{ $equipment->frequency_days }} días
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="ri-team-line mr-1"></i> {{ $equipment->role ? $equipment->role->name : 'N/A' }}
                            </span>
                        </div>
                        
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2 flex-1">
                            {{ $equipment->comments ?: 'Sin comentarios adicionales.' }}
                        </p>
                        
                        <div class="border-t border-gray-100 pt-4 mt-auto">
                            <p class="text-sm text-gray-600 mb-3 flex items-center">
                                <i class="ri-calendar-event-line mr-2 text-gray-400"></i>
                                Próximo: <span class="font-bold ml-1 {{ \Carbon\Carbon::parse($equipment->next_maintenance_date)->isPast() ? 'text-red-600' : 'text-gray-800' }}">{{ \Carbon\Carbon::parse($equipment->next_maintenance_date)->format('d M, Y') }}</span>
                            </p>
                            
                            <a href="{{ route('maintenance.show', $equipment->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-emerald-600 text-sm font-medium rounded-lg text-emerald-600 bg-white hover:bg-emerald-50 focus:outline-none transition-colors">
                                Ver Detalles <i class="ri-arrow-right-line ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-20 bg-white/80 backdrop-blur-sm rounded-3xl shadow-sm border border-gray-200">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-500 mb-4">
                        <i class="ri-tools-line text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">No hay equipos registrados</h3>
                    <p class="text-gray-500">Agrega un nuevo equipo para comenzar a gestionar sus mantenimientos.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Create Modal -->
@if(auth()->user()->hasRole('Admin'))
<div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('create-modal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl leading-6 font-bold text-white" id="modal-title">
                    Registrar Nuevo Equipo
                </h3>
                <button type="button" class="text-white hover:text-emerald-100 transition" onclick="document.getElementById('create-modal').classList.add('hidden')">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            
            <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data" class="bg-white px-6 pt-5 pb-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Máquina / Equipo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2 px-3 border bg-gray-50">
                    </div>

                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700">Modelo</label>
                        <input type="text" name="model" id="model" class="mt-1 focus:ring-emerald-500 focus:border-emerald-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2 px-3 border bg-gray-50">
                    </div>

                    <div>
                        <label for="frequency_days" class="block text-sm font-medium text-gray-700">Frecuencia de Mantenimiento (días) <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="frequency_days" id="frequency_days" required min="1" value="30" class="focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2 px-3 border bg-gray-50">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">días</span>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Departamento Responsable <span class="text-red-500">*</span></label>
                        <select id="role_id" name="role_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="">Selecciona el rol responsable...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="comments" class="block text-sm font-medium text-gray-700">Comentarios Adicionales</label>
                        <textarea id="comments" name="comments" rows="3" class="mt-1 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border border-gray-300 rounded-lg py-2 px-3 bg-gray-50"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del Equipo</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition cursor-pointer" onclick="document.getElementById('image').click()">
                            <div class="space-y-1 text-center">
                                <i class="ri-upload-cloud-2-line text-4xl text-gray-400"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                        <span>Sube un archivo</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500" id="file-name">PNG, JPG, GIF hasta 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                    <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="bg-white py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                        Cancelar
                    </button>
                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none transition transform hover:scale-105">
                        <i class="ri-save-line mr-2"></i> Guardar Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    .animate-fade-in-down {
        animation: fadeInDown 0.5s ease-out;
    }
    @keyframes fadeInDown {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
