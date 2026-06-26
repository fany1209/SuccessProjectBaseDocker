<x-modal id="modal-evaluacion-desempeno">
    <form method="POST" action="{{ route('rh.evaluacion_desempeno.pdf') }}" class="space-y-6">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Evaluación del Desempeño</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre del evaluado</label>
                    <input type="text" name="nombre_evaluado" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de evaluación</label>
                    <input type="date" name="fecha_evaluacion" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Área</label>
                    <input type="text" name="area" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puesto evaluado</label>
                    <input type="text" name="puesto_evaluado" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre del evaluador</label>
                    <input type="text" name="nombre_evaluador" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puesto del evaluador</label>
                    <input type="text" name="puesto_evaluador" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Generar Evaluación
            </x-button>
        </div>
    </form>
</x-modal>
