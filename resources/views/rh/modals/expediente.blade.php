<x-modal id="modal-expediente">
    <form method="POST" action="{{ route('rh.expediente.pdf') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Generales</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre completo</label>
                <input type="text" name="nombre" placeholder="Nombre del empleado"
                       value="{{ old('nombre') }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Fotografía Infantil</label>
                <input type="file" name="foto" accept="image/jpeg, image/png, image/jpg"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-md py-1 px-2">
                <p class="text-xs text-gray-400 mt-1">Formatos permitidos: JPG, PNG.</p>
            </div>
        </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Requisitos de Contratación</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-3 py-2 w-1/2">Requisito</th>
                            <th class="px-3 py-2 text-center">Estatus</th>
                            <th class="px-3 py-2">Comentario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $requisitos = [
                                'Fotografía infantil', 'Solicitud de empleo o CV', 'Copia de acta de nacimiento',
                                'Copia de comprobante de domicilio', 'Copia de INE', 'Copia de CURP', 'Copia de RFC',
                                'Número de Seguro Social', 'Copia del último grado de estudios', 'Constancias de cursos tomados',
                                'Copia de licencia de manejo', '2 cartas de recomendación laboral'
                            ];
                        @endphp
                        @foreach($requisitos as $i => $req)
                        <tr class="border-t">
                            <td class="px-3 py-2 font-medium">{{ $req }}
                                <input type="hidden" name="requisitos[{{$i}}][nombre]" value="{{ $req }}">
                            </td>
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                <label class="inline-flex items-center mr-3">
                                    <input type="radio" name="requisitos[{{$i}}][status]" value="OK" class="text-green-600 focus:ring-green-600"> 
                                    <span class="ml-1 text-xs">OK</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="requisitos[{{$i}}][status]" value="PENDIENTE" class="text-orange-500 focus:ring-orange-500"> 
                                    <span class="ml-1 text-xs">Pendiente</span>
                                </label>
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="requisitos[{{$i}}][comentario]" placeholder="Observación..."
                                       class="w-full text-xs rounded border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-green-600">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Historial en la Empresa</h3>
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso_1" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Duración</label>
                    <input type="text" name="duracion_1" placeholder="Ej. 1 año" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de baja</label>
                    <input type="date" name="fecha_baja_1" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>


            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <span class="block text-sm font-medium mb-1">Motivo de baja</span>
                    <select name="motivo_baja" id="motivo_baja" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                        <option value="">Seleccione...</option>
                        <option value="Renuncia">Renuncia</option>
                        <option value="Despido">Despido</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div>
                    <span class="block text-sm font-medium mb-1">Mencione (Si es Otro)</span>
                    <input type="text" name="motivo_baja_otro" placeholder="Especifique el motivo"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Vacaciones</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Años cumplidos en la empresa</label>
                    <input type="number" name="anios_empresa" id="anios_empresa" placeholder="Ej. 1" min="0"
                           oninput="calcularVacaciones()"
                           value="{{ old('anios_empresa') }}"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Días que le corresponden</label>
                    <input type="number" name="dias_vacaciones" id="dias_vacaciones" placeholder="Se calcula automático..."
                           value="{{ old('dias_vacaciones') }}" readonly
                           class="w-full bg-gray-100 rounded-md border border-gray-300 px-3 py-2 focus:outline-none cursor-not-allowed">
                </div>
            </div>
        </div>

   
        <div>
            <h3 class="text-base font-semibold mb-3 border-b pb-1 text-gray-800">Datos Familiares y Médicos</h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="block text-sm font-medium mb-1">Sexo</span>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="sexo" value="M" {{ old('sexo') === 'M' ? 'checked' : '' }} class="mr-2 text-green-600 focus:ring-green-600">
                            <span>M</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sexo" value="F" {{ old('sexo') === 'F' ? 'checked' : '' }} class="mr-2 text-green-600 focus:ring-green-600">
                            <span>F</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sexo" value="Otro" {{ old('sexo') === 'Otro' ? 'checked' : '' }} class="mr-2 text-green-600 focus:ring-green-600">
                            <span>Otro</span>
                        </label>
                    </div>
                </div>

                <div>
                    <span class="block text-sm font-medium mb-1">Estado Civil</span>
                    <select name="estado_civil" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                        <option value="">Seleccione...</option>
                        <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado</option>
                        <option value="Divorciado" {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                        <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                        <option value="Unión Libre" {{ old('estado_civil') == 'Unión Libre' ? 'selected' : '' }}>Unión Libre</option>
                        <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                        <option value="Otro" {{ old('estado_civil') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div>
                    <span class="block text-sm font-medium mb-1">¿Tiene hijos?</span>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tiene_hijos" value="Si" class="mr-2 text-green-600 focus:ring-green-600" onchange="toggleHijos(true)"> Si
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tiene_hijos" value="No" checked class="mr-2 text-green-600 focus:ring-green-600" onchange="toggleHijos(false)"> No
                        </label>
                    </div>
                </div>
                <div id="div_cuantos_hijos" style="display: none;">
                    <label class="block text-sm font-medium mb-1">Cuántos</label>
                    <input type="number" name="cuantos_hijos" min="0" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div id="seccion_hijos" style="display: none;" class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-600">Registro de Hijos</h4>
                    <button type="button" id="btn-add-hijo" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1 text-xs hover:bg-gray-50">
                        + Agregar Hijo
                    </button>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg p-2">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 text-left w-3/4">Nombre</th>
                                <th class="px-2 py-1 text-left">Edad</th>
                                <th class="px-2 py-1"></th>
                            </tr>
                        </thead>
                        <tbody id="hijos-rows">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="block text-sm font-medium mb-1">¿Su esposa(o) o pareja trabaja?</span>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center"><input type="radio" name="pareja_trabaja" value="Si" class="mr-2"> Si</label>
                        <label class="inline-flex items-center"><input type="radio" name="pareja_trabaja" value="No" class="mr-2"> No</label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre de la empresa</label>
                    <input type="text" name="empresa_pareja" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <h4 class="text-sm font-semibold mt-4 mb-2 text-red-600">En caso de accidente llamar a:</h4>
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Nombre Completo / Dirección</label>
                    <input type="text" name="accidente_nombre" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Parentesco</label>
                    <input type="text" name="accidente_parentesco" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="accidente_telefono" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <span class="block text-sm font-medium mb-1">¿Es alérgico a algo?</span>
                    <div class="flex gap-4 pt-1">
                        <label class="inline-flex items-center"><input type="radio" name="alergico" value="Si" class="mr-2"> Si</label>
                        <label class="inline-flex items-center"><input type="radio" name="alergico" value="No" class="mr-2"> No</label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Mencione (alergias)</label>
                    <input type="text" name="alergias_desc" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de sangre</label>
                    <select name="tipo_sangre" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                        <option value="">Seleccione...</option>
                        <option value="A+">A+</option><option value="A-">A-</option>
                        <option value="B+">B+</option><option value="B-">B-</option>
                        <option value="O+">O+</option><option value="O-">O-</option>
                        <option value="AB+">AB+</option><option value="AB-">AB-</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-4 border-t">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white">
                Guardar Expediente
            </x-button>
        </div>
    </form>

    <script>
        function toggleHijos(show) {
            const divCuantos = document.getElementById('div_cuantos_hijos');
            if (show) {
                divCuantos.style.display = 'block';
            } else {
                divCuantos.style.display = 'none';
                document.querySelector('input[name="cuantos_hijos"]').value = '';
            }
        }

        function calcularVacaciones() {
            const inputAnios = document.getElementById('anios_empresa').value;
            const inputDias = document.getElementById('dias_vacaciones');
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
    </script>


    <script>
        function toggleHijos(show) {
            const divCuantos = document.getElementById('div_cuantos_hijos');
            const secHijos = document.getElementById('seccion_hijos');
            if (show) {
                divCuantos.style.display = 'block';
                secHijos.style.display = 'block';
                if(document.getElementById('hijos-rows').children.length === 0) {
                    document.getElementById('btn-add-hijo').click();
                }
            } else {
                divCuantos.style.display = 'none';
                secHijos.style.display = 'none';
            }
        }

        (function(){
            const tbodyHijos = document.getElementById('hijos-rows');
            const btnAddHijo = document.getElementById('btn-add-hijo');
            let idxHijo = 0;

            function rowHijoTemplate(i) {
                return `
                <tr class="border-t dyn-hijo">
                    <td class="px-2 py-2">
                        <input type="text" name="hijos[${i}][nombre]" placeholder="Nombre del hijo/a" class="w-full text-sm rounded-md border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-green-600">
                    </td>
                    <td class="px-2 py-2">
                        <input type="number" name="hijos[${i}][edad]" placeholder="Edad" class="w-full text-sm rounded-md border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-green-600">
                    </td>
                    <td class="px-2 py-2 text-center">
                        <button type="button" class="btn-remove-hijo text-red-600 hover:underline text-xs font-bold">X</button>
                    </td>
                </tr>`;
            }

            btnAddHijo?.addEventListener('click', () => {
                tbodyHijos.insertAdjacentHTML('beforeend', rowHijoTemplate(idxHijo));
                idxHijo++;
            });

            tbodyHijos.addEventListener('click', (e) => {
                if(e.target.classList.contains('btn-remove-hijo')){
                    e.target.closest('tr')?.remove();
                }
            });
        })();
    </script>
</x-modal>