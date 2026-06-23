{{--
Profile
Fecha de creación: 23-09-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 23-09-2025
--}}
@extends('layouts.app')
@section('content')
<div class="flex justify-center items-center">
    <div class="flex flex-col lg:flex-row lg:justify-between items-start gap-4 m-4 w-full">
        <div class="flex flex-col items-center w-full lg:w-1/4 gap-2">
            @include('profile.userTarget')
        </div>
        <div class="flex flex-col items-center w-full lg:w-3/4 gap-2">
            @include('profile.profileInformation')
            @include('profile.updatePassword')
            @include('profile.browserSessions')
        </div>
    </div>
</div>
@endsection