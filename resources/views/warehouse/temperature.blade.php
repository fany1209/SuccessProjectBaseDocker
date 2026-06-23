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
                        <div class="h-3 w-2 bg-gray-200 shadow-inner shadow-black rounded-sm">
                </div>
                    @endfor
            </div>
        </div>
    </div>
    {{-- reloj f --}}
    <div class="w-full max-w-xl px-2">
        <div class="bg-gray-200 mt-4 px-4 flex justify-center items-center space-x-5 rounded-md shadow-md">
            {{-- CONTENEDOR HORAS --}}
            <div id="hours-container" class="bg-gray-200 mt-4 px-4 flex flex-wrap justify-center items-center space-x-5 rounded-md shadow-md">
                @foreach ($hours as $i => $hour)
                    @php $isChecked = $feedback[$i] ?? false; @endphp
                    <div id="hour-{{ $i }}" class="{{ $isChecked ? 'bg-green-200' : 'bg-red-200' }} h-24 w-32 flex flex-row items-center justify-center my-4 py-6 rounded-md shadow-md space-x-4">
                        <p class="font-bold text-lg">{{ $hour }}</p>
                        <img id="hour-icon-{{ $i }}" src="{{ asset('images/' . ($isChecked ? 'check.png' : 'uncheck.png')) }}" width="36" alt="">
                    </div>
                @endforeach
            </div>
        </div>
        {{-- SELECT --}}
        <div class="bg-gray-200 w-full mt-4 rounded-md shadow-md px-2 py-2">
            <label for="selected_warehouse" class="block text-sm font-medium text-gray-700">Warehouse</label>
            <select id="selected_warehouse" class="block w-full mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm disabled:bg-gray-200">
                <option value="">None</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->warehouse_id }}">
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @if ($activeHour)
            <div class="bg-gray-200 w-full h-full rounded-md py-2 px-2 space-y-1 shadow-md">
                <div>
                    <p class="font-bold">Active Hour: {{ $activeHour }}</p>
                </div>
                <div class="bg-gray-300 rounded-md px-2 py-2 flex space-x-2">
                    <div class="w-full">
                        <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature</label>
                        <input type="text" id="temperature" class="w-full mt-1 border-gray-300 rounded-md">
                    </div>
                    <div class="w-full">
                        <label for="humidity" class="block text-sm font-medium text-gray-700">Humidity</label>
                        <input type="text" id="humidity" class="w-full mt-1 border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="py-1 flex items-center justify-end">
                    <button id="btnSet" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Set</button>
                </div>
            </div>
        @endif
        {{-- MENSAJE ERROR --}}
        @if ($errors->has('in_time'))
            <div class="w-full bg-red-500 mt-4 rounded-md text-center py-2">
                <p class="text-white font-bold">Sorry, your registration is out of time</p>
            </div>
        @endif
    </div>
</section>
@push('js')
<script>
    function setColor(color) {
        selected = document.getElementById(color);
        watch_left = document.getElementById("watch-left");
        watch_main = document.getElementById("watch-main");
        watch_right = document.getElementById("watch-right");
        watch_left.classList.forEach(className => {
            if (className.includes('bg')) {
                watch_left.classList.remove(className);
            }
        });
        watch_main.classList.forEach(className => {
            if (className.includes('bg')) {
                watch_main.classList.remove(className);
            }
        });
        watch_right.classList.forEach(className => {
            if (className.includes('bg')) {
                watch_right.classList.remove(className);
            }
        });
        if (color == 'gray') {
            watch_left.classList.add("bg-" + color + "-800");
            watch_main.classList.add("bg-" + color + "-800");
            watch_right.classList.add("bg-" + color + "-800");
        } else {
            watch_left.classList.add("bg-" + color + "-500");
            watch_main.classList.add("bg-" + color + "-500");
            watch_right.classList.add("bg-" + color + "-500");
        }
    }
    var pHours = document.getElementById('hours');
    var pMinutes = document.getElementById('minutes');
    var pDayName = document.getElementById('dayName');
    var pMonth = document.getElementById('monthName');
    var pDayNum = document.getElementById('dayNum');
    var pYear = document.getElementById('year');
    const dateAux = new Date();
    function setHours(num) {
        pHours.textContent = num < 10 ? '0' + num : num;
    }
    function setMinutes(num) {
        pMinutes.textContent = num < 10 ? '0' + num : num;
    }
    let prev = {
        h: null,
        m: null
    }
    function setYear() {
        const year = dateAux.getFullYear();
        pYear.textContent = year;
    }
    function setMonth() {
        const monthNames = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
        const month = dateAux.getMonth();
        pMonth.textContent = monthNames[month];
    }
    function setDay() {
        const dayNames = ["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"];
        const day = dateAux.getDay();
        pDayName.textContent = dayNames[day];
        pDayNum.textContent = dateAux.getDate();
    }
    setInterval(function() {
        const date = new Date();
        if (prev.h !== date.getHours()) {
            prev.h = date.getHours();
            setHours(date.getHours(date.getHours()));
        }
        if (prev.m !== date.getMinutes()) {
            prev.m = date.getMinutes();
            setMinutes(date.getMinutes(date.getMinutes()));
        }
    }, 300);
    setDay();
    setMonth();
    setYear();
$(document).ready(function() {
    $(document).on('click', '#btnSet', function(e) {
        e.preventDefault();
        let temp = $('#temperature').val();
        let humidity = $('#humidity').val();
        let warehouse = $('#selected_warehouse').val();
        console.log('Temp:', temp, 'Humidity:', humidity, 'Warehouse:', warehouse);
        $.ajax({
            url: '{{ route('warehouse.temperature') }}',
            method: 'POST',
            data: {
                temperature: temp,
                humidity: humidity,
                warehouse_id: warehouse,
                active_hour: '{{ $activeHour }}', 
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Guardado!');
                // Encuentra el índice de la hora activa
                let activeIndex = {!! json_encode(array_search($activeHour, $hours) !== false ? array_search($activeHour, $hours) : null) !!};
                console.log('Active index:', activeIndex);
                // Cambia clases
                $('#hour-' + activeIndex)
                    .removeClass('bg-red-200')
                    .addClass('bg-green-200');
                // Cambia imagen
                $('#hour-icon-' + activeIndex)
                    .attr('src', '{{ asset('images/check.png') }}');
            }
        });
    });
});
</script>
@endpush