<x-modal id="history-modal" maxWidth="2xl">
    <div class="flex flex-col items-center w-full gap-2 p-4">
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="hist-modal-title">Historial de Movimientos</x-tittle-form>
                <p class="text-sm text-gray-500" id="hist-product-desc">Cargando...</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div class="w-full mt-4 overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID Movimiento</th>
                        <th scope="col" class="px-6 py-3">Fecha</th>
                        <th scope="col" class="px-6 py-3">Tipo</th>
                        <th scope="col" class="px-6 py-3">Cantidad</th>
                    </tr>
                </thead>
                <tbody id="hist-tbody">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">Cargando historial...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 class="close-modal" type="button" onclick="closeHistoryModal()" colorBtn="green">Cerrar</x-button-1>
        </x-wrapper-form-1>
    </div>
</x-modal>

<script>
    function openHistoryModal(invId, desc) {
        document.getElementById('hist-product-desc').innerText = `Producto: ${desc}`;
        document.getElementById('history-modal').classList.remove('hidden');
        document.getElementById('hist-tbody').innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center">Cargando historial...</td></tr>';
        
        // Fetch movements
        $.ajax({
            url: `/production/proteamin/inventories/${invId}/movements`,
            type: 'GET',
            success: function(response) {
                let tbody = document.getElementById('hist-tbody');
                tbody.innerHTML = '';
                
                if (response.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center">No hay movimientos registrados.</td></tr>';
                    return;
                }
                
                response.forEach(function(mov) {
                    let tipoBadge = '';
                    if (mov.tipo === 'Entrada') {
                        tipoBadge = '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Entrada</span>';
                    } else if (mov.tipo === 'Salida') {
                        tipoBadge = '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Salida</span>';
                    } else {
                        tipoBadge = '<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400">Ajuste</span>';
                    }

                    // format date
                    let d = new Date(mov.created_at);
                    let formattedDate = d.toLocaleDateString() + ' ' + d.toLocaleTimeString();

                    tbody.innerHTML += `
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">${mov.movement_id}</td>
                            <td class="px-6 py-4">${formattedDate}</td>
                            <td class="px-6 py-4">${tipoBadge}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">${mov.cantidad}</td>
                        </tr>
                    `;
                });
            },
            error: function() {
                document.getElementById('hist-tbody').innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Error al cargar historial.</td></tr>';
            }
        });
    }

    function closeHistoryModal() {
        document.getElementById('history-modal').classList.add('hidden');
    }
</script>
