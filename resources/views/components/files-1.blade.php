@props([
    'id' => 'file-input',
    'name' => 'files[]',
    'multiple' => true,
    'accept' => 'application/pdf',
    'label' => 'Haz clic para subir',
    'info' => 'Solo PDF',
    'class' => '',
])

<label for="{{ $id }}" {{ $attributes->merge([
        'class' => "flex flex-col items-center justify-center w-full h-38 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 $class"
    ]) }}>
    <div class="flex flex-col items-center justify-center pt-4 pb-6">
        <svg class="w-8 h-8 mb-2 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 20 16">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5A5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
        </svg>
        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">{{ $label }}</span> o arrastra</p>
        <p class="text-xs text-gray-500">{{ $info }}</p>
    </div>
    <input 
        type="file" 
        name="{{ $name }}" 
        id="{{ $id }}" 
        class="hidden" 
        @if($multiple) multiple @endif
        accept="{{ $accept }}"
    />
</label>

<div id="preview-container-{{ str_replace('[]', '', $name) }}" class="flex justify-between w-full items-center gap-4 mt-2"></div>