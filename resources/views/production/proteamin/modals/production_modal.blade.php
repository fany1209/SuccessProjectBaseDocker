<x-modal id="production-modal" maxWidth="lg">
    <form class="flex flex-col items-center w-full gap-2" id="production-form" action="{{ route('production.proteamin.storeProduction') }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="prod-method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="prod-modal-title">Nuevo Registro de Producción</x-tittle-form>
                <p class="text-sm text-gray-500">Registro de producción de Proteamin</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="prod_fecha_preparacion">Fecha de Preparación</x-label>
                <x-input-1 type="date" name="fecha_preparacion" id="prod_fecha_preparacion"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="prod_kg_preparados">Kg Preparados</x-label>
                <x-input-1 type="number" step="0.01" name="kg_preparados" id="prod_kg_preparados"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="prod_fecha_ensacado">Fecha de Ensacado</x-label>
                <x-input-1 type="date" name="fecha_ensacado" id="prod_fecha_ensacado"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="prod_kg_ensacados">Kg Ensacados</x-label>
                <x-input-1 type="number" step="0.01" name="kg_ensacados" id="prod_kg_ensacados"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="prod_num_sacos"># de Sacos</x-label>
                <x-input-1 type="number" name="num_sacos" id="prod_num_sacos"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="prod_descripcion">Descripción</x-label>
                <x-input-1 type="text" name="descripcion" id="prod_descripcion"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="button" onclick="closeProductionModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Guardar</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function openCreateProduction() {
        document.getElementById('production-form').action = "{{ route('production.proteamin.storeProduction') }}";
        document.getElementById('prod-method').value = "POST";
        document.getElementById('prod-modal-title').innerText = "Nuevo Registro de Producción";
        
        document.getElementById('production-form').reset();
        
        document.getElementById('production-modal').classList.remove('hidden');
    }

    function editProduction(p) {
        document.getElementById('production-form').action = `/production/proteamin/productions/${p.proteamin_production_id}`;
        document.getElementById('prod-method').value = "PUT";
        document.getElementById('prod-modal-title').innerText = "Editar Registro de Producción";
        
        document.getElementById('prod_fecha_preparacion').value = p.fecha_preparacion;
        document.getElementById('prod_kg_preparados').value = p.kg_preparados;
        document.getElementById('prod_fecha_ensacado').value = p.fecha_ensacado;
        document.getElementById('prod_kg_ensacados').value = p.kg_ensacados;
        document.getElementById('prod_num_sacos').value = p.num_sacos;
        document.getElementById('prod_descripcion').value = p.descripcion;
        
        document.getElementById('production-modal').classList.remove('hidden');
    }

    function closeProductionModal() {
        document.getElementById('production-modal').classList.add('hidden');
    }

    $(function() {
        $('#production-form').submit(function(event) {
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
