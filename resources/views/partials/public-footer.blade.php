@php
    $refs = [
        (Object) ['link' => '/', 'section' => 'Inicio'],
        (Object) ['link' => '/contact', 'section' => 'Contacto'],
    ];
@endphp
<footer class="bg-gray-800 text-gray-200 py-12">
    <x-public-site.section-1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10">
            <div>
                <img src="{{ asset('images/pruebaSuccess.png') }}" width="200" alt="Logo Footer" />
                <p class="text-gray-400 leading-relaxed">
                    La excelencia, nuestro estilo de vida.
                </p>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Links</h3>
                <ul class="space-y-2">
                    @foreach ($refs as $ref)
                    <li><a href="{{ $ref->link }}" class="hover:text-white transition-colors">{{ $ref->section }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Contacto</h3>
                <ul class="space-y-2">
                    <li class="text-gray-400">Correo: contacto@suministrossustentables.com</li>
                    <li class="text-gray-400">Tel: +52 (461) 616 9975  / +52 (461) 156 8547</li>
                    <li>
                        <div class="flex space-x-4 mt-2">
                            <a target="_blank" href="https://www.facebook.com/success.suministros.sustentables" class="hover:text-white text-3xl transition-colors"><i class="ri-facebook-circle-fill"></i></a>
                            <a target="_blank" href="https://www.instagram.com/successsuministrossustentables/" class="hover:text-white text-3xl transition-colors"><i class="ri-instagram-line"></i></a>
                            <a target="_blank" href="#" class="hover:text-white text-3xl transition-colors"><i class="ri-linkedin-box-fill"></i></a>
                        </div>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Ubicación</h3>
                <p class="text-gray-400 leading-relaxed">
                    Emiliano Zapata #7 Col. Rancho Nuevo, <br />C.P. 38197 Apaseo el Grande, Guanajuato, México.
                </p>
            </div>
        </div>
        <div class="mt-10 border-t border-gray-800 pt-4 text-center text-gray-500 text-sm">
            Copyright © 2024 SUCCESS | Powered by G-Softlutions
        </div>
    </x-public-site.section-1>
</footer>