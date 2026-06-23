@props([
    'id' => null,
    'class' => null,
])
<div id="{{ $id }}-wrapper" class="flex flex-col items-start gap-1 w-full {{ $class }}">
    {{ $slot }}
</div>