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
        @php
            $notesGroups = $isAdmin ? $notes->groupBy('user_id') : ['all' => $notes];
        @endphp

        <div class="space-y-6">
            @foreach($notesGroups as $groupId => $groupNotes)
                <div class="bg-white rounded-2xl p-5 md:px-8 border border-gray-100 shadow-sm transition-shadow hover:shadow-md">
                    @php $groupUser = $isAdmin ? $groupNotes->first()->user : auth()->user(); @endphp
                    <div class="flex items-center justify-between {{ $isAdmin ? 'cursor-pointer group' : '' }} pb-4 border-b border-gray-50" {!! $isAdmin ? 'onclick="document.getElementById(\'timeline-' . $groupId . '\').classList.toggle(\'hidden\'); document.getElementById(\'icon-' . $groupId . '\').classList.toggle(\'rotate-180\')"' : '' !!}>
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                                {{ strtoupper(substr($groupUser->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 {{ $isAdmin ? 'group-hover:text-indigo-600 transition-colors' : '' }}">{{ $groupUser->name ?? 'Usuario Desconocido' }}</h3>
                                <p class="text-sm font-medium text-gray-500">{{ $groupNotes->count() }} registro(s) de desempeño</p>
                            </div>
                        </div>
                        @if($isAdmin)
                            <div class="text-gray-400 group-hover:text-indigo-600 transition-colors bg-gray-50 rounded-full p-2">
                                <svg id="icon-{{ $groupId }}" class="w-6 h-6 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        @endif
                    </div>

                    <div id="timeline-{{ $groupId }}" class="{{ $isAdmin ? 'hidden mt-6 pt-2' : 'mt-8' }} relative max-w-4xl mx-auto">
                        <!-- Timeline line -->
                        <div class="absolute inset-0 left-8 md:left-1/2 w-0.5 bg-gray-200 transform md:-translate-x-1/2"></div>
                        
                        <div class="space-y-12">
                            @foreach($groupNotes as $index => $note)
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
                </div>
            @endforeach
        </div>
    @endif
</div>

@if($isAdmin)
<!-- Modal Añadir Nota -->
<x-modal id="addNoteModal" maxWidth="lg">
    <form action="{{ route('performance_notes.store') }}" method="POST" id="create-note-form" class="flex flex-col items-center w-full gap-2">
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Nuevo Registro de Desempeño</x-tittle-form>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="user_id">Usuario / Empleado</x-label>
                <x-select-1 id="user_id" name="user_id" required>
                    <option value="" disabled selected>Seleccione un usuario...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="type">Tipo de Retroalimentación</x-label>
                <x-select-1 id="type" name="type" required>
                    <option value="positive" class="text-green-600 font-bold">Logro / Comentario Positivo</option>
                    <option value="improvement" class="text-amber-600 font-bold">Área de Mejora / Advertencia</option>
                    <option value="neutral" class="text-blue-600 font-bold">Nota Neutral / Seguimiento</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="comments">Detalles del comentario</x-label>
                <textarea id="comments" name="comments" rows="4" required class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm p-2 text-sm" placeholder="Describe aquí el desempeño o aspecto a notar..."></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 type="button" class="close-modal" onclick="document.getElementById('addNoteModal').classList.add('hidden')" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Guardar Registro</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@endif
@endsection
