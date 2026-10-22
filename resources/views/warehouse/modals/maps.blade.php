{{--
Warehouse
Warehouse (WH maps)
Fecha de creación: 28-07-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 01-09-25
--}}
<x-modal id="maps">
    <div class="flex flex-col items-center w-full gap-2 my-2">
        <div class="flex justify-between items-center w-full">
            <h2 class="my-2 text-2xl row-span-1 row-start-1 w-1/2">Maps</h2>
            <div class="flex flex-col items-start w-1/2">
                <label for="" class="mb-1 block font-medium text-sm text-gray-700">Select a warehouse</label>
                <select id="current-warehouse-maps" class="w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2">
                    <option value="">Please, select a warehouse</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="map-content" class="flex justify-center items-center w-full h-[700px] overflow-auto">
            <div class="flex justify-center items-center w-full h-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
                <div class="flex items-center space-x-2">
                    <svg class="w-16 h-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    <p class="text-4xl font-semibold text-gray-600">Please, select a warehouse</p>
                </div>
            </div>
        </div>
    </div>
    <x-button class="close-modal">Close</x-button>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    const available_locations = @json($available_locations);
    function fillLocations(){
        $('#map-content .location-map').each(function(){
            var location = $(this).text().trim();
            available_locations.forEach(l => {                    
                if(location === l.name){
                    $(this).removeClass('bg-gray-700 text-white hover:bg-blue-500 hover:text-white').addClass('bg-blue-500 text-white hover:bg-blue-600 hover:text-white');
                }
            });
        });
    }
    function chooseWH(){
        $('#current-warehouse-maps').on('change', function () {                    
            const warehouse = parseInt($(this).val());
            switch(warehouse){
                case 1:
                    $('#map-content').html(warehouse_1);
                break;
                case 2:
                    $('#map-content').html(warehouse_2);
                break;
                default:
                    $('#map-content').html(warning);
            }
            fillLocations();
        });
    }
    const warning = `
        <div class="flex justify-center items-center w-full h-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
            <div class="flex items-center space-x-2">
                <!-- Ícono de información (Heroicons) -->
                <svg class="w-16 h-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
                <!-- Mensaje de texto -->
                <p class="text-4xl font-semibold text-gray-600">Please, select a warehouse</p>
            </div>
        </div>
    `;
    const warehouse_1 = `
        <div class="flex justify-center items-center w-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
            <div class="grid grid-cols-8 gap-2 bg-gray-200 rounded-lg w-full f-full p-1">
            <!--Columna 1-->
            <div class="col-span-2 flex flex-col items-center p-1">
                <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[120px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Zone 2</div>
                </div>
                <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Quarantine</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Y6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Y5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Y4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Y3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Y2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Y1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full h-[120px]"></div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">X6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">X5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">X4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">X3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">X2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">X1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">W6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">W5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">W4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">W3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">W2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">W1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">V6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">V5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">V4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">V3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">V2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">V1</div>
                </div>
            </div>
            <!--Columna 2-->
            <div class="col-span-4 flex flex-col items-center p-1">
                <div class="flex justify-center items-center w-full">
                    <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">J6</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">J5</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">J4</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">J3</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">J2</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">J1</div>
                    </div>
                    <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">I6</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">I5</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">I4</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">I3</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">I2</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">I1</div>
                    </div>
                    <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">H6</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">H5</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">H4</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">H3</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">H2</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">H1</div>
                    </div>
                </div>
                <div class="flex justify-center items-center h-auto w-full pt-[60px] px-4">
                    <div class="flex flex-col items-center w-full">
                        <div class="grid grid-cols-3 grid-rows-1 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">U6</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">U5</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">U4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">U3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">U2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">U1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">T6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">T5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">T4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">T3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">T2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">T1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">S6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">S5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">S4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">S3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">S2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">S1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">R6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">R5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">R4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">R3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">R2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">R1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Q6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Q5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Q4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Q3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Q2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Q1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">P6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">P5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">P4</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center w-full">
                        <div class="grid grid-cols-3 grid-rows-1 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">K1</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">K2</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">K3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">K4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">K5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">K6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">L1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">L2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">L3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">L4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">L5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">L6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">M1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">M2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">M3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">M4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">M5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">M6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">N1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">N2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">N3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">N4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">N5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">N6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">O1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">O2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">O3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">O4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">O5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">O6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">P1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">P2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">P3</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Columna 3-->
            <div class="col-span-2 flex flex-col items-center p-1">
                <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[120px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Zone 1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">G6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">G5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">G4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">G3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">G2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">G1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">F6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">F5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">F4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">F3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">F2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">F1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">E6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">E5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">E4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">E3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">E2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">E1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">D6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">D5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">D4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">D3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">D2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">D1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">C6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">C5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">C4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">C3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">C2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">C1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">B6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">B5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">B4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">B3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">B2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">B1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">A6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">A5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">A4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">A3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">A2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">A1</div>
                </div>
            </div>
            </div>
        </div>
    `;
    const warehouse_2 = `
        <div class="flex justify-center items-center w-full px-2 py-3 border-2 border-dashed border-gray-500 rounded-md">
            <div class="grid grid-cols-8 gap-2 bg-gray-200 rounded-lg w-full f-full p-1">
                <!--Secciones 1, 2 y 3 (Zona P + Racks inferiores)-->
                <div class="col-span-6 flex flex-col p-1">
                    <!--Fila superior: Zona P + Rack ZA (ZA13 a ZA18)-->
                    <div class="flex items-center w-full gap-2">
                        <!--Zone P: Rectángulo desde antigua Zona 8 / AK3 hasta AO4 / Zona 4-->
                        <div class="flex justify-center items-center w-[calc(100%-104px)] h-[192px] border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-full font-bold text-lg bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Zone P</div>
                        </div>
                        <!--Rack ZA: 2 columnas x 3 filas (ZA13 a ZA18)-->
                        <div class="w-[96px] h-[192px] border-4 border-[tan] bg-gray-300">
                            <div class="grid grid-cols-2 grid-rows-3 w-full h-full">
                                <!-- Fila 1: ZA13, ZA16 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA13</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA16</div>
                                <!-- Fila 2: ZA14, ZA17 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA14</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA17</div>
                                <!-- Fila 3: ZA15, ZA18 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA15</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA18</div>
                            </div>
                        </div>
                    </div>
                    <!--Zona ZA: 4 columnas x 3 filas desde donde estaba ZA4 (25%) hasta el final de Zona P-->
                    <div class="flex justify-end w-[calc(100%-104px)] my-[12px]">
                        <div class="w-3/4 h-[80px] border-4 border-[tan] bg-gray-300">
                            <div class="grid grid-cols-4 grid-rows-3 w-full h-full">
                                <!-- Fila 1: ZA1, ZA4, ZA7, ZA10 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA1</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA4</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA7</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA10</div>
                                <!-- Fila 2: ZA2, ZA5, ZA8, ZA11 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA2</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA5</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA8</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA11</div>
                                <!-- Fila 3: ZA3, ZA6, ZA9, ZA12 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA3</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA6</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA9</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZA12</div>
                            </div>
                        </div>
                    </div>
                    <!--Fila inferior: Columnas 1, 2 y 3 debajo de Zona P-->
                    <div class="grid grid-cols-6 gap-2 w-full relative">
                        <!--Sub-columna 1: AJ, AI, AH, AG, AF-->
                        <div class="col-span-2 w-[100px] flex flex-col items-center">
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AJ6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AJ5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AJ4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AJ3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AJ2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AJ1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AI6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AI5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AI4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AI3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AI2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AI1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AH6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AH5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AH4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AH3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AH2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AH1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AG6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AG5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AG4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AG3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AG2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AG1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AF6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AF5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AF4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AF3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AF2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AF1</div>
                            </div>
                        </div>

                        <!--Sub-columna 2: ZB (ZB1 a ZB18, 3 columnas x 6 filas)-->
                        <div class="col-span-2 flex flex-col items-center relative">
                            <div class="grid grid-cols-3 grid-rows-6 w-full h-[216px] border-4 border-[tan] bg-gray-300 lg:absolute -left-[112px] top-[144px]">
                                <!-- Fila 1 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB16</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB17</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB18</div>
                                <!-- Fila 2 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB13</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB14</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB15</div>
                                <!-- Fila 3 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB10</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB11</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB12</div>
                                <!-- Fila 4 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB7</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB8</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB9</div>
                                <!-- Fila 5 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB4</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB5</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB6</div>
                                <!-- Fila 6 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB1</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB2</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZB3</div>
                            </div>
                        </div>

                        <!--Sub-columna 3: Inspection-->
                        <div class="col-span-2 flex flex-col items-center relative">
                            <div class="flex flex-col items-center w-[calc(100%+104px)] h-[216px] border-4 border-[tan] bg-gray-300 lg:absolute -left-[104px] top-[144px]">
                                <div class="w-full h-full font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">Inspection</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Columna 4-->
                <div class="col-span-2 flex flex-col items-center p-1">
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AE6</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AE5</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AE4</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AE3</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AE2</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AE1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AD6</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AD5</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AD4</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AD3</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AD2</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">AD1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full h-[40px]"></div>
                    <!-- Bloque 1: ZC1 a ZC6 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC3</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC2</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC1</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC6</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC5</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC4</div>
                    </div>
                    <!-- Bloque 2: ZC7 a ZC12 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC9</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC8</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC7</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC12</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC11</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC10</div>
                    </div>
                    <!-- Bloque 3: ZC13 a ZC18 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC15</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC14</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC13</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC18</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC17</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC16</div>
                    </div>
                    <!-- Bloque 4: ZC19 a ZC24 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC21</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC20</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC19</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC24</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC23</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer open-modal location-map" data-target="infoLocation">ZC22</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    chooseWH();
});
</script>
@endpush