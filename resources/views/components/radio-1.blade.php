@props([
    'id' => null,
    'name',
    'value' => null,
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
])

<input
    type="radio"
    id="{{ $id ?? $name . '_' . $value }}"
    name="{{ $name }}"
    value="{{ $value }}"

    {{-- Mantener estado con old() o con checked --}}
    @checked(old($name) == $value || $checked)

    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif

    {{ $attributes->merge([
        'class' => "text-green-500 focus:ring-green-500"
    ]) }}
/>
