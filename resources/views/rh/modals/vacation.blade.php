<x-modal id="modal-vacaciones">
    <form method="POST" action="{{ route('rh.vacaciones.pdf') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Generales</h3>
            
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Nombre completo</label>
                    <input type="text" name="employee_name" placeholder="Nombre del empleado"
                           value="{{ old('employee_name') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Área / Departamento</label>
                    <input type="text" name="department" placeholder="Ej. Sistemas"
                           value="{{ old('department') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puesto</label>
                    <input type="text" name="position" placeholder="Ej. Desarrollador"
                           value="{{ old('position') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Solicitud</label>
                    <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Jornada de Trabajo</label>
                    <div class="flex gap-6 pt-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="work_schedule" value="L-V" {{ old('work_schedule') === 'L-V' ? 'checked' : '' }} class="mr-2 text-green-600 focus:ring-green-600">
                            <span>Lunes a Viernes</span>
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="radio" name="work_schedule" value="L-S" {{ old('work_schedule', 'L-S') === 'L-S' ? 'checked' : '' }} class="mr-2 text-green-600 focus:ring-green-600">
                            <span>Lunes a Sábado</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Control de Días</h3>
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Antigüedad (Años)</label>
                    <input type="number" name="seniority_years" id="seniority_years" placeholder="0" min="0"
                           oninput="calcularDiasVacaciones()"
                           value="{{ old('seniority_years') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Días Correspondientes</label>
                    <input type="number" name="days_earned" id="days_earned" placeholder="Automático..."
                           value="{{ old('days_earned') }}" readonly
                           class="w-full bg-gray-100 rounded-md border border-gray-300 px-3 py-2 focus:outline-none cursor-not-allowed text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Días Disponibles</label>
                    <input type="number" name="days_available" placeholder="Ej. 14" min="0"
                           value="{{ old('days_available') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Días Requeridos</label>
                    <input type="number" name="days_requested" placeholder="Ej. 5" min="1"
                           value="{{ old('days_requested') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Periodo de Vacaciones</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Inicio</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Término</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Regreso</label>
                    <input type="date" name="return_date" value="{{ old('return_date') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600 text-sm">
                </div>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-2 border-b pb-1">
                <h3 class="text-base font-semibold text-gray-800">Actividades Pendientes a tomar en cuenta</h3>
                <button type="button" id="btn-add-tarea" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1 text-xs font-medium hover:bg-gray-50 focus:ring-2 focus:ring-green-600">
                    + Agregar Actividad
                </button>
            </div>
            
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-3 py-2 w-2/3">Actividad / Pendiente</th>
                            <th class="px-3 py-2 w-1/3">Responsable</th>
                            <th class="px-3 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="tareas-rows">
                        </tbody>
                </table>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Generar Solicitud
            </x-button>
        </div>
    </form>

    <script>
        function calcularDiasVacaciones() {
            const inputAnios = document.getElementById('seniority_years').value;
            const inputDias = document.getElementById('days_earned');
            let anios = parseInt(inputAnios);

            if (isNaN(anios) || anios < 1) {
                inputDias.value = '';
                return;
            }

            let dias = 0;
            if (anios === 1) dias = 12;
            else if (anios === 2) dias = 14;
            else if (anios === 3) dias = 16;
            else if (anios === 4) dias = 18;
            else if (anios === 5) dias = 20;
            else if (anios >= 6 && anios <= 10) dias = 22;   
            else if (anios >= 11 && anios <= 15) dias = 24;  
            else if (anios >= 16 && anios <= 20) dias = 26;
            else if (anios >= 21 && anios <= 25) dias = 28;
            else if (anios >= 26) dias = 30;

            inputDias.value = dias;
        }

        (function(){
            const tbodyTareas = document.getElementById('tareas-rows');
            const btnAddTarea = document.getElementById('btn-add-tarea');
            let idxTarea = 0;

            function rowTareaTemplate(i) {
                return `
                <tr class="border-t dyn-tarea">
                    <td class="px-3 py-2">
                        <input type="text" name="pending_tasks[${i}][description]" placeholder="Descripción de la actividad pendiente..." class="w-full text-xs rounded-md border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-green-600">
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" name="pending_tasks[${i}][responsible_name]" placeholder="Nombre del responsable" class="w-full text-xs rounded-md border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-green-600">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="button" class="btn-remove-tarea text-red-600 hover:text-red-800 font-bold text-sm">×</button>
                    </td>
                </tr>`;
            }

            if(tbodyTareas && tbodyTareas.children.length === 0) {
                tbodyTareas.insertAdjacentHTML('beforeend', rowTareaTemplate(idxTarea));
                idxTarea++;
            }

            btnAddTarea?.addEventListener('click', () => {
                tbodyTareas.insertAdjacentHTML('beforeend', rowTareaTemplate(idxTarea));
                idxTarea++;
            });

            tbodyTareas?.addEventListener('click', (e) => {
                if(e.target.classList.contains('btn-remove-tarea')){
                    e.target.closest('tr')?.remove();
                }
            });
        })();
    </script>
</x-modal>