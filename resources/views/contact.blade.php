{{--
contacto
21/07/25
stefany
Actualizado por: Jacob
Fecha de actualización: 12-12-2025
--}}
@extends('layouts.main')
@section('content')
    @include('contact.face')
    @include('contact.form-contact')
    @include('contact.maps')
@push('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto+Slab:wght@100..900&display=swap');
    .roboto-slab {
        font-family: "Roboto Slab", serif;
        font-optical-sizing: auto;
        font-weight: <weight>;
        font-style: normal;
    }
</style>
@endpush
@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var spinner = document.getElementById('spinner');
        var title = document.getElementById('title')
        window.addEventListener('load', function() {
            spinner.style.display = 'none';
            title.style.display = 'block';
        });
    });
</script>
@endpush
@endsection
