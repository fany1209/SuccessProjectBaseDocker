<div id="{{ $id }}" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl w-full p-4 mt-16 relative"><!--Modal-->
        {{ $slot }}
    </div>
</div>