@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#198754]">Contratos de Trabajadores</h1>
        <a href="{{ route('rh.index') }}" class="text-white bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded-lg">Volver a RH</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">Trabajadores</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 border-b border-gray-200">Trabajador</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Mes 1</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Mes 2</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Mes 3</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Indefinido</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Confidencialidad</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($trabajadores as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-800 font-medium">{{ $user->name }}</td>
                            
                            @foreach(['mes_1', 'mes_2', 'mes_3', 'indefinido', 'confidencialidad'] as $campo)
                                <td class="py-3 px-4 text-center">
                                    @if($user->contrato && $user->contrato->$campo)
                                        <a href="{{ asset($user->contrato->$campo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver PDF
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="py-3 px-4 text-center">
                                <button onclick="openModal('{{ $user->id }}', '{{ addslashes($user->name) }}')" class="text-white bg-[#198754] hover:bg-[#157347] text-xs px-3 py-1 rounded shadow">
                                    Gestionar Contratos
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-4 text-center text-gray-500">No hay trabajadores activos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">Practicantes</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 border-b border-gray-200">Practicante</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Mes 1</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Mes 2</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Mes 3</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Indefinido</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Confidencialidad</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 border-b border-gray-200">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($practicantes as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-800 font-medium">{{ $user->name }}</td>
                            
                            @foreach(['mes_1', 'mes_2', 'mes_3', 'indefinido', 'confidencialidad'] as $campo)
                                <td class="py-3 px-4 text-center">
                                    @if($user->contrato && $user->contrato->$campo)
                                        <a href="{{ asset($user->contrato->$campo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver PDF
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="py-3 px-4 text-center">
                                <button onclick="openModal('{{ $user->id }}', '{{ addslashes($user->name) }}')" class="text-white bg-[#198754] hover:bg-[#157347] text-xs px-3 py-1 rounded shadow">
                                    Gestionar Contratos
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-4 text-center text-gray-500">No hay practicantes activos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para subir PDF -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                Gestionar Contratos - <span id="modalUserName" class="text-[#198754]"></span>
            </h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="{{ route('rh.contratos.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" id="user_id">
            
            <p class="text-xs text-gray-500 mb-4">* Puedes subir todos los archivos a la vez o solo actualizar los que necesites. Se sobrescribirán los anteriores del mismo periodo si existen.</p>
            
            <div class="mb-4">
                <label for="mes_1" class="block text-sm font-medium text-gray-700 mb-2">Mes 1 (PDF)</label>
                <input type="file" name="mes_1" id="mes_1" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>
            <div class="mb-4">
                <label for="mes_2" class="block text-sm font-medium text-gray-700 mb-2">Mes 2 (PDF)</label>
                <input type="file" name="mes_2" id="mes_2" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>
            <div class="mb-4">
                <label for="mes_3" class="block text-sm font-medium text-gray-700 mb-2">Mes 3 (PDF)</label>
                <input type="file" name="mes_3" id="mes_3" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>
            <div class="mb-4">
                <label for="indefinido" class="block text-sm font-medium text-gray-700 mb-2">Indefinido (PDF)</label>
                <input type="file" name="indefinido" id="indefinido" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>
            <div class="mb-4">
                <label for="confidencialidad" class="block text-sm font-medium text-gray-700 mb-2">Confidencialidad (PDF)</label>
                <input type="file" name="confidencialidad" id="confidencialidad" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#198754] text-white rounded hover:bg-[#157347] transition-colors shadow">Guardar Contratos</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(userId, userName) {
        document.getElementById('user_id').value = userId;
        document.getElementById('modalUserName').textContent = userName;
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('uploadModal').classList.add('hidden');
    }
</script>
@endsection
