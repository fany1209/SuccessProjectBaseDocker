<x-modal id="output-modal" maxWidth="md">
    <form class="flex flex-col items-center w-full gap-2" id="output-form" action="" method="POST">
        @csrf
        <input type="hidden" name="_method" id="out-method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="out-modal-title">Registrar Salida de Inventario</x-tittle-form>
                <p class="text-sm text-gray-500" id="out-product-desc">Descontar producto del stock</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="out_cantidad_salida">Cantidad a retirar <span class="text-red-500">*</span></x-label>
                <x-input-1 type="number" step="0.01" name="cantidad_salida" id="out_cantidad_salida" required></x-input-1>
                <p class="text-xs text-gray-400 mt-1" id="out-current-stock"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeOutputModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Confirmar Salida</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function openOutputInventory(inv) {
        document.getElementById('output-form').action = `/production/vitayela/inventories/${inv.vitayela_inventory_id}/output`;
        document.getElementById('out-method').value = "POST";
        
        document.getElementById('out-product-desc').innerText = `Producto: ${inv.producto_descripcion}`;
        document.getElementById('out-current-stock').innerText = `Stock actual: ${inv.cantidad} ${inv.unidad || ''}`;
        
        document.getElementById('output-form').reset();
        
        document.getElementById('output-modal').classList.remove('hidden');
    }

    function closeOutputModal() {
        document.getElementById('output-modal').classList.add('hidden');
    }

    $(function() {
        $('#output-form').submit(function(event) {
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
                    if(response.alert) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Mínimo Alcanzado',
                            text: response.message,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#f59e0b' // amber
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message || 'La salida fue registrada correctamente.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'No se pudo registrar la salida.'
                    });
                    saveBtn.prop('disabled', false);
                }
            });
        });
    });
</script>
