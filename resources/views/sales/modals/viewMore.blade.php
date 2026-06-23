<x-modal id="view-more">
    <input type="hidden" id="sale">
    <div class="flex flex-col items-center w-full">
        <div class="flex justify-between items-center gap-2 space-y-1 border-b-2 w-full border-dashed border-gray-300 pb-3 mb-2">
            <h1 class="text-lg font-semibold text-gray-900 tracking-wide border-s-4 border-green-700 ps-2">Sale Detail</h1>
            <div class="flex flex-col items-end gap-1">
                <p class="text-gray-700 text-md font-bold">Folio: <span id="folio" class="bg-green-600 text-white font-semibold tracking-[2px] rounded-full px-2 py-0.5 ml-1"></span></p>
                <p class="text-gray-400 text-xs italic">Creation date: <span id="date-created" class="text-gray-700 text-sm not-italic ml-1"></span></p>
            </div>
        </div>

        <div class="space-y-1 w-full border-b-2 border-dashed border-gray-300 pb-3 mb-2">
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Sector</span>
                <span id="sector" class="bg-green-600 text-white font-semibold tracking-[2px] rounded-full px-2 py-0.5"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Client</span>
                <span id="client" class="text-gray-700 font-semibold tracking-[2px] border-b-2 border-dashed border-green-700"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Seller</span>
                <span id="seller" class="text-gray-700 font-semibold tracking-[2px] border-b-2 border-dashed border-green-700"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Client Code</span>
                <span id="customer_code" class="bg-green-600 text-white font-semibold tracking-[2px] rounded-full px-2 py-0.5"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Order / Invoice</span>
                <p class="text-gray-700"><span id="order" class="font-bold"></span> / <span id="invoice" class="font-bold"></span></p>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Payment Info</span>
                <p class="text-gray-700"><span id="sale-type"></span> - <span id="term" class="italic"></span></p>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Service Date</span>
                <span id="date" class="font-semibold text-blue-600"></span>
            </div>
        </div>

        <table class="w-full text-sm text-left text-gray-700 border-separate border-spacing-y-1 border-b-2 border-dashed border-gray-300 pb-3 mb-2">
            <thead class="text-xs uppercase text-gray-500 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-2">Product</th>
                    <th class="px-3 py-2">Batch/Info</th>
                    <th class="px-3 py-2 text-right">Qty</th>
                    <th class="px-3 py-2 text-right">Cost $</th>
                    <th class="px-3 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="products">
                </tbody>
        </table>

        <div class="flex justify-end items-center w-full">
            <div class="flex flex-col items-end text-base font-semibold text-gray-900 w-2/3 gap-2">
                <span class="text-gray-500">Subtotal</span>
                <span class="text-gray-500" id="iva-label">IVA (16%)</span>
                <span>Total $</span>
            </div>
            <div class="flex flex-col items-end text-base font-semibold text-gray-900 w-1/3 gap-2">
                <span id="subtotal-general">$0.00</span>
                <span id="iva-value">$0.00</span>
                <span id="total" class="bg-gray-700 text-white font-semibold tracking-[2px] rounded-full px-3 py-1"></span>
            </div>
        </div>
    </div>
    <x-button class="close-modal mt-4" colorBtn="red">Close</x-button>
</x-modal>
@push('js')
<script>
$(function(){
    const father = $('#view-more');
    
    $(document).on('click', '.view-more-btn', function(){
        const id = $(this).data('id') || $('#sale').val(); 
        
        $.ajax({
            type: 'GET',
            url: `/sales/${id}`,
            success: function(r){
                father.find('#folio').text(`#${r.sale.folio}`);
                const date = new Date(r.sale.created_at);
                const formatted = date.toLocaleDateString("es-ES", {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                });
                father.find('#date-created').text(formatted);
                father.find('#client').text(r.sale.client_name || 'N/A');
                father.find('#seller').text(r.sale.seller_name || r.sale.seller || 'N/A');
                father.find('#order').text(r.sale.purchase_order || 'N/A');
                father.find('#invoice').text(r.sale.invoice || 'N/A');
                father.find('#sale-type').text(r.sale.sale_type);
                father.find('#term').text(r.sale.term ? r.sale.term : 'N/A');
                father.find('#date').text(r.sale.date);
                father.find('#customer_code').text(r.sale.customer_code || 'N/A');
                father.find('#sector').text(r.sale.sector_name || 'N/A');

                let subtotalGeneral = 0;
                let acumuladoIva = 0;
                
                father.find('#products').empty();

                r.sale_detail.forEach(item => {
                    const cost = parseFloat(item.cost || 0);
                    const qty = parseFloat(item.quantity || 0);
                    const subtotalLinea = cost * qty;
                    
                    const ivaLinea = (parseInt(item.has_tax) === 1) ? (subtotalLinea * 0.16) : 0;

                    father.find('#products').append(`
                        <tr class="bg-white border-b border-gray-50">
                            <td class="px-3 py-2">
                                ${item.public_product_name || item.original_product_name}
                                ${parseInt(item.has_tax) === 0 ? '<span class="text-[10px] text-orange-500 font-bold ml-1">(Exento)</span>' : ''}
                            </td>
                            <td class="px-3 py-2 text-gray-500">${item.public_batch || ''}</td>
                            <td class="px-3 py-2 text-right">${qty}</td>
                            <td class="px-3 py-2 text-right">$${cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td class="px-3 py-2 text-right font-medium">$${subtotalLinea.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                    `);

                    subtotalGeneral += subtotalLinea;
                    acumuladoIva += ivaLinea;
                });

                const granTotal = subtotalGeneral + acumuladoIva;

                const formatMoney = (val) => `$${val.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

                father.find('#subtotal-general').text(formatMoney(subtotalGeneral));
                father.find('#iva-value').text(formatMoney(acumuladoIva));
                father.find('#total').text(formatMoney(granTotal));
                father.find('#iva-label').text(acumuladoIva > 0 ? 'IVA (16%)' : 'IVA (0%)');
            }
        });
    });
});
</script>
@endpush