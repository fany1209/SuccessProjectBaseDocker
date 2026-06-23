@props([
    'id' => null,
    'type' => 'submit',
    'colorBtn' => 'gray',
])

<button
    id="{{ $id }}"
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "
            inline-flex items-center px-4 py-2 
            bg-{$colorBtn}-500 border border-transparent 
            rounded-md font-semibold text-xs text-white uppercase tracking-widest 
            hover:bg-{$colorBtn}-600 focus:bg-{$colorBtn}-600 active:bg-{$colorBtn}-400 
            focus:outline-none focus:ring-2 focus:ring-{$colorBtn}-500 focus:ring-offset-2 
            transition ease-in-out duration-150
        ",
    ]) }}
>
    {{ $slot }}
</button>