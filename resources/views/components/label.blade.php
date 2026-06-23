@props([
    'for' => null,
    'class' => '',
])
<label @if($for) for="{{ $for }}" id="{{ $for }}-tag" @endif {{ $attributes->merge(['class' => "mb-1 block font-medium text-md text-gray-700 $class"]) }}>{{ $slot }}</label>