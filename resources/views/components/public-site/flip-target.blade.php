@props(['image'=>'','title'=>''])

<div class="w-full h-full" data-aos="flip-left" data-aos-duration="600">
    <div class="border rounded-xl shadow-sm bg-white 
                h-full flex flex-col p-6 text-center">

        <img src="{{ $image }}" 
             class="mx-auto mb-4 h-16 object-contain" 
             alt="">

        <h4 class="font-bold uppercase mb-4 text-gray-900">
            {{ $title }}
        </h4>

        <p class="text-gray-500 text-sm flex-grow">
            {{ $slot }}
        </p>

    </div>
</div>