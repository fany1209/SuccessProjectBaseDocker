<x-modal id="modal-dnc">
    <form method="POST" action="{{ route('rh.dnc.pdf') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- ================= BLOQUE DE INSTRUCCIONES ================= --}}
        <div class="bg-green-50 border-l-4 border-green-600 p-3 rounded-r-md">
            <h4 class="text-sm font-semibold text-green-800 mb-1">Instrucciones:</h4>
            <p class="text-xs text-green-700 text-justify">
                Lea cuidadosamente cada pregunta y marque <b>SI</b> o <b>NO</b> según sus necesidades de capacitación. 
                En caso de seleccionar afirmativamente, por favor especifique el <b>Nombre del curso</b> sugerido 
                y describa brevemente <b>cómo lo aplicaría</b> en sus actividades diarias.
            </p>
        </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Generales</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre completo del colaborador</label>
                    <input type="text" name="employee_name" placeholder="Nombre del empleado" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puesto</label>
                    <input type="text" name="position" placeholder="Ej. Desarrollador" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Área / Departamento</label>
                    <input type="text" name="department" placeholder="Ej. Sistemas" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Evaluación</label>
                    <input type="date" name="evaluation_date" value="{{ date('Y-m-d') }}" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

    <div>
        <label class="block text-sm font-medium mb-1">Antigüedad en la empresa</label>
        <input type="text" name="emp_seniority" placeholder="Ej. 2 años 5 meses" required
        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
    </div>
    
    <div>
        <label class="block text-sm font-medium mb-1">Antigüedad en el puesto</label>
        <input type="text" name="pos_seniority" placeholder="Ej. 1 año" required
            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
    </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Cuestionario de Capacitación</h3>
            
            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                @php
                    $preguntas = [
                        "¿Requiero capacitación para fortalecer el conocimiento y dominio de las actividades, habilidades y actitudes relacionadas con mi puesto?",
                        "¿Requiero capacitación para mejorar la comunicación y relaciones con mis compañeros (as) y jefe inmediato?",
                        "¿Requiero capacitación para mejorar el grado de precisión y confiabilidad en mi trabajo?",
                        "¿Requiero capacitación para desarrollar actitudes positivas hacia mis compañeros (as) y mejorar la atención a usuarios?",
                        "¿Requiero capacitación para a fortalecer habilidades como el pensamiento crítico, la organización laboral, la comunicación y la flexibilidad mental?",
                        "¿Requiero capacitación de liderazgo para equipos de trabajo?",
                        "¿Requiero de un curso para detectar y aplicar habilidades blandas y duras?",
                        "¿Requiero capacitación para la gestión efectiva del tiempo?"
                    ];
                @endphp

                @foreach($preguntas as $i => $pregunta)
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm font-medium text-gray-800 mb-2">{{ $i + 1 }}. {{ $pregunta }}</p>
                    <input type="hidden" name="questions[{{ $i }}][pregunta]" value="{{ $pregunta }}">
                    
                    <div class="grid md:grid-cols-12 gap-3 items-start">
                        <div class="md:col-span-2 flex gap-3 pt-2">
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="questions[{{ $i }}][respuesta]" value="SI" class="mr-1 text-green-600 focus:ring-green-600"> SI
                            </label>
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="questions[{{ $i }}][respuesta]" value="NO" class="mr-1 text-green-600 focus:ring-green-600"> NO
                            </label>
                        </div>
                        <div class="md:col-span-4">
                            <input type="text" name="questions[{{ $i }}][curso]" placeholder="Nombre del curso" 
                                   class="w-full text-xs rounded-md border border-gray-300 px-2 py-1.5 focus:ring-2 focus:ring-green-600">
                        </div>
                        <div class="md:col-span-6">
                            <input type="text" name="questions[{{ $i }}][aplicacion]" placeholder="¿Cómo lo aplicaría en mi área?" 
                                   class="w-full text-xs rounded-md border border-gray-300 px-2 py-1.5 focus:ring-2 focus:ring-green-600">
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2 border-b pb-1">Cursos Adicionales / Sugerencias</h4>
                    @for($j = 0; $j < 3; $j++)
                    <div class="grid md:grid-cols-12 gap-3 mb-2">
                        <div class="md:col-span-4">
                            <input type="hidden" name="extra_questions[{{ $j }}][pregunta]" value="¿En cuál tema le gustaría ser capacitado?">
                            <input type="text" name="extra_questions[{{ $j }}][curso]" placeholder="Tema o nombre del curso" 
                                   class="w-full text-xs rounded-md border border-gray-300 px-2 py-1.5 focus:ring-2 focus:ring-green-600">
                        </div>
                        <div class="md:col-span-8">
                            <input type="text" name="extra_questions[{{ $j }}][aplicacion]" placeholder="¿Cómo lo aplicaría en su área de trabajo?" 
                                   class="w-full text-xs rounded-md border border-gray-300 px-2 py-1.5 focus:ring-2 focus:ring-green-600">
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Generar Formato DNC
            </x-button>
        </div>
    </form>
</x-modal>