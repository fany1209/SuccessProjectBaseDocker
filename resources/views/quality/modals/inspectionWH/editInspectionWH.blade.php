<x-modal id="edit-inspection">
  <form id="edit-inspection-form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="inspection-id" name="inspection_id">
    <div class="space-y-6">
      <h3 class="text-base font-semibold mb-3">Información de la inspección</h3>
      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Fecha de inspección</label>
          <input type="date" name="fecha_inspeccion" id="fecha-inspeccion" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Inspector</label>
          <input type="text" name="inspector" id="inspector" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Hora</label>
          <input type="time" name="hora_turno" id="hora-turno" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
        </div>
      </div>
      <div class="mt-4 grid md:grid-cols-3 gap-4">
        <div>
          <span class="block text-sm font-medium mb-1">Inspección</span>
          <label class="inline-flex items-center mr-4">
            <input type="radio" name="turno" value="1" class="mr-2"> <span>1</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="turno" value="2" class="mr-2"> <span>2</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="turno" value="3" class="mr-2"> <span>3</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="turno" value="mixto" class="mr-2"> <span>Mixto</span>
          </label>
        </div>
        <div class="md:col-span-2">
          <span class="block text-sm font-medium mb-1">Área</span>
          <label class="inline-flex items-center mr-4">
            <input type="checkbox" name="area[]" value="nave1" class="mr-2"><span>Nave 1</span>
          </label>
          <label class="inline-flex items-center mr-4">
            <input type="checkbox" name="area[]" value="nave2" class="mr-2"><span>Nave 2</span>
          </label>
          <label class="inline-flex items-center">
            <input id="area-otro-check" type="checkbox" name="area[]" value="otro" class="mr-2"><span>Otro</span>
          </label>
          <input id="area-otro-text" type="text" name="area_otro" placeholder="Especifique"class="mt-2 w-full md:w-1/2 rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
        </div>
      </div>
    <div class="mt-6">
      <div class="flex items-center justify-between mb-2">
        <h4 class="text-sm font-semibold">Observaciones</h4>
        <button type="button" id="btn-add-obs-1"
                class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50">
          + Agregar fila
        </button>
      </div>

      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left">
              <th class="px-3 py-2 w-[20%]">Observación</th>
              <th class="px-3 py-2 w-[16%]">Evidencia (imagen)</th>
              <th class="px-3 py-2 w-[18%]">Ubicación</th>
              <th class="px-3 py-2 w-[14%]" colspan="2">Revisión</th>
              <th class="px-3 py-2 w-[10%] text-center">Acción</th>
            </tr>
          </thead>
          <tbody id="obs-rows-1">
            @php
              $catalogoObs = [
                'Producto sucio','Insectos','Producto mal identificado',
                'Lugar de trabajo desordenado y sucio','Material o producto fuera de su lugar',
                'Producto expuesto','Piso sucio','Tarima o rack sin identificación',
                'Tarima rota','Producto sin identificación FIFO','Producto con emplaye roto o maltratado',
              ];
            @endphp
          </tbody>
        </table>
      </div>

      {{-- ================= Comentarios de calidad ================= --}}
      <div class="mt-6">
        <h4 class="text-sm font-semibold">Comentarios de calidad</h4>
        <textarea name="comentarios_q" id="comentarios_q" rows="5"
                  placeholder="Observaciones generales, acuerdos, notas…"
                  class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">{{ old('comentarios_q') }}</textarea>
      </div>
    </div>
  </div>

    <div class="flex justify-end gap-2 mt-6">
      <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
      <x-button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white">Guardar cambios</x-button>
    </div>
  </form>
</x-modal>

@push('js')
<script>
  let obsIndex = 0; 

  function appendObservationRow(obs = null) {
      const i = obsIndex++; 
      const catalogo = @json($catalogoObs);

      let isOther = false;
      let otherValue = '';
      let selectedValue = '';

      if (obs) {
          if (catalogo.includes(obs.name)) {
              selectedValue = obs.name;
          } else if (obs.name) {
              isOther = true;
              otherValue = obs.name;
              selectedValue = '__otro__';
          }
      }

      let options = `<option value="">Elige…</option>`;
      catalogo.forEach(opt => {
          options += `<option value="${opt}" ${opt === selectedValue ? 'selected' : ''}>${opt}</option>`;
      });
      options += `<option value="__otro__" ${isOther ? 'selected' : ''}>Otro…</option>`;

      const row = $(`
          <tr class="border-t wrapper">
              <td class="px-3 py-2">
                  <input type="hidden" name="obs[${i}][id]" value="${obs?.id || ''}">
                  <input type="hidden" name="obs[${i}][name]" value="${obs?.name ?? (isOther ? otherValue : '')}" class="obs-name-hidden">

                  <select class="obs-name-select w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
                      ${options}
                  </select>

                  <input type="text"
                        class="obs-name-other mt-2 w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600"
                        placeholder="Especifica otra observación"
                        value="${otherValue}"
                        style="${isOther ? '' : 'display:none;'}">
              </td>

              <td class="px-3 py-2">
                  <input type="file" name="obs[${i}][evidencia_file]" accept="image/*" class="block w-full text-xs">
                  <input type="hidden" name="obs[${i}][existing_evidencia_path]" value="${obs?.evidencia_path || ''}">
                  <div class="preview-container mt-2" data-existing-url="${obs?.evidencia_url || ''}">
                      ${obs?.evidencia_url ? `<img src="${obs.evidencia_url}" class="preview-img" style="width:80px; height:80px; object-fit:cover; border-radius:4px;">` : ''}
                  </div>
              </td>

              <td class="px-3 py-2">
                <input type="text"
                      name="obs[${i}][ubicacion]"
                      placeholder="Ej. Rack A-3 / Andén 2"
                      value="${obs?.ubicacion ?? ''}"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-600">
              </td>

              <td class="px-3 py-2">
                  <label class="inline-flex items-center">
                      <input type="radio" name="obs[${i}][rev]" value="cumple" class="mr-2" ${obs?.rev === 'cumple' ? 'checked' : ''}> Cumple
                  </label>
              </td>
              <td class="px-3 py-2">
                  <label class="inline-flex items-center">
                      <input type="radio" name="obs[${i}][rev]" value="no_cumple" class="mr-2" ${obs?.rev === 'no_cumple' ? 'checked' : ''}> No cumple
                  </label>
              </td>
              <td class="px-3 py-2 text-center">
                  <button type="button" class="btn-remove-obs text-red-600 hover:underline"
                      ${obs?.id ? `data-id="${obs.id}" data-path="${obs.evidencia_path}"` : ''}>
                      Quitar
                  </button>
              </td>
          </tr>
      `);

      $('#obs-rows-1').append(row);
  }

  $(document).ready(function(){
      $(document).on('click', '.edit-btn', function () {
          const id = $(this).data('id');
          $('#inspection-id').val(id);
          $.ajax({
              type: 'GET',
              url: "{{ route('quality.getInspection') }}",
              data: { id },
              success: function (r) {
                  const ins  = r?.inspection ?? r;
                  const $form = $('#edit-inspection-form');

                  $('#obs-rows-1').empty();
                  obsIndex = 0;

                  (r.observations || []).forEach(observation => {
                      appendObservationRow(observation);
                  });

                  $form.find('[name="fecha_inspeccion"]').val(ins?.fecha_inspeccion || '');
                  $form.find('[name="inspector"]').val(ins?.inspector || '');
                  $form.find('[name="hora_turno"]').val(ins?.hora_turno || '');
                  $form.find('input[name="turno"]').prop('checked', false);
                  if (ins?.turno){
                      $form.find(`input[name="turno"][value="${ins.turno}"]`).prop('checked', true);
                  }
                  $form.find('input[name="area[]"]').prop('checked', false);
                  (ins?.area || []).forEach(v => {
                      $form.find(`input[name="area[]"][value="${v}"]`).prop('checked', true);
                  });
                  $form.find('#area-otro-check').prop('checked', (ins?.area || []).includes('otro'));
                  $form.find('[name="area_otro"]').val(ins?.area_otro || '');
                  $form.find('[name="responsable"]').val(ins?.responsable || '');
                  $form.find('[name="comentarios_q"]').val(ins?.comentarios_q || '');

                  document.getElementById('edit-inspection')?.classList.add('open');
              },
              error: function (e) {
                  console.error(e);
                  Swal.fire({ icon:'error', title:'Error', text:'The inspection could not be obtained.' });
              }
          });
      });

      $('#btn-add-obs-1').on('click', function(){
          appendObservationRow();
      });

      $('#obs-rows-1').on('click', '.btn-remove-obs', function(){
          var obvId = $(this).data('id');
          var wrapper = $(this).closest('.wrapper');
          var path = $(this).data('path');
          if(!path){
              wrapper.remove();
          } else {
              Swal.fire({
                  title: "Sure?",
                  text: "You won't be able to reverse this",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Yes, delete"
              }).then((result) => {
                  if (result.isConfirmed) {
                      $.ajax({
                          url: "{{ route('quality.eliminarObs') }}",
                          method: "DELETE",
                          data: { id: obvId, path: path },
                          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                          success: function () { wrapper.remove(); },
                          error: function() { Swal.fire({ icon:'error', title:'Error', text:'Could not be deleted.' }); }
                      });
                  }
              });
          }
      });

      $('#obs-rows-1').on('change', '.obs-name-select', function(){
          const val = $(this).val();
          const otherInput = $(this).siblings('.obs-name-other');
          const hiddenInput = $(this).siblings('.obs-name-hidden');
          if(val === '__otro__'){
              otherInput.show().focus();
              hiddenInput.val('');
          } else {
              otherInput.hide();
              hiddenInput.val(val);
          }
      });

      $('#obs-rows-1').on('input', '.obs-name-other', function(){
          $(this).siblings('.obs-name-hidden').val($(this).val());
      });

      $('#obs-rows-1').on('change', 'input[type="file"]', function(){
          const fileInput = this;
          const file = fileInput.files[0];
          const container = $(fileInput).siblings('.preview-container');
          const existingUrl = container.data('existing-url'); 

          container.empty();

          if(file && file.type.startsWith('image/')){
              const reader = new FileReader();
              reader.onload = function(e){
                  $('<img>')
                      .attr('src', e.target.result)
                      .addClass('preview-img')
                      .css({ width: '80px', height: '80px', objectFit: 'cover', borderRadius: '4px' })
                      .appendTo(container);
              }
              reader.readAsDataURL(file);
          } else if(existingUrl){
              $('<img>')
                  .attr('src', existingUrl)
                  .addClass('preview-img')
                  .css({ width: '80px', height: '80px', objectFit: 'cover', borderRadius: '4px' })
                  .appendTo(container);
          }
      });

      // envío de formulario
      $('#edit-inspection-form').on('submit', function(e){
          e.preventDefault(); 
          const form = this;
          const formData = new FormData(form); 

          $.ajax({
              url: "{{ route('quality.updateInspection') }}", 
              method: "POST", 
              data: formData,
              processData: false, 
              contentType: false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response){
                  Swal.fire({
                      icon: 'success',
                      title: '¡Success!',
                      text: 'The inspection was updated successfully.'
                  });
                  $('#edit-inspection').removeClass('open');
              },
              error: function(xhr){
                  console.error(xhr);
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'A problem occurred while saving the inspection.'
                  });
              }
          });
      });
  });
</script>
@endpush