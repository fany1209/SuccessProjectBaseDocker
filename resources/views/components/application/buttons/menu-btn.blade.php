@props([
    'link' => '#',
    'module' => '',
    'image' => asset('images/caja.png')
])

<a href="{{ $link }}" class="bg-[#198754] p-8 rounded-lg focus:scale-95 transition-transform duration-300">
    <div class="flex flex-col justify-center items-center">
        <img src="{{ $image }}" width="100" class="mb-3" alt="{{ $module }}">
        <h5 class="text-2xl text-white font-semibold tracking-[3px]">{{ $slot }}</h5>
    </div>
</a>