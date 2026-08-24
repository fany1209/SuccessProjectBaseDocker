@props(['id', 'maxWidth' => '5xl'])
@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    '7xl' => 'max-w-7xl',
    'full' => 'w-[95vw] max-w-none', // 95% of viewport width instead of normal full
][$maxWidth] ?? 'max-w-5xl';
@endphp
<div id="{{ $id }}" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg {{ $maxWidthClass }} w-full p-4 mt-16 relative"><!--Modal-->
        {{ $slot }}
    </div>
</div>