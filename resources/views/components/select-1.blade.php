@props([
    'id' => null,
    'name' => null,
    'disabled' => false,
    'required' => false,
])

<select
    name="{{ $name }}"
    id="{{ $id ?? $name}}"
    @if($disabled) disabled @endif
    @if($required) required @endif
    {{ $attributes->merge([
        'class' => 'w-full rounded-lg border border-gray-300 text-gray-400 focus:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 px-2 py-2'
    ]) }}
>
    {{ $slot }}
</select>
