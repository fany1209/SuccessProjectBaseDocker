@extends('layouts.main')
@push('css')
<style>
    .whatsapp-widget {
        position: fixed;
        bottom: 0.75rem;
        left: 0.75rem;
        width: 55px;
        height: 55px;
        background-color: #25D366;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-bottom: 3px;
        padding-left: 1px;
        cursor: pointer;
        box-shadow: 0 10px 13px rgba(0, 0, 0, 0.562);
    }
</style>
@endpush

@section('content')
    @include('home.hero')
    @include('home.sectors')
    @include('home.information')
    @include('home.cards')
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.swiper', {
        effect: 'cards',
        loop: true,
        grabCursor: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        cardsEffect: {
            perSlideOffset: 6,
        },
    });
</script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endpush
