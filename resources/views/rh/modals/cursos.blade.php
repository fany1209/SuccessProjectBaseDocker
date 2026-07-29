<x-modal id="modal-cursos">
    <form method="POST" action="{{ route('rh.cursos.store') }}" class="space-y-6">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Registrar Nuevo Curso</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha</label>
                    <input type="date" name="fecha" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Sede</label>
                    <input type="text" name="sede" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Horario</label>
                    <input type="text" name="horario" placeholder="Ej. 9:30 a 12:00" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Curso</label>
                    <input type="text" name="curso" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Objetivo</label>
                    <textarea name="objetivo" rows="2" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Asistentes (Opcional)</label>
                    <div id="asistentes-container" class="space-y-2">
                        <input type="text" name="asistentes[]" placeholder="Nombre del asistente" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                    </div>
                    <button type="button" onclick="agregarAsistente()" class="mt-2 text-sm text-green-600 hover:text-green-800 font-medium">+ Agregar otro asistente</button>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Guardar Curso
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    function agregarAsistente() {
        const container = document.getElementById('asistentes-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'asistentes[]';
        input.placeholder = 'Nombre del asistente';
        input.className = 'w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 mt-2';
        container.appendChild(input);
    }
</script>
