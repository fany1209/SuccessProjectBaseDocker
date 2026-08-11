@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-12">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 mt-6 print:shadow-none print:border-none print:p-0">
        
        <!-- Encabezado del Formato -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-4 border-b border-gray-200">
            <div class="mb-4 md:mb-0">
                <a href="{{ route('sistemas-ti.index') }}" class="text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold mb-2 print:hidden">
                    <i class="fas fa-chevron-left text-sm"></i> Volver
                </a>
                <h4 class="text-xl md:text-2xl font-bold text-[#198754] flex items-center">
                    <i class="fas fa-clipboard-check mr-2"></i> Inspección de Equipos y Dispositivos de TI
                </h4>
                <p class="text-gray-500 text-sm mt-1 font-medium">Cédula Técnica de Revisión y Diagnóstico</p>
            </div>
            <div class="text-left md:text-right w-full md:w-auto mt-4 md:mt-0">
                <div class="mb-2">
                    <strong class="text-gray-700">Folio:</strong> 
                    <span class="text-red-600 font-bold ml-1">{{ $folio ?? 'INS-2026-001' }}</span>
                </div>
                <div class="flex items-center md:justify-end">
                    <strong class="text-gray-700 mr-2">Fecha:</strong>
                    <input type="date" form="inspeccionForm" name="date" class="border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50 text-sm py-1 font-medium text-gray-700" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 print:hidden">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-semibold">
                        Revise los siguientes errores:
                    </p>
                    <ul class="list-disc pl-5 text-sm text-red-700 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form id="inspeccionForm" action="{{ route('sistemas-ti.inspecciones.store') }}" method="POST">
            @csrf
            <input type="hidden" name="folio" value="{{ $folio ?? 'INS-2026-001' }}">
            
            <!-- Sección 1: Datos y Condiciones del Equipo -->
            <div class="bg-slate-800 text-white px-4 py-2 rounded-lg font-semibold mb-4 flex items-center justify-between shadow-sm">
                <div><i class="fas fa-laptop mr-2"></i> 1. Datos y Condiciones del Equipo</div>
                <a href="{{ route('sistemas-ti.inventario') }}" class="text-sm font-normal text-blue-300 hover:text-white transition print:hidden" title="Ir al inventario a registrar un nuevo equipo">
                    <i class="fas fa-plus-circle mr-1"></i> Añadir equipo nuevo
                </a>
            </div>
            
            <div class="mb-6 bg-blue-50 border border-blue-100 rounded-lg p-4 print:hidden">
                <label class="block text-sm font-bold text-blue-900 mb-2">Seleccionar Equipo del Inventario (Autocompletado)</label>
                <select id="equipmentSelect" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/50">
                    <option value="">-- Seleccione un equipo existente para llenar los datos automáticamente --</option>
                    @foreach($equipments as $eq)
                        <option value="{{ $eq->id }}" 
                            data-brand="{{ $eq->brand }}" 
                            data-model="{{ $eq->model }}" 
                            data-serial="{{ $eq->serial_number }}" 
                            data-department="{{ $eq->department }}" 
                            data-responsible="{{ $eq->responsible }}">
                            {{ $eq->article }} {{ $eq->brand }} {{ $eq->model }} - Serie: {{ $eq->serial_number ?? 'N/A' }} ({{ $eq->department ?? 'Sin Asignar' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-blue-700 mt-2">
                    * Si el equipo no está en la lista, debes <a href="{{ route('sistemas-ti.inventario') }}" class="underline font-semibold hover:text-blue-900">añadirlo al inventario primero</a>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-8">
                <div class="md:col-span-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Marca</label>
                    <input type="text" id="inputBrand" name="brand" class="w-full bg-gray-100 border-gray-200 rounded-lg shadow-sm focus:ring-0 text-sm text-gray-600 cursor-not-allowed" placeholder="Autocompletado" readonly>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Modelo</label>
                    <input type="text" id="inputModel" name="model" class="w-full bg-gray-100 border-gray-200 rounded-lg shadow-sm focus:ring-0 text-sm text-gray-600 cursor-not-allowed" placeholder="Autocompletado" readonly>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Serie</label>
                    <input type="text" id="inputSerial" name="serial_number" class="w-full bg-gray-100 border-gray-200 rounded-lg shadow-sm focus:ring-0 text-sm text-gray-600 cursor-not-allowed" placeholder="Autocompletado" readonly>
                </div>
                <div class="md:col-span-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Ubicación</label>
                    <input type="text" id="inputLocation" name="location" class="w-full bg-gray-100 border-gray-200 rounded-lg shadow-sm focus:ring-0 text-sm text-gray-600 cursor-not-allowed" placeholder="Autocompletado" readonly>
                </div>
                <div class="md:col-span-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Área / Responsable</label>
                    <input type="text" id="inputArea" name="area" class="w-full bg-gray-100 border-gray-200 rounded-lg shadow-sm focus:ring-0 text-sm text-gray-600 cursor-not-allowed" placeholder="Autocompletado" readonly>
                </div>
            </div>

            <!-- Sección 2: Revisión del Equipo -->
            <div class="bg-slate-800 text-white px-4 py-2 rounded-lg font-semibold mb-4 flex items-center shadow-sm">
                <i class="fas fa-list-check mr-2"></i> 2. Revisión del Equipo
            </div>
            <div class="mb-8 overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                <table class="w-full text-sm text-left divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700">Requisito / Punto de Inspección</th>
                            <th class="px-2 py-3 font-semibold text-gray-700 text-center w-24">Cumple</th>
                            <th class="px-2 py-3 font-semibold text-gray-700 text-center w-24">No Cumple</th>
                            <th class="px-2 py-3 font-semibold text-gray-700 text-center w-24">No Aplica</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium">Limpieza física exterior e interior libre de polvo</td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req1" value="cumple" class="text-[#198754] focus:ring-[#198754] h-4 w-4 cursor-pointer" checked></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req1" value="nocumple" class="text-red-600 focus:ring-red-500 h-4 w-4 cursor-pointer"></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req1" value="na" class="text-gray-500 focus:ring-gray-400 h-4 w-4 cursor-pointer"></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium">Estado óptimo de periféricos (Teclado, Mouse, Pantalla)</td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req2" value="cumple" class="text-[#198754] focus:ring-[#198754] h-4 w-4 cursor-pointer" checked></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req2" value="nocumple" class="text-red-600 focus:ring-red-500 h-4 w-4 cursor-pointer"></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req2" value="na" class="text-gray-500 focus:ring-gray-400 h-4 w-4 cursor-pointer"></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium">Cargador, cables de alimentación y conectores en buen estado</td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req3" value="cumple" class="text-[#198754] focus:ring-[#198754] h-4 w-4 cursor-pointer" checked></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req3" value="nocumple" class="text-red-600 focus:ring-red-500 h-4 w-4 cursor-pointer"></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req3" value="na" class="text-gray-500 focus:ring-gray-400 h-4 w-4 cursor-pointer"></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium">Funcionamiento correcto del Sistema Operativo y Software</td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req4" value="cumple" class="text-[#198754] focus:ring-[#198754] h-4 w-4 cursor-pointer" checked></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req4" value="nocumple" class="text-red-600 focus:ring-red-500 h-4 w-4 cursor-pointer"></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req4" value="na" class="text-gray-500 focus:ring-gray-400 h-4 w-4 cursor-pointer"></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-800 font-medium">Antivirus actualizado y activo</td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req5" value="cumple" class="text-[#198754] focus:ring-[#198754] h-4 w-4 cursor-pointer" checked></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req5" value="nocumple" class="text-red-600 focus:ring-red-500 h-4 w-4 cursor-pointer"></td>
                            <td class="px-2 py-3 text-center"><input type="radio" name="req5" value="na" class="text-gray-500 focus:ring-gray-400 h-4 w-4 cursor-pointer"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Sección 3: Observaciones -->
            <div class="bg-slate-800 text-white px-4 py-2 rounded-lg font-semibold mb-4 flex items-center shadow-sm">
                <i class="fas fa-comment-alt mr-2"></i> 3. Observaciones
            </div>
            <div class="mb-12">
                <textarea name="observations" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50 text-sm" rows="4" placeholder="Escriba aquí los detalles, hallazgos o recomendaciones detectadas durante la inspección..."></textarea>
            </div>

            <!-- Sección 4: Firma del Inspector -->
            <div class="text-center mt-12 mb-8">
                <div class="border-t-2 border-gray-400 w-64 mx-auto mb-2"></div>
                <p class="mb-0 font-bold text-gray-800">Nombre y firma de quien realizó la inspección</p>
                <p class="text-sm text-gray-500 font-medium">Técnico / Responsable de TI</p>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200 print:hidden">
                <button type="button" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition shadow-sm flex items-center" onclick="preparePrint()">
                    <i class="fas fa-print mr-2"></i> Imprimir / PDF
                </button>
                <button type="submit" class="px-4 py-2 bg-[#198754] text-white rounded-lg font-medium hover:bg-[#157347] transition shadow-sm flex items-center">
                    <i class="fas fa-check mr-2"></i> Guardar Inspección
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FORMATO DE IMPRESIÓN CORPORATIVO (Oculto en pantalla, visible al imprimir) -->
<div id="printFormat" class="hidden print:block w-full bg-white">
    <style>
        @media print {
            @page { margin: 15px; }
            body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; background: #fff; }
            .print-tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .print-tbl td, .print-tbl th { border: 1px solid #000; padding: 4px; word-wrap: break-word; }
            .bg-gray { background: #f2f2f2 !important; font-weight: bold; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .th-green { background:#92D050 !important; font-weight:bold; color:#000; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .c { text-align: center; }
            .header-table { width:100%; border-collapse:collapse; border:1px solid #000; margin-bottom: 15px; }
            .header-table td { border:1px solid #000; vertical-align: middle; }
        }
    </style>

    <!-- 1. ENCABEZADO INSTITUCIONAL -->
    <table class="header-table">
        <tr>
            <td rowspan="2" style="width:20%; text-align:center; padding: 5px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:50px; max-width:100%; object-fit:contain;" onerror="this.style.display='none'">
            </td>
            <td colspan="3" style="width:60%; text-align:center; font-weight:bold; font-size:14pt; padding:10px; background:#92D050 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                CÉDULA TÉCNICA DE REVISIÓN Y DIAGNÓSTICO
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:5px;">
                <b>Código:</b><br> STI-FOR-01
            </td>
        </tr>
        <tr>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px; height: 35px;">
                <b>Fecha de elaboración:</b><br> 11-Ago-2026
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px;">
                <b>Fecha de actualización:</b><br> --
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px;">
                <b>Versión:</b><br> 01
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px;">
                <b>Pág.</b> 1 de 1
            </td>
        </tr>
    </table>

    <!-- 2. DATOS DEL EQUIPO -->
    <table class="print-tbl" style="margin-bottom: 15px;">
        <tr>
            <td class="bg-gray" style="width: 20%;">Folio:</td>
            <td style="width: 30%; color: red; font-weight: bold;">{{ $folio ?? '' }}</td>
            <td class="bg-gray" style="width: 20%;">Fecha:</td>
            <td style="width: 30%;"><span id="p_date"></span></td>
        </tr>
        <tr>
            <td class="bg-gray">Marca:</td>
            <td><span id="p_brand"></span></td>
            <td class="bg-gray">Modelo:</td>
            <td><span id="p_model"></span></td>
        </tr>
        <tr>
            <td class="bg-gray">Número de Serie:</td>
            <td><span id="p_serial"></span></td>
            <td class="bg-gray">Ubicación:</td>
            <td><span id="p_location"></span></td>
        </tr>
        <tr>
            <td class="bg-gray">Área / Responsable:</td>
            <td colspan="3"><span id="p_area"></span></td>
        </tr>
    </table>

    <!-- 3. CHECKLIST -->
    <table class="print-tbl" style="margin-bottom: 15px;">
        <tr>
            <td class="th-green c" style="width: 55%;">Requisito / Punto de Inspección</td>
            <td class="th-green c" style="width: 15%;">Cumple</td>
            <td class="th-green c" style="width: 15%;">No Cumple</td>
            <td class="th-green c" style="width: 15%;">No Aplica</td>
        </tr>
        <tr>
            <td>Limpieza física exterior e interior libre de polvo</td>
            <td class="c font-bold"><span id="p_req1_cumple"></span></td>
            <td class="c font-bold"><span id="p_req1_nocumple"></span></td>
            <td class="c font-bold"><span id="p_req1_na"></span></td>
        </tr>
        <tr>
            <td>Estado óptimo de periféricos (Teclado, Mouse, Pantalla)</td>
            <td class="c font-bold"><span id="p_req2_cumple"></span></td>
            <td class="c font-bold"><span id="p_req2_nocumple"></span></td>
            <td class="c font-bold"><span id="p_req2_na"></span></td>
        </tr>
        <tr>
            <td>Cargador, cables de alimentación y conectores en buen estado</td>
            <td class="c font-bold"><span id="p_req3_cumple"></span></td>
            <td class="c font-bold"><span id="p_req3_nocumple"></span></td>
            <td class="c font-bold"><span id="p_req3_na"></span></td>
        </tr>
        <tr>
            <td>Funcionamiento correcto del Sistema Operativo y Software</td>
            <td class="c font-bold"><span id="p_req4_cumple"></span></td>
            <td class="c font-bold"><span id="p_req4_nocumple"></span></td>
            <td class="c font-bold"><span id="p_req4_na"></span></td>
        </tr>
        <tr>
            <td>Antivirus actualizado y activo</td>
            <td class="c font-bold"><span id="p_req5_cumple"></span></td>
            <td class="c font-bold"><span id="p_req5_nocumple"></span></td>
            <td class="c font-bold"><span id="p_req5_na"></span></td>
        </tr>
    </table>

    <!-- 4. OBSERVACIONES -->
    <table class="print-tbl" style="margin-bottom: 30px;">
        <tr>
            <td class="th-green" style="padding: 6px;">Observaciones / Hallazgos:</td>
        </tr>
        <tr>
            <td style="height: 80px; vertical-align: top;"><span id="p_observations"></span></td>
        </tr>
    </table>

    <!-- 5. FIRMAS -->
    <table style="width: 100%; text-align: center; margin-top: 50px;">
        <tr>
            <td style="width: 50%;">
                <div style="border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 5px; font-weight: bold; font-size: 10pt;">
                    Firma del Inspector / Técnico
                </div>
            </td>
            <td style="width: 50%;">
                <div style="border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 5px; font-weight: bold; font-size: 10pt;">
                    Firma de Conformidad (Usuario)
                </div>
            </td>
        </tr>
    </table>
</div>

<script>
    function preparePrint() {
        // Copiar datos del formulario al formato de impresión
        document.getElementById('p_date').innerText = document.querySelector('input[name="date"]').value;
        document.getElementById('p_brand').innerText = document.getElementById('inputBrand').value;
        document.getElementById('p_model').innerText = document.getElementById('inputModel').value;
        document.getElementById('p_serial').innerText = document.getElementById('inputSerial').value;
        document.getElementById('p_location').innerText = document.getElementById('inputLocation').value;
        document.getElementById('p_area').innerText = document.getElementById('inputArea').value;
        
        ['req1','req2','req3','req4','req5'].forEach(req => {
            let selected = document.querySelector(`input[name="${req}"]:checked`);
            let val = selected ? selected.value : '';
            document.getElementById(`p_${req}_cumple`).innerText = val === 'cumple' ? 'X' : '';
            document.getElementById(`p_${req}_nocumple`).innerText = val === 'nocumple' ? 'X' : '';
            document.getElementById(`p_${req}_na`).innerText = val === 'na' ? 'X' : '';
        });

        document.getElementById('p_observations').innerText = document.querySelector('textarea[name="observations"]').value;
        
        // Lanzar diálogo de impresión
        window.print();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('equipmentSelect');
        const brand = document.getElementById('inputBrand');
        const model = document.getElementById('inputModel');
        const serial = document.getElementById('inputSerial');
        const location = document.getElementById('inputLocation');
        const area = document.getElementById('inputArea');

        select.addEventListener('change', function() {
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                brand.value = selectedOption.getAttribute('data-brand') || 'N/A';
                model.value = selectedOption.getAttribute('data-model') || 'N/A';
                serial.value = selectedOption.getAttribute('data-serial') || 'N/A';
                
                const depto = selectedOption.getAttribute('data-department') || 'Sin Asignar';
                location.value = depto;

                const resp = selectedOption.getAttribute('data-responsible');
                area.value = resp ? resp : depto;
            } else {
                brand.value = '';
                model.value = '';
                serial.value = '';
                location.value = '';
                area.value = '';
            }
        });
    });
</script>
@endsection
