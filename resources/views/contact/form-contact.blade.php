{{--
Fecha de actualización: 17/12/2025
Actualizado por Jacob
--}}
<x-public-site.section-1 class="my-4">
    <div class="flex flex-col lg:flex-row lg:justify-between items-center gap-3">
        <form class="flex flex-col items-start justify-center gap-3 w-full lg:w-2/4" id="contact-form" method="POST">
            @csrf
            {{-- Honeypot field --}}
            <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; z-index: -1;" aria-hidden="true">
                <label for="bot_check">Deja este campo vacío si eres humano:</label>
                <input type="text" name="bot_check" id="bot_check" tabindex="-1" autocomplete="off">
            </div>
            <x-wrapper-form-1>
                <x-tittle-form class="border-s-2 border-[#198754] ps-3">Envíanos un mensaje</x-tittle-form>
            </x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-input-1 required name="name" placeholder="Nombre completo"></x-input-1>
                <x-input-1 required name="phone" placeholder="Número de telefono"></x-input-1>
            </x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-input-1 required type="email" name="email" placeholder="Correo"></x-input-1>
            </x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-textarea-1 required name="message" placeholder="Escribe tu mensaje"></x-textarea-1>
            </x-wrapper-form-1>
            <x-wrapper-form-1>
                <x-button-1 id="send-contact" colorBtn="green">Envíar</x-button-1>
            </x-wrapper-form-1>
        </form>
        <div class="flex flex-col items-start justify-center rounded-lg shadow p-6 gap-3 w-full lg:w-2/4 bg-[#198754]">
            <h3 class="text-3xl text-white font-semibold">Centro de Distribución</h3>
            <p class="font-base text-white mb-3"><i class="ri-map-pin-fill"></i>
                Emiliano Zapata No. 7<br>
                Rancho Nuevo<br>
                Apaseo el Grande 38197<br>
                Guanajuato
            </p>
            <p class="font-base text-white mb-3">
                <i class="ri-phone-fill"></i> +52 (461) 156 8547 / +52 (461) 616 9975 <br>
                <i class="ri-mail-fill"></i> contacto@suministrossustentables.com
            </p>
        </div>
    </div>
</x-public-site.section-1>
@push('js')
<script>
$(function () {
    $("#contact-form").on("submit", function (event) {
        event.preventDefault();
        const form = this;
        Swal.fire({
            icon: 'success',
            title: 'Gracias por su mensaje',
            text: 'Te contactaremos lo antes posible al teléfono que nos proporcionaste.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                form.setAttribute("action", "{{ route('contact.store') }}");
                form.submit();
            }
        });
    });
});
</script>    
@endpush