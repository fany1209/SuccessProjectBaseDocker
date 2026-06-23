<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Soil Internal Analyses
      </h1>
      <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <table id="soil-analyses-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left mt-4">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th class="px-6 py-4 text-left">REPORT</th>
          <th class="px-6 py-4 text-left">FECHA INGRESO</th>
          <th class="px-6 py-4 text-left">FECHA EMISIÓN</th>
          <th class="px-6 py-4 text-left">NOMBRE DEL PRODUCTOR</th>
          <th class="px-6 py-4 text-center">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm"></tbody>
    </table>
  </div>

  <script>
    (function() {
      document.addEventListener('DOMContentLoaded', function() {
        const tableElement = $('#soil-analyses-table');

        const soilTable = tableElement.DataTable({
          ajax: {
            url: "{{ route('soil.analyses.json') }}",
            dataSrc: 'data'
          },
          columns: [
            { data: 'report_code', defaultContent: '—' },
            { data: 'entry_date', defaultContent: '—' },
            { data: 'issue_date', defaultContent: '—' },
            { data: 'client_name', defaultContent: '—' },
            {
              data: null,
              className: 'text-center',
              orderable: false,
              render: function(data, type, row) {
                let buttons = `<div class="flex justify-center gap-2">`;

                @can('laboratory.delete')
                buttons += `
                  <button type="button" 
                          class="js-soil-delete-action bg-red-500 hover:bg-red-600 text-white p-2 rounded-sm transition-colors"
                          data-url="${row.delete_url}">
                    <img width="18" src="{{ asset('images/borrar.png') }}" alt="Eliminar" class="pointer-events-none"/>
                  </button>`;
                @endcan

                buttons += `
                  <button type="button" 
                          class="js-soil-pdf-action bg-green-600 hover:bg-green-700 text-white p-2 rounded-sm transition-colors"
                          data-url="${row.pdf_url}">
                    <img width="18" src="{{ asset('images/pdf.png') }}" alt="PDF" class="pointer-events-none"/>
                  </button>
                </div>`;

                return buttons;
              }
            }
          ],
          order: [[1, 'desc']],
          pageLength: 5,
          lengthChange: false,
          searching: false,
          language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
          }
        });

        // --- ACCIÓN ELIMINAR ---
        tableElement.on('click', '.js-soil-delete-action', async function(e) {
          e.preventDefault();
          e.stopImmediatePropagation(); 

          const btn = $(this);
          const url = btn.attr('data-url');

          if (!url || url === "undefined") {
            Swal.fire('Error', 'No se pudo obtener la ruta del registro.', 'error');
            return;
          }

          const result = await Swal.fire({
            title: '¿Eliminar registro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
          });

          if (result.isConfirmed) {
            try {
              const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                }
              });

              const res = await response.json();

              if (response.ok) {
                Swal.fire('Eliminado', res.message || 'Registro borrado.', 'success');
                soilTable.ajax.reload(null, false);
              } else {
                Swal.fire('Error', res.message || 'No se pudo eliminar.', 'error');
              }
            } catch (err) {
              Swal.fire('Error', 'Ocurrió un error al procesar la solicitud.', 'error');
            }
          }
        });

        // --- ACCIÓN PDF ---
        tableElement.on('click', '.js-soil-pdf-action', function(e) {
          e.preventDefault();
          e.stopImmediatePropagation(); 

          const url = $(this).attr('data-url');

          if (url && url !== "undefined") {
            window.open(url, '_blank');
          } else {
            Swal.fire('Error', 'URL de PDF no válida.', 'error');
          }
        });

      });
    })();
  </script>
</section>