<x-modal id="inventory-modal" maxWidth="lg">
    <form class="flex flex-col items-center w-full gap-3 p-2" id="inventory-form" action="{{ route('production.records.storeInventory') }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="inv-method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="inv-modal-title">Nuevo Producto en Inventario</x-tittle-form>
                <p class="text-sm text-gray-500">Gestión de inventario y stock mínimo por línea de producción</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <!-- Selector Visual de Área -->
        <div class="w-full px-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Línea / Área <span class="text-red-500">*</span></label>
            <input type="hidden" name="area" id="inv_area_input" value="fertil" required>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" id="btn-inv-area-fertil" onclick="selectInvArea('fertil')" class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-green-600 bg-green-50 text-green-800">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-600"></span>
                    Fertil
                </button>
                <button type="button" id="btn-inv-area-vitayela" onclick="selectInvArea('vitayela')" class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    Vitayela
                </button>
            </div>
        </div>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="inv_producto_descripcion">Descripción o Producto <span class="text-red-500">*</span></x-label>
                <x-input-1 type="text" name="producto_descripcion" id="inv_producto_descripcion" placeholder="Ej. Vitayela Porcino / Fertilizante..." required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="inv_cantidad">Cantidad Actual</x-label>
                <x-input-1 type="number" step="0.01" min="0" name="cantidad" id="inv_cantidad" value="0"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="inv_unidad">Unidad de Medida</x-label>
                <x-input-1 type="text" list="unidades_sugeridas" name="unidad" id="inv_unidad" placeholder="Ej. Sacos, Kg, Lts..."></x-input-1>
                <datalist id="unidades_sugeridas">
                    <option value="Sacos"></option>
                    <option value="Kg"></option>
                    <option value="Litros"></option>
                    <option value="Cubetas"></option>
                    <option value="Garrafas"></option>
                    <option value="Piezas"></option>
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="inv_stock_min">Alerta de Stock Mínimo</x-label>
                <x-input-1 type="number" step="0.01" min="0" name="stock_min" id="inv_stock_min" value="0"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeInventoryModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Guardar Producto</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function selectInvArea(area) {
        document.getElementById('inv_area_input').value = area;
        const btnFertil = document.getElementById('btn-inv-area-fertil');
        const btnVitayela = document.getElementById('btn-inv-area-vitayela');

        if (area === 'fertil') {
            btnFertil.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-green-600 bg-green-50 text-green-800 shadow-xs';
            btnVitayela.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        } else {
            btnVitayela.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-indigo-600 bg-indigo-50 text-indigo-800 shadow-xs';
            btnFertil.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        }
    }

    function openCreateInventory() {
        document.getElementById('inventory-form').action = "{{ route('production.records.storeInventory') }}";
        document.getElementById('inv-method').value = "POST";
        document.getElementById('inv-modal-title').innerText = "Nuevo Producto de Inventario";

        document.getElementById('inventory-form').reset();
        const defaultArea = (window.currentAreaFilter === 'vitayela') ? 'vitayela' : 'fertil';
        selectInvArea(defaultArea);

        document.getElementById('inventory-modal').classList.remove('hidden');
        setTimeout(() => document.getElementById('inv_producto_descripcion').focus(), 100);
    }

    function editInventory(inv) {
        document.getElementById('inventory-form').action = `/production/records/inventories/${inv.production_inventory_id}`;
        document.getElementById('inv-method').value = "PUT";
        document.getElementById('inv-modal-title').innerText = "Editar Producto de Inventario";

        selectInvArea(inv.area || 'fertil');
        document.getElementById('inv_producto_descripcion').value = inv.producto_descripcion ?? '';
        document.getElementById('inv_cantidad').value = inv.cantidad ?? 0;
        document.getElementById('inv_unidad').value = inv.unidad ?? '';
        document.getElementById('inv_stock_min').value = inv.stock_min ?? 0;

        document.getElementById('inventory-modal').classList.remove('hidden');
    }

    function closeInventoryModal() {
        document.getElementById('inventory-modal').classList.add('hidden');
    }

    $(function() {
        $('#inventory-form').submit(function(event) {
            event.preventDefault();
            const form = this;
            const data = new FormData(form);
            const saveBtn = $(form).find('button[type="submit"]');

            saveBtn.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: form.action,
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: response.message || 'El producto fue guardado correctamente.',
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'No se pudo guardar el producto.'
                    });
                    saveBtn.prop('disabled', false);
                }
            });
        });
    });
</script>

