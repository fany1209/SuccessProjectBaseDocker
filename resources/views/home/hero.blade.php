<x-public-site.section-1 id="hero">
    <div class="hero-container" data-aos="zoom-in-down" data-aos-duration="1000">
        <img src="{{ asset('images/successIconG.ico') }}" class="mb-4" width="190" alt="">
        <h1>La Excelencia, Nuestro Estilo de Vida</h1>
        <a href="{{ route('contact') }}" class="btn btn-outline-light">CONTÁCTANOS</a>
    </div>
</x-public-site.section-1>
@push('css')
<style>
    #hero {
        width: 100%;
        height: 70vh;
        background: url("/images/bg.jpg") top center;
        background-size: cover;
        position: relative;
        border-bottom: 1px solid #222;
    }

    #hero:before {
        content: "";
        background: rgba(0, 0, 0, 0.7);
        position: absolute;
        bottom: 0;
        top: 0;
        left: 0;
        right: 0;
    }

    #hero .hero-container {
        position: absolute;
        bottom: 0;
        top: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        text-align: center;
        padding: 0 15px;
    }

    #hero h1 {
        margin: 0 0 30px 0;
        font-size: 48px;
        font-weight: 390;
        line-height: 56px;
        color: #fff;
    }

    #hero h2 {
        color: #eee;
        margin-bottom: 40px;
        font-size: 22px;
    }

    #hero .btn-get-started {
        font-family: "Raleway", sans-serif;
        font-weight: 500;
        font-size: 16px;
        letter-spacing: 2px;
        display: inline-block;
        padding: 10px 28px;
        border-radius: 5px;
        transition: 0.5s;
        border: 2px solid #4eb478;
        color: #fff;
    }

    #hero .btn-get-started:hover {
        background: #4eb478;
        border: 2px solid #4eb478;
    }

    @media (min-width: 1024px) {
        #hero {
            background-attachment: fixed;
        }

        #hero h1,
        #hero h2 {
            width: 50%;
        }
    }

    @media (max-width: 768px) {
        #hero {
            height: 100vh;
        }

        #hero h1 {
            font-size: 28px;
            line-height: 36px;
        }

        #hero h2 {
            font-size: 18px;
            line-height: 24px;
            margin-bottom: 30px;
        }
    }
</style>
@endpush