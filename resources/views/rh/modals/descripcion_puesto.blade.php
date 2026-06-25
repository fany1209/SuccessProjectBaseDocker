<x-modal id="modal-descripcion">
    <form method="POST" action="{{ route('rh.descripcion_puesto.pdf') }}" class="space-y-6">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">
                Descripción de Puesto
            </h3>

            <div class="space-y-4">

                {{-- Nombre del Puesto --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Nombre del Puesto
                    </label>
                    <input
                        type="text"
                        name="nombre_puesto"
                        value="{{ old('nombre_puesto') }}"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >
                </div>

                {{-- Escolaridad --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Escolaridad
                    </label>
                    <input
                        type="text"
                        name="escolaridad"
                        value="{{ old('escolaridad') }}"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >
                </div>

                {{-- Misión --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Misión del Puesto
                    </label>
                    <textarea
                        name="mision"
                        rows="3"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >{{ old('mision') }}</textarea>
                </div>

                {{-- Habilidades --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Habilidades
                    </label>
                    <textarea
                        name="habilidades"
                        rows="3"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >{{ old('habilidades') }}</textarea>
                </div>

                {{-- Requisitos --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Requisitos
                    </label>
                    <textarea
                        name="requisitos"
                        rows="3"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >{{ old('requisitos') }}</textarea>
                </div>

                {{-- Funciones y Responsabilidades --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Funciones y Responsabilidades
                    </label>
                    <textarea
                        name="funciones_responsabilidades"
                        rows="5"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >{{ old('funciones_responsabilidades') }}</textarea>
                </div>

                {{-- Estructura Organizativa --}}
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Estructura Organizativa
                    </label>

                    <textarea
                        name="estructura_organizativa"
                        rows="5"
                        placeholder="Director General&#10;Jefe REH&#10;Encargado de TI"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                    >{{ old('estructura_organizativa') }}</textarea>

                    <p class="text-xs text-gray-500 mt-1">
                        Capture un puesto por renglón, desde el nivel superior hasta el puesto descrito.
                    </p>
                </div>

            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
                Cancelar
            </x-button>

            <x-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                Generar Descripción
            </x-button>
        </div>

    </form>
</x-modal>