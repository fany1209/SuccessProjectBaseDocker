<x-modal id="inventory-modal" maxWidth="lg">
    <form class="flex flex-col items-center w-full gap-2" id="inventory-form" action="{{ route('production.vitayela.storeInventory') }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="inv-method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="inv-modal-title">Nuevo Producto</x-tittle-form>
                <p class="text-sm text-gray-500">Gestión de inventario y stock</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="inv_producto_descripcion">Descripción o Producto <span class="text-red-500">*</span></x-label>
                <x-input-1 type="text" name="producto_descripcion" id="inv_producto_descripcion" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="inv_cantidad">Cantidad</x-label>
                <x-input-1 type="number" step="0.01" name="cantidad" id="inv_cantidad"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="inv_unidad">Unidad</x-label>
                <x-input-1 type="text" name="unidad" id="inv_unidad"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="inv_stock_min">Stock Min</x-label>
                <x-input-1 type="number" step="0.01" name="stock_min" id="inv_stock_min"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeInventoryModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Guardar</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function openCreateInventory() {
        document.getElementById('inventory-form').action = "{{ route('production.vitayela.storeInventory') }}";
        document.getElementById('inv-method').value = "POST";
        document.getElementById('inv-modal-title').innerText = "Nuevo Producto de Inventario";
        
        document.getElementById('inventory-form').reset();
        
        document.getElementById('inventory-modal').classList.remove('hidden');
    }

    function editInventory(inv) {
        document.getElementById('inventory-form').action = `/production/vitayela/inventories/${inv.vitayela_inventory_id}`;
        document.getElementById('inv-method').value = "PUT";
        document.getElementById('inv-modal-title').innerText = "Editar Producto de Inventario";
        
        document.getElementById('inv_producto_descripcion').value = inv.producto_descripcion;
        document.getElementById('inv_cantidad').value = inv.cantidad;
        document.getElementById('inv_unidad').value = inv.unidad;
        document.getElementById('inv_stock_min').value = inv.stock_min;
        
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
                success: function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'El registro fue guardado correctamente.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'No se pudo guardar el registro.'
                    });
                    saveBtn.prop('disabled', false);
                }
            });
        });
    });
</script>
