
<x-modal id="modal-practicantes">
    <form method="POST" action="{{ route('rh.practicantes.pdf') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- ================= DATOS GENERALES Y ACADÉMICOS ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Generales y Académicos</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre completo del practicante</label>
                    <input type="text" name="nombre" placeholder="Nombre completo" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Institución Educativa</label>
                    <input type="text" name="institucion" placeholder="Nombre de la escuela/universidad"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Dirección de la Institución</label>
                    <input type="text" name="direccion_inst" placeholder="Calle, número, colonia, municipio"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">N° Control</label>
                            <input type="text" name="n_control" placeholder="Matrícula"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Edad</label>
                            <input type="number" name="edad" placeholder="Años"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Carrera</label>
                    <input type="text" name="carrera" placeholder="Ej. Ingeniería en Sistemas"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Teléfono</label>
                            <input type="tel" name="telefono" placeholder="10 dígitos"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" placeholder="ejemplo@correo.com"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Sexo</label>
                            <select name="sexo" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Estado Civil</label>
                            <select name="estado_civil" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                                <option value="Soltero">Soltero(a)</option>
                                <option value="Casado">Casado(a)</option>
                                <option value="Divorciado">Divorciado(a)</option>
                                <option value="Viudo">Viudo(a)</option>
                                <option value="Unión Libre">Unión Libre</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= FECHAS, HORARIOS Y LOGÍSTICA ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Control de Periodo y Horarios</h3>
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Ingreso</label>
                    <input type="date" name="fecha_ingreso" value="{{ date('Y-m-d') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha Estimada Término</label>
                    <input type="date" name="fecha_estimada"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha Real Término</label>
                    <input type="date" name="fecha_real"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Horario Laboral</label>
                    <input type="text" name="horario_laboral" placeholder="Ej. 08:00 a 14:00"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Horas a Cubrir</label>
                    <input type="number" name="horas_cubrir" placeholder="Ej. 500"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Periodo Proyectado / Fecha</label>
                    <input type="text" name="periodo_proyectado" placeholder="Ej. Ene-Jun 2026"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Días de asistencia</label>
                    <div class="flex flex-wrap gap-3 pt-1">
                        @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                            <label class="inline-flex items-center text-sm">
                                <input type="checkbox" name="dias_asistencia[]" value="{{ $dia }}" class="rounded text-green-600 focus:ring-green-600 mr-1"> {{ $dia }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Medio por el que se enteró</label>
                    <select name="medio_origen" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                        <option value="Recomendación">Recomendación</option>
                        <option value="Expo de residencias">Expo de residencias</option>
                        <option value="Redes Sociales">Redes Sociales</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ================= INFORMACIÓN DE PROYECTO ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Información del Proyecto</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Nombre del Proyecto</label>
                    <input type="text" name="nombre_proyecto" placeholder="Nombre completo del proyecto o residencia"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Área de Aplicación</label>
                    <input type="text" name="area_aplicacion" placeholder="Ej. Sistemas / Calidad"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Asesor Externo asignado</label>
                    <input type="text" name="asesor_externo" placeholder="Nombre del encargado en la empresa"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Persona que firmará el Acuerdo de Trabajo (Estudiante-Escuela-Empresa)</label>
                    <input type="text" name="firma_acuerdo" placeholder="Nombre completo y cargo de la autoridad correspondiente"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        {{-- ================= CARGA ACADÉMICA PENDIENTE ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Materias Pendientes en Escuela</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">¿Tiene materias pendientes?</label>
                    <div class="flex gap-4 pt-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="materias_pendientes" value="SI" class="text-green-600 focus:ring-green-600 mr-1"> SI
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="materias_pendientes" value="NO" checked class="text-green-600 focus:ring-green-600 mr-1"> NO
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre de la Materia</label>
                    <input type="text" name="materia_nombre" placeholder="Si aplica, nombre de la materia"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Horario de cursado</label>
                    <input type="text" name="materia_horario" placeholder="Ej. 16:00 a 18:00"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        {{-- ================= REQUISITOS DE INGRESO ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Requisitos de Ingreso</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left font-semibold text-gray-700">Requisito</th>
                            <th class="p-3 text-center font-semibold text-gray-700 w-20">OK</th>
                            <th class="p-3 text-left font-semibold text-gray-700">Comentario / Observación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach(['Fotografía infantil', 'Solicitud de empleo o CV', 'Copia de acta de nacimiento', 'Copia de comprobante de domicilio', 'Copia de INE', 'Copia de CURP', 'Copia de RFC', 'Número de Seguro Social', 'Copia del ultimo grado de estudios', 'Constancias de cursos tomados', 'Copia de licencia de manejo', 'Solicitud de Residencias', 'Liberación de servicio social', 'Carta de Presentación', 'Carta de Aceptación', 'Convenio de Colaboración', 'Carta de Terminación'] as $req)
                        <tr>
                            <td class="p-3 text-gray-800 font-medium">{{ $req }}</td>
                            <td class="p-3 text-center">
                                <input type="checkbox" name="req[{{$loop->index}}][ok]" value="1" class="rounded text-green-600 focus:ring-green-600 h-4 w-4">
                            </td>
                            <td class="p-2">
                                <input type="text" name="req[{{$loop->index}}][com]" placeholder="Añadir comentario..."
                                       class="w-full text-xs rounded-md border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-green-600">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= DATOS FAMILIARES ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Familiares</h3>
            <div class="grid md:grid-cols-3 gap-4 mb-3">
                <div>
                    <label class="block text-sm font-medium mb-1">¿Tiene hijos?</label>
                    <div class="flex gap-4 pt-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="tiene_hijos" value="SI" class="text-green-600 focus:ring-green-600 mr-1"> SI
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="tiene_hijos" value="NO" checked class="text-green-600 focus:ring-green-600 mr-1"> NO
                        </label>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">¿Cuántos?</label>
                    <input type="number" name="cuantos_hijos" placeholder="Número de hijos"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 border rounded-lg">
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <input type="text" name="hijo1_nombre" placeholder="Nombre Hijo 1" class="w-full rounded-md border border-gray-300 p-1.5 text-xs">
                    </div>
                    <div>
                        <input type="number" name="hijo1_edad" placeholder="Edad" class="w-full rounded-md border border-gray-300 p-1.5 text-xs">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <input type="text" name="hijo2_nombre" placeholder="Nombre Hijo 2" class="w-full rounded-md border border-gray-300 p-1.5 text-xs">
                    </div>
                    <div>
                        <input type="number" name="hijo2_edad" placeholder="Edad" class="w-full rounded-md border border-gray-300 p-1.5 text-xs">
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">¿Su esposo(a) / pareja trabaja?</label>
                    <div class="flex gap-4 pt-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="pareja_trabaja" value="SI" class="text-green-600 focus:ring-green-600 mr-1"> SI
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="pareja_trabaja" value="NO" checked class="text-green-600 focus:ring-green-600 mr-1"> NO
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre de la Empresa</label>
                    <input type="text" name="pareja_empresa" placeholder="Empresa de la pareja"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Dirección de la Empresa</label>
                    <input type="text" name="pareja_direccion" placeholder="Dirección de la empresa"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono Laboral Pareja</label>
                    <input type="tel" name="pareja_telefono" placeholder="Teléfono"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        {{-- ================= SEGURIDAD SOCIAL Y SALUD ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Seguridad Social y Salud</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Régimen de Seguridad Social</label>
                    <select name="seguridad_social" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                        <option value="IMSS">IMSS</option>
                        <option value="ISSSTE">ISSSTE</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">N° Seguridad Social (NSS)</label>
                    <input type="text" name="nss" placeholder="Número de Afiliación"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Sangre</label>
                    <input type="text" name="tipo_sangre" placeholder="Ej. O+"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">¿Es alérgico a algo?</label>
                    <input type="text" name="alergias" placeholder="Especifique alergias o 'Ninguna'"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        {{-- ================= CONTACTO DE EMERGENCIA ================= --}}
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-red-600">En caso de accidente llamar a:</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Nombre del Contacto</label>
                    <input type="text" name="emergencia_nombre" placeholder="Nombre completo"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Parentesco</label>
                    <input type="text" name="emergencia_parentesco" placeholder="Ej. Padre / Madre / Hermano"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono de Emergencia</label>
                    <input type="tel" name="emergencia_telefono" placeholder="10 dígitos"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        {{-- ================= ACCIONES DEL MODAL ================= --}}
        <div class="flex justify-end gap-2 pt-4 border-t">
            <button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancelar
            </button>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                Generar Expediente Practicante
            </button>
        </div>
    </form>
</x-modal>
