<x-modal id="prueba">
    <form id="inspection-form" method="POST" action="{{ route('quality.store') }}" class="w-full">
        @csrf

        <div class="flex flex-col items-start gap-4 mb-4 w-full">
            {{-- SUPPLIER --}}
            <div class="flex flex-col items-start gap-1 w-full">
                <x-label value="Suppliers" class="mb-2"/>
                <select id="supplier" name="supplier" class="supplier w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2" required>
                    <option value="">--Select a supplier--</option>
                </select>
            </div>

            <input type="hidden" name="supplier_name" id="supplier_name">

            <div class="w-full">
                <p class="supplier-code text-sm text-gray-700">Supplier code:</p>
            </div>

            {{-- FECHAS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                <div class="flex flex-col gap-1">
                    <x-label for="arrival_date" value="Fecha de llegada">
                        Fecha llegada
                    </x-label>
                    <input type="date" id="arrival_date" name="arrival_date"
                        class="arrival-date w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <small class="text-xs text-gray-500 arrival-hint"></small>
                </div>

                <div class="flex flex-col gap-1">
                    <x-label for="inspection_date" value="Fecha de inspección">
                        Fecha inspección
                    </x-label>
                    <input type="date" id="inspection_date" name="inspection_date"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <small class="text-xs text-gray-500 inspection-hint"></small>
                </div>
            </div>
        </div>

        {{-- ================== PRODUCTOS ================== --}}
        <div class="w-full mt-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold">Productos</h3>
                <button type="button" class="add-product inline-flex items-center rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50">
                    + Add product
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="px-3 py-2 w-[32%]">Nombre del producto</th>
                            <th class="px-3 py-2 w-[22%]">No. de lote</th>
                            <th class="px-3 py-2 w-[16%]">Cantidad</th>
                            <th class="px-3 py-2 w-[22%]">Empaque</th>
                            <th class="px-3 py-2 w-[8%] text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="product-rows">
                        {{-- Fila inicial --}}
                        <tr class="product-row">
                            <td class="px-3 py-2">
                                <input type="text" name="products[0][name]" placeholder="Ej. Harina de maíz"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="products[0][lot]" placeholder="Ej. L230901"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="products[0][qty]" min="0" step="any" placeholder="0"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 text-right" required>
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="products[0][pack]" placeholder="Ej. Saco 25 kg"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" class="remove-product text-red-600 hover:underline">Quitar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Total opcional (suma cantidades) --}}
            <div class="flex justify-end mt-2 text-sm">
                <div> Total cantidad: <span class="total-qty font-semibold">0</span></div>
            </div>
        </div>

        {{-- ================== LIBERACIÓN DE PRODUCTO ================== --}}
        <div class="w-full mt-6" id="liberacion-producto">
            <h3 class="text-center font-semibold mb-2">Liberación de producto</h3>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-green-200">
                        <tr class="text-left"> 
                            <th class="px-3 py-2 w-[50%]">Concepto</th>
                            <th class="px-3 py-2 w-[10%] text-center">Valor</th>
                            <th class="px-3 py-2 w-[15%] text-center">Evaluación</th>
                            <th class="px-3 py-2 w-[25%]">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody id="lib-rows">
                        <tr data-key="cantidad" data-valor="5">
                            <td class="px-3 py-2">Cantidad solicitada</td>
                            <td class="px-3 py-2 text-center">5</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[cantidad]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="5" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[cantidad]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr data-key="identificacion" data-valor="5">
                            <td class="px-3 py-2">Identificación del producto (nombre, lote, cantidad)</td>
                            <td class="px-3 py-2 text-center">5</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[identificacion]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="5" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[identificacion]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr data-key="empaque" data-valor="10">
                            <td class="px-3 py-2">Empaque (no roto, no sucio)</td>
                            <td class="px-3 py-2 text-center">10</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[empaque]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="10" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[empaque]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr data-key="sellado" data-valor="15">
                            <td class="px-3 py-2">Sellado (embonado de tapa, sin derrames)</td>
                            <td class="px-3 py-2 text-center">15</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[sellado]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="15" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[sellado]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr data-key="limpieza" data-valor="15">
                            <td class="px-3 py-2">Libre de materia extraña y fauna nociva</td>
                            <td class="px-3 py-2 text-center">15</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[limpieza]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="15" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[limpieza]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr data-key="caducidad" data-valor="25">
                            <td class="px-3 py-2">Fecha de caducidad vigente</td>
                            <td class="px-3 py-2 text-center">25</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[caducidad]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="25" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[caducidad]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr data-key="certificado" data-valor="25">
                            <td class="px-3 py-2">Certificado de calidad de proveedor</td>
                            <td class="px-3 py-2 text-center">25</td>
                            <td class="px-3 py-2 text-center">
                            <input type="number" name="release_eval[certificado]" class="eval w-24 text-center rounded-md border border-gray-300"
                                    min="0" max="25" step="1" value="0">
                            </td>
                            <td class="px-3 py-2">
                            <input type="text" name="release_obs[certificado]" class="w-full rounded-md border border-gray-300 px-2 py-1">
                            </td>
                        </tr>

                        <tr class="border-t font-semibold">
                            <td class="px-3 py-2">Total</td>
                            <td class="px-3 py-2 text-center">100</td>
                            <td class="px-3 py-2 text-center">
                            <span class="lib-total">0</span>
                            <input type="hidden" name="release_total" class="lib-total-input" value="0">
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                </table>
            </div>

            <div class="mt-2 text-right text-sm">
                <span>Estado de la carga: </span>
                <span class="lib-status font-bold">—</span>
                <input type="hidden" name="release_status" class="lib-status-input" value="">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">¿El proveedor tiene certificado de calidad?</label>
            <select id="has_certificate" name="has_certificate" class="border rounded px-3 py-2 w-full">
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>
        </div>

        <div id="certificate_select_wrapper" class="mb-4 hidden">
            <label for="certificate_id" class="block text-gray-700 font-medium mb-2">Seleccionar certificado</label>
            <select id="certificate_id" name="certificate_id" class="border rounded px-3 py-2 w-full">
                <option value="">-- Selecciona un certificado --</option>
            </select>
        </div>

        <div class="border rounded-lg p-4 space-y-3">
            <h2 class="font-semibold">Liberación de producto</h2>

            <div class="overflow-x-auto border rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                        <th class="px-3 py-2 w-[70%] text-left">Producto liberado</th>
                        <th class="px-3 py-2 w-[15%] text-center">Sí</th>
                        <th class="px-3 py-2 w-[15%] text-center">No</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                        <td class="px-3 py-2 text-left">Seleccione una opción</td>
                        <td class="px-3 py-2">
                            <input type="radio" class="release-radio" name="producto_liberado" value="si">
                        </td>
                        <td class="px-3 py-2">
                            <input type="radio" class="release-radio" name="producto_liberado" value="no">
                        </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
            <label for="folio_text" class="block text-sm font-medium mb-1">Folio</label>
            <textarea id="folio_text" name="folio" rows="1"
                class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm h-9 resize-none"
                placeholder="Escribe el folio aquí..."></textarea>
            </div>
        </div>

        {{-- ================== INCIDENCIAS ================== --}}
        <div class="w-full mt-6" id="incidencias">
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-green-200">
                        <tr>
                            <th class="px-3 py-2 w-[55%]">Incidencias</th>
                            <th class="px-3 py-2 w-[10%] text-center">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="incidents[has]" id="inc_yes" value="1" class="accent-green-600">
                                    <span>Sí</span>
                                </label>
                            </th>
                            <th class="px-3 py-2 w-[10%] text-center">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="incidents[has]" id="inc_no" value="0" class="accent-green-600" checked>
                                    <span>No</span>
                                </label>
                            </th>
                            <th class="px-3 py-2 w-[25%] text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="font-semibold">Folio:</span>
                                    <input type="text" name="incidents[folio]" id="inc_folio"
                                        class="w-36 rounded-md border border-gray-300 px-2 py-1 text-right"
                                        placeholder="SRI0001"
                                        list="inc_folios">
                                    <datalist id="inc_folios"></datalist>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="inc-extra">
                            <td colspan="4" class="px-3 py-2 align-top">
                                <div class="font-semibold mb-1">Descripción:</div>
                                <textarea name="incidents[description]" id="inc_desc" rows="4"
                                    class="w-full rounded-md border border-gray-300 px-2 py-2"
                                    placeholder="• Se detectaron sacos rotos y sucios.
                                                • Se observan sacos con terrones de levadura.
                                                • ..."></textarea>
                            </td>
                        </tr>

                        <tr class="inc-extra">
                            <td colspan="4" class="px-3 py-2 align-top">
                                <div class="font-semibold mb-1">Acciones que se implementaron:</div>
                                <textarea name="incidents[actions]" id="inc_actions" rows="3"
                                    class="w-full rounded-md border border-gray-300 px-2 py-2"
                                    placeholder="• Se reensacaron los sacos dañados.
                                                • Se clasificaron para devolución al proveedor.
                                                • ..."></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    {{-- =============== FIRMAS =============== --}}
    <div class="border rounded-lg p-4">
        <h2 class="font-semibold mb-3">Nombre</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
            <x-label value="Nombre de quien realizó la inspección"/>
            <input type="text" name="inspector_nombre" class="w-full rounded border border-gray-300 px-3 py-2" placeholder="Nombre del responsable">
            </div>
        </div>
    </div>
    
        {{-- ========= FOOTER DE ACCIONES ========= --}}
        <div class="mt-6 flex items-center justify-end gap-3">
            <x-button type="button" class="close-modal bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</x-button>
            <x-button type="submit" id="submit-inspection" class="bg-green-600 hover:bg-green-700">
                Guardar inspección
            </x-button>
        </div>
    </form>
</x-modal>

@push('js')
<script>
$(document).ready(function (){

    /* =========================================================
     * SUPPLIER 
     * =======================================================*/
    const slctSupplier = $('select.supplier');

    function bindSupplierChange(){ 
        slctSupplier.off('change.supp').on('change.supp', function(){
            const code = $(this).val() || '';
            const name = $(this).find('option:selected').text() || ''; 
            $('.supplier-code').text('Supplier code: ' + code);
            $('#supplier_name').val(name); 
        });
    }

    function getData(){
        $.ajax({
            url: "{{ route('quality.getDataq') }}",
            method: "GET",
            success: function (response) {
                if (!response || !Array.isArray(response.suppliers)) return;

                slctSupplier.html('<option value="">--Select a supplier--</option>');

                // 🔹 Ordenar proveedores alfabéticamente por nombre
                response.suppliers
                    .sort((a, b) => (a.name ?? '').localeCompare(b.name ?? ''))
                    .forEach(supplier => {
                        const code = supplier.supplier_code ?? '';
                        const name = supplier.name ?? '';
                        if (code) {
                            slctSupplier.append(
                                `<option value="${code}" data-name="${name}">${name}</option>`
                            );
                        }
                    });

                bindSupplierChange();        
                slctSupplier.trigger('change'); 
            },
            error: function(xhr, status, error) {
                console.error('Request error (suppliers):', status, error);
            }
        });
    }


    /* =========================================================
     * FECHAS (manuales)
     * =======================================================*/
    const $arrival    = $('#arrival_date');
    const $inspection = $('#inspection_date');
    const $release    = $('#release_date');

    const fmt = (v) => {
        if(!v) return '';
        const d  = new Date(v + 'T00:00:00');
        const dd = String(d.getDate()).padStart(2,'0');
        const mm = String(d.getMonth()+1).padStart(2,'0');
        const yy = d.getFullYear();
        return `${dd}/${mm}/${yy}`;
    };

    function setMinMax() {
        $inspection.attr('min', $arrival.val() || '');
        $release.attr('min', $inspection.val() || ($arrival.val() || ''));
    }

    function showHints() {
        $('.arrival-hint').text($arrival.val()     ? `Seleccionada: ${fmt($arrival.val())}`     : '');
        $('.inspection-hint').text($inspection.val()? `Seleccionada: ${fmt($inspection.val())}` : '');
        $('.release-hint').text($release.val()     ? `Seleccionada: ${fmt($release.val())}`     : '');
    }

    function validateOrder() {
        const a = $arrival.val();
        const i = $inspection.val();
        const r = $release.val();
        if (a && i && i < a) {
            alert('Inspection cannot take place prior to arrival.');
            $inspection.val('');
        }
        if (i && r && r < i) {
            alert('Release cannot occur prior to inspection.');
            $release.val('');
        }
    }

    $arrival.on('change', function(){ setMinMax(); validateOrder(); showHints(); });
    $inspection.on('change', function(){ setMinMax(); validateOrder(); showHints(); });
    $release.on('change', function(){ setMinMax(); validateOrder(); showHints(); });
    let productsData = []; 

    function fetchProducts(){
        return $.ajax({
            url: "{{ route('quality.getProducts') }}",
            method: "GET",
            success: function (response) {
                if (response && Array.isArray(response.products)) {
                    productsData = response.products;
                } else if (response && Array.isArray(response.inventory)) {
                    const byProd = {};
                    response.inventory.forEach(row => {
                        const pid = row.product_id;
                        if (!pid) return;
                        if (!byProd[pid]) byProd[pid] = { product_id: pid, name: row.name, batches: [] };
                        if (row.batch && !byProd[pid].batches.includes(row.batch)) {
                            byProd[pid].batches.push(row.batch);
                        }
                    });
                    productsData = Object.values(byProd);
                } else {
                    productsData = [];
                }
            },
            error: function(xhr, status, error) {
                console.error('Request error (products):', status, error);
            }
        });
    }

    function productOptionsHTML(){
        let html = `<option value="">Seleccione…</option>`;
        productsData.forEach(p => {
            html += `<option value="${$('<div>').text(p.name ?? '').html()}" data-pid="${p.product_id}">${$('<div>').text(p.name ?? '').html()}</option>`;
        });
        return html;
    }

    function batchOptionsHTML(pid){
        let html = `
            <option value="">Seleccione…</option>
            <option value="no-proporcionado">Lote no proporcionado</option>
        `;

        const prod = productsData.find(p => String(p.product_id) === String(pid));
        if (prod && Array.isArray(prod.batches)) {
            prod.batches.forEach(b => {
                const safeBatch = $('<div>').text(b ?? '').html(); 
                html += `<option value="${safeBatch}">${safeBatch}</option>`;
            });
        }
        return html;
    }

    const $tbody    = $('.product-rows');
    const $totalQty = $('.total-qty');

    function reindexProductRows(){
        $tbody.find('tr.product-row').each(function(i){
            $(this).find('input, select, textarea').each(function(){
                const name = $(this).attr('name');
                if(!name) return;
                const newName = name.replace(/products\[\d+\]/, 'products['+i+']');
                $(this).attr('name', newName);
            });
        });
    }

    function calcTotalQty(){
        let total = 0;
        $tbody.find('input[name*="[qty]"]').each(function(){
            const v = parseFloat($(this).val());
            if(!isNaN(v)) total += v;
        });
        $totalQty.text(Number.isInteger(total) ? total : total.toFixed(2));
    }

    function rowTemplate(index){
        return `
        <tr class="product-row">
            <td class="px-3 py-2">
                <select name="products[${index}][name]" class="product-select w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    ${productOptionsHTML()}
                </select>
            </td>
            <td class="px-3 py-2">
                <select name="products[${index}][lot]" class="batch-select w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" disabled>
                    <option value="">Seleccione…</option>
                </select>
            </td>
            <td class="px-3 py-2">
                <input type="number" name="products[${index}][qty]" min="0" step="any" placeholder="0"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 text-right">
            </td>
            <td class="px-3 py-2">
                <input type="text" name="products[${index}][pack]" placeholder="Ej. Saco 25 kg"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="remove-product text-red-600 hover:underline">Quitar</button>
            </td>
        </tr>`;
    }

    function initRow($row){
        const $prod = $row.find('select.product-select');
        const pid   = $prod.find('option:selected').data('pid');
        const $lot  = $row.find('select.batch-select');
        if (pid) {
            $lot.html(batchOptionsHTML(pid)).prop('disabled', false);
        } else {
            $lot.html('<option value="">Seleccione…</option>').prop('disabled', true);
        }
    }

    $(document).on('change', 'select.product-select', function(){
        const pid  = $(this).find('option:selected').data('pid');
        const $lot = $(this).closest('tr').find('select.batch-select');
        if (pid) {
            $lot.html(batchOptionsHTML(pid)).prop('disabled', false);
        } else {
            $lot.html('<option value="">Seleccione…</option>').prop('disabled', true);
        }
    });

    $(document).on('click', '.add-product', function(){
        const nextIdx = $tbody.find('tr.product-row').length;
        $tbody.append(rowTemplate(nextIdx));
        const $newRow = $tbody.find('tr.product-row').last();
        initRow($newRow);
    });

    $(document).on('click', '.remove-product', function(){
        const rows = $tbody.find('tr.product-row').length;
        if (rows <= 1) {
            $(this).closest('tr').find('input, select').val('');
            $(this).closest('tr').find('select.batch-select').html('<option value="">Seleccione…</option>').prop('disabled', true);
            calcTotalQty();
        } else {
            $(this).closest('tr').remove();
            reindexProductRows();
            calcTotalQty();
        }
    });

    $(document).on('input', 'input[name*="[qty]"]', function(){
        calcTotalQty();
    });

    /* =========================================================
     * Inicialización
     * =======================================================*/
    bindSupplierChange();
    getData();
    setMinMax();
    showHints();

    fetchProducts().then(function(){
        $tbody.find('tr.product-row').each(function(){
            const $nameCell = $(this).find('td').eq(0);
            const $lotCell  = $(this).find('td').eq(1);
            if ($nameCell.find('select.product-select').length === 0) {
                const idx = $tbody.find('tr.product-row').index(this);
                $nameCell.html(`
                    <select name="products[${idx}][name]" class="product-select w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        ${productOptionsHTML()}
                    </select>
                `);
            }
            if ($lotCell.find('select.batch-select').length === 0) {
                const idx = $tbody.find('tr.product-row').index(this);
                $lotCell.html(`
                    <select name="products[${idx}][lot]" class="batch-select w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" disabled>
                        <option value="">Seleccione…</option>
                    </select>
                `);
            }
            initRow($(this));
        });
        calcTotalQty();
    });
    // ===== Liberación de producto: cálculo de total y estado =====
    const LIB_THRESHOLD = 70; 

    function clampInt(v, min, max){
    let n = parseInt(v, 10);
    if (isNaN(n)) n = 0;
    if (n < min) n = min;
    if (n > max) n = max;
    return n;
    }

    function calcLiberacion(){
    let total = 0;

    $('#lib-rows tr[data-valor]').each(function(){
        const valor  = parseInt($(this).data('valor'), 10) || 0;
        const $input = $(this).find('input.eval');
        const val    = clampInt($input.val(), 0, valor);
        if (String(val) !== String($input.val())) $input.val(val);
        total += val;
    });

    $('.lib-total').text(total);
    $('.lib-total-input').val(total);

    const $status = $('.lib-status');
    const $statusInput = $('.lib-status-input');
    if (total >= LIB_THRESHOLD) {
        $status.text('ACEPTABLE').removeClass('text-red-600').addClass('text-green-600');
        $statusInput.val('ACEPTABLE');
    } else {
        $status.text('NO ACEPTABLE').removeClass('text-green-600').addClass('text-red-600');
        $statusInput.val('NO ACEPTABLE');
    }
    }

    $(document).on('input change', '#lib-rows input.eval', calcLiberacion);

    calcLiberacion();

    function syncIncidenciasUI(){
    const hasInc = $('input[name="incidents[has]"]:checked').val() === '1';
    const $folio = $('#inc_folio');
    const $desc  = $('#inc_desc');
    const $acts  = $('#inc_actions');
    const $insp  = $('#inc_inspector');

    if (hasInc) {
        $('.inc-extra').removeClass('hidden');
        $desc.prop('disabled', false);
        $acts.prop('disabled', false);
        $insp.prop('disabled', false);

        $folio.prop('readonly', false).prop('disabled', false).attr('required', true);
        if ($folio.val() === 'NA') $folio.val('');
    } else {
        $('.inc-extra').addClass('hidden');
        $desc.val('').prop('disabled', true);
        $acts.val('').prop('disabled', true);
        $insp.val('').prop('disabled', true);

        $folio.val('NA').prop('readonly', true).prop('disabled', false).removeAttr('required');
    }
    }

    $(document).on('change', 'input[name="incidents[has]"]', syncIncidenciasUI);

    syncIncidenciasUI();

    /* =========================================================
    * INCIDENCIAS
    * =======================================================*/
    (function(){
    const $folio = $('#inc_folio');
    const $desc  = $('#inc_desc');
    const $list  = $('#inc_folios');
    let mapDesc = {};

    function loadIncidenciasDatalist(){
        $list.empty();
        mapDesc = {};

        $.getJSON("{{ route('quality.incidencias') }}", function(rows){
        (rows || []).forEach(r => {
            const folio = r.folio || '';
            let txt     = r.descripcion || '';

            try {
            const arr = JSON.parse(txt);
            if (Array.isArray(arr)) txt = '• ' + arr.map(x => String(x).trim()).filter(Boolean).join('\n• ');
            } catch(e){ }

            mapDesc[folio] = txt;
            const preview = (txt.split(/\r?\n/)[0] || '').slice(0, 100);
            $list.append($('<option>', { value: folio, label: preview }));
        });
        });
    }

    $folio.on('change', function(){
        const f = $(this).val().trim();
        if (mapDesc[f]) {
        $desc.val(mapDesc[f]);
        }
    });

    $(document).on('change', 'input[name="incidents[has]"]', function(){
        const hasInc = $('input[name="incidents[has]"]:checked').val() === '1';
        if (hasInc && $list.children().length === 0) {
        loadIncidenciasDatalist();
        }
    });

    if ($('input[name="incidents[has]"]:checked').val() === '1') {
        loadIncidenciasDatalist();
    }
    })();

    });

    $(document).on('submit', '#inspection-form', function () {
    const hasInc = $('input[name="incidents[has]"]:checked').val() === '1';
    const $folio = $('#inc_folio');

    $folio.prop('disabled', false);

    if (!hasInc) {
        $folio.val('NA');
    } else {
        if (!$folio.val().trim()) {
        alert('Selecciona o escribe el folio de incidencia.');
        $folio.focus();
        return false; 
        }
    }
    });

    $(document).ready(function(){
        $('#has_certificate').on('change', function(){
            if($(this).val() == "1"){
                $('#certificate_select_wrapper').removeClass('hidden');

                $.get("{{ route('supplier_certificates.index') }}", function(response){
                    let select = $('#certificate_id');
                    select.empty().append('<option value="">-- Selecciona un certificado --</option>');
                    
                    (response.certificates ?? []).forEach(cert => {
                        select.append(`<option value="${cert.id}">${cert.supplier_name} - ${cert.product_name}</option>`);
                    });
                });

            } else {
                $('#certificate_select_wrapper').addClass('hidden');
                $('#certificate_id').val('');
            }
        });
    });
</script>
@endpush
