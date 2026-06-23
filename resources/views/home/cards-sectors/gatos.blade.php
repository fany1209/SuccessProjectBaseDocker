<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Pet Food - Nutrición Felina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/successIconG.ico') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #050505;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

         .bg-glow {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, #686666 0%, #000 100%);
            z-index: -1;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reveal {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 32px;
            padding: 40px;
            transition: all 0.5s ease;
            height: 100%;
            backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-12px);
            background: rgba(34, 197, 94, 0.04);
            border-color: rgba(34, 197, 94, 0.4);
            box-shadow: 0 30px 60px rgba(0,0,0,0.8), 0 0 20px rgba(34, 197, 94, 0.1);
        }

        .img-wrapper {
            background: rgba(0,0,0,0.4);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-img {
            max-height: 240px;
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));
        }

        .product-card:hover .product-img {
            transform: scale(1.1) rotate(-2deg);
        }

        .btn-success-custom {
            background: #22c55e;
            color: #000;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 18px;
            border-radius: 16px;
            text-decoration: none;
            text-align: center;
            transition: 0.3s;
            margin-top: auto;
            border: none;
        }

        .btn-success-custom:hover {
            background: #ffffff;
            color: #000;
            box-shadow: 0 10px 25px rgba(255,255,255,0.2);
            transform: scale(1.02);
        }

        .tag-premium {
            color: #22c55e;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>

    <div class="bg-glow"></div>

    <nav class="container py-4 flex justify-between items-center reveal" style="animation-delay: 0.1s">
        <img src="{{ asset('images/successIconG.ico') }}" class="h-12 w-auto" alt="Success Logo">
        <a href="/" class="text-white no-underline font-bold text-sm hover:text-green-500 transition">← Volver al Inicio</a>
    </nav>

    <div class="container py-5">
        
        <div class="text-center mb-16 reveal" style="animation-delay: 0.2s">
            <h1 class="text-6xl md:text-7xl font-black italic uppercase tracking-tighter mb-4">
                Línea <span class="text-green-500">Health & Pet Cat</span>
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
               Nutrición premium diseñada para la vitalidad y cuidado específico de tu gato.
            </p>
        </div>

        <div class="row g-5">
            @php
                $productos = [
                    [
                        'tit' => 'Delichick cat', 
                        'tag' => 'Palante para croquetas', 
                        'desc' => 'Proteína altamente digestible y taurina esencial para el desarrollo de la visión y el corazón en etapas tempranas.',
                        'img' => 'petfood-portfolio-3.webp'
                    ],
                ];
            @endphp

            @foreach($productos as $index => $p)
            <div class="col-12 col-lg-6 reveal" style="animation-delay: {{ ($index + 3) * 0.1 }}s">
                <div class="product-card">
                    <div class="img-wrapper">
                        <img src="{{ asset('images/'.$p['img']) }}" class="product-img" alt="Success Gatos">
                    </div>
                    
                    <span class="tag-premium">{{ $p['tag'] }}</span>
                    <h2 class="text-4xl font-black mb-3">{{ $p['tit'] }}</h2>
                    <p class="text-gray-400 leading-relaxed mb-10 text-sm">
                        {{ $p['desc'] }}
                    </p>
                    
                    <a href="#" class="btn-success-custom">Ver Ficha Técnica</a>
                </div>
            </div>
            @endforeach
        </div>

        <footer class="mt-32 pb-10 text-center reveal" style="animation-delay: 1s">
            <div class="w-full h-px bg-white/10 mb-10"></div>
            <p class="text-gray-600 text-xs tracking-widest uppercase mb-4">Success Pet Food © 2026</p>
            <div class="flex justify-center gap-4">
                <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_10px_#22c55e]"></div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>