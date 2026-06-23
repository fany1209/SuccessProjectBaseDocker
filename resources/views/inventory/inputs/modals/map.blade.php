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
                <!--Columna 1-->
                <div class="col-span-2 w-[100px] flex flex-col items-center p-1">
                    <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 8">Zone 8</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AM6">AM6</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AM5">AM5</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AM4">AM4</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AM3">AM3</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AM2">AM2</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AM1">AM1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AL6">AL6</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AL5">AL5</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AL4">AL4</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AL3">AL3</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AL2">AL2</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AL1">AL1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AK6">AK6</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AK5">AK5</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AK4">AK4</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AK3">AK3</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AK2">AK2</div>
                        <div class="w-full h-[32px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AK1">AK1</div>
                    </div>
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
                <!--Columna 2-->
                <div class="col-span-2 flex flex-col items-center p-1 relative">
                    <div class="flex flex-col items-center w-full border-4 border-[tan] bg-gray-300 lg:absolute left-[15px]">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 7">Zone 7</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 7">Zone 7</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full h-[120px]"></div>
                    <div class="flex flex-col items-center w-full border-4 border-[tan] bg-gray-300 lg:absolute -left-[145px] top-[200px]">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 9">Zone 9</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 10">Zone 10</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 11">Zone 11</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 12">Zone 12</div>
                    </div>
                </div>
                <!--Columna 3-->
                <div class="col-span-2 flex flex-col items-center p-1 relative">
                    <div class="flex justify-center items-center w-full">
                        <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AN1">AN1</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AN4">AN4</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AN2">AN2</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AN5">AN5</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AN3">AN3</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AN6">AN6</div>
                        </div>
                        <div class="grid grid-cols-2 grid-rows-3 w-full border-4 border-[tan] bg-gray-300">
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AO1">AO1</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AO4">AO4</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AO2">AO2</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AO5">AO5</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AO3">AO3</div>
                            <div class="w-full h-[30px] font-bold text-xs bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AO6">AO6</div>
                        </div>
                    </div>
                    <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 6">Zone 6</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 3">Zone 3</div>
                    </div>
                    <div class="flex justify-center items-center w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 5">Zone 5</div>
                        <div class="w-full h-[60px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Zone 4">Zone 4</div>
                    </div>
                    <div class="flex flex-col items-center w-full lg:w-[300px] border-4 border-[tan] bg-gray-300 lg:absolute left-[-100px] bottom-[10px]">
                        <div class="w-full h-[250px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="Inspection">Inspection</div>
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
                    <div class="grid grid-cols-3 grid-rows-2 w-full h-[120px]"></div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AC6">AC6</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AC5">AC5</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AC4">AC4</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AC3">AC3</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AC2">AC2</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AC1">AC1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AB6">AB6</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AB5">AB5</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AB4">AB4</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AB3">AB3</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AB2">AB2</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AB1">AB1</div>
                    </div>
                    <div class="grid grid-cols-3 grid-rows-2 w-full border-4 border-[tan] bg-gray-300">
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AA6">AA6</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AA5">AA5</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AA4">AA4</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AA3">AA3</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AA2">AA2</div>
                        <div class="w-full h-[40px] font-bold text-sm bg-gray-700 text-white hover:bg-blue-500 hover:text-white border-1 border-gray-50 flex items-center justify-center cursor-pointer close-modal location-map" data-target="AA1">AA1</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    chooseWHReactive();
});
</script>
@endpush