<x-modal id="modal-entrevista-terminacion">
    <form method="POST" action="{{ route('rh.entrevista_terminacion.pdf') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- ================= DATOS DE IDENTIFICACIÓN ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Generales</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre Completo</label>
                    <input type="text" name="nombre" placeholder="Nombre del colaborador" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Jefe Inmediato</label>
                    <input type="text" name="jefe_inmediato" placeholder="Nombre del jefe directo" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Área / Departamento</label>
                    <input type="text" name="area" placeholder="Ej. Logística / Sistemas" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puesto</label>
                    <input type="text" name="puesto" placeholder="Ej. Auxiliar de CEDIS" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Fecha de último día de trabajo</label>
                    <input type="date" name="fecha_ultimo_dia" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>
        </div>

        {{-- ================= MOTIVOS DE LA RENUNCIA ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Motivos de la Separación</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">¿Cuál es la razón principal por la que renuncias?</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                    @foreach(['Problemas personales', 'Cambio de empleo', 'Horario', 'Mal ambiente de trabajo', 'Estrés', 'Presión', 'Trabajo pesado', 'Salario bajo', 'Crecimiento laboral', 'Falta de capacitación', 'Mal trato laboral'] as $razon)
                        <label class="inline-flex items-center p-1 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" name="razon_principal" value="{{ $razon }}" class="text-green-600 focus:ring-green-600 mr-2">
                            <span>{{ $razon }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">¿Qué te hizo empezar a buscar un nuevo trabajo?</label>
                    <textarea name="que_hizo_buscar" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Qué te hizo decidir aceptar el nuevo trabajo?</label>
                    <textarea name="decidir_aceptar" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Hace cuánto pensabas dejar tu puesto de trabajo?</label>
                    <input type="text" name="hace_cuanto_pensaba" placeholder="Ej. 1 mes, 2 semanas..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Habló con alguien sobre sus inquietudes antes de decidir irte?</label>
                    <input type="text" name="hablo_con_alguien" placeholder="Ej. Sí, con mi jefe directo / No, con nadie" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>
        </div>

        {{-- ================= EVALUACIÓN DE LA EMPRESA ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Expectativas y Experiencia</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="block text-sm font-medium mb-1">¿El trabajo cumplía con tus expectativas?</span>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center"><input type="radio" name="cumplio_expectativas" value="SI" class="mr-2 text-green-600"> Sí</label>
                        <label class="inline-flex items-center"><input type="radio" name="cumplio_expectativas" value="NO" class="mr-2 text-green-600"> No</label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Por qué?</label>
                    <input type="text" name="expectativas_porque" placeholder="Explica brevemente" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">¿Qué te haría reconsiderar tu decisión de irte?</label>
                    <textarea name="que_haria_reconsiderar" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Si pudieras hacer algún cambio en la empresa o en tu trabajo, ¿Cuál sería?</label>
                    <textarea name="cambio_empresa" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Recibió algún comentario constructivo que le haya ayudado con su desempeño?</label>
                    <textarea name="comentario_constructivo" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Qué es lo que más te gustó de trabajar con nosotros?</label>
                    <textarea name="que_mas_gusto" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                </div>
            </div>
        </div>

        {{-- ================= MEJORAS Y SATISFACCIÓN ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Áreas de Oportunidad</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">¿En qué podríamos mejorar como empresa? (Selecciona una principal)</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                    @foreach(['Instalaciones', 'Liderazgo', 'Designación de actividades', 'Motivación', 'Organización interna', 'Salario', 'Prestaciones', 'Comunicación', 'Horario', 'Herramientas y tecnología'] as $mejora)
                        <label class="inline-flex items-center p-1 cursor-pointer">
                            <input type="radio" name="mejorar_empresa" value="{{ $mejora }}" class="text-green-600 mr-2">
                            <span>{{ $mejora }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-2">
                    <input type="text" name="mejorar_empresa_otro" placeholder="Otro aspecto..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="block text-sm font-medium mb-1">¿Recomendarías la organización como un buen lugar para trabajar?</span>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center"><input type="radio" name="recomendaria" value="SI" class="mr-2 text-green-600"> Sí</label>
                        <label class="inline-flex items-center"><input type="radio" name="recomendaria" value="NO" class="mr-2 text-green-600"> No</label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Por qué?</label>
                    <input type="text" name="recomendaria_porque" placeholder="Explica tu razón" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Grado de satisfacción general en la empresa:</label>
                <div class="flex gap-6 text-sm">
                    <label class="inline-flex items-center"><input type="radio" name="grado_satisfaccion" value="Muy satisfecho" class="mr-2 text-green-600"> Muy Satisfecho</label>
                    <label class="inline-flex items-center"><input type="radio" name="grado_satisfaccion" value="Satisfecho" class="mr-2 text-green-600"> Satisfecho</label>
                    <label class="inline-flex items-center"><input type="radio" name="grado_satisfaccion" value="Insatisfecho" class="mr-2 text-green-600"> Insatisfecho</label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Comentarios adicionales de tu estancia:</label>
                <textarea name="comentario_adicional" rows="2" placeholder="Cualquier otra observación..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
            </div>
        </div>

        {{-- ================= ACCIONES ================= --}}
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">
                Generar Entrevista
            </x-button>
        </div>
    </form>
</x-modal>