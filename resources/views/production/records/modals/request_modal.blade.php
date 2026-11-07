<x-modal id="request-modal" maxWidth="2xl">
    <form class="flex flex-col items-center w-full gap-2 p-2" id="requestForm" action="{{ route('production.records.storeMaterialRequest') }}" method="POST">
        @csrf

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="request-modal-title">Solicitar Materiales a Almacén</x-tittle-form>
                <p class="text-sm text-gray-500">Crear una solicitud de insumos o materia prima para Producción</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <!-- Selector Visual de Área Solicitante -->
        <div class="w-full px-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Línea Solicitante <span class="text-red-500">*</span></label>
            <input type="hidden" name="area" id="req_area_input" value="fertil" required>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" id="btn-req-area-fertil" onclick="selectReqArea('fertil')" class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-green-600 bg-green-50 text-green-800">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-600"></span>
                    Área Fertil
                </button>
                <button type="button" id="btn-req-area-vitayela" onclick="selectReqArea('vitayela')" class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    Área Vitayela
                </button>
            </div>
        </div>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="req_applicant">Solicitante <span class="text-red-500">*</span></x-label>
                <x-input-1 type="text" name="applicant_name" id="req_applicant" value="{{ auth()->user()->name ?? '' }}" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div class="w-full mt-2 border-t pt-4">
            <div class="flex justify-between items-center mb-3 px-2">
                <h3 class="text-sm font-bold text-gray-700">Productos Necesarios</h3>
                <button type="button" onclick="addRequestProduct()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-bold shadow cursor-pointer transition-colors">
                    + Añadir Otro Producto
                </button>
            </div>

            <div id="requestProductsList" class="w-full flex flex-col gap-3 max-h-64 overflow-y-auto px-1">
                <div class="product-row relative bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label>Producto</x-label>
                            <x-input-1 list="db_products_list" name="products[0][name]" placeholder="Seleccione o escriba..." required autocomplete="off"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label>Cantidad Solicitada</x-label>
                            <x-input-1 type="number" step="0.01" min="0.01" name="products[0][quantity]" placeholder="Ej. 10" required></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <button type="button" onclick="removeRequestProduct(this)" class="remove-btn absolute -top-2 -right-2 text-white bg-red-500 hover:bg-red-600 font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-md transition-colors hidden" title="Eliminar" disabled>&times;</button>
                </div>
            </div>

            <datalist id="db_products_list">
                @if(isset($db_products))
                    @foreach($db_products as $p)
                        <option value="{{ $p->name }}"></option>
                    @endforeach
                @endif
            </datalist>
        </div>

        <x-wrapper-form-1 class="mt-2">
            <x-wrapper-form-2>
                <x-label for="req_comments">Comentarios Adicionales (Opcional)</x-label>
                <textarea name="comments" id="req_comments" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border text-sm" placeholder="Añade algún comentario extra si lo necesitas..."></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-3">
            <x-button-1 class="close-modal" type="button" onclick="closeRequestModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="blue" id="btnSubmitRequest">Enviar Solicitud</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<template id="productRowTemplate">
    <div class="product-row relative bg-gray-50 p-3 rounded-lg border border-gray-200">
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Producto</x-label>
                <x-input-1 list="db_products_list" name="products[__INDEX__][name]" placeholder="Seleccione o escriba..." required autocomplete="off"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label>Cantidad Solicitada</x-label>
                <x-input-1 type="number" step="0.01" min="0.01" name="products[__INDEX__][quantity]" placeholder="Ej. 10" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <button type="button" onclick="removeRequestProduct(this)" class="remove-btn absolute -top-2 -right-2 text-white bg-red-500 hover:bg-red-600 font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-md transition-colors" title="Eliminar">&times;</button>
    </div>
</template>

<script>
    let requestProductIndex = 1;
    const lowStockData = @json($lowStockItems->values());

    function selectReqArea(area) {
        document.getElementById('req_area_input').value = area;
        const btnFertil = document.getElementById('btn-req-area-fertil');
        const btnVitayela = document.getElementById('btn-req-area-vitayela');

        if (area === 'fertil') {
            btnFertil.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-green-600 bg-green-50 text-green-800 shadow-xs';
            btnVitayela.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        } else {
            btnVitayela.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-indigo-600 bg-indigo-50 text-indigo-800 shadow-xs';
            btnFertil.className = 'flex items-center justify-center gap-2 py-2 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        }
    }

    function openRequestModal() {
        document.getElementById('requestForm').reset();
        const defaultArea = (window.currentAreaFilter === 'vitayela') ? 'vitayela' : 'fertil';
        selectReqArea(defaultArea);

        const tBody = document.getElementById('requestProductsList');
        const rows = tBody.querySelectorAll('.product-row');
        for (let i = 1; i < rows.length; i++) {
            rows[i].remove();
        }
        updateRemoveButtons();
        document.getElementById('request-modal').classList.remove('hidden');
    }

    function openRequestModalWithLowStock() {
        openRequestModal();
        if (!lowStockData || lowStockData.length === 0) return;

        const targetArea = (window.currentAreaFilter === 'vitayela' || window.currentAreaFilter === 'fertil')
            ? window.currentAreaFilter
            : (lowStockData[0].area || 'fertil');

        selectReqArea(targetArea);

        const filteredLow = lowStockData.filter(item => item.area === targetArea);
        const listToFill = filteredLow.length > 0 ? filteredLow : lowStockData;

        const tBody = document.getElementById('requestProductsList');
        tBody.innerHTML = '';

        listToFill.forEach((item) => {
            addRequestProduct(item.producto_descripcion, Math.max(1, (parseFloat(item.stock_min) - parseFloat(item.cantidad)) || 10));
        });
    }

    function closeRequestModal() {
        document.getElementById('request-modal').classList.add('hidden');
    }

    function addRequestProduct(prefillName = '', prefillQty = '') {
        const tBody = document.getElementById('requestProductsList');
        let template = document.getElementById('productRowTemplate').innerHTML;

        template = template.replace(/__INDEX__/g, requestProductIndex);

        const dummy = document.createElement('div');
        dummy.innerHTML = template;
        const rowEl = dummy.firstElementChild;

        if (prefillName) {
            const nameInput = rowEl.querySelector(`input[name="products[${requestProductIndex}][name]"]`);
            if (nameInput) nameInput.value = prefillName;
        }
        if (prefillQty !== '') {
            const qtyInput = rowEl.querySelector(`input[name="products[${requestProductIndex}][quantity]"]`);
            if (qtyInput) qtyInput.value = prefillQty;
        }

        tBody.appendChild(rowEl);
        requestProductIndex++;
        updateRemoveButtons();
    }

    function removeRequestProduct(btn) {
        const row = btn.closest('.product-row');
        row.remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('#requestProductsList .product-row');
        rows.forEach((row) => {
            const btn = row.querySelector('.remove-btn');
            if (rows.length === 1) {
                btn.disabled = true;
                btn.classList.add('hidden');
            } else {
                btn.disabled = false;
                btn.classList.remove('hidden');
            }
        });
    }

    document.getElementById('requestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('btnSubmitRequest');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Enviando...';

        const formData = new FormData(form);
        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Solicitud enviada!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    closeRequestModal();
                    form.reset();
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Enviar Solicitud';
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Ocurrió un error al enviar la solicitud.'
                });
                submitBtn.disabled = false;
                submitBtn.innerText = 'Enviar Solicitud';
            }
        });
    });
</script>

