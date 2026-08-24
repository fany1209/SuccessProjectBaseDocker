<x-modal id="20">
  <form id="lot-request-form" class="space-y-4">
    @csrf

    @php
        // Fetch products for datalist autocomplete
        $products = \Illuminate\Support\Facades\DB::table('products')->select('name')->distinct()->pluck('name');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold mb-1">Departamento solicitante</label>
        <input type="text" name="department" class="w-full border rounded px-3 py-2" placeholder="Ej. Laboratory, Warehouse, Quality" required>
      </div>    

      <div>
        <label class="block text-sm font-semibold mb-1">Producto</label>
        <input type="text" name="product" list="products-list" class="w-full border rounded px-3 py-2" placeholder="Escribe o selecciona...">
        <datalist id="products-list">
            @foreach($products as $prod)
                <option value="{{ $prod }}">
            @endforeach
        </datalist>
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Cantidad</label>
        <input type="text" name="quantity" class="w-full border rounded px-3 py-2" placeholder="Ej. 10 kg, 5 litros...">
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Proveedor</label>
        <input type="text" name="provider" class="w-full border rounded px-3 py-2" placeholder="Escribe el proveedor...">
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Recolector</label>
        <input type="text" name="collector" class="w-full border rounded px-3 py-2" placeholder="Nombre de quien recolecta...">
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Sector</label>
        <input type="text" name="sector" class="w-full border rounded px-3 py-2" placeholder="Sector...">
      </div>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1">Comentarios (opcional)</label>
      <textarea name="comments" rows="2" class="w-full border rounded px-3 py-2" placeholder="Notas adicionales..."></textarea>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <x-button type="button" class="px-4 py-2 rounded-lg border close-modal">Cancelar</x-button>
      <x-button type="submit" id="submit-lot" class="px-4 py-2 rounded-lg bg-[#16a34a] text-white font-semibold hover:bg-[#15803d]">Guardar petición</x-button>
    </div>
  </form>
</x-modal>
<script>
$(function () {
  $(document).on('click', '.open-modal', function () {
    const target = $(this).data('target');  
    const $modal = $('#' + target);
    $modal.removeClass('hidden').addClass('flex'); 
  });

  $(document).on('click', '.close-modal, [data-modal-backdrop]', function (e) {
    if ($(e.target).is('[data-modal-backdrop]') || $(this).hasClass('close-modal')) {
      const $root = $(this).closest('.modal-root, [id="lote"], .fixed.inset-0');
      $root.addClass('hidden').removeClass('flex');
    }
  });

  $(document).on('submit', '#lot-request-form', function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn  = $('#submit-lot').prop('disabled', true).text('Guardando…');

    $.ajax({
      url: "{{ route('lot.request.store') }}",
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      data: $form.serialize(),
      success: function (res) {
        if (res?.success) {
          $form[0].reset();
          $('#lote').addClass('hidden').removeClass('flex');

          if (window.Swal) Swal.fire('Listo', 'Petición registrada y marcada como "pendiente".', 'success');
          else alert('Petición registrada y marcada como "pendiente".');
        } else {
          const msg = res?.message || 'No se pudo guardar la petición.';
          if (window.Swal) Swal.fire('Error', msg, 'error'); else alert(msg);
        }
      },
      error: function (xhr) {
        const msg = xhr?.responseJSON?.message || 'Error al guardar la petición.';
        if (window.Swal) Swal.fire('Error', msg, 'error'); else alert(msg);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Guardar petición');
      }
    });
  });
});
</script>

