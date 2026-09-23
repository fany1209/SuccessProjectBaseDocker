@section('content')
<div class="flex flex-col lg:flex-row items-start gap-4 p-3 lg:p-4">

  <div class="flex flex-col items-start gap-2.5 bg-white shadow-sm border border-gray-100 rounded-xl w-full lg:w-56 xl:w-64 shrink-0 p-3">
    <x-tittle-form class="border-b-2 border-green-700 pb-2 w-full">Menu</x-tittle-form>
    <x-nav-button data-button="requisitions" icon="ri-shopping-bag-2-fill" class="option-btn border-2 border-green-500">Requisitions</x-nav-button>
    
    @can('purchases.admin')
        <x-nav-button data-button="orders" class="option-btn">Orders</x-nav-button>
        <x-nav-button data-button="quotes" class="option-btn">Quotes</x-nav-button>
        <x-nav-button data-button="comparative" class="option-btn">Comparative Table</x-nav-button>
        <x-nav-button data-button="directory" class="option-btn">Directory Suppliers</x-nav-button>
        @endcan

        <x-nav-button data-button="comparative2" class="option-btn">Yours Comparative Table</x-nav-button>

        @canany(['purchases.warehouse', 'quality.purchases', 'warehouse.show'])
            <x-nav-button data-button="warehouse" class="option-btn">Warehouse</x-nav-button>
        @endcanany

        <x-tittle-form class="border-b-2 border-green-700 pb-2 w-full">Formats</x-tittle-form>
        <p class="text-sm text-gray-600">Select the format you want to generate.</p>

        <x-nav-button data-target="modal-insumos" class="open-modal" icon="ri-table-line">Comparative Table</x-nav-button>
        @include('purchases.comparative.table-comp')
         @can('purchases.admin')

        <x-nav-button data-target="g-purchase-order" class="open-modal" icon="ri-shopping-bag-3-fill">Purchase Order</x-nav-button>
        @include('purchases.modals.gPurchaseOrder')

        <x-nav-button data-target="g-supp-select-crit" class="open-modal" icon="ri-survey-line">Supplier selection criteria</x-nav-button>
        @include('purchases.modals.gSupSelectCrit')

        <x-nav-button data-target="g-supplier-evaluation" class="open-modal" icon="ri-checkbox-multiple-fill">Supplier evaluation</x-nav-button>
        @include('purchases.modals.gSupplierEvaluation')

        <x-nav-button data-target="add-supplier" class="open-modal" icon="ri-file-list-3-fill">Directory Suppliers</x-nav-button>
        @include('purchases.directory.modals.create')
    @endcan

    @canany(['purchases.admin', 'quality.purchases'])
        <x-nav-button data-target="g-warehouse" class="open-modal" icon="ri-checkbox-multiple-fill">Product entry warehouse</x-nav-button>
        @include('purchases.modals.gWarehouse')
    @endcanany

</div>

    <div class="flex flex-col items-start bg-white shadow-sm border border-gray-100 rounded-xl w-full flex-1 min-w-0 p-4">
        <div class="flex justify-center items-center w-full gap-3 my-3">
            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-orange-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-archive-stack-line text-4xl"></i> Your Requisitions</h3>
                <p class="text-2xl text-white font-bold">{{ $your_requisitions }}</p>
            </span>

            @can('purchases.requisitions.show')
            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-green-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-checkbox-circle-line text-4xl"></i> Requisiciones</h3>
                <p class="text-2xl text-white font-bold">{{ $requisitions_count }}</p>
            </span>

            <span class="flex justify-between items-center px-4 shadow-md p-2 rounded-md bg-red-500 gap-2 w-full">
                <h3 class="text-2xl text-white tracking-[2px]"><i class="ri-feedback-line text-4xl"></i> Requisiciones sin revisar</h3>
                <p class="text-2xl text-white font-bold">{{ $requisitions_no_check }}</p>
            </span>
            @endcan
        </div>

        <hr class="border-t-2 border-gray-600 border-dashed w-full my-2">
        <x-tittle-form id="opt-tittle" class="border-s-2 border-green-700 ps-2 ms-2">Requisitions</x-tittle-form>
        <div id="requisitions-layout" class="">
            @include('purchases.requisitions.tools')

            @can('purchases.requisitions.show')
                @include('purchases.requisitions.table')
            @endcan

            @can('purchases.requisitions.create')
                <x-tittle-form id="opt-tittle-2" class="border-s-2 border-green-700 ps-2 ms-2">Your Requisitions</x-tittle-form>
                @include('purchases.requisitions.yourRequisitions')
            @endcan

            @can('purchases.requisitions.update')
                @include('purchases.requisitions.modals.editRequisition')
            @endcan
        </div>
        
        <div id="orders-layout" class="hidden">
            @include('purchases.order')
        </div>

        <div id="quotes-layout" class="hidden">
        </div>

        <div id="comparative-layout" class="hidden">
            @include('purchases.comparative.index')
        </div>

         <div id="directory-layout" class="hidden w-full">
            @include('purchases.directory.index') 
        </div>

        <div id="comparative2-layout" class="hidden w-full">
            @include('purchases.comparative.index2') 
        </div>

        <div id="warehouse-layout" class="hidden w-full">
            <x-tittle-form class="border-s-2 border-green-700 ps-2 ms-2">Warehouse entries</x-tittle-form>
            <div class="w-full mt-2">
                @include('purchases.warehouse.table')
            </div>
        </div>

        <hr class="border-t-2 border-gray-600 border-dashed w-full my-2">

        <div class="flex justify-center items-center w-full gap-3 my-3">
            @can('purchases.requisitions.show')
            <div id="requiPerDepartment" class="w-full h-full lg:h-[500px]"></div>
            <div id="requiPerEmployee" class="w-full h-full lg:h-[500px]"></div>
            @endcan
        </div>

    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(requisitionPerEmployee);

    function requisitionPerEmployee() {
        $.ajax({
            url: "{{ route('purchases.charts') }}",
            dataType: 'json',
            success: function(response) {
                var dataArray = [];
                dataArray.push(['Employee', 'Requisitions']);
                response.requisitions_per_employee.forEach(function(item) {
                    dataArray.push([item.applicant, parseInt(item.quantity)]);
                });
                var data = google.visualization.arrayToDataTable(dataArray);
                var options = {
                    title: 'Requisition per Employee',
                    width: 900,
                    legend: { position: 'none' },
                    chart: {
                        title: 'Requisition per Employee',
                        subtitle: 'requisition by percentage'
                    },
                    bars: 'horizontal',
                    axes: {
                        x: { 0: { side: 'top', label: 'Percentage' } }
                    },
                    bar: { groupWidth: "90%" }
                };
                var chart = new google.charts.Bar(document.getElementById('requiPerEmployee'));
                chart.draw(data, options);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los datos:", error);
            }
        });
    };
</script>

<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(requisitionsPerDepartment);

    function requisitionsPerDepartment() {
        $.ajax({
            url: "{{ route('purchases.charts') }}",
            dataType: 'json',
            success: function(response) {
                var dataArray = [];
                dataArray.push(['Departments', 'Quantity']);
                response.requisitions_per_department.forEach(function(item) {
                    dataArray.push([item.department, parseInt(item.quantity)]);
                });
                var data = google.visualization.arrayToDataTable(dataArray);
                var options = {
                    title: 'Requisitions per Department',
                    pieHole: 0.4,
                };
                var chart = new google.visualization.PieChart(document.getElementById('requiPerDepartment'));
                chart.draw(data, options);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los datos:", error);
            }
        });
    }
</script>

<script>
$(function(){
    let ordersTableLoaded = false;

    function purchasesReactive(){
        $('.option-btn').on('click', function(){
            const option = $(this).data('button');

            $('.option-btn').removeClass('border-2 border-green-500');
            $(this).addClass('border-2 border-green-500');

            $('#requisitions-layout, #orders-layout, #quotes-layout, #warehouse-layout, #comparative-layout').addClass('hidden');

            switch(option){
                case 'requisitions':
                    $('#requisitions-layout').removeClass('hidden');
                    $('#opt-tittle').text('Requisitions');
                    break;

                case 'orders':
                    $('#orders-layout').removeClass('hidden');
                    $('#opt-tittle').text('Orders');

                    if (typeof window.initOrdersTable === 'function') {
                        window.initOrdersTable();
                    }
                    break;

                case 'quotes':
                    $('#quotes-layout').removeClass('hidden');
                    $('#opt-tittle').text('Quotes');
                    break;

                case 'warehouse':
                    $('#warehouse-layout').removeClass('hidden');
                    $('#opt-tittle').text('Warehouse');
                    break;
               
                case 'comparative':
                    $('#comparative-layout').removeClass('hidden');
                    $('#opt-tittle').text('Comparative Table');
                    if (typeof window.initComparativeTable === 'function') {
                        window.initComparativeTable();
                    }
                    break;

                case 'comparative2':
                $('#comparative2-layout').removeClass('hidden');
                $('#opt-tittle').text('Your Comparative Tables');
                break;

                    case 'directory':
                        $('#directory-layout').removeClass('hidden');
                        $('#opt-tittle').text('Directory Suppliers');
                        break;

                default:
                    $('#requisitions-layout').removeClass('hidden');
                    $('#opt-tittle').text('Requisitions');
                    break;
            }
        });
    }

    purchasesReactive();
});
</script>
@endpush
