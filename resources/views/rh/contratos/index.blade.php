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

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Trabajador</th>
                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Mes 1</th>
                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Mes 2</th>
                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Mes 3</th>
                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Indefinido</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-gray-800 font-medium">{{ $user->name }}</td>
                        
                        @foreach(['mes_1', 'mes_2', 'mes_3', 'indefinido'] as $periodo)
                            <td class="py-3 px-4 text-center">
                                @if($user->contrato && $user->contrato->$periodo)
                                    <div class="flex flex-col items-center gap-1">
                                        <a href="{{ asset('storage/' . $user->contrato->$periodo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver PDF
                                        </a>
                                        <button onclick="openModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $periodo }}')" class="text-xs text-gray-500 hover:text-gray-700">
                                            Actualizar
                                        </button>
                                    </div>
                                @else
                                    <button onclick="openModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $periodo }}')" class="text-white bg-[#198754] hover:bg-[#157347] text-xs px-3 py-1 rounded shadow">
                                        Subir PDF
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para subir PDF -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Subir Contrato</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="{{ route('rh.contratos.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" id="modal_user_id">
            <input type="hidden" name="periodo" id="modal_periodo">
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Trabajador: <span id="modal_user_name" class="font-semibold text-gray-800"></span></p>
                <p class="text-sm text-gray-600">Periodo: <span id="modal_periodo_label" class="font-semibold text-gray-800 capitalize"></span></p>
            </div>

            <div class="mb-4">
                <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">Archivo PDF (Max: 10MB)</label>
                <input type="file" name="archivo" id="archivo" accept="application/pdf" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            
            @error('archivo')
                <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
            @enderror
            @error('periodo')
                <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
            @enderror
            @error('user_id')
                <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
            @enderror

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#198754] text-white rounded hover:bg-[#157347] transition-colors shadow">Guardar Contrato</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(userId, userName, periodo) {
        document.getElementById('modal_user_id').value = userId;
        document.getElementById('modal_periodo').value = periodo;
        document.getElementById('modal_user_name').innerText = userName;
        document.getElementById('modal_periodo_label').innerText = periodo.replace('_', ' ');
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('uploadModal').classList.add('hidden');
    }
</script>
@endsection
