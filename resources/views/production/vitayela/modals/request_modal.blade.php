<x-modal id="request-modal" maxWidth="2xl">
    <form class="flex flex-col items-center w-full gap-2" id="requestForm" action="{{ route('production.vitayela.storeMaterialRequest') }}" method="POST">
        @csrf

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="request-modal-title">Solicitar Materiales a Almacén</x-tittle-form>
                <p class="text-sm text-gray-500">Crear una nueva solicitud de productos para el área de Vitayela</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="req_applicant">Solicitante</x-label>
                <x-input-1 type="text" name="applicant_name" id="req_applicant" value="{{ auth()->user()->name ?? '' }}" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div class="w-full mt-2 border-t pt-4">
            <div class="flex justify-between items-center mb-4 px-2">
                <h3 class="text-sm font-bold text-gray-700">Productos Necesarios</h3>
                <button type="button" onclick="addRequestProduct()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-bold shadow transition-colors">+ Añadir Producto</button>
            </div>
            
            <div id="requestProductsList" class="w-full flex flex-col gap-3">
                <div class="product-row relative bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label>Producto de la BD</x-label>
                            <x-input-1 list="db_products_list" name="products[0][name]" placeholder="Seleccione o escriba..." required autocomplete="off"></x-input-1>
                        </x-wrapper-form-2>
                        <x-wrapper-form-2>
                            <x-label>Cantidad</x-label>
                            <x-input-1 type="number" step="0.01" min="0.01" name="products[0][quantity]" placeholder="Ej. 10" required></x-input-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>
                    <button type="button" onclick="removeRequestProduct(this)" class="remove-btn absolute -top-3 -right-3 text-white bg-red-500 hover:bg-red-600 font-bold rounded-full w-7 h-7 flex items-center justify-center shadow-md transition-colors" title="Eliminar" disabled>&times;</button>
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
                <textarea name="comments" id="req_comments" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Añade algún comentario extra si lo necesitas..."></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 class="close-modal" type="button" onclick="closeRequestModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="blue" id="btnSubmitRequest">Enviar Solicitud</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<template id="productRowTemplate">
    <div class="product-row relative bg-gray-50 p-4 rounded-lg border border-gray-200">
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Producto de la BD</x-label>
                <x-input-1 list="db_products_list" name="products[__INDEX__][name]" placeholder="Seleccione o escriba..." required autocomplete="off"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label>Cantidad</x-label>
                <x-input-1 type="number" step="0.01" min="0.01" name="products[__INDEX__][quantity]" placeholder="Ej. 10" required></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <button type="button" onclick="removeRequestProduct(this)" class="remove-btn absolute -top-3 -right-3 text-white bg-red-500 hover:bg-red-600 font-bold rounded-full w-7 h-7 flex items-center justify-center shadow-md transition-colors" title="Eliminar">&times;</button>
    </div>
</template>

<script>
    let requestProductIndex = 1;

    function openRequestModal() {
        document.getElementById('requestForm').reset();
        document.getElementById('request-modal').classList.remove('hidden');
        
        // Reset rows
        let tBody = document.getElementById('requestProductsList');
        let rows = tBody.querySelectorAll('.product-row');
        for (let i = 1; i < rows.length; i++) {
            rows[i].remove();
        }
        updateRemoveButtons();
    }

    function closeRequestModal() {
        document.getElementById('request-modal').classList.add('hidden');
    }

    function addRequestProduct() {
        let tBody = document.getElementById('requestProductsList');
        let template = document.getElementById('productRowTemplate').innerHTML;
        
        template = template.replace(/__INDEX__/g, requestProductIndex);
        
        let dummy = document.createElement('div');
        dummy.innerHTML = template;
        
        tBody.appendChild(dummy.firstElementChild);
        requestProductIndex++;
        updateRemoveButtons();
    }

    function removeRequestProduct(btn) {
        let row = btn.closest('.product-row');
        row.remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        let rows = document.querySelectorAll('#requestProductsList .product-row');
        rows.forEach((row, index) => {
            let btn = row.querySelector('.remove-btn');
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
        let form = this;
        let submitBtn = document.getElementById('btnSubmitRequest');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Enviando...';

        let formData = new FormData(form);
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
                    confirmButtonText: 'OK'
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
