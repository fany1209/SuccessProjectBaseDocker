<x-modal id="bigbags-summary" maxWidth="6xl">
    <div class="flex flex-col w-full gap-4 max-h-[85vh] overflow-hidden">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-200 pb-3">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl">
                    <i class="ri-inbox-archive-line text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 tracking-wide">Inventario de BigBags en Almacén</h2>
                    <p class="text-xs text-gray-500">Consulta de total de BigBags por producto, pesos individuales y peso consolidado.</p>
                </div>
            </div>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-700 text-2xl font-bold p-1 leading-none rounded-lg hover:bg-gray-100 transition-colors">
                &times;
            </button>
        </div>

        <!-- KPI Cards Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-200 rounded-xl p-3 flex flex-col justify-between">
                <div class="flex items-center justify-between text-emerald-800">
                    <span class="text-xs font-semibold uppercase tracking-wider">Total BigBags</span>
                    <i class="ri-inbox-line text-lg"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span id="kpi-total-bags" class="text-2xl font-black text-emerald-900 tracking-tight">0</span>
                    <span class="text-xs font-medium text-emerald-700">uds</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200 rounded-xl p-3 flex flex-col justify-between">
                <div class="flex items-center justify-between text-blue-800">
                    <span class="text-xs font-semibold uppercase tracking-wider">Peso Consolidado</span>
                    <i class="ri-scales-3-line text-lg"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span id="kpi-total-weight" class="text-2xl font-black text-blue-900 tracking-tight">0.00</span>
                    <span class="text-xs font-medium text-blue-700">kg</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-xl p-3 flex flex-col justify-between">
                <div class="flex items-center justify-between text-purple-800">
                    <span class="text-xs font-semibold uppercase tracking-wider">Productos</span>
                    <i class="ri-shopping-bag-3-line text-lg"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span id="kpi-total-products" class="text-2xl font-black text-purple-900 tracking-tight">0</span>
                    <span class="text-xs font-medium text-purple-700">ítems</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200 rounded-xl p-3 flex flex-col justify-between">
                <div class="flex items-center justify-between text-amber-800">
                    <span class="text-xs font-semibold uppercase tracking-wider">Promedio / Bag</span>
                    <i class="ri-calculator-line text-lg"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span id="kpi-avg-weight" class="text-2xl font-black text-amber-900 tracking-tight">0.00</span>
                    <span class="text-xs font-medium text-amber-700">kg</span>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <i class="ri-search-line absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" id="bigbag-filter-search" placeholder="Buscar por producto, # barcina, lote o packing list..." class="w-full pl-9 pr-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                </div>
                <select id="bigbag-filter-warehouse" class="py-1.5 px-3 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white min-w-[180px]">
                    <option value="">Todos los Almacenes</option>
                    @if(isset($warehouses))
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->warehouse_id }}">{{ $wh->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <button type="button" id="bigbag-btn-refresh" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-lg text-sm font-medium transition-colors shadow-sm cursor-pointer" title="Recargar datos">
                <i class="ri-refresh-line"></i> Actualizar
            </button>
        </div>

        <!-- Content Area: Products & Individual BigBags -->
        <div id="bigbags-content-scroll" class="flex flex-col gap-3 overflow-y-auto pr-1 flex-1 min-h-[320px] max-h-[50vh]">
            <!-- Loading Indicator -->
            <div id="bigbags-loading" class="flex flex-col items-center justify-center py-12 text-gray-500">
                <div class="w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="mt-3 text-sm font-medium">Cargando inventario de BigBags...</span>
            </div>

            <!-- Empty State -->
            <div id="bigbags-empty" class="hidden flex flex-col items-center justify-center py-12 text-gray-400">
                <i class="ri-inbox-2-line text-5xl text-gray-300 mb-2"></i>
                <p class="text-base font-semibold text-gray-600">No se encontraron BigBags en almacén</p>
                <p class="text-xs text-gray-400 mt-0.5">No hay barcinas registradas con los filtros seleccionados.</p>
            </div>

            <!-- Products List Container -->
            <div id="bigbags-products-list" class="flex flex-col gap-3"></div>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center border-t border-gray-200 pt-3 text-xs text-gray-500">
            <div>
                <span class="font-medium text-gray-700">Nota:</span> Los pesos individuales corresponden a la medición real de cada BigBag en báscula.
            </div>
            <x-button type="button" class="close-modal bg-gray-700 hover:bg-gray-800 text-white px-5 py-2">
                Cerrar
            </x-button>
        </div>
    </div>
</x-modal>

<!-- Modal de Edición de BigBag -->
<div id="modal-edit-bigbag" class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/60 hidden" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative mx-4">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl">
                    <i class="ri-edit-box-line text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Editar BigBag / Barcina</h3>
                    <p class="text-xs text-gray-500">Actualiza peso, proteína, # de barcina y ubicación</p>
                </div>
            </div>
            <button type="button" class="close-edit-bb-modal text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none p-1 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                &times;
            </button>
        </div>

        <form id="form-edit-bigbag" class="flex flex-col gap-4">
            <input type="hidden" id="edit-bb-cli-id" name="cli_id" />

            <!-- Product and Batch details banner -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs">
                <div>
                    <span class="text-gray-400 block uppercase font-bold text-[10px]">Producto</span>
                    <span id="edit-bb-prod-display" class="font-bold text-gray-800 text-sm"></span>
                </div>
                <div class="text-right">
                    <span class="text-gray-400 block uppercase font-bold text-[10px]">Lote</span>
                    <span id="edit-bb-batch-display" class="font-mono font-bold text-gray-700"></span>
                </div>
            </div>

            <!-- Form Grid -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Bag Number -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="ri-hashtag text-gray-400 mr-0.5"></i># de Barcina
                    </label>
                    <input type="text" id="edit-bb-bag-number" name="bag_number" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium" placeholder="Ej. 294" />
                </div>

                <!-- Packing List -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="ri-file-list-3-line text-purple-500 mr-0.5"></i>Packing List
                    </label>
                    <input type="number" step="1" id="edit-bb-packing-list" name="packing_list" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono font-bold" placeholder="Ej. 104" />
                </div>

                <!-- Weight (Kg) - Destacado -->
                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1">
                        <i class="ri-scales-3-line text-emerald-600 mr-0.5"></i>Peso Real (kg) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="any" min="0.01" id="edit-bb-weight" name="weight" required class="w-full px-3 py-2 text-sm rounded-lg border-2 border-emerald-400 bg-emerald-50/30 text-emerald-900 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Ej. 1025.50" />
                </div>

                <!-- Protein (%) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <i class="ri-flask-line text-amber-500 mr-0.5"></i>Proteína (%)
                    </label>
                    <input type="number" step="any" min="0" max="100" id="edit-bb-protein" name="protein" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium" placeholder="Ej. 46.2" />
                </div>
            </div>

            <!-- Location Assignment -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    <i class="ri-map-pin-line text-blue-500 mr-0.5"></i>Ubicación en Almacén
                </label>
                <select id="edit-bb-location-id" name="location_id" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                    <option value="">Selecciona una ubicación</option>
                    @if(isset($warehouses) && isset($locations))
                        @foreach ($warehouses as $wh)
                            @php
                                $whLocs = $locations->where('warehouse_id', $wh->warehouse_id);
                            @endphp
                            @if($whLocs->count() > 0)
                                <optgroup label="{{ $wh->name }}">
                                    @foreach($whLocs as $loc)
                                        <option value="{{ $loc->location_id }}">{{ str_contains(strtolower($loc->name), strtolower($wh->name)) ? $loc->name : $wh->name . ' - ' . $loc->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center gap-2 pt-3 border-t border-gray-100 mt-2">
                <button type="button" class="close-edit-bb-modal px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" id="btn-save-bigbag" class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-colors cursor-pointer">
                    <i class="ri-save-line"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
$(function(){
    let bigbagsRawData = null;

    function formatNumber(num, decimals = 2) {
        let val = parseFloat(num);
        if (isNaN(val)) return '0.00';
        return val.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function renderBigBagsModal(data) {
        const $list = $('#bigbags-products-list');
        const $empty = $('#bigbags-empty');
        const $loading = $('#bigbags-loading');

        $loading.addClass('hidden');

        if (!data || !data.products || data.products.length === 0) {
            $list.empty();
            $empty.removeClass('hidden');
            $('#kpi-total-bags').text('0');
            $('#kpi-total-weight').text('0.00');
            $('#kpi-total-products').text('0');
            $('#kpi-avg-weight').text('0.00');
            return;
        }

        $empty.addClass('hidden');
        $list.empty();

        // Update Global KPIs
        $('#kpi-total-bags').text(data.grand_total_bags || 0);
        $('#kpi-total-weight').text(formatNumber(data.grand_total_weight || 0));
        $('#kpi-total-products').text(data.products_count || data.products.length);

        let grandAvg = (data.grand_total_bags > 0) 
            ? (data.grand_total_weight / data.grand_total_bags) 
            : 0;
        $('#kpi-avg-weight').text(formatNumber(grandAvg));

        // Render each product group
        data.products.forEach((prod, pIdx) => {
            let bagsRows = '';
            prod.bags.forEach(bag => {
                let proteinBadge = (bag.protein !== null && bag.protein !== undefined && bag.protein !== '')
                    ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">${parseFloat(bag.protein).toFixed(2)}%</span>`
                    : `<span class="text-gray-400 text-xs">N/A</span>`;

                let packingBadge = (bag.packing_list !== null && bag.packing_list !== undefined && bag.packing_list !== '')
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200 font-bold text-xs font-mono"><i class="ri-file-list-3-line text-purple-500"></i>${bag.packing_list}</span>`
                    : `<span class="text-gray-300 text-xs">-</span>`;

                bagsRows += `
                    <tr class="hover:bg-gray-50/80 transition-colors border-b border-gray-100 text-xs">
                        <td class="px-3 py-2 font-mono font-bold text-gray-800">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 border border-gray-300 rounded font-bold text-gray-700">
                                <i class="ri-hashtag text-gray-400"></i>${bag.bag_number || 'S/N'}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            ${packingBadge}
                        </td>
                        <td class="px-3 py-2 font-bold text-emerald-700 text-right">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-50 border border-emerald-200">
                                ${formatNumber(bag.weight)} kg
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            ${proteinBadge}
                        </td>
                        <td class="px-3 py-2 font-mono text-gray-600">
                            ${bag.batch || '-'}
                        </td>
                        <td class="px-3 py-2 text-gray-700">
                            ${bag.warehouse_name || 'Sin asignar'}
                        </td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded font-semibold text-xs">
                                ${bag.location_name || 'Sin asignar'}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-400 text-right">
                            ${bag.date || '-'}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button type="button" class="btn-edit-bigbag inline-flex items-center justify-center p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition-colors cursor-pointer shadow-2xs" 
                                data-cli-id="${bag.cli_id}" 
                                data-bag-number="${bag.bag_number || ''}" 
                                data-weight="${bag.weight || ''}" 
                                data-protein="${bag.protein !== null && bag.protein !== undefined ? bag.protein : ''}" 
                                data-packing-list="${bag.packing_list || ''}" 
                                data-location-id="${bag.location_id || ''}" 
                                data-warehouse-id="${bag.warehouse_id || ''}"
                                data-product-name="${prod.product_name}"
                                data-batch="${bag.batch || ''}"
                                title="Editar información de este BigBag">
                                <i class="ri-edit-line text-sm"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            let productHtml = `
                <div class="product-group border border-gray-200 rounded-xl bg-white overflow-hidden shadow-sm" data-product-id="${prod.product_id}">
                    <!-- Product Header -->
                    <div class="product-header flex flex-col md:flex-row items-start md:items-center justify-between gap-2 p-3 bg-gray-50/90 border-b border-gray-200 cursor-pointer select-none hover:bg-gray-100/70 transition-colors">
                        <div class="flex items-center gap-2">
                            <span class="product-toggle-icon w-6 h-6 flex items-center justify-center rounded-full bg-white border border-gray-300 text-gray-600 shadow-2xs transition-transform duration-200">
                                <i class="ri-arrow-down-s-line text-lg"></i>
                            </span>
                            <div>
                                <h3 class="font-bold text-sm text-gray-800 tracking-wide">${prod.product_name}</h3>
                                <span class="text-[11px] text-gray-500 font-medium">${prod.unit ? 'Unidad: ' + prod.unit : 'BigBags'}</span>
                            </div>
                        </div>

                        <div class="flex items-center flex-wrap gap-2 text-xs">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-bold border border-blue-200 shadow-2xs">
                                <i class="ri-inbox-line"></i> ${prod.total_bags} BigBags
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold border border-emerald-200 shadow-2xs">
                                <i class="ri-scales-line"></i> Total: ${formatNumber(prod.total_weight)} kg
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 font-semibold border border-gray-300 shadow-2xs">
                                Promedio: ${formatNumber(prod.avg_weight)} kg
                            </span>
                        </div>
                    </div>

                    <!-- Individual BigBags Table -->
                    <div class="product-bags-table-wrapper overflow-x-auto">
                        <table class="w-full text-left divide-y divide-gray-200">
                            <thead class="bg-gray-100/70 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                                <tr>
                                    <th scope="col" class="px-3 py-2"># Barcina</th>
                                    <th scope="col" class="px-3 py-2 text-center">Packing List</th>
                                    <th scope="col" class="px-3 py-2 text-right">Peso Individual</th>
                                    <th scope="col" class="px-3 py-2 text-center">Proteína %</th>
                                    <th scope="col" class="px-3 py-2">Lote</th>
                                    <th scope="col" class="px-3 py-2">Almacén</th>
                                    <th scope="col" class="px-3 py-2">Ubicación</th>
                                    <th scope="col" class="px-3 py-2 text-right">Ingreso</th>
                                    <th scope="col" class="px-3 py-2 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                ${bagsRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            $list.append(productHtml);
        });
    }

    function filterAndDisplay() {
        if (!bigbagsRawData) return;

        let search = ($('#bigbag-filter-search').val() || '').toLowerCase().trim();
        let selectedWh = $('#bigbag-filter-warehouse').val();

        let filteredProducts = [];
        let grandBags = 0;
        let grandWeight = 0;

        bigbagsRawData.products.forEach(prod => {
            let matchesProduct = prod.product_name.toLowerCase().includes(search);

            let matchingBags = prod.bags.filter(bag => {
                let matchWh = (!selectedWh || bag.warehouse_id == selectedWh);
                let matchText = matchesProduct ||
                    (bag.bag_number && bag.bag_number.toString().toLowerCase().includes(search)) ||
                    (bag.packing_list && bag.packing_list.toString().toLowerCase().includes(search)) ||
                    (bag.batch && bag.batch.toLowerCase().includes(search)) ||
                    (bag.location_name && bag.location_name.toLowerCase().includes(search));

                return matchWh && matchText;
            });

            if (matchingBags.length > 0) {
                let totalW = matchingBags.reduce((acc, b) => acc + (parseFloat(b.weight) || 0), 0);
                grandBags += matchingBags.length;
                grandWeight += totalW;

                filteredProducts.push({
                    product_id: prod.product_id,
                    product_name: prod.product_name,
                    unit: prod.unit,
                    total_bags: matchingBags.length,
                    total_weight: totalW,
                    avg_weight: totalW / matchingBags.length,
                    bags: matchingBags
                });
            }
        });

        renderBigBagsModal({
            grand_total_bags: grandBags,
            grand_total_weight: grandWeight,
            products_count: filteredProducts.length,
            products: filteredProducts
        });
    }

    function fetchBigBagsData() {
        const $loading = $('#bigbags-loading');
        const $empty = $('#bigbags-empty');
        const $list = $('#bigbags-products-list');

        $loading.removeClass('hidden');
        $empty.addClass('hidden');
        $list.empty();

        let wh = $('#bigbag-filter-warehouse').val();
        let url = "{{ route('warehouse.bigbagsSummary') }}";
        if (wh) {
            url += '?warehouse_id=' + encodeURIComponent(wh);
        }

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                let data = res.data || res;
                bigbagsRawData = data;
                filterAndDisplay();
            },
            error: function(xhr) {
                $loading.addClass('hidden');
                $empty.removeClass('hidden').find('p.text-base').text('Error al consultar BigBags');
                console.error('Error fetching bigbags summary:', xhr);
            }
        });
    }

    // Toggle collapse/expand product
    $(document).on('click', '#bigbags-summary .product-header', function(){
        const $group = $(this).closest('.product-group');
        const $tableWrapper = $group.find('.product-bags-table-wrapper');
        const $icon = $(this).find('.product-toggle-icon');

        $tableWrapper.slideToggle(200, function() {
            if ($tableWrapper.is(':visible')) {
                $icon.css('transform', 'rotate(0deg)');
            } else {
                $icon.css('transform', 'rotate(-90deg)');
            }
        });
    });

    // Modal open trigger
    $(document).on('click', '.open-modal[data-target="bigbags-summary"]', function(){
        fetchBigBagsData();
    });

    // Refresh button
    $('#bigbag-btn-refresh').on('click', function(){
        fetchBigBagsData();
    });

    // Filter changes
    $('#bigbag-filter-warehouse').on('change', function(){
        fetchBigBagsData();
    });

    $('#bigbag-filter-search').on('input', function(){
        filterAndDisplay();
    });

    // Abrir modal de edición de BigBag
    $(document).on('click', '.btn-edit-bigbag', function(e){
        e.stopPropagation();
        const cliId = $(this).data('cli-id');
        const bagNumber = $(this).data('bag-number');
        const weight = $(this).data('weight');
        const protein = $(this).data('protein');
        const packingList = $(this).data('packing-list');
        const locationId = $(this).data('location-id');
        const prodName = $(this).data('product-name');
        const batch = $(this).data('batch');

        $('#edit-bb-cli-id').val(cliId);
        $('#edit-bb-prod-display').text(prodName);
        $('#edit-bb-batch-display').text(batch || 'S/L');
        $('#edit-bb-bag-number').val(bagNumber);
        $('#edit-bb-weight').val(weight);
        $('#edit-bb-protein').val(protein !== '' ? protein : '');
        $('#edit-bb-packing-list').val(packingList !== '' ? packingList : '');
        $('#edit-bb-location-id').val(locationId || '');

        $('#modal-edit-bigbag').removeClass('hidden').css('display', 'flex');
    });

    // Cerrar modal de edición
    $(document).on('click', '.close-edit-bb-modal', function(){
        $('#modal-edit-bigbag').addClass('hidden').css('display', 'none');
    });

    // Enviar formulario de edición de BigBag vía AJAX
    $('#form-edit-bigbag').on('submit', function(e){
        e.preventDefault();
        const cliId = $('#edit-bb-cli-id').val();
        if (!cliId) return;

        const weight = parseFloat($('#edit-bb-weight').val());
        if (isNaN(weight) || weight <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peso Inválido',
                text: 'Por favor ingresa un peso válido mayor a 0 kg.'
            });
            return;
        }

        const $btn = $('#btn-save-bigbag');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin mr-1"></i> Guardando...');

        const payload = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            weight: weight,
            bag_number: $('#edit-bb-bag-number').val() || null,
            protein: $('#edit-bb-protein').val() !== '' ? parseFloat($('#edit-bb-protein').val()) : null,
            packing_list: $('#edit-bb-packing-list').val() || null,
            location_id: $('#edit-bb-location-id').val() || null
        };

        $.ajax({
            url: `/warehouse/bigbags/${cliId}`,
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(originalText);
                $('#modal-edit-bigbag').addClass('hidden').css('display', 'none');

                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'La información del BigBag ha sido actualizada.',
                    timer: 1800,
                    showConfirmButton: false
                });

                // Recargar datos y recalcular totales en el modal
                fetchBigBagsData();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                let message = 'Ocurrió un error al actualizar el BigBag.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    message = xhr.responseJSON.error;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });
});
</script>
@endpush
