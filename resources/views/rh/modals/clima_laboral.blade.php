<x-modal id="modal-clima-laboral">
    <style>
        /* Estilos personalizados para asegurar que el círculo se ponga verde y el número siga visible */
        input.input-clima:checked + .circulo-clima {
            background-color: #16a34a !important; /* Verde */
            border-color: #15803d !important;
            color: #ffffff !important; /* Número en blanco para que resalte y no se pierda */
        }
        /* Para que el texto descriptivo también resalte en verde al seleccionarse */
        input.input-clima:checked ~ .texto-clima {
            color: #15803d !important;
            font-weight: bold !important;
        }
    </style>
    <div class="p-2">
        <div class="mb-5 text-center">
            <h2 class="text-xl font-bold text-gray-800">Encuesta de Clima Laboral</h2>
            <p class="text-sm text-gray-600 mt-1 mb-3">Tu opinión nos ayuda a mejorar continuamente.</p>
            <div class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-50 border border-green-200 text-green-800 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="text-sm font-bold">Encuesta 100% Anónima y Confidencial</span>
            </div>
        </div>

        <form method="POST" action="{{ route('rh.clima_laboral.store') }}" class="space-y-6">
            @csrf

            @php
                $questions = [
                    ['name' => 'q1_ambiente', 'label' => '1. ¿Cómo describirías el ambiente en tu área de trabajo?', 'dim' => 'Ambiente laboral', 'options' => ['Muy negativo', 'Negativo', 'Regular', 'Positivo', 'Muy positivo']],
                    ['name' => 'q2_respeto', 'label' => '2. ¿Sientes respeto y buen trato entre compañeros y superiores?', 'dim' => 'Respeto', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q3_comunicacion_oportuna', 'label' => '3. ¿La información importante llega clara y oportuna?', 'dim' => 'Comunicación', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q4_comunicacion_escucha', 'label' => '4. ¿Te sientes escuchado(a) cuando expresas tus ideas o preocupaciones?', 'dim' => 'Comunicación', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q5_liderazgo', 'label' => '5. ¿Tu jefe inmediato brinda apoyo y orientación cuando lo necesitas?', 'dim' => 'Liderazgo', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q6_reconocimiento', 'label' => '6. ¿Tu jefe reconoce y valora tu trabajo?', 'dim' => 'Reconocimiento', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q7_desarrollo', 'label' => '7. ¿La empresa ofrece oportunidades de capacitación y crecimiento?', 'dim' => 'Desarrollo', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q8_motivacion', 'label' => '8. ¿Te sientes motivado(a) para dar lo mejor en tu trabajo?', 'dim' => 'Motivación', 'options' => ['Nada', 'Poco', 'Regular', 'Motivado', 'Muy motivado']],
                    ['name' => 'q9_satisfaccion', 'label' => '9. ¿Recomendarías a alguien trabajar en esta empresa?', 'dim' => 'Satisfacción', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                    ['name' => 'q10_bienestar_carga', 'label' => '10. ¿Consideras que tu carga de trabajo es adecuada?', 'dim' => 'Bienestar', 'options' => ['Nada', 'Poco', 'Regular', 'Adecuada', 'Muy adecuada']],
                    ['name' => 'q11_bienestar_preocupacion', 'label' => '11. ¿La empresa se preocupa por tu bienestar físico y emocional?', 'dim' => 'Bienestar', 'options' => ['Nunca', 'Casi nunca', 'A veces', 'Casi siempre', 'Siempre']],
                ];
            @endphp

            <div class="space-y-5">
                @foreach($questions as $q)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="mb-2">
                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wider">{{ $q['dim'] }}</span>
                        <p class="text-sm font-medium text-gray-800 mt-1">{{ $q['label'] }}</p>
                    </div>
                    
                    <div class="flex items-start justify-between mt-4 w-full">
                        @foreach($q['options'] as $index => $optionText)
                        @php $val = $index + 1; @endphp
                        <label class="flex flex-col items-center cursor-pointer group w-1/5 text-center px-1">
                            <input type="radio" name="{{ $q['name'] }}" value="{{ $val }}" required class="sr-only input-clima">
                            <div class="w-10 h-10 mb-1 rounded-full flex items-center justify-center border-2 border-gray-300 text-gray-700 bg-white transition-all duration-200 group-hover:border-green-400 shadow-sm flex-shrink-0 circulo-clima">
                                <span class="font-bold text-sm">{{ $val }}</span>
                            </div>
                            <span class="text-[10px] sm:text-xs leading-tight text-gray-500 texto-clima">{{ $optionText }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="mb-2">
                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wider">Pregunta Abierta</span>
                        <p class="text-sm font-medium text-gray-800 mt-1">12. ¿Qué sugerencias tienes para mejorar el clima laboral en la empresa? <span class="text-red-500">*</span></p>
                        <p class="text-xs text-gray-500 italic mt-1">Respuesta obligatoria</p>
                    </div>
                    <textarea name="q12_sugerencias" rows="4" class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:border-transparent resize-none shadow-sm" placeholder="Escribe tus sugerencias aquí..." required></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">
                    Cancelar
                </x-button>
                <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                    Enviar Encuesta
                </x-button>
            </div>
        </form>
    </div>
</x-modal>
