<x-modal id="output-modal" maxWidth="md">
    <form class="flex flex-col items-center w-full gap-3 p-2" id="output-form" action="" method="POST">
        @csrf
        <input type="hidden" name="_method" id="out-method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="out-modal-title">Registrar Salida de Inventario</x-tittle-form>
                <p class="text-sm font-semibold text-gray-700" id="out-product-desc">Descontar producto del stock</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="out_cantidad_salida">Cantidad a retirar <span class="text-red-500">*</span></x-label>
                <x-input-1 type="number" step="0.01" min="0.01" name="cantidad_salida" id="out_cantidad_salida" required></x-input-1>
                <div class="flex justify-between items-center mt-1.5">
                    <p class="text-xs font-semibold text-emerald-700" id="out-current-stock"></p>
                    <div class="flex gap-1">
                        <button type="button" onclick="setQuickOutput(1)" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-2 py-0.5 rounded cursor-pointer">+1</button>
                        <button type="button" onclick="setQuickOutput(5)" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-2 py-0.5 rounded cursor-pointer">+5</button>
                        <button type="button" onclick="setQuickOutput(10)" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-2 py-0.5 rounded cursor-pointer">+10</button>
                    </div>
                </div>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeOutputModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Confirmar Salida</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function setQuickOutput(val) {
        const input = document.getElementById('out_cantidad_salida');
        const current = parseFloat(input.value) || 0;
        input.value = (current + val).toFixed(2).replace(/\.00$/, '');
    }

    function openOutputInventory(inv) {
        document.getElementById('output-form').action = `/production/records/inventories/${inv.production_inventory_id}/output`;
        document.getElementById('out-method').value = "POST";

        const areaLabel = (inv.area || 'fertil').toUpperCase();
        document.getElementById('out-product-desc').innerText = `[${areaLabel}] ${inv.producto_descripcion}`;
        document.getElementById('out-current-stock').innerText = `Disponible: ${inv.cantidad} ${inv.unidad || ''}`;

        document.getElementById('output-form').reset();
        document.getElementById('output-modal').classList.remove('hidden');
        setTimeout(() => document.getElementById('out_cantidad_salida').focus(), 100);
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
                success: function(response) {
                    if (response.alert) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Mínimo Alcanzado',
                            text: response.message,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#f59e0b'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Salida Registrada',
                            text: response.message || 'La salida fue registrada correctamente.',
                            timer: 1400,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
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

