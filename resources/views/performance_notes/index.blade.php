@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Retrospectivas y Desempeño</h2>
            <p class="mt-1 text-sm text-gray-500">Historial de comentarios y evaluaciones continuas.</p>
        </div>
        @if($isAdmin)
            <button onclick="document.getElementById('addNoteModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Añadir Comentario
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($notes->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay registros</h3>
            <p class="mt-1 text-sm text-gray-500">Aún no se han registrado comentarios de desempeño.</p>
        </div>
    @else
        <div class="relative max-w-4xl mx-auto">
            <!-- Timeline line -->
            <div class="absolute inset-0 left-8 md:left-1/2 w-0.5 bg-gray-200 transform md:-translate-x-1/2"></div>
            
            <div class="space-y-12">
                @foreach($notes as $index => $note)
                    @php
                        $isLeft = $index % 2 === 0;
                        $colorClass = 'bg-gray-500';
                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        
                        if($note->type === 'positive') {
                            $colorClass = 'bg-green-500 text-white';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>';
                        } elseif($note->type === 'improvement') {
                            $colorClass = 'bg-amber-500 text-white';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>';
                        } else {
                            $colorClass = 'bg-blue-500 text-white';
                            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>';
                        }
                    @endphp

                    <div class="relative flex items-center justify-between md:justify-normal w-full {{ $isLeft ? 'md:flex-row-reverse' : '' }}">
                        <!-- Icon -->
                        <div class="absolute left-8 md:left-1/2 transform -translate-x-1/2 w-10 h-10 rounded-full {{ $colorClass }} shadow-md border-4 border-white flex items-center justify-center z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
                        </div>

                        <!-- Content Card -->
                        <div class="w-full pl-20 md:pl-0 md:w-5/12">
                            <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 relative">
                                <!-- Triangles for tail -->
                                <div class="hidden md:block absolute top-5 w-4 h-4 bg-white border-t border-l border-gray-100 transform rotate-45 {{ $isLeft ? '-right-2' : '-left-2 border-b-0 border-r-0' }}" style="{{ $isLeft ? 'border-bottom: 0; border-left: 0;' : 'border-top: 0; border-right: 0;' }}"></div>
                                
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-bold uppercase tracking-wider {{ 
                                        $note->type === 'positive' ? 'text-green-600' : 
                                        ($note->type === 'improvement' ? 'text-amber-600' : 'text-blue-600') 
                                    }}">
                                        {{ $note->type === 'positive' ? 'Logro / Positivo' : ($note->type === 'improvement' ? 'Área de Mejora' : 'Comentario Neutral') }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">{{ $note->created_at->format('d M, Y') }}</span>
                                </div>
                                
                                <p class="text-gray-800 text-sm leading-relaxed mb-4">{{ $note->comments }}</p>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ substr($note->admin->name ?? 'A', 0, 1) }}
                                        </div>
                                        <span class="text-xs text-gray-500 font-medium">Por {{ $note->admin->name ?? 'Admin' }}</span>
                                    </div>
                                    @if($isAdmin)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-semibold bg-gray-100 px-2 py-1 rounded text-gray-700 truncate max-w-[120px]" title="{{ $note->user->name }}">
                                                Para: {{ explode(' ', trim($note->user->name))[0] }}
                                            </span>
                                            <form action="{{ route('performance_notes.destroy', $note->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 transition-colors" title="Eliminar nota">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@if($isAdmin)
<!-- Modal Añadir Nota -->
<div id="addNoteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('addNoteModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
            <form action="{{ route('performance_notes.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Nuevo Registro de Desempeño
                            </h3>
                            <div class="mt-4 space-y-4">
                                
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario / Empleado</label>
                                    <select id="user_id" name="user_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                        <option value="" disabled selected>Seleccione un usuario...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700">Tipo de Retroalimentación</label>
                                    <select id="type" name="type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                        <option value="positive" class="text-green-600 font-bold">Logro / Comentario Positivo</option>
                                        <option value="improvement" class="text-amber-600 font-bold">Área de Mejora / Advertencia</option>
                                        <option value="neutral" class="text-blue-600 font-bold">Nota Neutral / Seguimiento</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="comments" class="block text-sm font-medium text-gray-700">Detalles del comentario</label>
                                    <textarea id="comments" name="comments" rows="4" required class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Describe aquí el desempeño o aspecto a notar..."></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Guardar Registro
                    </button>
                    <button type="button" onclick="document.getElementById('addNoteModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
