<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - Nutrición Porcina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/successIconG.ico') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #eef1f6;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(34, 197, 94, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(34, 197, 94, 0.1) 0%, transparent 50%);
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        @keyframes zoomFadeIn {
            from { opacity: 0; transform: scale(0.9) translateY(40px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .reveal {
            opacity: 0;
            animation: zoomFadeIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .product-card {
            background: #ffffff;
            border-radius: 80px 15px 80px 15px;
            padding: 0 40px 40px 40px;
            margin-top: 100px;
            height: calc(100% - 100px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.04);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid rgba(255,255,255,0.5);
        }

        .product-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(34, 197, 94, 0.15);
            border-radius: 15px 80px 15px 80px; 
        }

        .img-pop-out {
            position: relative;
            width: 180px;
            height: 180px;
            margin-top: -80px; 
            margin-bottom: 30px;
            background: linear-gradient(135deg, #dcfce7, #86efac);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 15px 30px rgba(34, 197, 94, 0.3);
            border: 8px solid #eef1f6;
            transition: all 0.5s;
            z-index: 2;
        }

        .product-card:hover .img-pop-out {
            border-color: #ffffff;
            transform: scale(1.05);
        }

        .product-img {
            max-height: 230px;
            margin-top: -60px; 
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.25));
        }

        .product-card:hover .product-img {
            transform: scale(1.15) rotate(4deg);
        }

        .tag-premium {
            background: #22c55e;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 8px 16px;
            border-radius: 30px;
            margin-bottom: 20px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.4);
        }

        .btn-success-custom {
            background: #1e293b;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 32px;
            border-radius: 25px 6px 25px 6px; 
            text-decoration: none;
            transition: 0.4s;
            margin-top: auto;
            width: 100%;
        }

        .btn-success-custom:hover {
            background: #22c55e;
            color: #ffffff;
            border-radius: 6px 25px 6px 25px;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
        }
    </style>
</head>
<body>

    <nav class="container py-4 flex justify-between items-center reveal" style="animation-delay: 0.1s">
        <img src="{{ asset('images/successIconG.ico') }}" class="h-12 w-auto" alt="Success Logo">
        <a href="/" class="text-slate-500 no-underline font-bold text-sm hover:text-green-600 transition">← Volver al Inicio</a>
    </nav>

    <div class="container py-5">
        
        <div class="text-center mb-5 reveal" style="animation-delay: 0.2s">
            <h1 class="text-6xl md:text-7xl font-black italic uppercase tracking-tighter mb-4 text-slate-800">
                Línea <span class="text-green-500">Porcina</span>
            </h1>
            <p class="text-slate-500 text-lg max-w-2xl mx-auto font-medium">
                Nutrición de alto rendimiento diseñada para potenciar la rentabilidad de tu granja.
            </p>
        </div>

        <div class="row g-5 mt-12">
            @php
                $productos = [
                    [
                        'tit' => 'Vitayela Porc', 
                        'tag' => 'Aditivo para Cerdos', 
                        'desc' => 'Aumenta la ganancia de peso disminuyendo el tiempo de estadía y mejorando el sistema inmunológico.',
                        'img' => 'porcicultura-sch-2.png'
                    ],
                ];
            @endphp

            @foreach($productos as $index => $p)
            <div class="col-12 col-lg-6 reveal" style="animation-delay: {{ ($index + 3) * 0.1 }}s">
                <div class="product-card">
                    
                    <div class="img-pop-out">
                        <img src="{{ asset('images/'.$p['img']) }}" class="product-img" alt="Success Product">
                    </div>
                    
                    <span class="tag-premium">{{ $p['tag'] }}</span>
                    <h2 class="text-4xl font-black mb-3 text-slate-800">{{ $p['tit'] }}</h2>
                    <p class="text-slate-500 leading-relaxed mb-10 text-sm px-4">
                        {{ $p['desc'] }}
                    </p>
                    
                    {{--<a href="#" class="btn-success-custom">Ver Ficha Técnica</a>--}}
                </div>
            </div>
            @endforeach
        </div>

        <footer class="mt-32 pb-10 text-center reveal" style="animation-delay: 1s">
            <div class="w-full h-px bg-slate-300 mb-10"></div>
            <p class="text-slate-400 text-xs tracking-widest uppercase mb-4 font-bold">Success Nutrición Animal © 2026</p>
            <div class="flex justify-center gap-4">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>