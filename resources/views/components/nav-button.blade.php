@props([
    'id' => null,
    'icon' => 'ri-corner-down-right-fill'
])
<div id="{{ $id }}" {{ $attributes->merge(['class' => 'flex justify-start items-center gap-2 cursor-pointer transition-all duration-200 bg-white rounded-md shadow-md p-2 w-full active:scale-95 select-none']) }}>
    <i class="{{ $icon }} text-2xl text-gray-500 transition"></i>
    <p class="text-lg text-gray-700 tracking-[2px] font-semibold transition">{{ $slot }}</p>
</div>