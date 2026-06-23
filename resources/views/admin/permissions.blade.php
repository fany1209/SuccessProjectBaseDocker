{{--permissions
12/08/25
stefany 
--}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="badge badge-success">Permissions</h1>
@stop

@section('content')
<div>
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.permissions.index') }}" id="searchForm" class="d-flex w-100">
            <input type="text" class="form-control" name="search" placeholder="Search Name" value="{{ request('search') }}" id="searchInput">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Since</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->description ?? '-' }}</td>
                        <td>{{ $permission->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No permissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-2">
            {{ $permissions->appends(['search' => request('search')])->links('bootstrap-links') }}
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let typingTimer;
    const doneTypingInterval = 500; // 500ms debounce
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    searchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            searchForm.submit();
        }, doneTypingInterval);
    });

    searchInput.addEventListener('keydown', () => {
        clearTimeout(typingTimer);
    });
</script>
@stop