@props([
    'id' => 'toggle-' . Str::random(5),
    'name' => null,
    'value' => 'on',
    'checked' => false,
    'onLabel' => 'Yes',
    'offLabel' => 'No',
    'onColor' => 'emerald-500',
    'offColor' => 'red-700',
    'textColor' => 'white',
    'class' => '',
])
<label {{ $attributes->merge(['class' => "relative inline-flex cursor-pointer items-center $class"]) }}>
    <input type="hidden" name="{{ $name ?? $id }}" value="{{ $checked ? $onLabel : $offLabel }}">

    <input
        type="checkbox"
        id="{{ $id }}"
        value="{{ $checked ? $onLabel : $offLabel }}"
        class="peer sr-only"
        @checked($checked)
    />

    <div
        class="peer flex w-full items-center justify-center py-2 px-3 rounded-md 
            bg-{{ $offColor }}
            text-{{ $textColor }}
            after:absolute after:left-0 after:h-full after:w-1/2 after:rounded-md after:bg-white/40 
            after:transition-all after:translate-x-full after:content-[''] 
            peer-checked:bg-{{ $onColor }} peer-checked:after:translate-x-0 duration-300 
            text-sm gap-2"
    >
        <span>{{ $onLabel }}</span>
        <span class="ms-4">{{ $offLabel }}</span>
    </div>
</label>