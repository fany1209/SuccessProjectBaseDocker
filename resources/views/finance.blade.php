{{--
Finance
Fecha de creación: 18-12-2025
Creado por: Stefany
Actualizado por: Stefany
Fecha de actualización: 23-01-2026
--}}
@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('finance.formats')

@endsection