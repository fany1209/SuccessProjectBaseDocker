<x-modal id="modal-convenio-instituciones">
    <form method="POST" action="{{ route('rh.convenio_instituciones.pdf') }}" class="space-y-6">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Convenio con Instituciones</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">N° de convenio</label>
                    <input type="text" name="no_convenio" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="telefono" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Escuela</label>
                    <input type="text" name="escuela" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subdirectora de Vinculación</label>
                    <input type="text" name="subdirectora" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Domicilio</label>
                    <input type="text" name="domicilio" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Jefe de Gestión Tecnológica</label>
                    <input type="text" name="jefe_departamento" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Representante (Director)</label>
                    <input type="text" name="representante" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tiempo de residencias</label>
                    <input type="text" name="tiempo_residencias" placeholder="Ej: 4 meses min - 6 meses max" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha del convenio</label>
                    <input type="date" name="fecha_convenio" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tiempo de validez</label>
                    <input type="text" name="validez_convenio" placeholder="Ej: 5 años" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Clave de la escuela</label>
                    <input type="text" name="clave_escuela" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Representante de la empresa (Director)</label>
                    <input type="text" name="representante_empresa" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Responsable del resguardo del convenio (Líder SGC)</label>
                    <input type="text" name="responsable_resguardo" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Consecutivo</label>
                    <input type="text" name="consecutivo" placeholder="Ej: SSS2023-02" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Generar Convenio
            </x-button>
        </div>
    </form>
</x-modal>
