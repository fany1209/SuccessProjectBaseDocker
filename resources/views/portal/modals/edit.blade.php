<x-modal id="edit-portal-user">
  <div class="px-6 py-5 bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-green-600"></div>

    <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-5 mt-2">
      <div class="flex items-center gap-3">
        <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
          <i class="fas fa-user-edit text-lg"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Editar Usuario de Portal</h3>
          <p class="text-sm text-gray-500 mt-0.5">Modifica los accesos o cambia la contraseña del cliente.</p>
        </div>
      </div>
      <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="edit-portal-user-form" method="POST">
      @csrf
      @method('PUT')
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Vincular con Cliente Interno</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-building text-gray-400"></i>
            </div>
            <select name="customer_id" id="edit-customer_id" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 bg-gray-50 hover:bg-white transition-colors text-sm text-gray-700" required>
              <option value="">-- Selecciona un cliente --</option>
              @foreach($clientesSistemas as $cli)
                <option value="{{ $cli->customer_id }}">{{ $cli->name }} (Código: {{ $cli->customer_code }})</option>
              @endforeach
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre del Contacto</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-400"></i>
            </div>
            <input type="text" name="nombre_contacto" id="edit-nombre_contacto" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 bg-gray-50 hover:bg-white transition-colors text-sm" required>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Empresa / Comercial</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-briefcase text-gray-400"></i>
            </div>
            <input type="text" name="empresa" id="edit-empresa" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 bg-gray-50 hover:bg-white transition-colors text-sm" required>
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Correo Electrónico (Usuario)</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" name="email" id="edit-email" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 bg-gray-50 hover:bg-white transition-colors text-sm" required>
          </div>
        </div>

        <div class="md:col-span-2 bg-amber-50/70 border border-amber-200 rounded-xl p-3 flex items-start gap-3">
            <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
            <p class="text-xs text-amber-800 font-medium leading-relaxed">
                Dejar los campos de contraseña vacíos si <strong>no</strong> deseas cambiar la contraseña actual del cliente.
            </p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nueva Contraseña</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" name="password" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 bg-gray-50 hover:bg-white transition-colors text-sm" placeholder="Mínimo 6 caracteres">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirmar Nueva Contraseña</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-check-circle text-gray-400"></i>
            </div>
            <input type="password" name="password_confirmation" class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 bg-gray-50 hover:bg-white transition-colors text-sm" placeholder="Repite contraseña">
          </div>
        </div>

        <div class="md:col-span-2 bg-emerald-50/50 border border-emerald-100 rounded-xl p-4 mt-1">
          <div class="flex items-center">
            <input type="checkbox" name="is_active" id="edit-is_active" value="1" class="h-5 w-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer">
            <label for="edit-is_active" class="ml-3 block text-sm text-emerald-900 font-semibold cursor-pointer select-none">
              Cuenta Activa (Permitir acceso)
            </label>
          </div>
        </div>

      </div>

      <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-100">
        <button type="button" class="close-modal bg-white text-gray-600 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-gray-800 transition-all font-semibold shadow-sm">
          Cancelar
        </button>
        <button type="submit" class="bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-2.5 rounded-xl hover:from-emerald-500 hover:to-green-500 transition-all font-semibold shadow-md shadow-emerald-500/30 flex items-center gap-2">
          <i class="fas fa-save"></i> Actualizar Cambios
        </button>
      </div>

    </form>
  </div>
</x-modal>