<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
        height: 38px !important;
        border-radius: 0.375rem !important; 
        display: flex; align-items: center;
    }
    .input-with-list {
        width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem;
        padding: 0.5rem; font-size: 0.875rem; height: 38px;
    }
    .input-with-list:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
</style>

<datalist id="companyOptions">
    @if(isset($db_customers))
        @foreach($db_customers as $customer)
            <option value="{{ $customer->name }}">
        @endforeach
    @endif
</datalist>

<datalist id="presentationOptions">
    <option value="Saco de polipropileno">
    <option value="Saco de papel kraft">
    <option value="Supersaco de polipropileno">
    <option value="Tambo">
    <option value="Bidón">
    <option value="Granel">
    <option value="Cubeta">
    <option value="Big bag">
</datalist>

<form id="quoteForm" action="{{ route('quotes.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
     <div>
            <label class="block font-bold text-gray-700">Folio</label>
            <input type="text" 
                name="folio" 
                value="SCT-XXX (Consecutivo)" 
                class="w-full border-blue-300 rounded p-2 bg-blue-100 font-mono font-bold text-blue-800 italic" 
                readonly 
                required>
            <p class="text-[10px] text-blue-500 mt-1 uppercase tracking-wider font-semibold">
                * El folio real se asignará automáticamente al guardar
            </p>
        </div>
        <div>
            <label class="block font-bold text-gray-700">Empresa (Company)</label>
            <input type="text" name="company" list="companyOptions" class="input-with-list" placeholder="Elegir cliente o escribir nuevo..." required>
        </div>
        <div>
            <label class="block font-bold text-gray-700">Fecha</label>
            <input type="date" name="date" class="w-full border-gray-300 rounded p-2" value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
        <div><label class="block font-bold text-gray-700">Atención</label><input type="text" name="attention" class="w-full border-gray-300 rounded p-2"></div>
        <div><label class="block font-bold text-gray-700">Departamento</label><input type="text" name="department" class="w-full border-gray-300 rounded p-2"></div>
        <div><label class="block font-bold text-gray-700">Teléfono</label><input type="text" name="phone" class="w-full border-gray-300 rounded p-2"></div>
    </div>

    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 uppercase italic text-xs tracking-wider">Productos / Requerimientos</h3>
            <button type="button" id="addProductBtn" class="bg-blue-600 text-white px-4 py-1 rounded-full hover:bg-blue-700 font-bold text-lg">+</button>
        </div>
        
        <div class="hidden lg:flex gap-2 mb-2 px-2 text-[10px] font-bold text-gray-400 uppercase text-center border-b pb-2">
            <div class="w-full lg:w-1/3 text-left">Descripción / Requerimiento</div>
            <div class="w-full lg:w-1/6 text-left pl-2">Presentación</div>
            <div class="w-24">Unidad</div> 
            <div class="w-20">Cant.</div>
            <div class="w-28">Precio Unit.</div>
            <div class="w-24">IVA</div>
            <div class="w-32">Importe</div>
            <div class="w-10"></div>
        </div>

        <div id="productsContainer" class="space-y-1"></div>

        <div class="mt-6 flex flex-col items-end border-t border-gray-100 pt-4">
            <div class="w-full md:w-80 space-y-1">
                <div class="flex justify-between text-gray-600 px-2"><span>Subtotal:</span><span class="font-bold font-mono">$<span id="subtotal_display">0.00</span></span></div>
                <div class="flex justify-between text-gray-500 px-2"><span>IVA:</span><span class="font-bold font-mono">$<span id="iva_display">0.00</span></span></div>
                <div class="flex justify-between text-blue-800 bg-blue-50 p-2 rounded mt-2"><span class="font-bold">Total General:</span><span class="font-bold font-mono text-xl">$<span id="total_display">0.00</span></span></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-sm">
        <div class="space-y-4">
            <div><label class="block font-bold text-gray-700">Lugar de Entrega</label><input type="text" name="place_of_delivery" class="w-full border-gray-300 rounded p-2"></div>
            <div><label class="block font-bold text-gray-700">Especificación de Transporte</label><input type="text" name="transport_specification" class="w-full border-gray-300 rounded p-2"></div>
            <div><label class="block font-bold text-gray-700">Tiempo de Entrega</label><input type="date" name="deadline" class="w-full border-gray-300 rounded p-2"></div>
        </div>
        <div class="space-y-4">
            <div><label class="block font-bold text-gray-700">Términos y Condiciones</label><textarea name="terms" rows="2" class="w-full border-gray-300 rounded p-2"></textarea></div>
            <div><label class="block font-bold text-gray-700">Nota Adicional</label><textarea name="notes" rows="2" class="w-full border-gray-300 rounded p-2"></textarea></div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center border-t border-gray-200 pt-6 gap-4">
        <div class="w-full md:w-1/3 text-sm">
            <label class="block font-bold text-gray-700 mb-1">Estatus:</label>
            <select name="quotes_status_id" class="w-full border-gray-300 rounded p-2 bg-white">
                <option value="1">Borrador / Pendiente</option><option value="2">Enviada</option><option value="3">Aceptada</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="button" id="calculateBtn" class="bg-gray-800 text-white px-6 py-2 rounded-md font-bold">Calcular</button>
            <button type="submit" class="bg-green-600 text-white px-10 py-2 rounded-md font-bold hover:bg-green-700">Guardar Cotización</button>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const productsContainer = document.getElementById('productsContainer');
    const addProductBtn = document.getElementById('addProductBtn');
    let productIndex = 0;

    function initSelect2(row) {
        $(row).find('.product-select').select2({
            placeholder: "-- Buscar Producto --",
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            $(this).closest('.product-row').find('.hidden-product-name').val(selectedText);
            calculateTotals();
        });
    }

   function rowTemplate(idx) {
        return `
        <div class="flex flex-wrap lg:flex-nowrap gap-2 items-center product-row border-b border-gray-50 py-2 hover:bg-gray-50">
            <div class="w-full lg:w-1/3">
                <select name="products[${idx}][product_id]" class="product-select w-full">
                    <option value="">-- Buscar Producto --</option>
                    @foreach($db_products->sortBy('name') as $product)
                        <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="products[${idx}][quote_product_name]" class="hidden-product-name">
            </div>
            <div class="w-full lg:w-1/6">
                <input type="text" name="products[${idx}][presentation]" list="presentationOptions" class="input-with-list" placeholder="Escribir...">
            </div>
            <div class="w-24"><input type="text" name="products[${idx}][unit]" placeholder="Kg" class="w-full border-gray-300 rounded text-sm p-1.5"></div>
            <div class="w-20"><input type="number" step="0.001" name="products[${idx}][quantity]" class="qty-input w-full border-gray-300 rounded text-sm p-1.5 text-center" value="1"></div>
            <div class="w-28"><input type="number" step="0.01" name="products[${idx}][cost]" class="cost-input w-full border-gray-300 rounded text-sm p-1.5 text-center" value="0"></div>
            
            <div class="w-24">
                <select name="products[${idx}][iva]" class="iva-select w-full border-gray-300 rounded text-sm p-1.5">
                    <option value="0.16">16%</option>
                    <option value="0.00">0%</option>
                </select>
            </div>

            <div class="w-full lg:w-32 text-right pr-2 font-mono font-bold text-gray-700">$ <span class="import">0.00</span></div>
            <div class="w-10 text-center"><button type="button" class="remove-row text-red-300 hover:text-red-600 text-xl font-bold">×</button></div>
        </div>`;
    }

    function calculateTotals() {
        let subtotal = 0, iva = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const c = parseFloat(row.querySelector('.cost-input').value) || 0;
            const i = parseFloat(row.querySelector('.iva-select').value) || 0;
            const lineSub = q * c;
            subtotal += lineSub;
            iva += lineSub * i;
            row.querySelector('.import').textContent = lineSub.toLocaleString('en-US', {minimumFractionDigits: 2});
        });
        document.getElementById('subtotal_display').textContent = subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('iva_display').textContent = iva.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('total_display').textContent = (subtotal + iva).toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    if (productsContainer) {
        productsContainer.addEventListener('input', calculateTotals);
        $(productsContainer).on('change', '.iva-select', calculateTotals);
        productsContainer.addEventListener('click', e => {
            if(e.target.classList.contains('remove-row')) { e.target.closest('.product-row').remove(); calculateTotals(); }
        });
    }

    addProductBtn.addEventListener('click', () => {
        productsContainer.insertAdjacentHTML('beforeend', rowTemplate(productIndex++));
        initSelect2(productsContainer.lastElementChild);
    });

    document.getElementById('calculateBtn').addEventListener('click', calculateTotals);

    document.addEventListener('DOMContentLoaded', () => {
        if (productsContainer.children.length === 0) { addProductBtn.click(); }
        calculateTotals();
    });

    $('#quoteForm').submit(function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Guardando...', didOpen: () => Swal.showLoading() });
        $.ajax({
            type: 'POST', url: $(this).attr('action'), data: new FormData(this), processData: false, contentType: false,
            success: function() { Swal.fire({ icon: 'success', title: 'Éxito' }).then(() => window.location.href = "{{ route('quotes') }}"); },
            error: function() { Swal.fire({ icon: 'error', title: 'Error' }); }
        });
    });
</script>