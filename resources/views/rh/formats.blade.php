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

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 flex-wrap justify-items-center w-full px-4 max-w-6xl">
        
        
        <a href="{{ route('attendance.index') }}" class="{{ $btnBase }} {{ $btnCard }}">
            <svg class="h-10 w-10 text-white mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold uppercase tracking-wider">Asistencia</span>
        </a>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-expediente">
            <img src="{{ asset('images/rh/exp-01.png') }}" alt="Expediente de Personal" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Expediente</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-descripcion">
            <img src="{{ asset('images/rh/des-02.png') }}" alt="Descripción de Puesto" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Descripción de puesto</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-entrevista-terminacion">
            <img src="{{ asset('images/rh/exit-03.png') }}" alt="Entrevista de Terminación" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Entrevista de <br> Terminación</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-vacaciones">
            <img src="{{ asset('images/rh/vaca-08.png') }}" alt="Solicitud de Vacaciones" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Solicitud de <br> Vacaciones</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-dnc">
            <img src="{{ asset('images/rh/dnc-05.png') }}" alt="Cuestionario DNC" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Cuestionario DNC</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-practicantes">
            <img src="{{ asset('images/rh/pract-06.png') }}" alt="Expediente de Practicantes" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}"> 
            <span class="text-sm font-bold uppercase tracking-wider">Expediente <br> Practicantes</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-evaluacion-desempeno">
            <img src="{{ asset('images/rh/img-04.png') }}" alt="Evaluación de Desempeño" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}">
            <span class="text-sm font-bold uppercase tracking-wider">Evaluación de <br> Desempeño</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-solicitud-personal">
            <img src="{{ asset('images/rh/img-05.png') }}" alt="Solicitud de Personal" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}">
            <span class="text-sm font-bold uppercase tracking-wider">Solicitud de <br> Personal</span>
        </button>

        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-convenio-instituciones">
            <img src="{{ asset('images/rh/img-06.png') }}" alt="Convenio con Instituciones" class="{{ $imgCls ?? 'w-12 h-12 mx-auto mb-2' }}">
            <span class="text-sm font-bold uppercase tracking-wider">Convenio con <br> Instituciones</span>
        </button>

        <!-- Clima Laboral -->
        <button type="button" class="open-modal {{ $btnBase }} {{ $btnCard }}" data-target="modal-clima-laboral">
            <svg class="h-10 w-10 text-white mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold uppercase tracking-wider">Clima Laboral <br> (Encuesta)</span>
        </button>

        <a href="{{ route('rh.clima_laboral.resultados') }}" class="{{ $btnBase }} {{ $btnCard }}">
            <svg class="h-10 w-10 text-white mb-1 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm font-bold uppercase tracking-wider">Resultados <br> Clima Laboral</span>
        </a>

    </div>

    @include('rh.modals.expediente')
    @include('rh.modals.descripcion_puesto')
    @include('rh.modals.entrevista_terminacion')
    @include('rh.modals.vacation')
    @include('rh.modals.dnc')
    @include('rh.modals.practicantes') 
    @include('rh.modals.evaluacion_desempeno')
    @include('rh.modals.solicitud_personal')
    @include('rh.modals.convenio_instituciones')
    @include('rh.modals.clima_laboral')

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#198754'
                });
            });
        </script>
    @endif

</section>