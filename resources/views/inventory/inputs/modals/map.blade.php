<div class="search-location fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl w-full p-4 mt-16 relative">
        <div class="flex flex-col justify-center items-center gap-2 p-2">
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-tittle-form>Select a location</x-tittle-form>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label>Select a warehouse</x-label>
                    <x-select-1 class="warehouse">
                        <option value="">Select a warehouse</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </x-select-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
            <div class="map-content flex justify-center items-center w-full h-[700px] overflow-auto">
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
            <x-wrapper-form-1>
                <x-button-1 type="button" class="close-modal" colorBtn="gray">Close</x-button-1>
            </x-wrapper-form-1>
        </div>
    </div>
</div>
@push('js')
<script>
$(function(){
    const father = $('#make-input');
    const available_locations = @json($available_locations);
    function chooseWHReactive(){

        father.on('click','.location-map',function(){
            const wrapper = $(this).closest('.row-wrapper');
            const location = $(this).text();
            wrapper.find('.location_name').val(location);
        });

        father.on('change','.warehouse', function () {
            const wrapper = $(this).closest('.row-wrapper');
            const warehouse = parseInt($(this).val());
            switch(warehouse){
                case 1:
                    wrapper.find('.map-content').html(warehouse_1);
                break;
                case 2:
                    wrapper.find('.map-content').html(warehouse_2);
                break;
                default:
                    wrapper.find('.map-content').html(warning);
            }
            father.find('.location-map').each(function(){
                var location = $(this).text().trim();
                available_locations.forEach(l => {        
                    if(location === l.name){
                        $(this).removeClass('bg-gray-700 text-white hover:bg-blue-500 hover:text-white').addClass('bg-blue-500 text-white hover:bg-blue-600 hover:text-white');
                    }
                });
            });
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
                    <div class="w-full h-[120px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 2">Zone 2</div>
                </div>
                <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Quarantine">Quarantine</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Y6">Y6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Y5">Y5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Y4">Y4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Y3">Y3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Y2">Y2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Y1">Y1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full h-[120px]"></div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="X6">X6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="X5">X5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="X4">X4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="X3">X3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="X2">X2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="X1">X1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="W6">W6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="W5">W5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="W4">W4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="W3">W3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="W2">W2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="W1">W1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="V6">V6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="V5">V5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="V4">V4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="V3">V3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="V2">V2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="V1">V1</div>
                </div>
            </div>
            <!--Columna 2-->
            <div class="col-span-4 flex flex-col items-center p-1">
                <div class="flex justify-center items-center w-full">
                    <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="J6">J6</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="J5">J5</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="J4">J4</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="J3">J3</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="J2">J2</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="J1">J1</div>
                    </div>
                    <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="I6">I6</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="I5">I5</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="I4">I4</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="I3">I3</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="I2">I2</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="I1">I1</div>
                    </div>
                    <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="H6">H6</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="H5">H5</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="H4">H4</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="H3">H3</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="H2">H2</div>
                        <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="H1">H1</div>
                    </div>
                </div>
                <div class="flex justify-center items-center h-auto w-full pt-[60px] px-4">
                    <div class="flex flex-col items-center w-full">
                        <div class="grid grid-cols-3 grid-rows-1 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="U6">U6</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="U5">U5</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="U4">U4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="U3">U3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="U2">U2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="U1">U1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="T6">T6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="T5">T5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="T4">T4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="T3">T3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="T2">T2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="T1">T1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="S6">S6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="S5">S5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="S4">S4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="S3">S3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="S2">S2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="S1">S1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="R6">R6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="R5">R5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="R4">R4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="R3">R3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="R2">R2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="R1">R1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Q6">Q6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Q5">Q5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Q4">Q4</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Q3">Q3</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Q2">Q2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Q1">Q1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="P6">P6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="P5">P5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="P4">P4</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center w-full">
                        <div class="grid grid-cols-3 grid-rows-1 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="K1">K1</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="K2">K2</div>
                            <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="K3">K3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="K4">K4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="K5">K5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="K6">K6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="L1">L1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="L2">L2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="L3">L3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="L4">L4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="L5">L5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="L6">L6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="M1">M1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="M2">M2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="M3">M3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="M4">M4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="M5">M5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="M6">M6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="N1">N1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="N2">N2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="N3">N3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="N4">N4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="N5">N5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="N6">N6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="O1">O1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="O2">O2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="O3">O3</div>
                        </div>
                        <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="O4">O4</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="O5">O5</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="O6">O6</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="P1">P1</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="P2">P2</div>
                            <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="P3">P3</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Columna 3-->
            <div class="col-span-2 flex flex-col items-center p-1">
                <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[120px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 1">Zone 1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="G6">G6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="G5">G5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="G4">G4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="G3">G3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="G2">G2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="G1">G1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="F6">F6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="F5">F5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="F4">F4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="F3">F3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="F2">F2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="F1">F1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="E6">E6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="E5">E5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="E4">E4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="E3">E3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="E2">E2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="E1">E1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="D6">D6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="D5">D5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="D4">D4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="D3">D3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="D2">D2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="D1">D1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="C6">C6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="C5">C5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="C4">C4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="C3">C3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="C2">C2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="C1">C1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="B6">B6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="B5">B5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="B4">B4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="B3">B3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="B2">B2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="B1">B1</div>
                </div>
                <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="A6">A6</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="A5">A5</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="A4">A4</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="A3">A3</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="A2">A2</div>
                    <div class="w-full h-[30px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="A1">A1</div>
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
                            <div class="w-full h-full font-bold text-lg bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone P">Zone P</div>
                        </div>
                        <!--Rack ZA: 2 columnas x 3 filas (ZA13 a ZA18)-->
                        <div class="w-[96px] h-[192px] border-4 border-[tan] bg-gray-300">
                            <div class="grid grid-cols-2 grid-rows-3 w-full h-full">
                                <!-- Fila 1: ZA13, ZA16 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA13">ZA13</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA16">ZA16</div>
                                <!-- Fila 2: ZA14, ZA17 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA14">ZA14</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA17">ZA17</div>
                                <!-- Fila 3: ZA15, ZA18 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA15">ZA15</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA18">ZA18</div>
                            </div>
                        </div>
                    </div>
                    <!--Zona ZA: 4 columnas x 3 filas desde donde estaba ZA4 (25%) hasta el final de Zona P-->
                    <div class="flex justify-end w-[calc(100%-104px)] my-[12px]">
                        <div class="w-3/4 h-[80px] border-4 border-[tan] bg-gray-300">
                            <div class="grid grid-cols-4 grid-rows-3 w-full h-full">
                                <!-- Fila 1: ZA1, ZA4, ZA7, ZA10 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA1">ZA1</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA4">ZA4</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA7">ZA7</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA10">ZA10</div>
                                <!-- Fila 2: ZA2, ZA5, ZA8, ZA11 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA2">ZA2</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA5">ZA5</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA8">ZA8</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA11">ZA11</div>
                                <!-- Fila 3: ZA3, ZA6, ZA9, ZA12 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA3">ZA3</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA6">ZA6</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA9">ZA9</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZA12">ZA12</div>
                            </div>
                        </div>
                    </div>
                    <!--Fila inferior: Columnas 1, 2 y 3 debajo de Zona P-->
                    <div class="grid grid-cols-6 gap-2 w-full relative">
                        <!--Sub-columna 1: AJ, AI, AH, AG, AF-->
                        <div class="col-span-2 w-[100px] flex flex-col items-center">
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AJ6">AJ6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AJ5">AJ5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AJ4">AJ4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AJ3">AJ3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AJ2">AJ2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AJ1">AJ1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AI6">AI6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AI5">AI5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AI4">AI4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AI3">AI3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AI2">AI2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AI1">AI1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AH6">AH6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AH5">AH5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AH4">AH4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AH3">AH3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AH2">AH2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AH1">AH1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AG6">AG6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AG5">AG5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AG4">AG4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AG3">AG3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AG2">AG2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AG1">AG1</div>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AF6">AF6</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AF5">AF5</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AF4">AF4</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AF3">AF3</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AF2">AF2</div>
                                <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AF1">AF1</div>
                            </div>
                        </div>

                        <!--Sub-columna 2: ZB (ZB1 a ZB18, 3 columnas x 6 filas)-->
                        <div class="col-span-2 flex flex-col items-center relative">
                            <div class="grid grid-cols-3 grid-rows-6 w-full h-[216px] border-4 border-[tan] bg-gray-300 lg:absolute -left-[112px] top-[144px]">
                                <!-- Fila 1 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB16">ZB16</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB17">ZB17</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB18">ZB18</div>
                                <!-- Fila 2 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB13">ZB13</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB14">ZB14</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB15">ZB15</div>
                                <!-- Fila 3 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB10">ZB10</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB11">ZB11</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB12">ZB12</div>
                                <!-- Fila 4 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB7">ZB7</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB8">ZB8</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB9">ZB9</div>
                                <!-- Fila 5 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB4">ZB4</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB5">ZB5</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB6">ZB6</div>
                                <!-- Fila 6 -->
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB1">ZB1</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB2">ZB2</div>
                                <div class="w-full h-full font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZB3">ZB3</div>
                            </div>
                        </div>

                        <!--Sub-columna 3: Inspection-->
                        <div class="col-span-2 flex flex-col items-center relative">
                            <div class="flex flex-col items-center w-[calc(100%+104px)] h-[216px] border-4 border-[tan] bg-gray-300 lg:absolute -left-[104px] top-[144px]">
                                <div class="w-full h-full font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Inspection">Inspection</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Columna 4-->
                <div class="col-span-2 flex flex-col items-center p-1">
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AE6">AE6</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AE5">AE5</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AE4">AE4</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AE3">AE3</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AE2">AE2</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AE1">AE1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AD6">AD6</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AD5">AD5</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AD4">AD4</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AD3">AD3</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AD2">AD2</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AD1">AD1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full h-[40px]"></div>
                    <!-- Bloque 1: ZC1 a ZC6 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC3">ZC3</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC2">ZC2</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC1">ZC1</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC6">ZC6</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC5">ZC5</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC4">ZC4</div>
                    </div>
                    <!-- Bloque 2: ZC7 a ZC12 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC9">ZC9</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC8">ZC8</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC7">ZC7</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC12">ZC12</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC11">ZC11</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC10">ZC10</div>
                    </div>
                    <!-- Bloque 3: ZC13 a ZC18 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC15">ZC15</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC14">ZC14</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC13">ZC13</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC18">ZC18</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC17">ZC17</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC16">ZC16</div>
                    </div>
                    <!-- Bloque 4: ZC19 a ZC24 -->
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC21">ZC21</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC20">ZC20</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC19">ZC19</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC24">ZC24</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC23">ZC23</div>
                        <div class="w-full h-[40px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="ZC22">ZC22</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    chooseWHReactive();
});
</script>
@endpush