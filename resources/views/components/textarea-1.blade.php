@props([
    'id' => null,
    'name' => null,
    'rows' => 3,
    'cols' => 30,
    'placeholder' => 'Write here...'
])

<textarea
    id="{{ $name ?? $id }}"
    name="{{ $name }}"
    cols="{{ $cols }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => "w-full rounded-lg border border-gray-300 text-gray-400
                    focus:text-gray-700 focus:outline-none focus:ring-2 
                    focus:ring-green-500 px-2 py-2"
    ]) }}
>{{ $slot }}</textarea>