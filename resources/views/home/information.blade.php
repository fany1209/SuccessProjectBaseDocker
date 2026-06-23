<x-public-site.section-1>
    <div class="flex flex-col lg:flex-row lg:justify-center items-center gap-3">
        <div class="flex flex-col items-start justify-center gap-3">
            <h1 class="text-4xl lg:text-6xl font-light text-green-700">¿QUÉ ES LA LEVADURA INACTIVA?</h1>            
            <p class="" style="text-align: justify">
                La levadura inactiva es un producto obtenido a partir de la desactivación de la levadura viva.
                Su uso se ha extendido en la industria alimentaria y suplementaria debido a sus <strong>
                    numerosos
                    beneficios para la salud.</strong> Rica en vitaminas, minerales y proteínas, la levadura
                    inactiva
                contribuye al fortalecimiento del sistema inmunológico y favorece la digestión.
                <br>
                <br>
                La levadura inactiva es la mejor opción para mejorar la calidad de vida y el rendimiento de
                nuestras mascotas y animales de granja.
            </p>
        </div>
        <div class="flex flex-col items-start justify-center gap-3 my-4">
            <img class="max-w-md lg:max-w-xl p-2 shadow rounded-lg" loading="lazy" src="{{ asset('images/cebada.jpg') }}" alt="">
        </div>
    </div>
    <div class="flex flex-col items-center justify-center shadow rounded-lg px-4 py-2 gap-3 my-4">
        <p class="text-center text-pt-serif m-0" style="font-size: 2rem;">La fórmula del éxito</p>
        <p class="text-center text-success text-pt-serif m-0" style="font-size: 2.5rem"><b>está en nuestras levaduras</b></p>
    </div>
</x-public-site.section-1>
@push('css')
<style>
    .swiper-slide:hover {
        box-shadow: 0 10px 20px rgba(19, 218, 95, 0.322);
    }
</style>
@endpush