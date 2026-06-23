{{--
Finance
Editar Pago 
Fecha de creación: 18-12-2025
Creado por: Stefany
Actualizado por: Stefany
Fecha de actualización: 30-03-2026
--}}

<x-modal id="edit-finance">
  <form class="flex flex-col items-center w-full gap-2"
        id="edit-finance-form"
        data-update-action="{{ route('finance.update', ['id' => '__ID__']) }}"
        data-show-action="{{ route('finance.show', ['id' => '__ID__']) }}">
    @csrf
    @method('PATCH')

    <input type="hidden" name="id" id="edit-finance-id">

    <x-wrapper-form-1>
      <x-tittle-form>Edit Payment</x-tittle-form>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
      <x-wrapper-form-2>
        <x-label for="edit-empresa">Empresa</x-label>
        <x-input-1 required name="empresa" id="edit-empresa" maxlength="150"></x-input-1>
      </x-wrapper-form-2>

      <x-wrapper-form-2>
        <x-label for="edit-cantidad">Cantidad</x-label>
        <x-input-1 required type="number" step="0.01" min="0" name="cantidad" id="edit-cantidad"></x-input-1>
      </x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
      <x-wrapper-form-2>
        <x-label for="edit-banco">Banco</x-label>
        <x-select-1 name="banco" id="edit-banco">
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
        <x-label for="edit-factura">Factura</x-label>
        <select name="factura"
                id="edit-factura"
                required
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
        <x-label for="edit-motivo">Motivo</x-label>
        <textarea required name="motivo" id="edit-motivo" rows="3"
          class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
      </x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
      <x-wrapper-form-2>
        <x-label for="edit-fecha-factura">Fecha factura</x-label>
        <x-input-1 type="date" name="fecha_factura" id="edit-fecha-factura"></x-input-1>
      </x-wrapper-form-2>

      <x-wrapper-form-2>
        <x-label for="edit-fecha-pago">Fecha pago</x-label>
        <x-input-1 type="date" name="fecha_pago" id="edit-fecha-pago"></x-input-1>
      </x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
      <x-wrapper-form-2>
        <x-label for="edit-semana">Semana</x-label>
        <x-input-1 required type="number" min="1" max="53" name="semana" id="edit-semana"></x-input-1>
      </x-wrapper-form-2>

      <x-wrapper-form-2>
        <x-label for="edit-anio">Año</x-label>
        <x-input-1 required type="number" min="2000" max="2100" name="anio" id="edit-anio"></x-input-1>
      </x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1>
      <x-wrapper-form-2>
        <x-label for="edit-estatus">Estatus</x-label>
        <x-select-1 required name="estatus" id="edit-estatus">
          <option value="PENDIENTE">Pendiente</option>
          <option value="PAGADO">Pagado</option>
          <option value="CANCELADO">Cancelado</option>
        </x-select-1>
      </x-wrapper-form-2>

      <x-wrapper-form-2>
        <x-label for="edit-comentarios">Tipo pago</x-label>
        <x-select-1 name="comentarios" id="edit-comentarios" required>
          <option value="">— Selecciona —</option>
          <option value="Efectivo">Efectivo</option>
          <option value="Transferencia">Transferencia</option>
          <option value="Tarjeta">Tarjeta</option>
        </x-select-1>
      </x-wrapper-form-2>
    </x-wrapper-form-1>

    <x-wrapper-form-1 id="edit-wrapper-extra" style="display: none;">
      <x-wrapper-form-2 class="w-full">
        <x-label for="edit-terminacion">Terminación (4 dígitos)</x-label>
        <x-input-1 name="terminacion" id="edit-terminacion" maxlength="4" placeholder="Ej. 5566"></x-input-1>
      </x-wrapper-form-2>
    </x-wrapper-form-1>

    <input type="hidden" name="efectivo" id="edit-efectivo-hidden">

    <x-wrapper-form-1>
      <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
      <x-button-1 id="update-finance" type="submit" colorBtn="green">Save</x-button-1>
    </x-wrapper-form-1>

  </form>
</x-modal>

@push('js')
<script>
$(function () {

  const fatherEdit = $('#edit-finance');

  function toggleEditFields(tipo, terminacionVal = '', efectivoVal = '') {
      const wrapper = $('#edit-wrapper-extra');
      const inputTerm = $('#edit-terminacion');
      const inputEfectivo = $('#edit-efectivo-hidden');

      wrapper.hide();
      inputTerm.val('').prop('required', false);
      inputEfectivo.val('');

      if (tipo === 'Tarjeta') {
          wrapper.show();
          inputTerm.val(terminacionVal).prop('required', true);
      } else if (tipo === 'Efectivo') {
          inputEfectivo.val(efectivoVal || 'Si');
      }
  }

  fatherEdit.on('change', '#edit-comentarios', function() {
      toggleEditFields($(this).val());
  });

  $(document).on('click', '.btn-edit-finance', function () {
    const id = $(this).data('id');
    if (!id) return;

    const form = fatherEdit.find('#edit-finance-form');
    const showUrl = String(form.data('show-action')).replace('__ID__', id);
    const updateUrl = String(form.data('update-action')).replace('__ID__', id);
    
    form.data('real-update', updateUrl);
    fatherEdit.find('#edit-finance-id').val(id);

    $.ajax({
      type: 'GET',
      url: showUrl,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function (res) {
        if (!res || res.success === false) return;
        const d = res.data || {};

        fatherEdit.find('#edit-empresa').val(d.empresa ?? '');
        fatherEdit.find('#edit-cantidad').val(d.cantidad ?? '');
        fatherEdit.find('#edit-banco').val(d.banco ?? '');
        fatherEdit.find('#edit-factura').val(d.factura ?? '');
        fatherEdit.find('#edit-motivo').val(d.motivo ?? '');
        fatherEdit.find('#edit-fecha-factura').val(d.fecha_factura ?? '');
        fatherEdit.find('#edit-fecha-pago').val(d.fecha_pago ?? '');
        fatherEdit.find('#edit-semana').val(d.semana ?? '');
        fatherEdit.find('#edit-anio').val(d.anio ?? '');
        fatherEdit.find('#edit-estatus').val(d.estatus ?? 'PENDIENTE');
        fatherEdit.find('#edit-comentarios').val(d.comentarios ?? '');

        toggleEditFields(d.comentarios, d.terminacion, d.efectivo);
      }
    });
  });

  fatherEdit.on('submit', '#edit-finance-form', function (event) {
    event.preventDefault();
    const form = $(this);
    const updateUrl = form.data('real-update');
    const data = new FormData(this);

    fatherEdit.find('#update-finance').prop('disabled', true);

    $.ajax({
      type: 'POST',
      url: updateUrl,
      data: data,
      processData: false,
      contentType: false,
      success: function (res) {
        Swal.fire({ icon: 'success', title: 'Success', text: 'Updated successfully.' }).then(() => {
          if ($.fn.DataTable.isDataTable('#finance-table')) {
            $('#finance-table').DataTable().ajax.reload(null, false);
          } else {
            location.reload();
          }
          fatherEdit.find('.close-modal').trigger('click');
        });
      },
      error: function (xhr) {
        let message = xhr.responseJSON?.message ?? 'Error inesperado';
        if (xhr.status === 422) message = Object.values(xhr.responseJSON.errors).flat().join('\n');
        Swal.fire({ icon: 'error', title: 'Error', text: message });
      },
      complete: function() {
        fatherEdit.find('#update-finance').prop('disabled', false);
      }
    });
  });
});
</script>
@endpush