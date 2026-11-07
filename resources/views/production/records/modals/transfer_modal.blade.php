<x-modal id="transfer-modal" maxWidth="2xl">
    <form class="flex flex-col items-center w-full gap-3 p-2" id="transfer-form" action="" method="POST">
        @csrf
        <input type="hidden" name="_method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Enviar Producto Terminado a Almacén</x-tittle-form>
                <p class="text-sm font-semibold text-teal-700" id="transfer-product-desc"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="trans_product_id">Producto Catálogo (Almacén) <span class="text-red-500">*</span></x-label>
                <select name="product_id" id="trans_product_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border text-sm">
                    <option value="">Seleccione un producto de almacén...</option>
                    @foreach($db_products as $prod)
                        <option value="{{ $prod->product_id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="trans_unit_type">Tipo de empaque / unidad <span class="text-red-500">*</span></x-label>
                <x-input-1 type="text" list="unidades_sugeridas" name="unit_type" id="trans_unit_type" placeholder="Ej. Saco, Garrafa, Cubeta" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="trans_cantidad_salida">Cantidad a Enviar <span class="text-red-500">*</span></x-label>
                <x-input-1 type="number" step="0.01" min="0.01" name="cantidad_salida" id="trans_cantidad_salida" oninput="updateTransferTotalPreview()" required></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="trans_peso_por_unidad">Contenido por unidad (Kg / L) <span class="text-red-500">*</span></x-label>
                <x-input-1 type="number" step="0.01" min="0.01" name="peso_por_unidad" id="trans_peso_por_unidad" value="25" oninput="updateTransferTotalPreview()" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div class="w-full px-2">
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-2.5 text-center">
                <span class="text-xs text-teal-800 font-semibold">Total a recibir en Almacén: </span>
                <strong id="trans_total_preview" class="text-sm text-teal-900 font-bold">0.00 Kg / L</strong>
            </div>
        </div>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeTransferModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Confirmar Envío a Almacén</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function updateTransferTotalPreview() {
        const qty = parseFloat(document.getElementById('trans_cantidad_salida').value) || 0;
        const wpu = parseFloat(document.getElementById('trans_peso_por_unidad').value) || 0;
        document.getElementById('trans_total_preview').innerText = `${(qty * wpu).toFixed(2)} Kg / L`;
    }

    function openTransferModal(inv) {
        document.getElementById('transfer-form').action = `/production/records/inventories/${inv.production_inventory_id}/transfer-to-warehouse`;

        const areaLabel = (inv.area || 'fertil').toUpperCase();
        document.getElementById('transfer-product-desc').innerText = `[${areaLabel}] ${inv.producto_descripcion} — Stock disponible: ${inv.cantidad} ${inv.unidad || ''}`;

        document.getElementById('transfer-form').reset();
        if (inv.unidad) {
            document.getElementById('trans_unit_type').value = inv.unidad;
        }
        document.getElementById('trans_peso_por_unidad').value = 25;
        updateTransferTotalPreview();

        // Intentar preseleccionar el producto de almacén si coincide por nombre
        const selectEl = document.getElementById('trans_product_id');
        const descLower = (inv.producto_descripcion || '').toLowerCase();
        for (let i = 0; i < selectEl.options.length; i++) {
            const optText = selectEl.options[i].text.toLowerCase();
            if (descLower && optText && (optText.includes(descLower) || descLower.includes(optText))) {
                selectEl.selectedIndex = i;
                break;
            }
        }

        document.getElementById('transfer-modal').classList.remove('hidden');
    }

    function closeTransferModal() {
        document.getElementById('transfer-modal').classList.add('hidden');
    }

    $(function() {
        const productUnits = {
            @foreach($db_products as $prod)
            "{{ $prod->product_id }}": "{{ $prod->unit ?? '' }}",
            @endforeach
        };

        $('#trans_product_id').on('change', function() {
            const selectedId = $(this).val();
            const unit = productUnits[selectedId];
            if (unit && !$('#trans_unit_type').val()) {
                $('#trans_unit_type').val(unit);
            }
        });

        $('#transfer-form').submit(function(event) {
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
                        title: '¡Enviado a Almacén!',
                        text: response.message || 'Producto enviado a almacén correctamente.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'No se pudo enviar el producto al almacén.'
                    });
                    saveBtn.prop('disabled', false);
                }
            });
        });
    });
</script>

