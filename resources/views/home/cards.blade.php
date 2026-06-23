<x-public-site.section-1>
    <style>
        /* Animación sutil de flotado para que no se vea estático */
        @keyframes float-card {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        .animate-modern-card {
            animation: float-card 6s ease-in-out infinite;
        }
        /* Retraso en la aparición de textos dentro del panel de cristal */
        .group:hover .reveal-text {
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition-delay: 200ms;
        }
    </style>

    <div class="container-xl mb-4" data-aos="zoom-in">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4 justify-content-center">
            
            @php
                $items = [
                    [
                        'tit' => 'Porcinos', 'img' => 'porcicultura-sch.png', 'alt' => 'Porcicultura',
                        'sub' => 'Optimizando la nutrición', 'prod' => 'Vitayela Porc',
                        'desc' => '',
                        //'desc' => 'Optimización del crecimiento y salud intestinal avanzada para porcicultura.',
                        'url' => route('porcinos.index')
                    ],
                    [
                        'tit' => 'Ganado', 'img' => 'pecuario.png', 'alt' => 'Ganado',
                        'sub' => 'Línea Pecuaria', 'prod' => 'Especial Ganadería',
                        'desc' => '',
                        //'desc' => 'Mejora el rendimiento y la calidad de producción de tu ganado bovino.',
                        'url' => route('bovinos.index')
                    ],
                    [
                        'tit' => 'Acuacultura', 'img' => 'acuacultura-sch.png', 'alt' => 'Acuacultura',
                        'sub' => 'Innovación', 'prod' => 'Bio-Acuática',
                        'desc' => '',
                        //'desc' => 'Nutrición especializada para el desarrollo óptimo en entornos acuáticos.',
                        'url' => '#'
                    ],
                    [
                        'tit' => 'Apicultura', 'img' => 'apicultura-sch.png', 'alt' => 'Apicultura',
                        'sub' => 'Nutrición Apícola', 'prod' => 'Bee-Power',
                        //'desc' => 'Fórmulas de alta energía para fortalecer la salud y producción de la colmena.',
                        'desc' => '',
                        'url' => '#'
                    ],
                    [
                        'tit' => 'Avicultura', 'img' => 'avicultura-sch.png', 'alt' => 'Avicultura',
                        'sub' => 'Aves de Corral', 'prod' => 'Avi-Grow',
                        //'desc' => 'Revolucionando la conversión alimenticia para aves de postura y engorde.',
                        'desc' => '',
                        'url' => '#'
                    ],
                    [
                        'tit' => 'Perros', 'img' => 'petfood.png', 'alt' => 'Perros',
                        'sub' => 'Pet Food - Caninos', 'prod' => 'Health & Pet Dog',
                        //'desc' => 'La solución más saludable y equilibrada para el bienestar de tus mejores amigos.',
                        'desc' => '',
                        'url' => route('perros.index')
                    ],
                    [
                        'tit' => 'Gatos', 'img' => 'petfood-portfolio-3.webp', 'alt' => 'Gatos',
                        'sub' => 'Pet Food - Felinos', 'prod' => 'Health & Pet Cat',
                        //'desc' => 'Nutrición premium diseñada para la vitalidad y cuidado específico de tu gato.',
                        'desc' => '',
                        'url' => route('gatos.index')
                    ],
                  [
                        'tit' => 'Caballos', 
                        'img' => 'caballo1.png', 
                        'alt' => 'Equinos Success',
                        'sub' => 'Línea Equina', 
                        'prod' => 'Equi-Force Premium',
                        'desc' => '',
                        //'desc' => 'Nutrición de alto rendimiento para fuerza, resistencia y salud atlética superior.',
                         'url' => '#'
                    ],
                ];
            @endphp

            @foreach($items as $item)
            <div class="px-5 d-flex justify-content-center">
                <div class="relative overflow-hidden w-72 h-96 rounded-xl bg-gray-900 shadow-2xl group cursor-pointer border border-gray-800 transition-all duration-500 hover:border-green-500/50 hover:shadow-[0_0_20px_rgba(34,197,94,0.3)] animate-modern-card">
                    
                    <img src="{{ asset('images/'.$item['img']) }}" alt="{{ $item['alt'] }}" 
                         class="absolute inset-0 object-cover w-full h-full transition-transform duration-1000 group-hover:scale-110">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-10 transition-opacity duration-500 group-hover:opacity-30"></div>

                    <div class="absolute bottom-6 left-6 z-40 transition-all duration-500 ease-in-out group-hover:bottom-80">
                        <h3 class="text-2xl font-black text-white uppercase tracking-tighter italic">
                            {{ $item['tit'] }}
                        </h3>
                        <div class="w-8 h-1 bg-green-500 transition-all duration-500 group-hover:w-full"></div>
                    </div>

                    <div class="absolute inset-0 z-30 flex flex-col justify-end p-6 bg-black/40 backdrop-blur-md translate-y-full transition-transform duration-500 ease-in-out group-hover:translate-y-0 border-t border-white/10">
                        
                        <div class="mb-3 transform translate-y-4 opacity-0 transition-all duration-500 reveal-text">
                            <span class="inline-block bg-green-500 text-black font-bold text-[10px] px-2 py-0.5 rounded uppercase tracking-widest">
                                {{ $item['sub'] }}
                            </span>
                            <h4 class="text-xl text-white font-bold leading-tight mt-2">
                                {{ $item['prod'] }}
                            </h4>
                        </div>

                        <p class="text-gray-300 text-[11px] leading-relaxed mb-6 transform translate-y-4 opacity-0 transition-all duration-500 reveal-text">
                            {{ $item['desc'] }}
                        </p>

                        <a href="{{ $item['url'] }}" class="w-full py-2.5 bg-white text-black text-[11px] text-center no-underline font-black rounded hover:bg-yellow-500 transition-colors duration-300 uppercase block">
                            Ver Productos
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="flex justify-center mt-12">
            <img src="{{ asset('images/mgto2.jpeg') }}" class="h-64 w-auto object-contain transition-all duration-700 hover:scale-105" alt="Marca Success">
        </div>
    </div>
</x-public-site.section-1>