<x-modal id="edit-yeast-modal">
    <form id="edit-yeast-form" class="flex flex-col items-center w-full gap-2">
        <input type="hidden" id="edit_yeast_id" name="yeast_id">

        <x-wrapper-form-1>
            <x-tittle-form>Completar Datos de Producción</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_internal_weight">Peso Interno</x-label>
                <x-input-1 type="number" step="0.01" name="internal_weight" id="edit_internal_weight"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_external_weight">Peso Externo</x-label>
                <x-input-1 type="number" step="0.01" name="external_weight" id="edit_external_weight"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_bags_natural">Sacos Natural</x-label>
                <x-input-1 type="number" name="bags_natural" id="edit_bags_natural"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_bags_mix">Sacos Mix</x-label>
                <x-input-1 type="number" name="bags_mix" id="edit_bags_mix"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_bags_white">Sacos Blanca</x-label>
                <x-input-1 type="number" name="bags_white" id="edit_bags_white"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_finished_product_kg">Producto Terminado (kg)</x-label>
                <x-input-1 type="number" step="0.01" name="finished_product_kg" id="edit_finished_product_kg"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <div class="flex justify-center gap-4 mt-4 w-full">
            <x-button-1 type="button" onclick="saveYeast()" colorBtn="green">Guardar</x-button-1>
            <x-button type="button" class="close-modal">Cancelar</x-button>
        </div>
    </form>
</x-modal>

<script>
    function saveYeast() {
        const id = document.getElementById('edit_yeast_id').value;
        const formData = {
            internal_weight: document.getElementById('edit_internal_weight').value,
            external_weight: document.getElementById('edit_external_weight').value,
            bags_natural: document.getElementById('edit_bags_natural').value,
            bags_mix: document.getElementById('edit_bags_mix').value,
            bags_white: document.getElementById('edit_bags_white').value,
            finished_product_kg: document.getElementById('edit_finished_product_kg').value,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        $.ajax({
            url: `/production/yeast/${id}`,
            type: 'POST', // with _method=PUT
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: response.message,
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
                    text: 'Ocurrió un error al guardar los datos.'
                });
            }
        });
    }
</script>
