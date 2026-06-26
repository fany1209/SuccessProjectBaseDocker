<x-modal id="modal-solicitud-personal">
    <form method="POST" action="{{ route('rh.solicitud_personal.pdf') }}" class="space-y-6">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Solicitud de Personal</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre del puesto</label>
                    <input type="text" name="nombre_puesto" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de incorporación</label>
                    <input type="date" name="fecha_incorporacion" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Motivo Específico (si es Otro)</label>
                    <input type="text" name="especifique_motivo" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nº de vacantes</label>
                    <input type="number" name="vacantes" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Personas que supervisa</label>
                    <input type="number" name="personas_supervisa" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Especifique Dedicación (si es Otro)</label>
                    <input type="text" name="especifique_dedicacion" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Especifique Horario (si es Otro)</label>
                    <input type="text" name="especifique_horario" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Funciones básicas del puesto</label>
                <textarea name="funciones_basicas" rows="3" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
            </div>

            <h4 class="text-sm font-semibold mb-2 mt-4 text-gray-700">Perfil Profesional</h4>
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Especialidad</label>
                    <input type="text" name="especialidad" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Profesión</label>
                    <input type="text" name="profesion" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Experiencia (Años)</label>
                    <input type="number" name="experiencia_anos" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Experiencia (Meses)</label>
                    <input type="number" name="experiencia_meses" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Cargos desempeñados</label>
                    <input type="text" name="cargos_desempenados" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <h4 class="text-sm font-semibold mb-2 mt-4 text-gray-700">Competencias</h4>
            <div class="grid md:grid-cols-3 gap-2 mb-4">
                <input type="text" name="competencia_1" placeholder="Competencia 1" class="w-full rounded-md border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-green-600">
                <input type="text" name="competencia_2" placeholder="Competencia 2" class="w-full rounded-md border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-green-600">
                <input type="text" name="competencia_3" placeholder="Competencia 3" class="w-full rounded-md border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-green-600">
                <input type="text" name="competencia_4" placeholder="Competencia 4" class="w-full rounded-md border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-green-600">
                <input type="text" name="competencia_5" placeholder="Competencia 5" class="w-full rounded-md border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-green-600">
                <input type="text" name="competencia_6" placeholder="Competencia 6" class="w-full rounded-md border border-gray-300 px-3 py-1 focus:ring-2 focus:ring-green-600">
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Edad sugerida</label>
                    <input type="number" name="edad_sugerida" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Comentarios adicionales</label>
                <textarea name="comentarios" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Generar Solicitud
            </x-button>
        </div>
    </form>
</x-modal>
