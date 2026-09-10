<!-- Edit Nomina Modal -->
<x-modal id="edit-nomina-modal" maxWidth="5xl">
  <div class="px-6 py-5 max-h-[88vh] overflow-y-auto">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-5">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
          <i class="ri-edit-2-line text-xl"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-gray-800" id="edit-modal-title">Editar Empleado en Nómina</h2>
          <p class="text-xs text-gray-500">Modifica la información del colaborador (Edad y Antigüedad se calculan automáticamente)</p>
        </div>
      </div>
      <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition text-2xl">
        <i class="ri-close-line"></i>
      </button>
    </div>

    <form id="edit-nomina-form">
      <input type="hidden" id="edit-nomina-id">

      <div class="space-y-6">

        <!-- Sección 1: Datos Personales e Identificación -->
        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1.5">
              <i class="ri-profile-line"></i> 1. Datos Personales e Identificación
            </h3>
            <!-- Indicador de Edad Calculada Dinámicamente (No editable) -->
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500 font-medium">Edad calculada:</span>
              <span id="edit-preview-edad" class="px-2.5 py-0.5 bg-blue-100 text-blue-800 font-bold text-xs rounded-full border border-blue-200">—</span>
            </div>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
              <input type="text" name="nombre" id="edit-nombre" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">CURP</label>
              <input type="text" name="curp" id="edit-curp" maxlength="18" class="w-full text-sm font-mono uppercase border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">RFC</label>
              <input type="text" name="rfc" id="edit-rfc" maxlength="13" class="w-full text-sm font-mono uppercase border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">NSS (Seguro Social)</label>
              <input type="text" name="nss" id="edit-nss" maxlength="20" class="w-full text-sm font-mono border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Sexo</label>
              <select name="sexo" id="edit-sexo" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Seleccionar...</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Estado Civil</label>
              <select name="estado_civil" id="edit-estado-civil" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Seleccionar...</option>
                <option value="Soltero(a)">Soltero(a)</option>
                <option value="Casado(a)">Casado(a)</option>
                <option value="Unión Libre">Unión Libre</option>
                <option value="Divorciado(a)">Divorciado(a)</option>
                <option value="Viudo(a)">Viudo(a)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha de Nacimiento</label>
              <input type="date" name="fecha_nacimiento" id="edit-fecha-nacimiento" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
        </div>

        <!-- Sección 2: Datos Laborales -->
        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1.5">
              <i class="ri-briefcase-line"></i> 2. Información Laboral
            </h3>
            <!-- Indicador de Antigüedad Calculada Dinámicamente (No editable) -->
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-500 font-medium">Antigüedad calculada:</span>
              <span id="edit-preview-antiguedad" class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full border border-emerald-200">—</span>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Puesto <span class="text-red-500">*</span></label>
              <input type="text" name="puesto" id="edit-puesto" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha de Ingreso <span class="text-red-500">*</span></label>
              <input type="date" name="fecha_ingreso" id="edit-fecha-ingreso" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha de Baja <span class="text-[10px] text-gray-400 font-normal">(Opcional)</span></label>
              <input type="date" name="fecha_baja" id="edit-fecha-baja" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Estatus</label>
              <select name="estatus" id="edit-estatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="Activo">Activo</option>
                <option value="Baja">Baja</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Sección 3: Datos del Beneficiario -->
        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
          <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i class="ri-user-heart-line"></i> 3. Información del Beneficiario
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre de Beneficiario</label>
              <input type="text" name="nombre_beneficiario" id="edit-nombre-beneficiario" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Parentesco</label>
              <input type="text" name="parentesco" id="edit-parentesco" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
        </div>

        <!-- Sección 4: Domicilio y Contacto -->
        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
          <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i class="ri-map-pin-user-line"></i> 4. Domicilio y Contacto
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sm:col-span-2 lg:col-span-3">
              <label class="block text-xs font-semibold text-gray-700 mb-1">Domicilio</label>
              <input type="text" name="domicilio" id="edit-domicilio" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Código Postal (CP)</label>
              <input type="text" name="cp" id="edit-cp" maxlength="10" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="sm:col-span-1 lg:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 mb-1">Teléfono</label>
              <input type="tel" name="telefono" id="edit-telefono" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="sm:col-span-1 lg:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 mb-1">Correo Electrónico</label>
              <input type="email" name="correo" id="edit-correo" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200">
        <button type="button" class="close-modal bg-white text-gray-700 px-5 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium text-sm">
          Cancelar
        </button>
        <button type="submit" id="btn-submit-edit-nomina" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-1.5 shadow-sm">
          <i class="ri-check-line"></i> Guardar Cambios
        </button>
      </div>
    </form>

  </div>
</x-modal>
