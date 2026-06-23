@props([
    'id' => null,
    'labelId' => null,
    'buttonId' => null,
    'label' => null,
    'buttonText' => 'Yes',
    'class' => 'border-green-400 bg-green-400 text-white',
])

<div id="{{ $id }}-opt" class="flex flex-col items-start gap-1 w-full">
    <label id="{{ $labelId ?? $id}}-tag" class="mb-1 block font-medium text-md text-gray-700">
        {{ $label }}
    </label>
    <span id="{{ $buttonId ?? $id }}-btn" 
          class="flex justify-center items-center w-full transition border-2 {{ $class }} p-1 tracking-[3px] text-lg cursor-pointer rounded-md">
        {{ $buttonText }}
    </span>
</div>
