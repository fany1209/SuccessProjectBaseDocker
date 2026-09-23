@props([
    'id' => null,
    'icon' => 'ri-corner-down-right-fill'
])
<div id="{{ $id }}" {{ $attributes->merge(['class' => 'flex justify-start items-center gap-2.5 cursor-pointer transition-all duration-200 bg-white hover:bg-gray-50 rounded-lg shadow-sm border border-gray-100 px-3 py-2.5 w-full active:scale-95 select-none']) }}>
    <i class="{{ $icon }} text-xl text-gray-500 transition shrink-0"></i>
    <p class="text-sm text-gray-700 font-medium tracking-normal transition truncate">{{ $slot }}</p>
</div>