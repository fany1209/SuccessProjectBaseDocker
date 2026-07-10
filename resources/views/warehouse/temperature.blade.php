<section class="col-span-12 w-full flex flex-col items-center py-4 px-4">
    <div class="bg-white mt-6 flex flex-col items-center justify-center">
        <div class="w-full text-center mb-2 flex items-center justify-center py-2 px-2">
            <div class="px-4 py-2 my-4 bg-gray-100 rounded-lg shadow-md">
                <p class="text-3xl font-semibold tracking-widest text-g uppercase">temperature and humidity control</p>
            </div>
        </div>
        
        {{-- COLORS --}}
        <div class="max-w-xl w-full mb-4 flex items-center justify-center">
            <div class="w-full flex space-x-2 bg-gray-200 rounded-md px-2 mx-2 py-2 shadow-md">
                <div id="red" class="w-full h-10 bg-red-500 rounded-md hover:scale-110 transition duration-150 shadow-md shadow-red-800" onclick="setColor('red')"></div>
                <div id="green" class="w-full h-10 bg-green-500 rounded-md hover:scale-110 transition duration-150 shadow-md shadow-green-800" onclick="setColor('green')"></div>
                <div id="yellow" class="w-full h-10 bg-yellow-500 rounded-md hover:scale-110 transition duration-150 shadow-md shadow-yellow-800" onclick="setColor('yellow')"></div>
                <div id="blue" class="w-full h-10 bg-blue-500 rounded-md hover:scale-110 transition duration-150 shadow-md shadow-blue-800" onclick="setColor('blue')"></div>
                <div id="gray" class="w-full h-10 bg-gray-800 rounded-md hover:scale-110 transition duration-150 shadow-md shadow-gray-800" onclick="setColor('gray')"></div>
                <div id="orange" class="w-full h-10 bg-orange-500 rounded-md hover:scale-110 transition duration-150 shadow-md shadow-orange-800" onclick="setColor('orange')"></div>
            </div>
        </div>
        
        {{-- WATCH --}}
        <div class="max-w-xl w-full flex items-center justify-center px-2">
            <div class="flex items-center justify-center bg-gray-200 w-full py-4 rounded-md overflow-hidden px-2 shadow-md">
                <div id='watch-left' class="h-20 w-36 bg-orange-500 transition-colors duration-500 rounded-l-xl"></div>
                <div id='watch-main' class="h-52 w-52 bg-orange-500 opacity-90 rounded-3xl px-2.5 py-2.5 flex items-center justify-center shadow-md shadow-gray-900 transition-colors duration-500">
                    <div class="bg-gray-100 hover:bg-white py-1 rounded-3xl overflow-hidden h-48 w-48 flex flex-col items-center shadow-sm hover:shadow-slate-50">
                        <div class="flex">
                            <p id='hours' class="text-5xl font-bold">00</p>
                            <p class="text-5xl font-bold animate-pulse">:</p>
                            <p id='minutes' class="text-5xl font-bold">00</p>
                        </div>
                        <div class="flex space-x-2">
                            <p id='dayName' class="text-2xl font-bold">--</p>
                            <p id='monthName' class="text-2xl font-bold">--</p>
                            <p id='dayNum' class="text-2xl font-bold">--</p>
                        </div>
                        <div>
                            <p id='year' class="text-2xl font-bold">--</p>
                        </div>
                        <div class="w-full h-full flex items-end justify-center">
                            <p class="text-xl font-bold">Week: {{ now()->format('W') }}</p>
                        </div>
                    </div>
                </div>
                <div id='watch-right' class="h-20 w-36 bg-orange-500 transition-colors duration-500 rounded-r-xl flex items-center justify-center space-x-5">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="h-3 w-2 bg-gray-200 shadow-inner shadow-black rounded-sm"></div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Semáforo de Horas --}}
        <div class="w-full max-w-xl px-2">
            <div class="bg-gray-200 mt-4 px-4 flex justify-center items-center space-x-5 rounded-md shadow-md">
                <div id="hours-container" class="bg-gray-200 mt-4 px-4 flex flex-wrap justify-center items-center space-x-5 rounded-md shadow-md">
                    @foreach ($hours as $i => $hour)
                        <div id="hour-{{ $i }}" class="bg-red-200 h-24 w-32 flex flex-row items-center justify-center my-4 py-6 rounded-md shadow-md space-x-4 transition-all duration-300">
                            <p class="font-bold text-lg">{{ $hour }}</p>
                            <img id="hour-icon-{{ $i }}" src="{{ asset('images/uncheck.png') }}" width="36" alt="">
                        </div>
                    @endforeach
                </div>
            </div>
            
            {{-- SELECT DE ALMACÉN --}}
            <div class="bg-gray-200 w-full mt-4 rounded-md shadow-md px-2 py-2">
                <label for="selected_warehouse" class="block text-sm font-medium text-gray-700">Warehouse</label>
                <select id="selected_warehouse" class="block w-full mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                    <option value="">None</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->warehouse_id }}" {{ (request()->query('warehouse_id') == $warehouse->warehouse_id) ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- FORMULARIO DIARIO TOTAL --}}
            <div id="measurement-form-container" class="hidden bg-gray-200 w-full mt-4 rounded-md py-4 px-4 space-y-4 shadow-md">
                <p class="font-bold text-lg text-gray-800 border-b pb-2">Daily Measurements Registration</p>
                
                @foreach ($hours as $i => $hour)
                    <div class="hour-row bg-gray-300 rounded-md p-3 flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4" data-index="{{ $i }}" data-hour="{{ $hour }}">
                        <div class="w-24 flex flex-col justify-center">
                            <span class="font-bold text-gray-700 text-base">{{ $hour }}</span>
                            <span class="status-label text-xs font-semibold hidden"></span>
                        </div>

                        <div class="w-full">
                            <label class="block text-xs font-medium text-gray-600">Temperature</label>
                            <input type="text" id="temp-{{ $i }}" class="temp-input w-full mt-1 border-gray-300 rounded-md text-sm p-1.5 disabled:bg-gray-100 disabled:text-gray-400" placeholder="-- °C">
                        </div>

                        <div class="w-full">
                            <label class="block text-xs font-medium text-gray-600">Humidity</label>
                            <input type="text" id="hum-{{ $i }}" class="hum-input w-full mt-1 border-gray-300 rounded-md text-sm p-1.5 disabled:bg-gray-100 disabled:text-gray-400" placeholder="-- %">
                        </div>
                    </div>
                @endforeach

                {{-- Botón único para guardar todo --}}
                <div class="py-1 flex items-center justify-end border-t pt-3">
                    <button type="button" id="btnSaveAll" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition duration-150 shadow-md">
                        Save Measurements
                    </button>
                </div>
            </div>

            {{-- MENSAJE ERROR --}}
            @if ($errors->has('in_time'))
                <div class="w-full bg-red-500 mt-4 rounded-md text-center py-2">
                    <p class="text-white font-bold">Sorry, your registration is out of time</p>
                </div>
            @endif
        </div>
    </div>
</section>

@push('js')
<script>
    function setColor(color) {
        selected = document.getElementById(color);
        watch_left = document.getElementById("watch-left");
        watch_main = document.getElementById("watch-main");
        watch_right = document.getElementById("watch-right");
        watch_left.classList.forEach(className => { if (className.includes('bg')) watch_left.classList.remove(className); });
        watch_main.classList.forEach(className => { if (className.includes('bg')) watch_main.classList.remove(className); });
        watch_right.classList.forEach(className => { if (className.includes('bg')) watch_right.classList.remove(className); });
        if (color == 'gray') {
            watch_left.classList.add("bg-" + color + "-800"); watch_main.classList.add("bg-" + color + "-800"); watch_right.classList.add("bg-" + color + "-800");
        } else {
            watch_left.classList.add("bg-" + color + "-500"); watch_main.classList.add("bg-" + color + "-500"); watch_right.classList.add("bg-" + color + "-500");
        }
    }
    
    var pHours = document.getElementById('hours');
    var pMinutes = document.getElementById('minutes');
    var pDayName = document.getElementById('dayName');
    var pMonth = document.getElementById('monthName');
    var pDayNum = document.getElementById('dayNum');
    var pYear = document.getElementById('year');
    const dateAux = new Date();
    
    function setHours(num) { pHours.textContent = num < 10 ? '0' + num : num; }
    function setMinutes(num) { pMinutes.textContent = num < 10 ? '0' + num : num; }
    let prev = { h: null, m: null }
    function setYear() { pYear.textContent = dateAux.getFullYear(); }
    function setMonth() {
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        pMonth.textContent = monthNames[dateAux.getMonth()];
    }
    function setDay() {
        const dayNames = ["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"];
        pDayName.textContent = dayNames[dateAux.getDay()]; pDayNum.textContent = dateAux.getDate();
    }
    
    setInterval(function() {
        const date = new Date();
        if (prev.h !== date.getHours()) { prev.h = date.getHours(); setHours(date.getHours()); }
        if (prev.m !== date.getMinutes()) { prev.m = date.getMinutes(); setMinutes(date.getMinutes()); }
    }, 300);
    
    setDay(); setMonth(); setYear();

$(document).ready(function() {

    $('#selected_warehouse').on('change', function() {
        let warehouseId = $(this).val();
        
        if (!warehouseId) {
            $('#measurement-form-container').addClass('hidden');
            resetFormState();
            return;
        }

        $.ajax({
            url: window.location.pathname, 
            method: 'GET',
            data: { warehouse_id: warehouseId },
            dataType: 'json', 
            cache: false, 
            success: function(data) {
                updateFormUI(data.feedback);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Error al conectar con el servidor.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });

    function updateFormUI(feedbackArray) {
        if($('#selected_warehouse').val()) {
            $('#measurement-form-container').removeClass('hidden');
        }

        $('.hour-row').each(function() {
            let row = $(this);
            let idx = row.data('index'); 
            let isRegistered = feedbackArray[idx] === true || feedbackArray[idx] === "true";

            let tempInput = row.find('.temp-input');
            let humInput = row.find('.hum-input');
            let statusLabel = row.find('.status-label');

            $('#hour-' + idx).removeClass('bg-red-200 bg-green-200');

            if (isRegistered) {
                $('#hour-' + idx).addClass('bg-green-200');
                $('#hour-icon-' + idx).attr('src', '{{ asset('images/check.png') }}');
                
                tempInput.prop('disabled', true).val('').attr('placeholder', 'Registered');
                humInput.prop('disabled', true).val('').attr('placeholder', 'Registered');
                statusLabel.removeClass('hidden').removeClass('text-red-600').addClass('text-green-600').text('(Saved)');
            } else {
                $('#hour-' + idx).addClass('bg-red-200');
                $('#hour-icon-' + idx).attr('src', '{{ asset('images/uncheck.png') }}');
                
                tempInput.prop('disabled', false).attr('placeholder', '-- °C').val('');
                humInput.prop('disabled', false).attr('placeholder', '-- %').val('');
                statusLabel.addClass('hidden');
            }
        });
    }

    function resetFormState() {
        $('.temp-input, .hum-input').val('').prop('disabled', false);
        $('.status-label').addClass('hidden');
        $('[id^="hour-"]').removeClass('bg-green-200').addClass('bg-red-200');
        $('[id^="hour-icon-"]').attr('src', '{{ asset('images/uncheck.png') }}');
    }

    let initialWarehouse = $('#selected_warehouse').val();
    if (initialWarehouse) {
        let initialFeedback = {!! json_encode($feedback ?? [false, false, false]) !!};
        updateFormUI(initialFeedback);
    }

    $(document).on('click', '#btnSaveAll', function(e) {
        e.preventDefault();
        
        let warehouse = $('#selected_warehouse').val();
        let measurementsData = [];
        let validationFailed = false;
        let processedIndexes = [];

        if(!warehouse) {
            Swal.fire({
                icon: 'warning',
                title: 'No Warehouse Selected',
                text: 'Please select a warehouse first.',
                confirmButtonColor: '#16a34a'
            });
            return;
        }

        $('.hour-row').each(function() {
            let row = $(this);
            let index = row.data('index');
            let hour = row.data('hour');
            let tempInput = row.find('.temp-input');
            let humInput = row.find('.hum-input');

            if (tempInput.is(':disabled')) return;

            let tempValue = tempInput.val().trim();
            let humValue = humInput.val().trim();

            if ((tempValue && !humValue) || (!tempValue && humValue)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Fields',
                    text: 'Please fill both Temperature and Humidity fields for ' + hour,
                    confirmButtonColor: '#16a34a'
                });
                validationFailed = true;
                return false; 
            }

            if (tempValue && humValue) {
                measurementsData.push({
                    hour: hour,
                    temperature: tempValue,
                    humidity: humValue
                });
                processedIndexes.push(index); 
            }
        });

        if (validationFailed) return;

        if (measurementsData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Data',
                text: 'Please enter at least one new measurement before saving.',
                confirmButtonColor: '#16a34a'
            });
            return;
        }
        
        $.ajax({
            url: '{{ route('warehouse.temperature') }}',
            method: 'POST',
            data: {
                warehouse_id: warehouse,
                measurements: measurementsData,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'All entries saved successfully for this warehouse!',
                    confirmButtonColor: '#16a34a',
                    timer: 3000,
                    timerProgressBar: true
                });

                processedIndexes.forEach(function(idx) {
                    let row = $('.hour-row[data-index="' + idx + '"]');
                    
                    $('#hour-' + idx).removeClass('bg-red-200').addClass('bg-green-200');
                    $('#hour-icon-' + idx).attr('src', '{{ asset('images/check.png') }}');
                    
                    row.find('.temp-input').prop('disabled', true).attr('placeholder', 'Registered').val('');
                    row.find('.hum-input').prop('disabled', true).attr('placeholder', 'Registered').val('');
                    row.find('.status-label').removeClass('hidden').addClass('text-green-600').text('(Saved)');
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Save Error',
                    text: 'Error al guardar los datos.',
                    confirmButtonColor: '#16a34a'
                });
            }
        });
    });
});
</script>
@endpush