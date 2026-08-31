{{--
Warehouse
Fecha de creación: 21-07-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 17-12-25
--}}
<x-application.section-1>
  <div class="flex flex-col justify-center items-center w-full">
    <div class="flex justify-center items-center gap-2 w-full my-1">
      <x-input class="w-full p-2" id="search" placeholder="Search..."></x-input>
      <select id="concept" class="p-2 block border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
        <option value="">All concepts</option>
        @foreach ($concepts as $concept)
          <option value="{{ $concept->concept_id }}">{{ $concept->name }}</option>
        @endforeach
      </select>
    </div>

    <table id="products-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
      <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
          <th scope="col" class="px-6 py-4 text-right">LOCATION</th>
          <th scope="col" class="px-6 py-4 text-right">CONCEPT</th>
          <th scope="col" class="px-6 py-4 text-right">PRODUCT</th>
          <th scope="col" class="px-6 py-4 text-right">BAG ID</th>
          <th scope="col" class="px-6 py-4 text-right">QUANTITY</th>
          <th scope="col" class="px-6 py-4 text-right">WEIGHT PER UNIT</th>
          <th scope="col" class="px-6 py-4 text-right">NET WEIGHT</th>
          <th scope="col" class="px-6 py-4 text-right">BATCH</th>
          <th scope="col" class="px-6 py-4">ACTIONS</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-md"></tbody>
    </table>
  </div>

  @include('warehouse.modals.editOperation')
</x-application.section-1>

@push('js')
<script>
$(document).ready(function () {

  const locations = @json($locations);
  const products_inventory = @json($products_inventory);
  const inventory = @json($inventory);

  // COLA DE ALERTAS (Swal)
  const alertQueue = [];
  function queueAlert(opts) {
    alertQueue.push(opts);
    if (alertQueue.length === 1) runNextAlert();
  }
  function runNextAlert() {
    if (alertQueue.length === 0) return;
    const opts = alertQueue[0];

    if (window.Swal && typeof Swal.fire === 'function') {
      Swal.fire(opts).then(() => {
        alertQueue.shift();
        runNextAlert();
      });
    } else {
      alert((opts.title || 'Aviso') + ': ' + (opts.text || ''));
      alertQueue.shift();
      runNextAlert();
    }
  }

  // DATATABLE
  function reloadData(){
    let table = $('#products-table').DataTable({
      destroy: true,
      ajax: {
        url: "{{ route('warehouse.getWarehouse') }}",
        data: function (d) {
          d.concept = $('#concept').val();
          d.search  = $('#search').val();
        },
        dataSrc: 'products'
      },
      columns: [
        { data: 'lName' },
        { data: 'cName' },
        { data: 'pName' },
        { data: 'bag_number' },
        { data: 'quantity' },
        { 
          data: 'weight_per_unit',
          render: function(data, type, row) {
              return parseFloat(data).toFixed(3);
          }
        },
        { 
          data: 'net_weight',
          render: function(data, type, row) {
              return parseFloat(data).toFixed(3);
          }
        },
        { data: 'batch' },
        { data: null } 
      ],
      columnDefs: [
        {
          targets: 8,
          orderable: false,
          searchable: false,
          className: 'text-right',
          render: function(data, type, row) {
            return `
              <button data-id="${row.cli_id}" data-target="edit-cli" class="open-modal edit-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
              </button>
              <button data-id="${row.cli_id}" class="delete-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2" title="Delete">
                <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
              </button>
            `;
          }
        }
      ],
      lengthChange: false,
      searching: false,
      pageLength: 15,
      responsive: true,
      autoWidth: false,
      language: {
        info: "Show _START_ to _END_ of _TOTAL_ products",
        lengthMenu: "Show _MENU_ products",
        infoEmpty: "There 're products available",
        zeroRecords: "No results found",
        infoFiltered: "(filtered on _MAX_ total records)",
        paginate: {
          first: "First",
          last: "Last",
          next: "Next",
          previous: "Previous"
        },
      }
    });

    $('#concept').off('change.dt').on('change.dt', function () {
      table.ajax.reload();
    });
    $('#search').off('keyup.dt').on('keyup.dt', function (){
      table.ajax.reload();
    });

    deleteCli(table);
    editOperation(table);
  }

  // EDIT
  function editOperation(table){
    $('#products-table tbody').off('click.edit').on('click.edit', '.edit-btn', function(){
      let id = $(this).data('id');

      $.ajax({
        type: 'GET',
        url: `/cli/${id}`,
        success: function(response){

          $('#edit-cli input#cli_id').val(response.cli.cli_id);
          $('#edit-cli select#warehouse').val(response.cli.warehouse_id);

          $('#edit-cli select#location_id').empty();
          locations.forEach(location => {
            if(response.cli.warehouse_id === location.warehouse_id){
              $('#edit-cli select#location_id').append(`<option value="${location.location_id}">${location.name}</option>`);
            }
          });
          $('#edit-cli select#location_id').val(response.cli.location_id);

          $('#edit-cli select#concept_id').val(response.cli.concept_id);
          $('#edit-cli input#product_id').val(response.cli.product_id);

          $('#edit-cli .unit').text(`Unit: ${products_inventory.find(i => i.product_id === response.cli.product_id)?.unit ?? ''}`);
          $('#edit-cli .product-name').text(`Product: ${products_inventory.find(i => i.product_id === response.cli.product_id)?.name ?? ''}`);

          $('#edit-cli #inventory_id').empty();
          inventory.forEach(item => {
            if(item.product_id === response.cli.product_id){
              $('#edit-cli #inventory_id').append(`<option value="${item.inventory_id}">${item.batch}</option>`);
            }
          });
          $('#edit-cli #inventory_id').val(response.cli.inventory_id);

          $('#edit-cli .stock').text(`Available: ${inventory.find(i => i.inventory_id === response.cli.inventory_id)?.stock_wh ?? 0.0}`);

          $('#edit-cli #quantity').val(response.cli.quantity);
          $('#edit-cli #weight_per_unit').val(response.cli.weight_per_unit);

          $('#edit-cli .total')
            .addClass('mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1')
            .text(`Total: ${response.cli.net_weight}`);

          let product = products_inventory.find(i => i.product_id === response.cli.product_id);
          if (product && product.name.includes('BGBG')) {
              $('#edit-cli .bag_numbers_wrapper').removeClass('hidden');
              $('#edit-cli #bag_number').val(response.cli.bag_number);
              $('#edit-cli #protein').val(response.cli.protein);
              $('#edit-cli #bag_weight_edit').val(response.cli.weight_per_unit);
          } else {
              $('#edit-cli .bag_numbers_wrapper').addClass('hidden');
              $('#edit-cli #bag_number').val('');
              $('#edit-cli #protein').val('');
          }

        },
        error: function(){
          queueAlert({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while got the Cli.',
            confirmButtonText: 'OK'
          });
        }
      });
    });
  }

  // DELETE
  function deleteCli(table){
    $('#products-table tbody').off('click.delete').on('click.delete', '.delete-btn', function(){
      let id = $(this).data('id');

      if (window.Swal && typeof Swal.fire === 'function') {
        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `/cli/${id}`,
              method: "DELETE",
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
              success: function () {
                table.ajax.reload();
                queueAlert({
                  title: "Deleted!",
                  text: "The Operation has been deleted.",
                  icon: "success"
                });
              },
              error: function(xhr) {
                console.error('Error al eliminar el CLI:', xhr);
                queueAlert({
                  icon: 'error',
                  title: 'Error',
                  text: 'An error occurred while deleted the Operation.',
                  confirmButtonText: 'OK'
                });
              }
            });
          }
        });
      } else {
        if (confirm("Are you sure?")) {
          $.ajax({
            url: `/cli/${id}`,
            method: "DELETE",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
              table.ajax.reload();
              alert("Deleted!");
            }
          });
        }
      }
    });
  }

  // ALERTAS
  function getJsonSafe(url) {
    const d = $.Deferred();
    $.getJSON(url)
      .done(res => d.resolve({ ok: true, res }))
      .fail(xhr => d.resolve({ ok: false, xhr }));
    return d.promise();
  }

  const urlInsps = "{{ route('quality.checkPendingWarehouseInspections') }}";
  const urlLots  = "{{ route('lot.request.check') }}";

  $.when(getJsonSafe(urlInsps), getJsonSafe(urlLots))
    .done(function (ins, lot) {

      // Inspecciones pendientes
      if (ins.ok) {
        const inspCount = ins.res?.pending?.length ?? (ins.res?.count ?? 0);
        if (inspCount > 0) {
          queueAlert({
            icon: 'info',
            title: 'Inspecciones Pendientes',
            html: `Tienes ${inspCount} inspección(es) de almacén pendiente(s).`,
            confirmButtonText: 'Aceptar'
          });
        }
      } else {
        console.error('Error al verificar inspecciones pendientes', ins.xhr);
      }

      // Peticiones de lote 
      if (lot.ok) {
        const lotCount = lot.res?.count ?? 0;
        if (lotCount > 0) {
          queueAlert({
            icon: 'info',
            title: 'Peticiones de lote y/o pendientes',
            html: `Hay <b>${lotCount}</b> peticiones pendientes.`,
            confirmButtonText: 'Aceptar'
          });
        }
      } else {
        console.error('Error al revisar peticiones de lote', lot.xhr);
      }

    });

  reloadData();

});
</script>
@endpush
