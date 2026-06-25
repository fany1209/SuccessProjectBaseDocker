<section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <div class="mt-6 w-full max-w-4xl text-center">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
            Recursos Humanos
        </h1>
        <p class="text-sm text-gray-600">Selecciona el módulo al que deseas ingresar.</p>
        <div class="mt-2 h-px bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent"></div>
    </div>

    @php
        $btnBase = 'text-white bg-[#198754] hover:bg-[#157347] focus:ring-2 focus:outline-none focus:ring-[#198754]/30 font-medium rounded-lg text-xs px-3 transition-all duration-300 shadow-sm hover:shadow-md';
        $btnCard = 'w-full sm:w-64 !h-auto min-h-[100px] py-3 flex flex-col items-center justify-center gap-2 text-center';
    @endphp

    <div class="mt-8 flex justify-center w-full">
        <a href="{{ route('attendance.index') }}" class="{{ $btnBase }} {{ $btnCard }}">
            <svg class="h-10 w-10 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold uppercase tracking-wider">Asistencia</span>
        </a>
    </div>

          <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-expediente">
              <img src="{{ asset('images/rh/exp-01.png') }}" alt="Expediente de Personal" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
        <span class="text-sm font-bold uppercase tracking-wider">Expediente</span>
        </button>
        @include('rh.modals.expediente')

          <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-descripcion">
              <img src="{{ asset('images/rh/des-02.png') }}" alt="Descripción de Puesto" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
        <span class="text-sm font-bold uppercase tracking-wider">Descripción de puesto </span>
        </button>
        @include('rh.modals.descripcion_puesto')

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-entrevista-terminacion">
            <img src="{{ asset('images/rh/exit-03.png') }}" alt="Entrevista de Terminación" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Entrevista de <br> Terminación</span>
        </button>
        @include('rh.modals.entrevista_terminacion')


</section>