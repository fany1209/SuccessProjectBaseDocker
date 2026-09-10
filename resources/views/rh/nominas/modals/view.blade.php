<!-- View Nomina Details Modal -->
<x-modal id="view-nomina-modal" maxWidth="4xl">
  <div class="px-6 py-5 max-h-[88vh] overflow-y-auto">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-5">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-[#198754] font-bold text-xl shadow-sm" id="view-avatar-badge">
          <i class="ri-user-3-line"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-xl font-bold text-gray-800" id="view-nombre">—</h2>
            <span id="view-estatus-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">—</span>
          </div>
          <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5">
            <span class="font-medium text-gray-700" id="view-puesto">—</span>
            <span>•</span>
            <span id="view-antiguedad-header" class="text-emerald-700 font-semibold">—</span>
          </p>
        </div>
      </div>
      <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition text-2xl">
        <i class="ri-close-line"></i>
      </button>
    </div>

    <!-- Contenido en Tarjetas -->
    <div class="space-y-4 text-sm">

      <!-- Tarjeta 1: Identificación y Datos Personales -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
        <h4 class="text-xs font-bold text-[#198754] uppercase tracking-wider mb-3 flex items-center gap-1.5 border-b pb-2 border-gray-100">
          <i class="ri-id-card-line"></i> Identificación y Datos Personales
        </h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div>
            <span class="block text-xs text-gray-400">CURP</span>
            <span class="font-mono font-bold text-gray-800 text-xs" id="view-curp">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">RFC</span>
            <span class="font-mono font-bold text-gray-800 text-xs" id="view-rfc">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">NSS (Seguro Social)</span>
            <span class="font-mono font-bold text-gray-800 text-xs" id="view-nss">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Edad</span>
            <span class="font-bold text-blue-700" id="view-edad">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Sexo</span>
            <span class="font-medium text-gray-700" id="view-sexo">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Estado Civil</span>
            <span class="font-medium text-gray-700" id="view-estado-civil">—</span>
          </div>
          <div class="sm:col-span-2">
            <span class="block text-xs text-gray-400">Fecha de Nacimiento</span>
            <span class="font-medium text-gray-700" id="view-fecha-nacimiento">—</span>
          </div>
        </div>
      </div>

      <!-- Tarjeta 2: Datos Laborales -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
        <h4 class="text-xs font-bold text-[#198754] uppercase tracking-wider mb-3 flex items-center gap-1.5 border-b pb-2 border-gray-100">
          <i class="ri-briefcase-4-line"></i> Información Laboral
        </h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div>
            <span class="block text-xs text-gray-400">Puesto</span>
            <span class="font-bold text-gray-800" id="view-puesto-field">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Fecha de Ingreso</span>
            <span class="font-medium text-gray-800" id="view-fecha-ingreso">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Fecha de Baja</span>
            <span class="font-medium text-gray-800" id="view-fecha-baja">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Antigüedad Total</span>
            <span class="font-bold text-emerald-700" id="view-antiguedad">—</span>
          </div>
        </div>
      </div>

      <!-- Tarjeta 3: Beneficiario -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
        <h4 class="text-xs font-bold text-[#198754] uppercase tracking-wider mb-3 flex items-center gap-1.5 border-b pb-2 border-gray-100">
          <i class="ri-user-heart-line"></i> Beneficiario Asignado
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <span class="block text-xs text-gray-400">Nombre del Beneficiario</span>
            <span class="font-bold text-gray-800" id="view-beneficiario">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Parentesco</span>
            <span class="font-medium text-gray-700" id="view-parentesco">—</span>
          </div>
        </div>
      </div>

      <!-- Tarjeta 4: Domicilio y Contacto -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs">
        <h4 class="text-xs font-bold text-[#198754] uppercase tracking-wider mb-3 flex items-center gap-1.5 border-b pb-2 border-gray-100">
          <i class="ri-map-pin-user-line"></i> Domicilio y Contacto
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <span class="block text-xs text-gray-400">Domicilio</span>
            <span class="font-medium text-gray-800" id="view-domicilio">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Código Postal (CP)</span>
            <span class="font-mono font-medium text-gray-700" id="view-cp">—</span>
          </div>
          <div>
            <span class="block text-xs text-gray-400">Teléfono</span>
            <span class="font-medium text-gray-800" id="view-telefono">—</span>
          </div>
          <div class="sm:col-span-2">
            <span class="block text-xs text-gray-400">Correo Electrónico</span>
            <span class="font-medium text-gray-800" id="view-correo">—</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer -->
    <div class="mt-6 flex justify-end pt-4 border-t border-gray-200">
      <button type="button" class="close-modal bg-gray-100 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
        Cerrar
      </button>
    </div>

  </div>
</x-modal>
