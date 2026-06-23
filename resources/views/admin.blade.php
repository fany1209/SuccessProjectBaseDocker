{{--}}
admin.blade
11/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="badge badge-success">Dashboard</h1>
@stop

@section('content')
    @extends('admin.dashboard')
    <button onclick="scrollToTop()" id="btop" class="btn btn-success back-to-top-btn"><img
        src="{{ asset('images/up.png') }}" width="16" alt="top"></button>
@stop

@section('css')
    <style>
        .back-to-top-btn {
            position: fixed;
            bottom: 15px;
            right: 15px;
            display: none;
            z-index: 5;
        }
    </style>
    @vite(['resources/js/app.js'])
@stop

@section('js')
    <script>
        const scrollToTopButton =
            document.getElementById('btop');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 350) {
                scrollToTopButton.style.display = 'block';
            } else {
                scrollToTopButton.style.display = 'none';
            }
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
@stop
