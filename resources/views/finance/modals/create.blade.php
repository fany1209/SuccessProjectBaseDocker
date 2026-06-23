{{--
Finance
Agregar Pago
Fecha de creación: 18-12-2025
Creado por: Stefany
Actualizado por: Stefany
Fecha de actualización: 28-03-2026
--}}

<x-modal id="add-finance">
    <form class="flex flex-col items-center w-full gap-2" id="add-payment-form">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Add Payment</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="empresa">Empresa</x-label>
                <x-input-1 required name="empresa" maxlength="150"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="cantidad">Cantidad</x-label>
                <x-input-1 required type="number" step="0.01" min="0" name="cantidad"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="banco">Banco</x-label>
                <x-select-1 name="banco">
                    <option value="">Selecciona un banco</option>
                    <option value="BBVA">BBVA</option>
                    <option value="Banamex">Banamex</option>
                    <option value="Banorte">Banorte</option>
                    <option value="Santander">Santander</option>
                    <option value="HSBC">HSBC</option>
                    <option value="Scotiabank">Scotiabank</option>
                    <option value="Inbursa">Inbursa</option>
                    <option value="Afirme">Afirme</option>
                    <option value="BanBajío">BanBajío</option>
                    <option value="BanRegio">BanRegio</option>
                    <option value="Hey Banco">Hey Banco</option>
                    <option value="Nu">Nu</option>
                    <option value="Otro">Otro</option>
                </x-select-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="factura">Factura</x-label>
                <select name="factura" id="edit-factura" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">— Selecciona una factura —</option>
                    @foreach ($facturas as $factura)
                        <option value="{{ $factura->factura_id }}">
                            {{ $factura->folio_factura }} — {{ $factura->empresa }} — ${{ number_format($factura->total, 2) }}
                        </option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2 class="w-full">
                <x-label for="motivo">Motivo</x-label>
                <textarea required name="motivo" rows="3"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="fecha_factura">Fecha factura</x-label>
                <x-input-1 type="date" name="fecha_factura"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="fecha_pago">Fecha pago</x-label>
                <x-input-1 type="date" name="fecha_pago"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="semana">Semana</x-label>
                <x-input-1 required type="number" min="1" max="53" name="semana"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="anio">Año</x-label>
                <x-input-1 required type="number" min="2000" max="2100" name="anio"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="estatus">Estatus</x-label>
                <x-select-1 required name="estatus">
                    <option value="PENDIENTE" selected>Pendiente</option>
                    <option value="PAGADO">Pagado</option>
                    <option value="CANCELADO">Cancelado</option>
                </x-select-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="comentarios">Tipo pago</x-label>
                <x-select-1 name="comentarios" id="tipo_pago_select" required>
                    <option value="">— Selecciona —</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Tarjeta">Tarjeta</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 id="extra_fields_wrapper" style="display: none;">
            <x-wrapper-form-2 class="w-full">
                <x-label for="terminacion">Terminación (4 dígitos)</x-label>
                <x-input-1 name="terminacion" id="terminacion" maxlength="4" placeholder="Ej. 1234"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <input type="hidden" name="efectivo" id="efectivo_hidden" value="">

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="save-payment" type="submit" colorBtn="green">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

@push('js')
<script>
$(function(){

    const father = $('#add-finance');

    father.on('change', '#tipo_pago_select', function() {
        const seleccion = $(this).val();
        const wrapperExtra = $('#extra_fields_wrapper');
        const inputEfectivo = $('#efectivo_hidden');
        const inputTerminacion = $('#terminacion');

        wrapperExtra.hide();
        inputTerminacion.val('').prop('required', false);
        inputEfectivo.val('');

        if (seleccion === 'Tarjeta') {
            wrapperExtra.show();
            inputTerminacion.prop('required', true);
        } 
        
        if (seleccion === 'Efectivo') {
            inputEfectivo.val('Si');
        }
    });

    // Función para guardar el pago
    function addPayment(){
        father.off('submit', '#add-payment-form');
        father.on('submit', '#add-payment-form', function(event) {
            event.preventDefault();

            const form = $(this)[0];
            const data = new FormData(form);

            father.find('#save-payment').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: "{{ route('finance.store') }}",
                data: data,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The payment was added successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if ($.fn.DataTable.isDataTable('#finance-table')) {
                                $('#finance-table').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                            father.find('.close-modal').trigger('click');
                            form.reset();
                            $('#extra_fields_wrapper').hide();
                        }
                    });
                    father.find('#save-payment').prop('disabled', false);
                },
                error: function(xhr){
                    let message = 'Ocurrió un error inesperado.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                        if (xhr.responseJSON.error) message = xhr.responseJSON.error;
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                    father.find('#save-payment').prop('disabled', false);
                }
            });
        });
    }

    addPayment();
});
</script>
@endpush