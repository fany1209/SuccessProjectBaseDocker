<x-modal id="transfer-modal" maxWidth="2xl">
    <form class="flex flex-col items-center w-full gap-2" id="transfer-form" action="" method="POST">
        @csrf
        <input type="hidden" name="_method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Enviar Producto a Almacén</x-tittle-form>
                <p class="text-sm text-gray-500" id="transfer-product-desc"></p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="trans_product_id">Producto Genérico (Almacén) <span class="text-red-500">*</span></x-label>
                <select name="product_id" id="trans_product_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                    <option value="">Seleccione un producto...</option>
                    @foreach($db_products as $prod)
                        <option value="{{ $prod->product_id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="trans_unit_type">Tipo de empaque / unidad <span class="text-red-500">*</span></x-label>
                <x-input-1 type="text" name="unit_type" id="trans_unit_type" placeholder="ej. Saco, Garrafa, Cubeta" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="trans_cantidad_salida">Cantidad <span class="text-red-500">*</span></x-label>
                <x-input-1 type="number" step="0.01" name="cantidad_salida" id="trans_cantidad_salida" required></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="trans_peso_por_unidad">Contenido por unidad (ej. kg, L) <span class="text-red-500">*</span></x-label>
                <x-input-1 type="number" step="0.01" name="peso_por_unidad" id="trans_peso_por_unidad" value="1" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeTransferModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Confirmar Envío</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function openTransferModal(inv, type) {
        let routeType = type === 'vitayela' ? 'vitayela' : 'fertil';
        let id = type === 'vitayela' ? inv.vitayela_inventory_id : inv.fertil_inventory_id;
        
        document.getElementById('transfer-form').action = `/production/${routeType}/inventories/${id}/transfer-to-warehouse`;
        
        document.getElementById('transfer-product-desc').innerText = `Producto local: ${inv.producto_descripcion}`;
        
        document.getElementById('transfer-form').reset();
        
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
            if (unit) {
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
                success: function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'Producto enviado a almacén correctamente.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr){
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
