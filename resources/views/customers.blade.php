@extends('layouts.app')
@section('content')
<x-section-1>
   @if(auth()->check() && auth()->user()->hasRole('Admin'))
   <a href="{{ route('admin.portal-users.index') }}" 
        class="inline-flex items-center px-4 py-2 bg-[#198754] hover:bg-[#157347] text-white rounded-lg transition font-medium shadow-sm ml-auto">
        <i class="fas fa-users-cog mr-2"></i> Customer Portal
    </a>
    @endif
    @include('customers.tools')
    @include('customers.table')
</x-section-1>
@endsection