<x-modal id="edit-warehouse">
  <form id="edit-warehouse-form" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <input type="hidden" id="inspection-id" name="inspection_id">

    <div class="space-y-6">
      <h3 class="text-base font-semibold mb-3">Información de la inspección</h3>

      <div class="mt-4 grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Responsable del área</label>
          <input type="text" name="responsable" id="responsable"
                 class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
        </div>
      </div>
    </div>

    <div class="mt-6">
      <div class="flex items-center justify-between mb-2">
        <h4 class="text-sm font-semibold">Observaciones</h4>
      </div>

      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left">
              <th class="px-3 py-2 w-[28%]">Observación</th>
              <th class="px-3 py-2 w-[18%]">Ubicación</th> {{-- ✅ SOLO VER --}}
              <th class="px-3 py-2 w-[16%]">Fecha de corrección</th>
              <th class="px-3 py-2 w-[30%]">Evidencia de corrección (subir nueva)</th>
            </tr>
          </thead>

          <tbody id="obs-rows-1">
            <tr>
              <td class="px-3 py-2 align-top">
                <input type="text" name="obs[0][name]" class="w-full rounded-md border px-3 py-2" readonly>
              </td>

              <td class="px-3 py-2 align-top">
                <input type="text" class="w-full rounded-md border px-3 py-2 bg-gray-50" readonly>
              </td>

              <td class="px-3 py-2 align-top">
                <input type="date" name="obs[0][fecha]" class="w-full rounded-md border px-3 py-2">
              </td>
              <td class="px-3 py-2 align-top">
                <input type="file" name="obs[0][ev_corr_file]" accept="image/*"
                       class="block w-full text-xs preview-input">
                <div class="mt-2 preview-container"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6">
        <h3 class="text-base font-semibold mb-2">Comentarios de almacén</h3>
        <textarea name="comentarios" id="comentarios" rows="5"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
      </div>
    </div>

    <div class="flex justify-end gap-2 mt-6">
      <x-button type="button" class="close-modal bg-gray-700 text-gray-800 hover:bg-gray-700">
        Cancelar
      </x-button>

      <x-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">
        Guardar cambios
      </x-button>
    </div>
  </form>
</x-modal>

@push('js')
<script>
$(document).on('click', '.edit-warehouse-btn', function () {
  const status = parseInt($(this).data('status'));
  const id = $(this).data('id');

  if (status === 1) {
    Swal.fire({
      icon: 'info',
      title: 'Not allowed',
      text: 'This inspection is now complete and cannot be edited.'
    });
    return;
  }

  $('#inspection-id').val(id);

  $.ajax({
    type: 'GET',
    url: "{{ route('quality.getWarehouseInspection') }}",
    data: { id },
    success: function (r) {
      const wh = r?.warehouse ?? r;

      $('#responsable').val(wh?.responsable || '');
      $('#comentarios').val(wh?.comentarios || '');

      const $tbody = $('#obs-rows-1');
      $tbody.empty();

      const toPublicUrl = (path, urlFromApi) => {
        if (urlFromApi) return urlFromApi;
        if (!path) return null;

        path = String(path).trim().replace(/^\/+/, '');

        if (/^https?:\/\//i.test(path)) return path;

        return '/' + path;
      };

      if (wh?.observaciones?.length) {
        wh.observaciones.forEach((obs, i) => {

          const evidenciaUrl = toPublicUrl(obs.evidencia_path, obs.evidencia_url);
          const corrUrl      = toPublicUrl(obs.ev_corr_path, obs.ev_corr_url);

          $tbody.append(`
            <tr>
              <td class="px-3 py-2 align-top">
                <input type="hidden" name="obs[${i}][id]" value="${obs.id}">
                <input type="text" name="obs[${i}][name]" value="${obs.name ?? ''}"
                       class="w-full rounded-md border px-3 py-2" readonly>

                ${evidenciaUrl ? `
                  <div class="mt-2">
                    <div class="text-xs font-semibold text-gray-600 mb-1">Evidencia original</div>
                    <a href="${evidenciaUrl}" target="_blank" class="text-xs text-blue-600 underline">Abrir imagen</a>
                    <img src="${evidenciaUrl}"
                         class="mt-2 w-full max-h-[220px] object-contain rounded border"
                         alt="Evidencia original">
                  </div>
                ` : ''}
              </td>

              <td class="px-3 py-2 align-top">
                <input type="text" value="${obs.ubicacion ?? ''}"
                       class="w-full rounded-md border px-3 py-2 bg-gray-50" readonly>
              </td>

              <td class="px-3 py-2 align-top">
                <input type="date" name="obs[${i}][fecha]" value="${obs.fecha ?? ''}"
                       class="w-full rounded-md border px-3 py-2">
              </td>

              <td class="px-3 py-2 align-top">
                <input type="file" name="obs[${i}][ev_corr_file]" accept="image/*"
                       class="block w-full text-xs preview-input" data-index="${i}">

                <div class="mt-2 preview-container">
                  ${corrUrl ? `
                    <div class="text-xs font-semibold text-gray-600 mb-1">Evidencia corrección (actual)</div>
                    <a href="${corrUrl}" target="_blank" class="text-xs text-blue-600 underline">Abrir imagen</a>
                    <img src="${corrUrl}"
                         class="mt-2 w-full max-h-[220px] object-contain rounded border"
                         title="Evidencia corrección"
                         alt="Evidencia corrección">
                  ` : ''}
                </div>
              </td>
            </tr>
          `);
        });
      } else {
        $tbody.append(`
          <tr>
            <td class="px-3 py-2 align-top">
              <input type="text" name="obs[0][name]" class="w-full rounded-md border px-3 py-2" readonly>
            </td>

            <td class="px-3 py-2 align-top">
              <input type="text" class="w-full rounded-md border px-3 py-2 bg-gray-50" readonly>
            </td>

            <td class="px-3 py-2 align-top">
              <input type="date" name="obs[0][fecha]" class="w-full rounded-md border px-3 py-2">
            </td>

            <td class="px-3 py-2 align-top">
              <input type="file" name="obs[0][ev_corr_file]" accept="image/*"
                     class="block w-full text-xs preview-input" data-index="0">
              <div class="mt-2 preview-container"></div>
            </td>
          </tr>
        `);
      }

      const modal = document.getElementById('edit-warehouse');
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    },
    error: function () {
      Swal.fire({ icon:'error', title:'Error', text:'Could not get warehouse inspection.' });
    }
  });
});

$(document).on('click', '#edit-warehouse .close-modal', function(){
  const modal = document.getElementById('edit-warehouse');
  if(modal){
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
});

$(document).on('change', '.preview-input', function() {
  const file = this.files?.[0];
  const $container = $(this).siblings('.preview-container');

  $container.find('img[data-preview="1"]').remove();

  if (file) {
    const url = URL.createObjectURL(file);
    $container.append(`
      <div class="mt-2">
        <div class="text-xs font-semibold text-gray-600 mb-1">Vista previa (nueva)</div>
        <img src="${url}"
             data-preview="1"
             class="w-full max-h-[260px] object-contain rounded border"
             alt="Vista previa">
      </div>
    `);
  }
});

// Envío del formulario
$('#edit-warehouse-form').submit(function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const id = $('#inspection-id').val();

  $.ajax({
    type: 'POST',
    url: `/quality/warehouse/${id}/updatew`,
    data: formData,
    processData: false,
    contentType: false,
    success: function(res) {
      Swal.fire({ icon:'success', title:'Success', text: res.message || 'Cambios guardados' });

      const modal = document.getElementById('edit-warehouse');
      if(modal){
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }

      $('#inspection-table').DataTable().ajax.reload(null, false);
    },
    error: function() {
      Swal.fire({ icon:'error', title:'Wrong', text:'Could not save.' });
    }
  });
});
</script>
@endpush
