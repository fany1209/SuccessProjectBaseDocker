<x-modal id="modal-clima-laboral">
    <div class="p-2">
        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold text-gray-800">Encuesta de Clima Laboral</h2>
            <p class="text-sm text-gray-500 mt-1">Tu opinión es importante para nosotros. (Las respuestas son anónimas)</p>
        </div>

        <form method="POST" action="{{ route('rh.clima_laboral.store') }}" class="space-y-6">
            @csrf

            @php
                $questions = [
                    ['name' => 'q1_ambiente', 'label' => '1. ¿Cómo describirías el ambiente en tu área de trabajo?', 'dim' => 'Ambiente laboral', 'scale' => '1 = Muy negativo, 5 = Muy positivo'],
                    ['name' => 'q2_respeto', 'label' => '2. ¿Sientes respeto y buen trato entre compañeros y superiores?', 'dim' => 'Respeto', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q3_comunicacion_oportuna', 'label' => '3. ¿La información importante llega clara y oportuna?', 'dim' => 'Comunicación', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q4_comunicacion_escucha', 'label' => '4. ¿Te sientes escuchado(a) cuando expresas tus ideas o preocupaciones?', 'dim' => 'Comunicación', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q5_liderazgo', 'label' => '5. ¿Tu jefe inmediato brinda apoyo y orientación cuando lo necesitas?', 'dim' => 'Liderazgo', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q6_reconocimiento', 'label' => '6. ¿Tu jefe reconoce y valora tu trabajo?', 'dim' => 'Reconocimiento', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q7_desarrollo', 'label' => '7. ¿La empresa ofrece oportunidades de capacitación y crecimiento?', 'dim' => 'Desarrollo', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q8_motivacion', 'label' => '8. ¿Te sientes motivado(a) para dar lo mejor en tu trabajo?', 'dim' => 'Motivación', 'scale' => '1 = Nada motivado, 5 = Muy motivado'],
                    ['name' => 'q9_satisfaccion', 'label' => '9. ¿Recomendarías a alguien trabajar en esta empresa?', 'dim' => 'Satisfacción', 'scale' => '1 = Nunca, 5 = Siempre'],
                    ['name' => 'q10_bienestar_carga', 'label' => '10. ¿Consideras que tu carga de trabajo es adecuada?', 'dim' => 'Bienestar', 'scale' => '1 = Nada adecuada, 5 = Muy adecuada'],
                    ['name' => 'q11_bienestar_preocupacion', 'label' => '11. ¿La empresa se preocupa por tu bienestar físico y emocional?', 'dim' => 'Bienestar', 'scale' => '1 = Nunca, 5 = Siempre'],
                ];
            @endphp

            <div class="space-y-5">
                @foreach($questions as $q)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="mb-2">
                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wider">{{ $q['dim'] }}</span>
                        <p class="text-sm font-medium text-gray-800 mt-1">{{ $q['label'] }}</p>
                        <p class="text-xs text-gray-500 italic mt-1">{{ $q['scale'] }}</p>
                    </div>
                    
                    <div class="flex items-center justify-between mt-3 max-w-sm mx-auto sm:mx-0">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="flex flex-col items-center cursor-pointer group">
                            <input type="radio" name="{{ $q['name'] }}" value="{{ $i }}" required class="sr-only peer">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 border-gray-300 text-gray-600 bg-white peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white peer-focus:ring-2 peer-focus:ring-green-400 peer-focus:ring-offset-1 transition-all duration-200 group-hover:border-green-400 shadow-sm">
                                <span class="font-bold text-sm">{{ $i }}</span>
                            </div>
                        </label>
                        @endfor
                    </div>
                </div>
                @endforeach

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="mb-2">
                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wider">Pregunta Abierta</span>
                        <p class="text-sm font-medium text-gray-800 mt-1">12. ¿Qué sugerencias tienes para mejorar el clima laboral en la empresa?</p>
                        <p class="text-xs text-gray-500 italic mt-1">Respuesta libre</p>
                    </div>
                    <textarea name="q12_sugerencias" rows="4" class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:border-transparent resize-none shadow-sm" placeholder="Escribe tus sugerencias aquí... (Opcional)"></textarea>
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
