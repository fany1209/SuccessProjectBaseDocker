@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 relative">
    <div class="absolute top-0 left-0 w-full h-72 bg-gradient-to-r from-gray-800 to-gray-900 shadow-lg z-0 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <nav class="flex text-gray-300 text-sm mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('maintenance.index') }}" class="hover:text-white transition flex items-center">
                            <i class="ri-tools-fill mr-1"></i> Mantenimiento
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="ml-1 md:ml-2 text-white font-semibold">{{ $equipment->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="flex justify-between items-end">
                <h1 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-md">
                    {{ $equipment->name }}
                </h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-gray-700 text-gray-100 border border-gray-600">
                    {{ $equipment->model ?? 'Sin Modelo' }}
                </span>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-white/90 backdrop-blur-md border-l-4 border-green-500 p-4 mb-8 rounded-r-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="ri-checkbox-circle-fill text-green-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Image and Details -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Image Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 group">
                    <div class="aspect-w-4 aspect-h-3 relative overflow-hidden bg-gray-100">
                        @if($equipment->image_path)
                            <img src="{{ Storage::url($equipment->image_path) }}" alt="{{ $equipment->name }}" class="w-full h-64 object-cover">
                        @else
                            <div class="w-full h-64 flex flex-col items-center justify-center text-gray-400">
                                <i class="ri-camera-off-line text-5xl mb-2"></i>
                                <span class="text-sm">Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Especificaciones</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Departamento</dt>
                                <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                    <i class="ri-team-line mr-2 text-indigo-500"></i> {{ $equipment->role ? $equipment->role->name : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Frecuencia</dt>
                                <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                    <i class="ri-refresh-line mr-2 text-emerald-500"></i> Cada {{ $equipment->frequency_days }} días
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Siguiente Mantenimiento</dt>
                                <dd class="mt-1 text-sm font-bold {{ \Carbon\Carbon::parse($equipment->next_maintenance_date)->isPast() ? 'text-red-600' : 'text-emerald-600' }} flex items-center">
                                    <i class="ri-calendar-check-line mr-2"></i> {{ \Carbon\Carbon::parse($equipment->next_maintenance_date)->format('d M, Y') }}
                                </dd>
                            </div>
                            @if($equipment->comments)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Comentarios</dt>
                                <dd class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    {{ $equipment->comments }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions and History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Action Card -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full opacity-50 blur-xl"></div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center relative z-10">
                        <i class="ri-settings-4-fill text-indigo-600 mr-2"></i> Estado de Mantenimiento
                    </h3>
                    
                    <div class="mt-6 relative z-10">
                        @if($currentRecord)
                            @if($currentRecord->status === 'pending')
                                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg mb-6">
                                    <div class="flex items-start">
                                        <i class="ri-alert-fill text-amber-500 text-xl mr-3"></i>
                                        <div>
                                            <h4 class="text-amber-800 font-bold">Mantenimiento Requerido</h4>
                                            <p class="text-amber-700 text-sm mt-1">El departamento responsable debe confirmar que se ha realizado el mantenimiento programado para el <b>{{ \Carbon\Carbon::parse($currentRecord->scheduled_date)->format('d/m/Y') }}</b>.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(auth()->user()->hasRole($equipment->role->name) || auth()->user()->hasRole('Admin'))
                                    <form action="{{ route('maintenance.confirmDepartment', $equipment->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas del mantenimiento (Opcional)</label>
                                            <textarea name="comments" rows="2" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg p-2 bg-gray-50 border" placeholder="Describe qué se revisó o cambió..."></textarea>
                                        </div>
                                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none shadow-lg transform transition hover:-translate-y-1">
                                            <i class="ri-check-double-line mr-2"></i> Confirmar Mantenimiento (Departamento)
                                        </button>
                                    </form>
                                @else
                                    <p class="text-sm text-gray-500 italic"><i class="ri-information-line"></i> Solo el personal de {{ $equipment->role->name }} puede confirmar esta etapa.</p>
                                @endif
                                
                            @elseif($currentRecord->status === 'pending_admin')
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                                    <div class="flex items-start">
                                        <i class="ri-admin-fill text-blue-500 text-xl mr-3"></i>
                                        <div>
                                            <h4 class="text-blue-800 font-bold">Esperando Confirmación del Administrador</h4>
                                            <p class="text-blue-700 text-sm mt-1">El departamento ya confirmó el mantenimiento el {{ \Carbon\Carbon::parse($currentRecord->department_confirmed_at)->format('d/m/Y H:i') }}. Falta la confirmación total por parte del administrador.</p>
                                            @if($currentRecord->comments)
                                                <div class="mt-2 text-sm bg-white/60 p-2 rounded text-blue-800 border border-blue-100">
                                                    <b>Notas dpto:</b> {{ $currentRecord->comments }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if(auth()->user()->hasRole('Admin'))
                                    <form action="{{ route('maintenance.confirmAdmin', $equipment->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none shadow-lg transform transition hover:-translate-y-1">
                                            <i class="ri-shield-check-line mr-2"></i> Confirmar Totalidad (Admin)
                                        </button>
                                    </form>
                                @else
                                    <p class="text-sm text-gray-500 italic"><i class="ri-information-line"></i> Esperando que un Administrador apruebe este registro.</p>
                                @endif
                            @endif
                        @else
                            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 text-emerald-500 mb-3">
                                    <i class="ri-check-line text-2xl"></i>
                                </div>
                                <h4 class="text-emerald-800 font-bold text-lg mb-1">Mantenimiento al Día</h4>
                                <p class="text-emerald-600 text-sm">No hay mantenimientos pendientes actualmente. El próximo está programado para el <b>{{ \Carbon\Carbon::parse($equipment->next_maintenance_date)->format('d/m/Y') }}</b>.</p>
                                
                                @if(auth()->user()->hasRole($equipment->role->name) || auth()->user()->hasRole('Admin'))
                                    <!-- Botón para forzar mantenimiento anticipado -->
                                    <form action="{{ route('maintenance.confirmDepartment', $equipment->id) }}" method="POST" class="mt-6 border-t border-emerald-100 pt-4">
                                        @csrf
                                        <button type="submit" class="text-sm text-emerald-700 hover:text-emerald-800 hover:underline inline-flex items-center">
                                            <i class="ri-add-circle-line mr-1"></i> Registrar mantenimiento manual ahora
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900"><i class="ri-history-line mr-2 text-gray-500"></i> Historial de Registros</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 text-left">Fecha Prog.</th>
                                    <th class="px-6 py-4 text-left">Estado</th>
                                    <th class="px-6 py-4 text-left">Conf. Depto</th>
                                    <th class="px-6 py-4 text-left">Conf. Admin</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @forelse($equipment->maintenanceRecords()->latest()->get() as $rec)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">
                                        {{ \Carbon\Carbon::parse($rec->scheduled_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($rec->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Completado</span>
                                        @elseif($rec->status === 'pending_admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Revisión Admin</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pendiente Depto</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        @if($rec->department_confirmed_at)
                                            <div class="flex flex-col">
                                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($rec->department_confirmed_at)->format('d/m/Y') }}</span>
                                                <span class="text-xs">{{ $rec->departmentUser->name ?? 'Usuario' }}</span>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        @if($rec->admin_confirmed_at)
                                            <div class="flex flex-col">
                                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($rec->admin_confirmed_at)->format('d/m/Y') }}</span>
                                                <span class="text-xs">{{ $rec->adminUser->name ?? 'Admin' }}</span>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No hay registros de mantenimiento históricos.
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
</div>
@endsection
