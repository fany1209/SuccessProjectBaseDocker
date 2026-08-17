{{--
Warehouse
Warehouse (WH maps)
Fecha de creación: 29-07-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 29-08-25
--}}
<x-modal id='infoLocation'>
    <div class="flex justify-center items-center w-full p-2 bg-green-400 rounded-md">
        <p class="location-tag text-white font-bold bg-green-400"></p>
    </div>
    <div class="info-location flex flex-col items-center w-full gap-2 overflow-y-auto h-[480px] my-2"></div>
    <x-button class="close-modal">Close</x-button>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function getInfoLocation(location){
        $.ajax({
            url: "{{ route('warehouse.getInfoLocation') }}",
            method: 'GET',
            data:{
                location:location
            },
            success: function(response) {
                $('.info-location').empty();
                if(response.products.length == 0){
                    $('.info-location').append(`
                        <div class="flex justify-center items-center w-full h-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
                        <div class="flex items-center space-x-2">
                            <svg class="w-16 h-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                            <p class="text-4xl font-semibold text-gray-600">There's not nothing</p>
                        </div>
                        </div>
                    `);
                }else{
                    response.products.forEach(product => {
                        let formattedTotal = product.total.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                        });
                        $('.info-location').append(`
                            <div class="flex flex-col items-center w-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
                                <div class="flex justify-between items-center gap-2 w-full my-1">
                                    <div class="flex flex-col items-start gap-1 w-full">
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Concept</label>
                                        <p class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                            ${product.cName}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-start gap-1 w-full">
                                        <label for="" class="text-sm font-medium text-gray-700">Product</label>
                                        <p class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                            ${product.pName}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center gap-2 w-full my-1">
                                    <div class="flex flex-col items-start gap-1 w-full">
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Batch</label>
                                        <p class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                            ${product.batch}
                                        </p>
                                    </div>
                                    ${(product.pName.includes('BGBG') && product.bag_number) ? `
                                    <div class="flex flex-col items-start gap-1 w-full">
                                        <label class="block mb-1 text-sm font-medium text-gray-700">Barcina #</label>
                                        <p class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                            ${product.bag_number}
                                        </p>
                                    </div>
                                    ` : ''}
                                </div>
                                <div class="flex justify-between items-center gap-2 w-full my-1">
                                    <div class="flex flex-col items-start gap-1 w-full">
                                        <label for="" class="text-sm font-medium text-gray-700">Quantity</label>
                                        <p class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                            ${product.quantity}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-start gap-1 w-full">
                                        <label for="" class="text-sm font-medium text-gray-700">Weight per Unit</label>
                                        <p class="w-full border border-gray-300 text-gray-400 focus:text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                                            ${product.wpu}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-start items-center gap-2 w-full my-1">
                                    <p class="text-white font-bold rounded-md bg-green-400 p-1">
                                        Total: ${formattedTotal} ${product.unit}
                                    </p>
                                </div>
                            </div>
                        `);
                    });
                }
            },
            error: function(xhr, status, error) {
                alert('Error al cargar los datos: ' + error);
            }
        });
    }
    $(document).off('click').on('click','.location-map',function(){
        //Toda función aquí para conservar la location
        let location = $(this).text();
        $('.location-tag').text(location)
        getInfoLocation(location);
    });
});
</script>
@endpush