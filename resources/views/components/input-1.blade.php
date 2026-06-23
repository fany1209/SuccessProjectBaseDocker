@props([
    'id' => null,
    'name' => null,
    'type' => 'text',
    'list' => null,
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'disabled' => false,
    'readonly' => false,
    'class' => '',
])

<input
    type="{{ $type }}"
    id="{{ $id ?? $name }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    @if($list) list="{{ $list }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => "w-full rounded-lg border border-gray-300 text-gray-400
                    focus:text-gray-700 focus:outline-none focus:ring-2 
                    focus:ring-green-500 px-2 py-2 $class"
    ]) }}
/>
